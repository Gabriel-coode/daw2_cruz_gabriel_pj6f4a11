<?php

    /* importamos el archivo con el array asociativo de productos */
    require_once __DIR__ . "/products.php";
    require_once __DIR__ . "/auth.php";

    requireAuthentication("json");


    const VAT_RATE = 0.21;
    function calculateOrderTotal($products) {

        $subTotal = 0;

        foreach($products as $product){
            $subTotal += $product['price'] * $product['quantity'];
        }

        return round($subTotal *  (1 + VAT_RATE), 2); 
    }

   

    function validateOrderData($order){

    // Revisamos que la estructura de la orden sea un JSON
    if (!is_array($order)) {
        throw new InvalidArgumentException(
            "Invalid order data"
        );
    }

    // Validamos el id de la orden
    /* la función isset responderá true si $order["orderId"] existe o si su valor
       es diferente de null, de otra forma responderá false. */
    if (!isset($order["orderId"]) || !is_string($order["orderId"]) || trim($order["orderId"]) == ""){
        throw new InvalidArgumentException("Order ID is required");
    }

    // validamos la estructura del cliente
    if (!isset($order["customer"]) || !is_array($order["customer"])){
        throw new InvalidArgumentException("Invalid customer data");
    }

    // validamos los datos obligatorios para un cliente
    $requiredFields = ["name", "address", "phone"];

    foreach ($requiredFields as $field){

        if (!isset($order["customer"][$field]) || !is_string($order["customer"][$field]) || trim($order["customer"][$field]) === "") {
            throw new InvalidArgumentException("Invalid customer field: " . $field);
        }

    }

    // se valida que formato del email sea el correcto
    $email = $order["customer"]["email"] ?? null;

    if (!is_string($email) || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
        throw new InvalidArgumentException("Invalid email address");
    }

    return true;


    }


    function createOrder($order, $products){

        $selectedProducts = [];

        if(empty($order["products"])) {
            throw new InvalidArgumentException(
                "Order must contain at least one product"
            );
        }

        foreach ($order['products'] as $selectedProduct){

            $quantity = $selectedProduct["quantity"];

            if (filter_var($quantity, FILTER_VALIDATE_INT) === false || (int) $quantity <= 0){

                throw new InvalidArgumentException(
                    "Quantity must be positive integer"
                );

            }  

            $found = false;

            foreach ($products as $catalogProduct){

                if($selectedProduct['id'] === $catalogProduct['id']){

                    $found = true;

                    $selectedProducts[] = [
                        "id" => $catalogProduct["id"],
                        "name" => $catalogProduct["name"],
                        "price" => $catalogProduct["price"],
                        "quantity" => (int) $quantity
                    ];

                    break;
                }
            }

            if (!$found){
                throw new InvalidArgumentException(
                    "Product not found: " . $selectedProduct['id']
                );
            }
        }

        $order["products"] = $selectedProducts;

        $order["totalWithVat"] = calculateOrderTotal($selectedProducts);

        return $order;


    }


        // se recibe como parámetro el array que contiene la orden
    function saveOrder($order){

        $folder = __DIR__ . "/../../orders";
        $file = $folder . "/orders.json";

        // se comprueba que el directorio existe
        if (!is_dir($folder)) {
            if (!mkdir($folder, 0755, true)) {
                throw new RuntimeException(("Cannot create orders directory"));
            }
        }

        // se inicializa el array en donde se guardarán las órdenes. 
        $orders = [];

        // Se lee la orden existente si el archivo existe
        if (file_exists($file)) {

            $json = file_get_contents(($file));

            if ($json === false) {
                throw new RuntimeException("Cannot read orders file");
            }

            $storedOrders = json_decode($json, true);

            if (!is_array($storedOrders)) {
                throw new RuntimeException("Invalid JSON data");
            }

            $orders = $storedOrders;

        }

        // Se revisa si el ID y por tanto la ORDEN ya existe dentro del JSON
        foreach ($orders as $existingOrder){
            if (($existingOrder["orderId"] ?? null) === ($order["orderId"] ?? null)) {
                throw new InvalidArgumentException("Order ID " . $existingOrder['orderId'] . " already exists." );
            }
        }

        // si el parámetro que recibimos en la función pasa todas las comprobaciones
        // anteriores, se procede a agregar la orden al array "$orders".
        $orders[] = $order;

        // Convertimos el array con la orden agregada a JSON
        $json  = json_encode($orders, JSON_PRETTY_PRINT);

        if ($json === false) {
            throw new RuntimeException("Cannot create order data");
        }

        // Guardar todas las ordenes
        if (file_put_contents($file, $json, LOCK_EX) === false) {
            throw new RuntimeException("Cannot save order");

        }

        return true;

    }

    header("Content-Type: application/json; charset=utf-8");

    // Only allow POST requests
    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        http_response_code(405);

        header("Allow: POST");

        echo json_encode(["error" => "Method not allowed"]);

        exit;
    }

    try {

    // Check incoming products structure
    if (!isset($_POST["products"]) || !is_array($_POST["products"])){
        throw new InvalidArgumentException("Select al least one product");
    
    }

    // Validate each received product structure
    foreach ($_POST["products"] as $product) {

        if (!is_array($product) || !isset($product["id"], $product["quantity"]) || !is_string($product["id"]) || !is_string($product["quantity"])) {
            
            throw new InvalidArgumentException("Ivalid product data");

        }

    }

    // Buil de order from the POST request
    $order = [
        "orderId" => $_POST["orderId"] ?? null,

         "customer" => [
            "name"=> $_POST["name"] ?? null,
            "address" => $_POST["address"] ?? null,
            "email" => $_POST["email"] ?? null,
            "phone" =>$_POST["phone"] ?? null
         ],

         "products" => $_POST["products"]
    ];

    // Validate customer and order information
    validateOrderData($order);

    // Validate products and calculate total
    $order = createOrder($order, $products);

    // Save the complete order
    saveOrder($order);

    // Successful creation
    http_response_code(201);

    echo json_encode(["totalWithVat" => $order["totalWithVat"]]);

    } catch (InvalidArgumentException $e){

        $status = str_contains($e->getMessage(), "already exists") ? 409 : 400;

        http_response_code($status);

        echo json_encode(["error" => $e->getMessage()]);

    } catch (RuntimeException $e) {

        http_response_code(500);

        echo json_encode(["error" => "Internal server error"]);
    }
    
?>
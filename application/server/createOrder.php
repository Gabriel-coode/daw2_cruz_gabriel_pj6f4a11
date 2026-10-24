<?php

    /* importamos el archivo con el array asociativo de productos */
    require_once __DIR__ . "/products.php";

    $order = [

        "orderId" => "ORD-001",

        "customer" => [
            "id" => "000123M",
            "name" => "Gabriel Cruz",
            "email" => "gabrielcruz@gmail.com",
            "phone" => "666777834",
            "address" => "Barcelona Street 1234"
        ],

        "products" => [
            [
                "id" => "BAT-003",
                "quantity" => 8
            ],
            [
                "id" => "WHE-002",
                "quantity" => 10 
            ]
        ],  

    ];


    const VAT_RATE = 0.21;
    function calculateOrderTotal($products) {

        $subTotal = 0;

        foreach($products as $product){
            $subTotal += $product['price'] * $product['quantity'];
        }

        return round($subTotal *  (1 + VAT_RATE), 2); 
    }
    function createOrder($order, $products){

        $selectedProducts = [];

        foreach ($order['products'] as $selectedProduct){
            foreach ($products as $catalogProduct){
                if($selectedProduct['id'] === $catalogProduct['id']){

                    $selectedProducts[] = [
                        "id" => $catalogProduct["id"],
                        "name" => $catalogProduct["name"],
                        "price" => $catalogProduct["price"],
                        "quantity" => $selectedProduct["quantity"]
                    ];

                    break;
                }
            }
        }

        $order["products"] = $selectedProducts;

        $order["totalWithVat"] = calculateOrderTotal($selectedProducts);

        return $order;


    }

    $order = createOrder($order, $products);
        
    echo "Total with VAT: " .
    number_format($order["totalWithVat"], 2) .
    " €";
?>
<?php

function escapeHtml($value) {
    return htmlspecialchars(
        (string) ($value ?? ""),
        ENT_QUOTES,
        "UTF-8"
    );
}

function readOrders() {
    $file = __DIR__ . "/../../orders/orders.json";

    if (!is_file($file) || !is_readable($file)) {
        http_response_code(404);
        throw new RuntimeException("Orders file not found or unreadable");
    }

    $json = file_get_contents($file);

    if ($json === false) {
        http_response_code(404);
        throw new RuntimeException("Cannot read orders file");
    }

    $orders = json_decode($json, true);

    if (json_last_error() !== JSON_ERROR_NONE || !is_array($orders) ) {
        
        http_response_code(500);
        throw new RuntimeException("Invalid orders file format");
    }

    return $orders;
}

function renderPageStart($title) {
    echo '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>' . escapeHtml($title) . '</title>
        <link rel="stylesheet" href="../client/styles.css">
    </head>
    <body>
    <main>
        <header class="brand-header">
            <img src="../client/brand-mark.svg" alt="Automotive Order Management logo">
            <div><h1>' . escapeHtml($title) . '</h1><p>Automotive Order Management</p></div>
        </header>';
}

function renderNavigation() {
    echo '
    <nav aria-label="Navigation">
        <p><a href="../client/viewOrders.html">
            Back to Order Queries
        </a></p>
        <p><a href="../client/operations.html">
            Back to Operations
        </a></p>
        <p><a href="../client/index.html">
            Back to Home
        </a></p>
    </nav>';
}

function renderPageEnd() {
    renderNavigation();
    echo '</main></body></html>';
}

function renderOrder($order) {
     echo '<section>';

    echo '<h2>Order ' .
        escapeHtml($order["orderId"] ?? "") .
        '</h2>';

    echo '<table border="1" cellpadding="8">
        <tbody>
        <tr>
            <th scope="row">Order ID</th>
            <td>' . escapeHtml($order["orderId"] ?? "") . '</td>
        </tr>';

    $customer = $order["customer"] ?? [];

    foreach (["id", "name", "address", "email", "phone"] as $field) {

        if (!isset($customer[$field])) {
            continue;
        }

        echo '<tr>
            <th scope="row">' . escapeHtml(ucfirst($field)) . '</th>
            <td>' . escapeHtml($customer[$field]) . '</td>
        </tr>';
    }

    echo '<tr>
        <th scope="row">Total with VAT</th>
        <td>' .
        number_format((float) ($order["totalWithVat"] ?? 0), 2) .
        ' EUR</td>
    </tr>
    </tbody>
    </table>';

    echo '<h3>Products</h3>';

    echo '<table border="1" cellpadding="8">
        <thead>
            <tr>
                <th>Product ID</th>
                <th>Product Name</th>
                <th>Unit Price (EUR)</th>
                <th>Quantity</th>
            </tr>
        </thead>
        <tbody>';

    foreach (($order["products"] ?? []) as $product) {
        echo '<tr>
            <td>' . escapeHtml($product["id"] ?? "") . '</td>
            <td>' . escapeHtml($product["name"] ?? "") . '</td>
            <td>' .
                number_format((float) ($product["price"] ?? 0), 2) .
            '</td>
            <td>' . escapeHtml($product["quantity"] ?? "") . '</td>
        </tr>';
    }

    echo '</tbody></table></section><hr>';
}

?>

<?php

require_once __DIR__ . "/orderViewFunctions.php";
require_once __DIR__ . "/auth.php";

requireAuthentication("html");

if ($_SERVER["REQUEST_METHOD"] !== "GET") {
    http_response_code(405);
    header("Allow: GET");

    renderPageStart("Method Not Allowed");
    echo "<p>Only GET requests are allowed.</p>";
    renderPageEnd();
    exit;
}

try {
    $orders = readOrders();

    $orderId = $_GET["orderId"] ?? "";

    if (!is_string($orderId) || trim($orderId) === "") {
        http_response_code(400);

        renderPageStart("Invalid Order ID");
        echo "<p>Please enter a valid order ID.</p>";
        renderPageEnd();
        exit;
    }

    $foundOrder = null;

    foreach ($orders as $order) {

        if (($order["orderId"] ?? null) === $orderId) {
            $foundOrder = $order;
            break;
        }
    }

    if ($foundOrder === null) {
        http_response_code(404);

        renderPageStart("Order Not Found");
        echo "<p>Order " . escapeHtml($orderId) .
             " was not found.</p>";
        renderPageEnd();
        exit;
    }

    http_response_code(200);

    renderPageStart("Order Details");
    renderOrder($foundOrder);
    renderPageEnd();

} catch (RuntimeException $e) {

    renderPageStart("Error");
    echo "<p>" . escapeHtml($e->getMessage()) . "</p>";
    renderPageEnd();
}
?>
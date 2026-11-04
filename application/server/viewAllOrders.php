<?php

require_once __DIR__ . "/orderViewFunctions.php";

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

    http_response_code(200);
    renderPageStart("All Orders");

    if (empty($orders)) {
        echo "<p>No orders have been created yet.</p>";
    } else {
        foreach ($orders as $order) {
            renderOrder($order);
        }
    }

    renderPageEnd();

} catch (RuntimeException $e) {
    renderPageStart("Error");
    echo "<p>" . escapeHtml($e->getMessage()) . "</p>";
    renderPageEnd(); 

}
?>
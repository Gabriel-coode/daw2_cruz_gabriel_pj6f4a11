
<?php

require_once __DIR__ . "/auth.php";

header("Content-Type: application/json; charset=utf-8");
header("Cache-Control: no-store");

if ($_SERVER["REQUEST_METHOD"] !== "GET") {
    http_response_code(405);
    header("Allow: GET");

    echo json_encode([
        "error" => "Method not allowed"
    ]);

    exit;
}

if (!isAuthenticated()) {
    http_response_code(401);

    echo json_encode([
        "authenticated" => false
    ]);

    exit;
}

http_response_code(200);

echo json_encode([
    "authenticated" => true,
    "username" => $_SESSION["username"]
]);
?>

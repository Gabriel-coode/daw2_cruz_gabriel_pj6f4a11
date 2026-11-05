
<?php

require_once __DIR__ . "/auth.php";

header("Content-Type: application/json; charset=utf-8");
header("Cache-Control: no-store");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    header("Allow: POST");

    echo json_encode([
        "error" => "Method not allowed"
    ]);

    exit;
}

startUserSession();

// Remove all session data
$_SESSION = [];

// Remove the session cookie
if (ini_get("session.use_cookies")) {

    $params = session_get_cookie_params();

    setcookie(
        session_name(),
        "",
        [
            "expires" => time() - 3600,
            "path" => $params["path"],
            "domain" => $params["domain"],
            "secure" => $params["secure"],
            "httponly" => $params["httponly"],
            "samesite" => $params["samesite"] ?? "Lax"
        ]
    );
}

// Destroy the server-side session
session_destroy();

http_response_code(200);

echo json_encode([
    "success" => true
]);
?>

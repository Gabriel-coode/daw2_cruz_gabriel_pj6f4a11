<?php

function startUserSession() {
    if (session_status() === PHP_SESSION_NONE) {

        session_start([
            "use_strict_mode" => true,
            "cookie_httponly" => true,
             "cookie_samesite" => "Lax",
            "cookie_secure" => !empty($_SERVER["HTTPS"])
                && $_SERVER["HTTPS"] !== "off"
        ]);

    } 
}

function isAuthenticated() {

    startUserSession();

    return isset($_SESSION["username"]);

}

function requireAuthentication($responseType = "html") {

    if (isAuthenticated()) {
        return;        
    }

    http_response_code(401);

    if ($responseType === "json") {

        header("Content-Type: application/json; charset=utf-8");

         echo json_encode([
            "error" => "Authentication required"
        ]);

    } else {

        header("Content-Type: text/html; charset=utf-8");

        echo '<!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <title>Authentication Required</title>
        </head>
        <body>
            <h1>Authentication Required</h1>
            <p>Please log in to access this operation.</p>
            <a href="../client/login.html">Go to Login</a>
        </body>
        </html>';
    }

    exit;
}

?>
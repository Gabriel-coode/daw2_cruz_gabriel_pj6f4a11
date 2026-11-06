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

$username = $_POST["username"] ?? null;
$password = $_POST["password"] ?? null;

if (
    !is_string($username) ||
    trim($username) === "" ||
    !is_string($password) ||
    $password === ""
) {
    http_response_code(400);

    echo json_encode([
        "error" => "Username and password are required"
    ]);

    exit;
}

$file = __DIR__ . "/../../users/users.json";

if (!is_file($file) || !is_readable($file)) {

    http_response_code(500);

    echo json_encode([
        "error" => "Authentication service unavailable"
    ]);

    exit;
}

$json = file_get_contents($file);

if ($json === false) {

    http_response_code(500);

    echo json_encode([
        "error" => "Authentication service unavailable"
    ]);

    exit;
}

$users = json_decode($json, true);

if (!is_array($users) ) {

    http_response_code(500);

    echo json_encode([
        "error" => "Invalid users file"
    ]);

    exit;
}

$authenticatedUser = null;

foreach ($users as $user) {

    if (
        is_array($user) &&
        isset($user["user"], $user["password"]) &&
        is_string($user["user"]) &&
        is_string($user["password"]) &&
        $user["user"] === $username &&
        
        /* password verify devuelve true cuando coincide con el hash y false 
           cuando no coincide. */
        password_verify($password, $user["password"])
    ) {
        $authenticatedUser = $user["user"];
        break;
    }
}


if ($authenticatedUser === null) {

    http_response_code(401);

    echo json_encode([
        "error" => "Invalid username or password"
    ]);

    exit;
}

startUserSession();

/* esta función genera un id de sessión nuevo después de indentificar al usuario */
session_regenerate_id(true);


// guardamos el nombre del usuario autenticado para comprobarlo en las siguiente peticiones
$_SESSION["username"] = $authenticatedUser;

http_response_code(200);

echo json_encode([
    "success" => true
]);


?>
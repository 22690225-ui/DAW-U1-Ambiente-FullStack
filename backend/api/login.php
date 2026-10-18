<?php

session_start();

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'ok' => false,
        'message' => 'Método no permitido.'
    ]);
    exit;
}

$datos = json_decode(file_get_contents('php://input'), true);

if (!is_array($datos)) {
    http_response_code(400);
    echo json_encode([
        'ok' => false,
        'message' => 'La solicitud no contiene datos válidos.'
    ]);
    exit;
}

// Permitir cerrar la sesión desde este mismo endpoint.
if (($datos['action'] ?? '') === 'logout') {
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $parametros = session_get_cookie_params();

        setcookie(
            session_name(),
            '',
            time() - 42000,
            $parametros['path'],
            $parametros['domain'],
            $parametros['secure'],
            $parametros['httponly']
        );
    }

    session_destroy();

    echo json_encode([
        'ok' => true,
        'message' => 'Sesión cerrada correctamente.'
    ]);
    exit;
}

$username = trim($datos['username'] ?? '');
$password = $datos['password'] ?? '';

// Credenciales de demostración para la práctica.
if ($username === 'admin' && $password === '123456') {
    session_regenerate_id(true);

    $_SESSION['user'] = [
        'username' => 'admin'
    ];

    echo json_encode([
        'ok' => true,
        'message' => 'Inicio de sesión correcto.',
        'user' => $_SESSION['user']
    ]);
    exit;
}

http_response_code(401);

echo json_encode([
    'ok' => false,
    'message' => 'Usuario o contraseña incorrectos.'
]);

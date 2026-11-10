<?php

session_start();

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/database.php';

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

if ($username === '' || $password === '') {
    http_response_code(422);
    echo json_encode([
        'ok' => false,
        'message' => 'Debes ingresar usuario y contraseña.'
    ]);
    exit;
}

try {
    $pdo = conectarDB();

    $consulta = $pdo->prepare(
        'SELECT id, usuario, nombre, email, password_hash, rol, activo
         FROM usuarios
         WHERE usuario = :usuario
         LIMIT 1'
    );

    $consulta->execute(['usuario' => $username]);
    $usuario = $consulta->fetch();

    if (
        !$usuario ||
        !password_verify($password, $usuario['password_hash']) ||
        (int) $usuario['activo'] !== 1
    ) {
        http_response_code(401);
        echo json_encode([
            'ok' => false,
            'message' => 'Usuario o contraseña incorrectos.'
        ]);
        exit;
    }

    session_regenerate_id(true);

    $_SESSION['user'] = [
        'id' => $usuario['id'],
        'username' => $usuario['usuario'],
        'nombre' => $usuario['nombre'],
        'email' => $usuario['email'],
        'rol' => $usuario['rol']
    ];

    echo json_encode([
        'ok' => true,
        'message' => 'Inicio de sesión correcto.',
        'user' => $_SESSION['user']
    ], JSON_UNESCAPED_UNICODE);

} catch (Throwable $e) {
    http_response_code(500);

    echo json_encode([
        'ok' => false,
        'message' => 'Error interno al iniciar sesión.'
    ]);
}
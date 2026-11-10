<?php

session_start();
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

// Solo el administrador del sistema puede acceder.
exigirRoles(['ADMIN_SISTEMA']);

$metodo = $_SERVER['REQUEST_METHOD'];

try {
    $pdo = conectarDB();

    // CONSULTAR USUARIOS
    if ($metodo === 'GET') {
        $consulta = $pdo->query(
            "SELECT id, usuario, nombre, email, rol, activo, created_at, updated_at
             FROM usuarios
             ORDER BY id DESC"
        );

        echo json_encode([
            'ok' => true,
            'usuarios' => $consulta->fetchAll()
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }

    // Leer los datos enviados.
    $datos = json_decode(file_get_contents('php://input'), true);

    if (!is_array($datos)) {
        http_response_code(400);
        echo json_encode([
            'ok' => false,
            'message' => 'Los datos enviados no son válidos.'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // REGISTRAR USUARIO
    if ($metodo === 'POST') {
        $usuario = trim($datos['usuario'] ?? '');
        $nombre = trim($datos['nombre'] ?? '');
        $email = trim($datos['email'] ?? '');
        $password = $datos['password'] ?? '';
        $rol = $datos['rol'] ?? 'CLIENTE';
        $activo = $datos['activo'] ?? 1;

        if ($usuario === '' || $nombre === '' || $email === '' || $password === '') {
            http_response_code(422);
            echo json_encode([
                'ok' => false,
                'message' => 'Todos los campos obligatorios deben completarse.'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            http_response_code(422);
            echo json_encode([
                'ok' => false,
                'message' => 'El correo electrónico no es válido.'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        if (strlen($password) < 6) {
            http_response_code(422);
            echo json_encode([
                'ok' => false,
                'message' => 'La contraseña debe tener al menos 6 caracteres.'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        if (!in_array($rol, ['ADMIN_SISTEMA', 'ADMIN_SERVICIOS', 'CLIENTE'], true)
            || !in_array((int)$activo, [0, 1], true)) {
            http_response_code(422);
            echo json_encode([
                'ok' => false,
                'message' => 'El rol o estado seleccionado no es válido.'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $consulta = $pdo->prepare(
            "SELECT id FROM usuarios WHERE usuario = :usuario OR email = :email LIMIT 1"
        );
        $consulta->execute([
            ':usuario' => $usuario,
            ':email' => $email
        ]);

        if ($consulta->fetch()) {
            http_response_code(409);
            echo json_encode([
                'ok' => false,
                'message' => 'El nombre de usuario o correo electrónico ya está registrado.'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);

        $consulta = $pdo->prepare(
            "INSERT INTO usuarios (usuario, nombre, email, password_hash, rol, activo)
             VALUES (:usuario, :nombre, :email, :password_hash, :rol, :activo)"
        );

        $consulta->execute([
            ':usuario' => $usuario,
            ':nombre' => $nombre,
            ':email' => $email,
            ':password_hash' => $hash,
            ':rol' => $rol,
            ':activo' => (int)$activo
        ]);

        http_response_code(201);
        echo json_encode([
            'ok' => true,
            'message' => 'Usuario registrado correctamente.',
            'id' => (int)$pdo->lastInsertId()
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }

    // ACTUALIZAR USUARIO
    if ($metodo === 'PUT') {
        $id = filter_var($datos['id'] ?? null, FILTER_VALIDATE_INT);
        $usuario = trim($datos['usuario'] ?? '');
        $nombre = trim($datos['nombre'] ?? '');
        $email = trim($datos['email'] ?? '');
        $rol = $datos['rol'] ?? '';

        if (!$id || $usuario === '' || $nombre === '' || $email === '') {
            http_response_code(422);
            echo json_encode([
                'ok' => false,
                'message' => 'Verifica los datos del usuario.'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            http_response_code(422);
            echo json_encode([
                'ok' => false,
                'message' => 'El correo electrónico no es válido.'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        if (!in_array($rol, ['ADMIN_SISTEMA', 'ADMIN_SERVICIOS', 'CLIENTE'], true)) {
            http_response_code(422);
            echo json_encode([
                'ok' => false,
                'message' => 'El rol seleccionado no es válido.'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $consulta = $pdo->prepare(
            "SELECT id FROM usuarios WHERE (usuario = :usuario OR email = :email)
             AND id <> :id LIMIT 1"
        );

        $consulta->execute([
            ':usuario' => $usuario,
            ':email' => $email,
            ':id' => $id
        ]);

        if ($consulta->fetch()) {
            http_response_code(409);
            echo json_encode([
                'ok' => false,
                'message' => 'El usuario o correo electrónico ya está registrado.'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $password = $datos['password'] ?? '';

        if ($password !== '') {
            if (strlen($password) < 6) {
                http_response_code(422);
                echo json_encode([
                    'ok' => false,
                    'message' => 'La contraseña debe tener al menos 6 caracteres.'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            $hash = password_hash($password, PASSWORD_DEFAULT);

            $consulta = $pdo->prepare(
                "UPDATE usuarios
                 SET usuario = :usuario, nombre = :nombre, email = :email,
                     rol = :rol, password_hash = :password_hash
                 WHERE id = :id"
            );

            $consulta->execute([
                ':usuario' => $usuario,
                ':nombre' => $nombre,
                ':email' => $email,
                ':rol' => $rol,
                ':password_hash' => $hash,
                ':id' => $id
            ]);
        } else {
            $consulta = $pdo->prepare(
                "UPDATE usuarios
                 SET usuario = :usuario, nombre = :nombre, email = :email, rol = :rol
                 WHERE id = :id"
            );

            $consulta->execute([
                ':usuario' => $usuario,
                ':nombre' => $nombre,
                ':email' => $email,
                ':rol' => $rol,
                ':id' => $id
            ]);
        }

        if ($consulta->rowCount() === 0) {
            $verificar = $pdo->prepare("SELECT id FROM usuarios WHERE id = :id");
            $verificar->execute([':id' => $id]);

            if (!$verificar->fetch()) {
                http_response_code(404);
                echo json_encode([
                    'ok' => false,
                    'message' => 'El usuario no existe.'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }
        }

        echo json_encode([
            'ok' => true,
            'message' => 'Usuario actualizado correctamente.'
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }

    // CAMBIAR ESTADO DEL USUARIO
    if ($metodo === 'PATCH' || $metodo === 'DELETE') {
        $id = filter_var($datos['id'] ?? null, FILTER_VALIDATE_INT);
        $activo = $metodo === 'DELETE' ? 0 : ($datos['activo'] ?? null);

        if (!$id || !in_array($activo, [0, 1, '0', '1'], true)) {
            http_response_code(422);
            echo json_encode([
                'ok' => false,
                'message' => 'El identificador o estado no es válido.'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $consulta = $pdo->prepare(
            "UPDATE usuarios SET activo = :activo WHERE id = :id"
        );

        $consulta->execute([
            ':activo' => (int)$activo,
            ':id' => $id
        ]);

        if ($consulta->rowCount() === 0) {
            $verificar = $pdo->prepare("SELECT id FROM usuarios WHERE id = :id");
            $verificar->execute([':id' => $id]);

            if (!$verificar->fetch()) {
                http_response_code(404);
                echo json_encode([
                    'ok' => false,
                    'message' => 'El usuario no existe.'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }
        }

        echo json_encode([
            'ok' => true,
            'message' => 'Estado del usuario actualizado correctamente.'
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }

    http_response_code(405);
    echo json_encode([
        'ok' => false,
        'message' => 'Método HTTP no permitido.'
    ], JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    if ($e->getCode() === '23000') {
        http_response_code(409);
        echo json_encode([
            'ok' => false,
            'message' => 'El usuario o correo electrónico ya está registrado.'
        ], JSON_UNESCAPED_UNICODE);
    } else {
        http_response_code(500);
        echo json_encode([
            'ok' => false,
            'message' => 'Error al procesar la operación.'
        ], JSON_UNESCAPED_UNICODE);
    }
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'ok' => false,
        'message' => 'Error interno del servidor.'
    ], JSON_UNESCAPED_UNICODE);
}
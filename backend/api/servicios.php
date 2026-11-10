<?php

session_start();

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

$metodo = $_SERVER['REQUEST_METHOD'];

try {
    $pdo = conectarDB();

    // Consultar servicios
    if ($metodo === 'GET') {
        $usuario = exigirRoles([
            'ADMIN_SISTEMA',
            'ADMIN_SERVICIOS',
            'CLIENTE'
        ]);

        if ($usuario['rol'] === 'CLIENTE') {
            $consulta = $pdo->prepare(
                'SELECT id, nombre, descripcion, precio, duracion_minutos, activo
                 FROM servicios
                 WHERE activo = 1
                 ORDER BY id DESC'
            );
        } else {
            $consulta = $pdo->prepare(
                'SELECT id, nombre, descripcion, precio, duracion_minutos, activo
                 FROM servicios
                 ORDER BY id DESC'
            );
        }

        $consulta->execute();

        echo json_encode([
            'ok' => true,
            'servicios' => $consulta->fetchAll()
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }

    // Las demás operaciones requieren permisos administrativos
    exigirRoles(['ADMIN_SISTEMA', 'ADMIN_SERVICIOS']);

    $datos = json_decode(file_get_contents('php://input'), true);

    if (!is_array($datos)) {
        http_response_code(400);
        echo json_encode([
            'ok' => false,
            'message' => 'Los datos enviados no son válidos.'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Registrar servicio
    if ($metodo === 'POST') {
        $nombre = trim($datos['nombre'] ?? '');
        $descripcion = trim($datos['descripcion'] ?? '');
        $precio = $datos['precio'] ?? null;
        $duracion = $datos['duracion_minutos'] ?? null;

        if (
            $nombre === '' ||
            $descripcion === '' ||
            !is_numeric($precio) ||
            (float) $precio < 0 ||
            filter_var($duracion, FILTER_VALIDATE_INT) === false ||
            (int) $duracion <= 0
        ) {
            http_response_code(422);
            echo json_encode([
                'ok' => false,
                'message' => 'Verifica el nombre, descripción, precio y duración.'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $consulta = $pdo->prepare(
            'INSERT INTO servicios
             (nombre, descripcion, precio, duracion_minutos, activo)
             VALUES (:nombre, :descripcion, :precio, :duracion, 1)'
        );

        $consulta->execute([
            'nombre' => $nombre,
            'descripcion' => $descripcion,
            'precio' => $precio,
            'duracion' => $duracion
        ]);

        http_response_code(201);

        echo json_encode([
            'ok' => true,
            'message' => 'Servicio registrado correctamente.',
            'id' => $pdo->lastInsertId()
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }

    // Modificar servicio
    if ($metodo === 'PUT') {
        $id = filter_var($datos['id'] ?? null, FILTER_VALIDATE_INT);
        $nombre = trim($datos['nombre'] ?? '');
        $descripcion = trim($datos['descripcion'] ?? '');
        $precio = $datos['precio'] ?? null;
        $duracion = $datos['duracion_minutos'] ?? null;

        if (
            !$id || $nombre === '' || $descripcion === '' ||
            !is_numeric($precio) || (float) $precio < 0 ||
            filter_var($duracion, FILTER_VALIDATE_INT) === false ||
            (int) $duracion <= 0
        ) {
            http_response_code(422);
            echo json_encode([
                'ok' => false,
                'message' => 'Verifica los datos del servicio.'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $consulta = $pdo->prepare(
            'UPDATE servicios
             SET nombre = :nombre,
                 descripcion = :descripcion,
                 precio = :precio,
                 duracion_minutos = :duracion
             WHERE id = :id'
        );

        $consulta->execute([
            'nombre' => $nombre,
            'descripcion' => $descripcion,
            'precio' => $precio,
            'duracion' => $duracion,
            'id' => $id
        ]);

        if ($consulta->rowCount() === 0) {
            $verificar = $pdo->prepare(
                'SELECT id FROM servicios WHERE id = :id'
            );
            $verificar->execute(['id' => $id]);

            if (!$verificar->fetch()) {
                http_response_code(404);
                echo json_encode([
                    'ok' => false,
                    'message' => 'El servicio no existe.'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }
        }

        echo json_encode([
            'ok' => true,
            'message' => 'Servicio actualizado correctamente.'
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }

    // Cambiar estado del servicio
    if ($metodo === 'PATCH') {
        $id = filter_var($datos['id'] ?? null, FILTER_VALIDATE_INT);
        $activo = $datos['activo'] ?? null;

        if (!$id || !in_array($activo, [0, 1, '0', '1'], true)) {
            http_response_code(422);
            echo json_encode([
                'ok' => false,
                'message' => 'El identificador o estado no son válidos.'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $consulta = $pdo->prepare(
            'UPDATE servicios SET activo = :activo WHERE id = :id'
        );

        $consulta->execute([
            'activo' => (int) $activo,
            'id' => $id
        ]);

        if ($consulta->rowCount() === 0) {
            $verificar = $pdo->prepare(
                'SELECT id FROM servicios WHERE id = :id'
            );
            $verificar->execute(['id' => $id]);

            if (!$verificar->fetch()) {
                http_response_code(404);
                echo json_encode([
                    'ok' => false,
                    'message' => 'El servicio no existe.'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }
        }

        echo json_encode([
            'ok' => true,
            'message' => 'Estado del servicio actualizado.'
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }

    // Inactivar servicio sin eliminarlo físicamente
    if ($metodo === 'DELETE') {
        $id = filter_var($datos['id'] ?? null, FILTER_VALIDATE_INT);

        if (!$id) {
            http_response_code(422);
            echo json_encode([
                'ok' => false,
                'message' => 'El identificador del servicio no es válido.'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $consulta = $pdo->prepare(
            'UPDATE servicios SET activo = 0 WHERE id = :id'
        );

        $consulta->execute(['id' => $id]);

        if ($consulta->rowCount() === 0) {
            $verificar = $pdo->prepare(
                'SELECT id FROM servicios WHERE id = :id'
            );
            $verificar->execute(['id' => $id]);

            if (!$verificar->fetch()) {
                http_response_code(404);
                echo json_encode([
                    'ok' => false,
                    'message' => 'El servicio no existe.'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }
        }

        echo json_encode([
            'ok' => true,
            'message' => 'Servicio inactivado correctamente.'
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }

    http_response_code(405);
    echo json_encode([
        'ok' => false,
        'message' => 'Método no permitido.'
    ], JSON_UNESCAPED_UNICODE);

} catch (Throwable $e) {
    http_response_code(500);

    echo json_encode([
        'ok' => false,
        'message' => 'Ocurrió un error al procesar los servicios.'
    ], JSON_UNESCAPED_UNICODE);
}
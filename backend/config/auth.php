<?php

function exigirAutenticacion(): array
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    if (!isset($_SESSION['user'])) {
        http_response_code(401);

        echo json_encode([
            'ok' => false,
            'message' => 'Debes iniciar sesión para continuar.'
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }

    return $_SESSION['user'];
}

function exigirRoles(array $rolesPermitidos): array
{
    $usuario = exigirAutenticacion();

    if (!in_array($usuario['rol'], $rolesPermitidos, true)) {
        http_response_code(403);

        echo json_encode([
            'ok' => false,
            'message' => 'No tienes permisos para realizar esta operación.'
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }

    return $usuario;
}
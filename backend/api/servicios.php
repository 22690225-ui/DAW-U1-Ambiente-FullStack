<?php
header('Content-Type: application/json; charset=utf-8');

$servicios = [
    [
        'id' => 1,
        'nombre' => 'Desarrollo web',
        'descripcion' => 'Creación de páginas y aplicaciones web.',
        'categoria' => 'Tecnología'
    ],
    [
        'id' => 2,
        'nombre' => 'Soporte técnico',
        'descripcion' => 'Mantenimiento y solución de problemas informáticos.',
        'categoria' => 'Tecnología'
    ],
    [
        'id' => 3,
        'nombre' => 'Diseño gráfico',
        'descripcion' => 'Diseño de imágenes y materiales digitales.',
        'categoria' => 'Diseño'
    ],
    [
        'id' => 4,
        'nombre' => 'Instalación de redes',
        'descripcion' => 'Configuración e instalación de redes informáticas.',
        'categoria' => 'Redes'
    ]
];

echo json_encode(
    $servicios,
    JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
);


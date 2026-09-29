<?php

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';
$path = '/' . trim($path, '/');

// Aceptar URLs con el prefijo /clinica
if ($path === '/clinica') {
    $path = '/';
} elseif (strpos($path, '/clinica/') === 0) {
    $path = substr($path, strlen('/clinica'));
}

// Las rutas de las páginas que usan los enlaces de tu cabecera
$pages = [
    '/' => __DIR__ . '/../index.php',
    '/index.php' => __DIR__ . '/../index.php',

    '/views/especialidades/especialidades.php'
        => __DIR__ . '/../views/especialidades/especialidades.php',

    '/views/nosotros/nosotros.php'
        => __DIR__ . '/../views/nosotros/nosotros.php',

    '/views/doctor/doctor.php'
        => __DIR__ . '/../views/doctor/doctor.php',

    '/views/users/login_register.php'
        => __DIR__ . '/../views/users/login_register.php',
];

// Las solicitudes de controladores siguen usando public/index.php
if (isset($_GET['controller'])) {
    require __DIR__ . '/../public/index.php';
    exit;
}

// Cargar la página correspondiente a la URL solicitada
if (isset($pages[$path]) && file_exists($pages[$path])) {
    require $pages[$path];
    exit;
}

http_response_code(404);
echo 'Página no encontrada: ' .
    htmlspecialchars($path, ENT_QUOTES, 'UTF-8');

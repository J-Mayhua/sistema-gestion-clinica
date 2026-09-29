<?php

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';

// Acepta URLs con el prefijo /clinica
if ($path === '/clinica') {
    $path = '/';
} elseif (strpos($path, '/clinica/') === 0) {
    $path = substr($path, strlen('/clinica'));
}

// Normaliza la ruta raíz para que cargue el Inicio
if ($path === '/') {
    $path = '/index.php';
}

$pages = [
    '/index.php' => __DIR__ . '/../index.php',

    '/views/especialidades/especialidades.php'
        => __DIR__ . '/../views/especialidades/especialidades.php',

    '/views/nosotros/nosotros.php'
        => __DIR__ . '/../views/nosotros/nosotros.php',

    '/views/doctor/login.php'
        => __DIR__ . '/../views/doctor/login.php',

    '/views/users/login_register.php'
        => __DIR__ . '/../views/users/login_register.php',
];

if (isset($_GET['controller'])) {
    require __DIR__ . '/../public/index.php';
    exit;
}

if (isset($pages[$path]) && file_exists($pages[$path])) {
    require $pages[$path];
    exit;
}

http_response_code(404);
echo 'Ruta no reconocida: ' .
    htmlspecialchars($path, ENT_QUOTES, 'UTF-8');

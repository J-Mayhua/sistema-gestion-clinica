<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';

// Acepta tanto /views/... como /clinica/views/...
if ($path === '/clinica') {
    $path = '/';
} elseif (strpos($path, '/clinica/') === 0) {
    $path = substr($path, strlen('/clinica'));
}

$pages = [
    '/' => __DIR__ . '/../index.php',
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

// Mantiene las rutas antiguas de controladores
if (isset($_GET['controller'])) {
    require __DIR__ . '/../public/index.php';
    exit;
}

if (!isset($pages[$path]) || !file_exists($pages[$path])) {
    http_response_code(404);
    exit('Ruta no encontrada: ' . htmlspecialchars($path, ENT_QUOTES, 'UTF-8'));
}

$viewFile = $pages[$path];

// Por ahora usa tu cabecera existente.
// Esta ya distingue entre paciente autenticado y público.
$headerFile = __DIR__ . '/../views/cabecera/cabecera.php';

$viewFile = $pages[$path];
require __DIR__ . '/../views/layout.php';

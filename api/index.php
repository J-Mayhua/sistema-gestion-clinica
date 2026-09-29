<?php

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';
$path = '/' . trim($path, '/');

// Acepta tanto /especialidades como /clinica/especialidades
if ($path === '/clinica') {
    $path = '/';
} elseif (strpos($path, '/clinica/') === 0) {
    $path = substr($path, strlen('/clinica'));
}

$pages = [
    '/'               => __DIR__ . '/../index.php',
    '/especialidades' => __DIR__ . '/../views/especialidades/especialidades.php',
    '/nosotros'       => __DIR__ . '/../views/nosotros/nosotros.php',
    '/doctor'         => __DIR__ . '/../views/doctor/doctor.php',
    '/acceder'        => __DIR__ . '/../views/users/login_register.php',
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
echo 'Página no encontrada: ' . htmlspecialchars($path, ENT_QUOTES, 'UTF-8');

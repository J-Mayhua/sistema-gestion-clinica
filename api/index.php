<?php

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = rtrim($path, '/') ?: '/';

/*
 * Páginas del sitio.
 * Ajusta cada ruta de archivo para que coincida con dónde está
 * guardada realmente esa página en tu proyecto.
 */
$pages = [
    '/'               => __DIR__ . '/../index.php',
    '/especialidades' => __DIR__ . '/../views/especialidades/especialidades.php',
    '/nosotros'       => __DIR__ . '/../views/nosotros/nosotros.php',
    '/doctor'         => __DIR__ . '/../views/doctor/doctor.php',
    '/acceder'        => __DIR__ . '/../views/users/login_register.php',
];

if (isset($pages[$path]) && file_exists($pages[$path])) {
    require $pages[$path];
    exit;
}

/*
 * Las peticiones de controladores, como
 * ?controller=doctor&action=login, se envían a tu enrutador actual.
 */
if (isset($_GET['controller'])) {
    require __DIR__ . '/../public/index.php';
    exit;
}

http_response_code(404);
echo 'Página no encontrada';

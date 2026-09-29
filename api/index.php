<?php
$root = realpath(__DIR__ . '/..');

// Ruta pedida, sin query string
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// Quitar el prefijo /clinica (así lo usabas en local)
$uri = preg_replace('#^/clinica#', '', $uri);

// Raíz o carpeta → index.php
if ($uri === '' || $uri === '/') {
    $uri = '/index.php';
} elseif (is_dir($root . $uri)) {
    $uri = rtrim($uri, '/') . '/index.php';
}

$file = realpath($root . $uri);

// Solo ejecutar archivos .php dentro del proyecto (y no este mismo router)
if (
    $file
    && strpos($file, $root . DIRECTORY_SEPARATOR) === 0
    && is_file($file)
    && strtolower(pathinfo($file, PATHINFO_EXTENSION)) === 'php'
    && $file !== realpath(__FILE__)
) {
    $_SERVER['SCRIPT_FILENAME'] = $file;
    $_SERVER['PHP_SELF'] = $uri;
    chdir(dirname($file)); // para que los include relativos funcionen
    require $file;
} else {
    http_response_code(404);
    echo 'Página no encontrada';
}

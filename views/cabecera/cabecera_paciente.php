<?php
// views/cabecera/cabecera_paciente.php
// Abre el documento de las páginas del paciente y reutiliza la cabecera común.

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$titulo_pagina_paciente = $titulo_pagina_paciente ?? 'HappyDent — Paciente';
$css_pagina_paciente = $css_pagina_paciente ?? null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= htmlspecialchars($titulo_pagina_paciente, ENT_QUOTES, 'UTF-8') ?></title>

    
    <link rel="stylesheet" href="/clinica/assets/css/cabecera.css">

    <?php if ($css_pagina_paciente !== null): ?>
        <link rel="stylesheet"
              href="<?= htmlspecialchars($css_pagina_paciente, ENT_QUOTES, 'UTF-8') ?>">
    <?php endif; ?>

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<?php require __DIR__ . '/cabecera.php'; ?>

<script src="/clinica/assets/js/cabecera.js"></script>

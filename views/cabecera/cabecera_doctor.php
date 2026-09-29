<?php
// views/cabecera/cabecera_doctor.php
// Abre el documento y muestra la navegación del doctor.

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$titulo_pagina_doctor = $titulo_pagina_doctor ?? 'HappyDent — Doctor';
$css_pagina_doctor = $css_pagina_doctor ?? null;

$pagina_actual = basename($_SERVER['SCRIPT_NAME'] ?? '');
$directorio_actual = basename(dirname($_SERVER['SCRIPT_NAME'] ?? ''));

$nombre_doctor = htmlspecialchars(
    (string) ($_SESSION['doctor_name'] ?? ''),
    ENT_QUOTES,
    'UTF-8'
);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= htmlspecialchars(
        (string) $titulo_pagina_doctor,
        ENT_QUOTES,
        'UTF-8'
    ) ?></title>

    <link rel="stylesheet" href="/clinica/assets/css/cabecera.css">

    <?php if ($css_pagina_doctor !== null): ?>
        <link
            rel="stylesheet"
            href="<?= htmlspecialchars(
                (string) $css_pagina_doctor,
                ENT_QUOTES,
                'UTF-8'
            ) ?>"
        >
    <?php endif; ?>

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    >
</head>
<body>

<header>
    <div class="hora">
        HORARIO: LUNES A VIERNES - 9am a 7pm / SÁBADOS - 9am a 1pm
    </div>

    <div class="header-content">
        <div class="logo">
            <a href="/clinica/views/doctor/dashboard.php">
                <img
                    src="/clinica/assets/images/tarjeta.jpg"
                    alt="Logo HappyDent"
                    class="logo-img"
                    width="80"
                    height="80"
                >
            </a>

            <a href="/clinica/views/doctor/dashboard.php">
                <h1>Happy<b>Dent</b></h1>
            </a>
        </div>

        <div id="icon-menu">
            <i class="fas fa-bars"></i>
        </div>

        <div class="menu" id="show-menu">
            <nav>
                <ul>
                    <li>
                        <a
                            href="/clinica/views/doctor/dashboard.php"
                            class="<?= $pagina_actual === 'dashboard.php'
                                && $directorio_actual === 'doctor'
                                    ? 'active'
                                    : '' ?>"
                        >
                            <i class="fas fa-home"></i>
                            <?= $nombre_doctor !== ''
                                ? 'Hola, Dr. ' . $nombre_doctor
                                : 'INICIO' ?>
                        </a>
                    </li>

                    <li>
                        <a
                            href="/clinica/views/doctor/add_patient.php"
                            class="<?= in_array(
                                $pagina_actual,
                                ['add_patient.php', 'doctor_add_patient.php'],
                                true
                            ) ? 'active' : '' ?>"
                        >
                            <i class="fas fa-user-plus"></i>
                            AGREGAR PACIENTE
                        </a>
                    </li>

                    <li>
                        <a
                            href="/clinica/views/doctor/doctor_appointments.php"
                            class="<?= $pagina_actual === 'doctor_appointments.php'
                                ? 'active'
                                : '' ?>"
                        >
                            <i class="fas fa-calendar-check"></i>
                            GESTIONAR CITAS
                        </a>
                    </li>

                    <li>
                        <a
                            href="/clinica/views/doctor/doctor_patient_list.php"
                            class="<?= $pagina_actual === 'doctor_patient_list.php'
                                ? 'active'
                                : '' ?>"
                        >
                            <i class="fas fa-users"></i>
                            LISTA DE PACIENTES
                        </a>
                    </li>

                    <li>
                        <a
                            href="/clinica/views/doctor/disponibilidad/index.php"
                            class="<?= $pagina_actual === 'index.php'
                                && $directorio_actual === 'disponibilidad'
                                    ? 'active'
                                    : '' ?>"
                        >
                            <i class="fas fa-calendar-alt"></i>
                            GESTIONAR HORARIOS
                        </a>
                    </li>

                    <li>
                        <a href="/clinica/views/doctor/logout.php">
                            <i class="fas fa-sign-out-alt"></i>
                            SALIR
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</header>

<script src="/clinica/assets/js/cabecera.js"></script>

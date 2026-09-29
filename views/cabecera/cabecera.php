<?php
// views/cabecera/cabecera.php

// Lo ideal es iniciar la sesión en la página que incluye esta cabecera,
// antes de imprimir cualquier HTML.
if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
    session_start();
}

// IMPORTANTE: cambia 'usuario_id' si tu login guarda el ID del paciente
// con otro nombre en $_SESSION. No uses el nombre como prueba de autenticación.
$paciente_autenticado = isset($_SESSION['usuario_id']);

$pagina_actual = basename($_SERVER['SCRIPT_NAME'] ?? '');

$nombre_paciente = htmlspecialchars(
    (string) ($_SESSION['nombre_completo']
        ?? $_SESSION['usuario_nombre']
        ?? $_SESSION['usuario']
        ?? ''),
    ENT_QUOTES,
    'UTF-8'
);
?>

<header>
    <div class="hora">
        HORARIO: LUNES A VIERNES - 9am a 7pm / SÁBADOS - 9am a 1pm
    </div>

    <div class="header-content">
        <div class="logo">
            <a href="<?= $paciente_autenticado
                ? '/clinica/views/users/patient_dashboard.php'
                : '/clinica/index.php' ?>">
                <img
                    src="/clinica/assets/images/tarjeta.jpg"
                    alt="Logo HappyDent"
                    class="logo-img"
                    width="80"
                    height="80"
                >
            </a>

            <a href="<?= $paciente_autenticado
                ? '/clinica/views/users/patient_dashboard.php'
                : '/clinica/index.php' ?>">
                <h1>Happy<b>Dent</b></h1>
            </a>
        </div>

        <div id="icon-menu">
            <i class="fas fa-bars"></i>
        </div>

        <div class="menu" id="show-menu">
            <nav>
                <ul>
                    <?php if ($paciente_autenticado): ?>
                        <!-- Menú del paciente -->
                        <li>
                            <a
                                href="/clinica/views/users/patient_dashboard.php"
                                class="<?= $pagina_actual === 'patient_dashboard.php' ? 'active' : '' ?>"
                            >
                                <i class="fas fa-home"></i>
                                <?= $nombre_paciente !== ''
                                    ? 'Hola, ' . $nombre_paciente
                                    : 'INICIO' ?>
                            </a>
                        </li>

                        <li>
                            <a
                                href="/clinica/views/especialidades/especialidades.php"
                                class="<?= $pagina_actual === 'especialidades.php' ? 'active' : '' ?>"
                            >
                                <i class="fas fa-tooth"></i> ESPECIALIDADES
                            </a>
                        </li>

                        <li>
                            <a
                                href="/clinica/views/users/patient_appointment_history.php"
                                class="<?= in_array(
                                    $pagina_actual,
                                    ['patient_appointment_history.php', 'patient_appointments.php'],
                                    true
                                ) ? 'active' : '' ?>"
                            >
                                <i class="fas fa-calendar-check"></i> MIS CITAS
                            </a>
                        </li>

                        <li>
                            <a
                                href="/clinica/views/users/disponibilidad/ver_horarios.php"
                                class="<?= in_array(
                                    $pagina_actual,
                                    ['ver_horarios.php', 'patient_calendar.php'],
                                    true
                                ) ? 'active' : '' ?>"
                            >
                                <i class="fas fa-clock"></i> HORARIOS
                            </a>
                        </li>

                        <li>
                            <a href="/clinica/views/users/logout.php">
                                <i class="fas fa-sign-out-alt"></i> SALIR
                            </a>
                        </li>
                    <?php else: ?>
                        <!-- Menú público -->
                        <li>
                            <a href="/clinica/index.php">
                                <i class="fas fa-home"></i> INICIO
                            </a>
                        </li>

                        <li>
                            <a href="/clinica/views/especialidades/especialidades.php">
                                <i class="fas fa-tooth"></i> ESPECIALIDADES
                            </a>
                        </li>

                        <li>
                            <a href="/clinica/views/nosotros/nosotros.php">
                                <i class="fas fa-users"></i> NOSOTROS
                            </a>
                        </li>

                        <li>
                            <a href="/clinica/views/doctor/login.php">
                                <i class="fas fa-user-md"></i> DOCTOR
                            </a>
                        </li>

                        <li>
                            <a href="/clinica/views/users/login_register.php">
                                <i class="fas fa-sign-in-alt"></i> ACCEDER
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </div>
</header>

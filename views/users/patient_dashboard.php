<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: login_register.php");
    exit();
}

$nombre_paciente = htmlspecialchars(
    (string) (
        $_SESSION['nombre_completo']
        ?? $_SESSION['usuario_nombre']
        ?? $_SESSION['usuario']
    ),
    ENT_QUOTES,
    'UTF-8'
);

$titulo_pagina_paciente = 'HappyDent — Mi panel';
$css_pagina_paciente = '/clinica/assets/css/patient_dashboard.css';

require_once __DIR__ . '/../cabecera/cabecera_paciente.php';
?>

<main class="patient-dashboard-main">
    <div class="patient-dashboard-content">

        <section class="dashboard-bienvenida" aria-labelledby="dashboard-titulo">
            <div class="dashboard-bienvenida-texto">
                <span class="dashboard-etiqueta">
                    <i class="fas fa-tooth" aria-hidden="true"></i>
                    Tu espacio de salud dental
                </span>

                <h1 id="dashboard-titulo">
                    ¡Hola, <span><?= $nombre_paciente ?></span>!
                </h1>

                <p>
                    Bienvenido/a a tu panel de paciente. Consulta tus citas,
                    revisa los horarios disponibles y conoce nuestros tratamientos.
                </p>
            </div>

            <div class="dashboard-emblema" aria-hidden="true">
                <i class="fas fa-tooth"></i>
            </div>
        </section>

        <section class="dashboard-servicios" aria-labelledby="dashboard-servicios-titulo">
            <div class="dashboard-seccion-titulo">
                <div>
                    <span class="dashboard-subtitulo">Accesos rápidos</span>
                    <h2 id="dashboard-servicios-titulo">¿Qué deseas hacer hoy?</h2>
                </div>
                <p>Todo lo que necesitas, a un toque de distancia.</p>
            </div>

            <div class="dashboard-acciones">
                <a href="/clinica/views/users/patient_appointment_history.php"
                   class="action-button">
                    <span class="action-icon action-icon-citas">
                        <i class="fas fa-calendar-check" aria-hidden="true"></i>
                    </span>

                    <span class="action-content">
                        <strong>Mis Citas</strong>
                        <small>Consulta tus próximas citas y tu historial.</small>
                    </span>

                    <i class="fas fa-arrow-right action-arrow" aria-hidden="true"></i>
                </a>

                <a href="/clinica/views/users/disponibilidad/ver_horarios.php"
                   class="action-button">
                    <span class="action-icon action-icon-horarios">
                        <i class="fas fa-clock" aria-hidden="true"></i>
                    </span>

                    <span class="action-content">
                        <strong>Ver Horarios</strong>
                        <small>Explora la disponibilidad de atención.</small>
                    </span>

                    <i class="fas fa-arrow-right action-arrow" aria-hidden="true"></i>
                </a>

                <a href="/clinica/views/especialidades/especialidades.php"
                   class="action-button">
                    <span class="action-icon action-icon-especialidades">
                        <i class="fas fa-tooth" aria-hidden="true"></i>
                    </span>

                    <span class="action-content">
                        <strong>Especialidades</strong>
                        <small>Descubre los tratamientos que ofrecemos.</small>
                    </span>

                    <i class="fas fa-arrow-right action-arrow" aria-hidden="true"></i>
                </a>
            </div>
        </section>

        <div class="dashboard-nota">
            <i class="fas fa-heart" aria-hidden="true"></i>
            <span>Tu sonrisa merece el mejor cuidado.</span>
        </div>

    </div>
</main>

<?php require_once __DIR__ . '/../cabecera/pie_paciente.php'; ?>

</body>
</html>

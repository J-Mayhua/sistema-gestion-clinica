<?php
session_start();

if (!isset($_SESSION['usuario']) || !isset($_SESSION['usuario_id'])) {
    header('Location: login_register.php');
    exit();
}

require_once __DIR__ . '/../../controllers/UserController.php';
require_once __DIR__ . '/../../config/csrf.php';

if (!isset($_GET['id'])) {
    header('Location: patient_appointment_history.php');
    exit();
}

$userController = new UserController();
$appointment = $userController->getAppointmentByIddd((int) $_GET['id']);

if (
    !$appointment ||
    (int) $appointment['usuario_id'] !== (int) $_SESSION['usuario_id']
) {
    header('Location: patient_appointment_history.php');
    exit();
}

$csrfToken = csrfToken();
$estado = (string) ($appointment['estado'] ?? 'Pendiente');

$clasesEstado = [
    'pendiente' => 'pendiente',
    'confirmada' => 'confirmada',
    'confirmado' => 'confirmada',
    'cancelada' => 'cancelada',
    'cancelado' => 'cancelada',
    'completada' => 'completada',
    'completado' => 'completada',
];

$estadoClase = $clasesEstado[strtolower(trim($estado))] ?? 'otro';

$titulo_pagina_paciente = 'HappyDent — Detalle de la cita';
$css_pagina_paciente = '/clinica/assets/css/view_appointment.css';

require_once __DIR__ . '/../cabecera/cabecera_paciente.php';
?>

<main class="detalle-cita-main">
    <div class="detalle-cita-container">

        <section class="detalle-cita-hero" aria-labelledby="detalle-cita-titulo">
            <div class="detalle-cita-hero-texto">
                <span class="detalle-cita-etiqueta">
                    <i class="fas fa-calendar-check" aria-hidden="true"></i>
                    Tu espacio de salud dental
                </span>

                <h1 id="detalle-cita-titulo">Información de la Cita</h1>

                <p>
                    Consulta los datos de tu solicitud y el estado
                    actual de tu atención.
                </p>
            </div>

            <div class="detalle-cita-hero-icono" aria-hidden="true">
                <i class="fas fa-calendar-alt"></i>
            </div>
        </section>

        <section class="detalle-cita-panel" aria-labelledby="detalle-cita-datos">
            <div class="detalle-cita-panel-header">
                <div>
                    <span class="detalle-cita-subtitulo">Detalle de tu solicitud</span>
                    <h2 id="detalle-cita-datos">Datos de la cita</h2>
                </div>

                <span class="detalle-cita-estado estado-<?= $estadoClase ?>">
                    <?= htmlspecialchars($estado, ENT_QUOTES, 'UTF-8') ?>
                </span>
            </div>

            <div class="detalle-cita-grid">
                <div class="detalle-cita-dato">
                    <span class="detalle-cita-dato-icono" aria-hidden="true">
                        <i class="far fa-calendar-alt"></i>
                    </span>
                    <div>
                        <span class="detalle-cita-label">Fecha de solicitud</span>
                        <strong>
                            <?= htmlspecialchars(
                                (string) ($appointment['fecha_solicitud'] ?? 'No disponible'),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </strong>
                    </div>
                </div>

                <div class="detalle-cita-dato">
                    <span class="detalle-cita-dato-icono" aria-hidden="true">
                        <i class="fas fa-tooth"></i>
                    </span>
                    <div>
                        <span class="detalle-cita-label">Especialidad</span>
                        <strong>
                            <?= htmlspecialchars(
                                (string) ($appointment['especialidad'] ?? ''),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </strong>
                    </div>
                </div>

                <div class="detalle-cita-dato">
                    <span class="detalle-cita-dato-icono" aria-hidden="true">
                        <i class="fas fa-user-md"></i>
                    </span>
                    <div>
                        <span class="detalle-cita-label">Doctor</span>
                        <strong>
                            <?= htmlspecialchars(
                                (string) ($appointment['nombre_doctor'] ?? 'Sin asignar'),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </strong>
                    </div>
                </div>

                <div class="detalle-cita-dato">
                    <span class="detalle-cita-dato-icono" aria-hidden="true">
                        <i class="far fa-id-card"></i>
                    </span>
                    <div>
                        <span class="detalle-cita-label">DNI</span>
                        <strong>
                            <?= htmlspecialchars(
                                (string) ($appointment['dni'] ?? ''),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </strong>
                    </div>
                </div>

                <div class="detalle-cita-dato">
                    <span class="detalle-cita-dato-icono" aria-hidden="true">
                        <i class="fas fa-birthday-cake"></i>
                    </span>
                    <div>
                        <span class="detalle-cita-label">Fecha de nacimiento</span>
                        <strong>
                            <?= htmlspecialchars(
                                (string) ($appointment['fecha_nacimiento'] ?? ''),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </strong>
                    </div>
                </div>

                <div class="detalle-cita-dato">
                    <span class="detalle-cita-dato-icono" aria-hidden="true">
                        <i class="fas fa-venus-mars"></i>
                    </span>
                    <div>
                        <span class="detalle-cita-label">Sexo</span>
                        <strong>
                            <?= htmlspecialchars(
                                (string) ($appointment['sexo'] ?? ''),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </strong>
                    </div>
                </div>

                <div class="detalle-cita-dato">
                    <span class="detalle-cita-dato-icono" aria-hidden="true">
                        <i class="fas fa-map-marker-alt"></i>
                    </span>
                    <div>
                        <span class="detalle-cita-label">Dirección</span>
                        <strong>
                            <?= htmlspecialchars(
                                (string) ($appointment['direccion'] ?? ''),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </strong>
                    </div>
                </div>

                <div class="detalle-cita-dato">
                    <span class="detalle-cita-dato-icono" aria-hidden="true">
                        <i class="fas fa-phone-alt"></i>
                    </span>
                    <div>
                        <span class="detalle-cita-label">Teléfono</span>
                        <strong>
                            <?= htmlspecialchars(
                                (string) ($appointment['telefono'] ?? ''),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </strong>
                    </div>
                </div>
            </div>

            <div class="detalle-cita-acciones">
                <a
                    href="patient_appointment_history.php"
                    class="detalle-cita-volver"
                >
                    <i class="fas fa-arrow-left" aria-hidden="true"></i>
                    Volver al historial de citas
                </a>

                <?php if ($estado === 'Pendiente'): ?>
                    <form action="cancel_appointment.php" method="post">
                        <input
                            type="hidden"
                            name="csrf_token"
                            value="<?= htmlspecialchars(
                                (string) $csrfToken,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                        >

                        <input
                            type="hidden"
                            name="id"
                            value="<?= (int) $appointment['cita_id'] ?>"
                        >

                        <button
                            type="submit"
                            class="detalle-cita-cancelar"
                            onclick="return confirm('¿Cancelar esta cita?');"
                        >
                            <i class="fas fa-times" aria-hidden="true"></i>
                            Cancelar cita
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </section>

    </div>
</main>

<?php require_once __DIR__ . '/../cabecera/pie_paciente.php'; ?>

</body>
</html>

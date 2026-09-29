<?php
session_start();

if (!isset($_SESSION['usuario']) || !isset($_SESSION['usuario_id'])) {
    header('Location: login_register.php');
    exit();
}

require_once __DIR__ . '/../../controllers/UserController.php';
require_once __DIR__ . '/../../config/csrf.php';

$userController = new UserController();
$appointments = $userController->getPatientAppointmentsByIdd($_SESSION['usuario_id']);
$csrfToken = csrfToken();

$message = $_SESSION['appointment_message'] ?? '';
unset($_SESSION['appointment_message']);

$titulo_pagina_paciente = 'HappyDent — Mis Citas';
$css_pagina_paciente = '/clinica/assets/css/patient_appointment_history.css';

require_once __DIR__ . '/../cabecera/cabecera_paciente.php';
?>

<main class="mis-citas-main">
    <div class="mis-citas-container">

        <section class="mis-citas-hero" aria-labelledby="mis-citas-titulo">
            <div class="mis-citas-hero-texto">
                <span class="mis-citas-etiqueta">
                    <i class="fas fa-calendar-check" aria-hidden="true"></i>
                    Tu espacio de salud dental
                </span>

                <h1 id="mis-citas-titulo">Mis Citas</h1>

                <p>
                    Consulta tus solicitudes, revisa su estado y accede
                    a los detalles de cada cita.
                </p>
            </div>

            <div class="mis-citas-hero-icono" aria-hidden="true">
                <i class="fas fa-calendar-check"></i>
            </div>
        </section>

        <?php if ($message !== ''): ?>
            <div class="mis-citas-mensaje" role="status">
                <i class="fas fa-circle-info" aria-hidden="true"></i>
                <span><?= htmlspecialchars((string) $message, ENT_QUOTES, 'UTF-8') ?></span>
            </div>
        <?php endif; ?>

        <section class="mis-citas-panel" aria-labelledby="mis-citas-listado">
            <div class="mis-citas-panel-header">
                <div>
                    <span class="mis-citas-subtitulo">Tu historial</span>
                    <h2 id="mis-citas-listado">Citas registradas</h2>
                </div>

                <span class="mis-citas-contador">
                    <?= count($appointments) ?>
                    <?= count($appointments) === 1 ? 'cita' : 'citas' ?>
                </span>
            </div>

            <?php if (empty($appointments)): ?>
                <div class="mis-citas-vacio">
                    <span class="mis-citas-vacio-icono">
                        <i class="far fa-calendar" aria-hidden="true"></i>
                    </span>

                    <h3>Aún no tienes citas registradas</h3>
                    <p>Cuando solicites una cita, podrás consultar aquí sus detalles y estado.</p>

                    <a href="/clinica/views/users/disponibilidad/ver_horarios.php"
                       class="mis-citas-boton-principal">
                        <i class="fas fa-clock" aria-hidden="true"></i>
                        Ver horarios disponibles
                    </a>
                </div>
            <?php else: ?>
                <p class="mis-citas-ayuda-tabla">
                    En pantallas pequeñas, desliza la tabla hacia los lados para ver todas las columnas.
                </p>

                <div class="mis-citas-tabla-scroll" tabindex="0"
                     role="region" aria-label="Tabla de citas; desplazable horizontalmente">
                    <table class="mis-citas-tabla">
                        <thead>
                            <tr>
                                <th scope="col">Fecha de solicitud</th>
                                <th scope="col">Especialidad</th>
                                <th scope="col">Doctor</th>
                                <th scope="col">Estado</th>
                                <th scope="col">Acciones</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($appointments as $appointment): ?>
                                <?php
                                $estado = (string) ($appointment['estado'] ?? 'Pendiente');

                                // Lista cerrada: evita generar clases CSS a partir
                                // de texto recibido de la base de datos.
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
                                $citaId = (int) $appointment['cita_id'];
                                ?>
                                <tr>
                                    <td>
                                        <?= htmlspecialchars(
                                            (string) ($appointment['fecha_solicitud'] ?? ''),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </td>

                                    <td>
                                        <span class="mis-citas-especialidad">
                                            <i class="fas fa-tooth" aria-hidden="true"></i>
                                            <?= htmlspecialchars(
                                                (string) ($appointment['especialidad'] ?? ''),
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>
                                        </span>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            (string) ($appointment['nombre_doctor'] ?? 'Sin asignar'),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </td>

                                    <td>
                                        <span class="mis-citas-estado estado-<?= $estadoClase ?>">
                                            <?= htmlspecialchars($estado, ENT_QUOTES, 'UTF-8') ?>
                                        </span>
                                    </td>

                                    <td>
                                        <div class="mis-citas-acciones">
                                            <a
                                                class="mis-citas-ver"
                                                href="view_appointment.php?id=<?= $citaId ?>"
                                            >
                                                <i class="fas fa-eye" aria-hidden="true"></i>
                                                Ver detalles
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
                                                        value="<?= $citaId ?>"
                                                    >

                                                    <button
                                                        type="submit"
                                                        class="mis-citas-cancelar"
                                                        onclick="return confirm('¿Cancelar esta cita?');"
                                                    >
                                                        <i class="fas fa-times" aria-hidden="true"></i>
                                                        Cancelar
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </section>

       
    </div>
</main>

<?php require_once __DIR__ . '/../cabecera/pie_paciente.php'; ?>

</body>
</html>

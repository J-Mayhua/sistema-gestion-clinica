<?php
session_start();

if (!isset($_SESSION['doctor_id'])) {
    header('Location: login.php');
    exit();
}

require_once __DIR__ . '/../../controllers/AppointmentController.php';
require_once __DIR__ . '/../../controllers/DoctorController.php';
require_once __DIR__ . '/../../config/csrf.php';

$doctorController = new DoctorController();
$doctorId = (int) $_SESSION['doctor_id'];

$transitions = [
    'Pendiente'  => ['Pendiente', 'Confirmada', 'Cancelada'],
    'Confirmada' => ['Confirmada', 'Completada', 'Cancelada'],
    'Cancelada'  => ['Cancelada'],
    'Completada' => ['Completada'],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validarCsrf($_POST['csrf_token'] ?? null)) {
        http_response_code(403);
        exit('Solicitud no válida.');
    }

    $appointmentId = filter_var(
        $_POST['cita_id'] ?? null,
        FILTER_VALIDATE_INT
    );

    $action = $_POST['action'] ?? '';
    $success = false;

    if ($appointmentId && $action === 'cancel') {
        $success = $doctorController->cancelAppointmentForDoctor(
            $appointmentId,
            $doctorId
        );
    } elseif ($appointmentId && $action === 'update') {
        $required = [
            'dni',
            'fecha_nacimiento',
            'sexo',
            'direccion',
            'telefono',
            'especialidad',
            'estado',
        ];

        $valid = true;

        foreach ($required as $field) {
            if (
                !isset($_POST[$field]) ||
                !is_string($_POST[$field]) ||
                trim($_POST[$field]) === ''
            ) {
                $valid = false;
                break;
            }
        }

        if ($valid) {
            $birthDate = DateTime::createFromFormat(
                '!Y-m-d',
                $_POST['fecha_nacimiento']
            );

            $valid = $birthDate !== false
                && $birthDate->format('Y-m-d') === $_POST['fecha_nacimiento']
                && $birthDate <= new DateTime('today')
                && in_array($_POST['sexo'], ['M', 'F'], true)
                && strlen(trim($_POST['dni'])) <= 20
                && strlen(trim($_POST['direccion'])) <= 255
                && strlen(trim($_POST['telefono'])) <= 20
                && strlen(trim($_POST['especialidad'])) <= 100;
        }

        if ($valid) {
            $success = $doctorController->updateAppointmentForDoctor(
                $appointmentId,
                $doctorId,
                [
                    'dni'              => trim($_POST['dni']),
                    'fecha_nacimiento' => $_POST['fecha_nacimiento'],
                    'sexo'             => $_POST['sexo'],
                    'direccion'        => trim($_POST['direccion']),
                    'telefono'         => trim($_POST['telefono']),
                    'especialidad'     => trim($_POST['especialidad']),
                    'estado'           => $_POST['estado'],
                ]
            );
        }
    }

    $_SESSION['appointment_message'] = $success
        ? 'Cambios guardados.'
        : 'No se pudo cambiar la cita. Revisa propiedad, datos y estado actual.';

    header('Location: doctor_appointments.php');
    exit();
}

$message = $_SESSION['appointment_message'] ?? '';
unset($_SESSION['appointment_message']);

$appointments = $doctorController->getAppointmentsByDoctorId($doctorId);
$csrfToken = csrfToken();

$titulo_pagina_doctor = 'HappyDent — Gestionar citas';
$css_pagina_doctor = '/clinica/assets/css/doctor_appointments.css';

require_once __DIR__ . '/../cabecera/cabecera_doctor.php';
?>

<main class="doctor-citas-main">
    <div class="doctor-citas-contenedor">

        <div class="doctor-citas-intro">
            <span class="doctor-citas-etiqueta">
                <i class="fas fa-calendar-check" aria-hidden="true"></i>
                Área del doctor
            </span>

            <h1>Gestionar citas</h1>

            <p>
                Consulta las citas de tus pacientes, actualiza sus datos
                o cambia el estado cuando corresponda.
            </p>
        </div>

        <?php if ($message !== ''): ?>
            <div
                class="doctor-citas-mensaje <?= $message === 'Cambios guardados.'
                    ? 'doctor-citas-mensaje-exito'
                    : 'doctor-citas-mensaje-error' ?>"
                role="<?= $message === 'Cambios guardados.'
                    ? 'status'
                    : 'alert' ?>"
            >
                <?= htmlspecialchars((string) $message, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <section class="doctor-citas-panel" aria-labelledby="doctor-citas-titulo">
            <div class="doctor-citas-panel-encabezado">
                <div>
                    <span class="doctor-citas-subtitulo">Seguimiento</span>
                    <h2 id="doctor-citas-titulo">Todas las citas</h2>
                </div>

                <span class="doctor-citas-contador">
                    <?= count($appointments) ?>
                    <?= count($appointments) === 1 ? 'cita' : 'citas' ?>
                </span>
            </div>

            <?php if (empty($appointments)): ?>
                <div class="doctor-citas-vacio">
                    <i class="far fa-calendar-alt" aria-hidden="true"></i>
                    <h3>Aún no hay citas para mostrar</h3>
                    <p>Las citas de tus pacientes aparecerán aquí.</p>
                </div>
            <?php else: ?>
                <div
                    class="doctor-citas-tabla-contenedor"
                    role="region"
                    aria-label="Tabla de citas; desplázate horizontalmente para ver todas las columnas"
                    tabindex="0"
                >
                    <table class="doctor-citas-tabla">
                        <thead>
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">DNI</th>
                                <th scope="col">Fecha de nacimiento</th>
                                <th scope="col">Sexo</th>
                                <th scope="col">Dirección</th>
                                <th scope="col">Teléfono</th>
                                <th scope="col">Especialidad</th>
                                <th scope="col">Paciente</th>
                                <th scope="col">Estado</th>
                                <th scope="col">Acciones</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($appointments as $appointment): ?>
                                <?php
                                $citaId = (int) $appointment['cita_id'];
                                $modalId = 'doctor-cita-modal-' . $citaId;
                                $estado = (string) ($appointment['estado'] ?? 'Pendiente');

                                $estadoClase = match ($estado) {
                                    'Confirmada' => 'confirmada',
                                    'Cancelada'  => 'cancelada',
                                    'Completada' => 'completada',
                                    default     => 'pendiente',
                                };

                                $puedeModificar = in_array(
                                    $estado,
                                    ['Pendiente', 'Confirmada'],
                                    true
                                );
                                ?>

                                <tr>
                                    <td><?= $citaId ?></td>

                                    <td>
                                        <?= htmlspecialchars(
                                            (string) ($appointment['dni'] ?? ''),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            (string) ($appointment['fecha_nacimiento'] ?? ''),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            (string) ($appointment['sexo'] ?? ''),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            (string) ($appointment['direccion'] ?? ''),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            (string) ($appointment['telefono'] ?? ''),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            (string) ($appointment['especialidad'] ?? ''),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            (string) ($appointment['nombre_paciente'] ?? ''),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </td>

                                    <td>
                                        <span class="doctor-citas-estado doctor-citas-estado-<?= $estadoClase ?>">
                                            <?= htmlspecialchars(
                                                $estado,
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>
                                        </span>
                                    </td>

                                    <td>
                                        <?php if ($puedeModificar): ?>
                                            <div class="doctor-citas-acciones">
                                                <button
                                                    type="button"
                                                    class="doctor-citas-boton doctor-citas-boton-editar"
                                                    data-abrir-dialogo="<?= $modalId ?>"
                                                    aria-haspopup="dialog"
                                                >
                                                    <i
                                                        class="fas fa-pen"
                                                        aria-hidden="true"
                                                    ></i>
                                                    Modificar
                                                </button>

                                                <form
                                                    action="doctor_appointments.php"
                                                    method="post"
                                                    class="doctor-citas-form-cancelar"
                                                >
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
                                                        name="cita_id"
                                                        value="<?= $citaId ?>"
                                                    >

                                                    <button
                                                        type="submit"
                                                        name="action"
                                                        value="cancel"
                                                        class="doctor-citas-boton doctor-citas-boton-cancelar"
                                                        data-confirmar-cancelacion
                                                    >
                                                        Cancelar
                                                    </button>
                                                </form>
                                            </div>
                                        <?php else: ?>
                                            <span class="doctor-citas-sin-acciones">
                                                Sin acciones
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </section>

        <?php foreach ($appointments as $appointment): ?>
            <?php
            $citaId = (int) $appointment['cita_id'];
            $modalId = 'doctor-cita-modal-' . $citaId;
            $estado = (string) ($appointment['estado'] ?? 'Pendiente');

            if (!in_array($estado, ['Pendiente', 'Confirmada'], true)) {
                continue;
            }
            ?>

            <dialog
                id="<?= $modalId ?>"
                class="doctor-citas-dialog"
                aria-labelledby="<?= $modalId ?>-titulo"
            >
                <div class="doctor-citas-dialog-encabezado">
                    <span class="doctor-citas-dialog-icono" aria-hidden="true">
                        <i class="fas fa-pen"></i>
                    </span>

                    <h2 id="<?= $modalId ?>-titulo">
                        Modificar cita #<?= $citaId ?>
                    </h2>

                    <p>
                        Actualiza los datos y guarda los cambios de esta cita.
                    </p>
                </div>

                <form
                    action="doctor_appointments.php"
                    method="post"
                    class="doctor-citas-formulario"
                >
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
                        name="cita_id"
                        value="<?= $citaId ?>"
                    >

                    <div class="doctor-citas-campos">
                        <label>
                            DNI
                            <input
                                type="text"
                                name="dni"
                                maxlength="20"
                                value="<?= htmlspecialchars(
                                    (string) ($appointment['dni'] ?? ''),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                                required
                            >
                        </label>

                        <label>
                            Fecha de nacimiento
                            <input
                                type="date"
                                name="fecha_nacimiento"
                                value="<?= htmlspecialchars(
                                    (string) ($appointment['fecha_nacimiento'] ?? ''),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                                required
                            >
                        </label>

                        <label>
                            Sexo
                            <select name="sexo" required>
                                <option
                                    value="M"
                                    <?= ($appointment['sexo'] ?? '') === 'M'
                                        ? 'selected'
                                        : '' ?>
                                >
                                    Masculino
                                </option>

                                <option
                                    value="F"
                                    <?= ($appointment['sexo'] ?? '') === 'F'
                                        ? 'selected'
                                        : '' ?>
                                >
                                    Femenino
                                </option>
                            </select>
                        </label>

                        <label>
                            Teléfono
                            <input
                                type="tel"
                                name="telefono"
                                maxlength="20"
                                value="<?= htmlspecialchars(
                                    (string) ($appointment['telefono'] ?? ''),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                                required
                            >
                        </label>

                        <label class="doctor-citas-campo-completo">
                            Dirección
                            <input
                                type="text"
                                name="direccion"
                                maxlength="255"
                                value="<?= htmlspecialchars(
                                    (string) ($appointment['direccion'] ?? ''),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                                required
                            >
                        </label>

                        <label>
                            Especialidad
                            <input
                                type="text"
                                name="especialidad"
                                maxlength="100"
                                value="<?= htmlspecialchars(
                                    (string) ($appointment['especialidad'] ?? ''),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                                required
                            >
                        </label>

                        <label>
                            Estado
                            <select name="estado" required>
                                <?php foreach (
                                    $transitions[$estado] ?? [$estado] as $status
                                ): ?>
                                    <option
                                        value="<?= htmlspecialchars(
                                            $status,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>"
                                        <?= $status === $estado
                                            ? 'selected'
                                            : '' ?>
                                    >
                                        <?= htmlspecialchars(
                                            $status,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </label>
                    </div>

                    <div class="doctor-citas-dialog-acciones">
                        <button
                            type="button"
                            class="doctor-citas-boton-volver"
                            data-cerrar-dialogo
                        >
                            Volver
                        </button>

                        <button
                            type="submit"
                            name="action"
                            value="update"
                            class="doctor-citas-boton-guardar"
                        >
                            Guardar cambios
                        </button>
                    </div>
                </form>
            </dialog>
        <?php endforeach; ?>

    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-abrir-dialogo]').forEach(function (boton) {
        boton.addEventListener('click', function () {
            const dialogo = document.getElementById(
                boton.dataset.abrirDialogo
            );

            if (dialogo) {
                dialogo.showModal();
            }
        });
    });

    document.querySelectorAll('[data-cerrar-dialogo]').forEach(function (boton) {
        boton.addEventListener('click', function () {
            boton.closest('dialog').close();
        });
    });

    document.querySelectorAll('[data-confirmar-cancelacion]').forEach(function (boton) {
        boton.closest('form').addEventListener('submit', function (evento) {
            if (!window.confirm(
                '¿Cancelar esta cita? Se conservará el historial.'
            )) {
                evento.preventDefault();
            }
        });
    });
});
</script>

<?php require_once __DIR__ . '/../cabecera/pie_paciente.php'; ?>

</body>
</html>

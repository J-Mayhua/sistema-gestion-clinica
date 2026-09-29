<?php
session_start();

if (!isset($_SESSION['usuario'], $_SESSION['usuario_id'])) {
    header('Location: login_register.php');
    exit();
}

$disponibilidad_id = isset($_GET['disponibilidad_id'])
    ? (int) $_GET['disponibilidad_id']
    : 0;

require_once __DIR__ . '/../../models/Disponibilidad.php';
require_once __DIR__ . '/../../config/csrf.php';

$availabilityModel = new Disponibilidad();
$slot = $availabilityModel->getDisponibleFuturoById($disponibilidad_id);

if (!$slot) {
    $_SESSION['message'] = 'El horario seleccionado ya no está disponible.';
    $_SESSION['message_type'] = 'error';

    header('Location: patient_dashboard.php?action=calendar');
    exit();
}

$doctor_id_pre = (int) $slot['doctor_id'];
$fecha = $slot['fecha'] ?? null;

$message = $_SESSION['message'] ?? '';
$messageType = $_SESSION['message_type'] ?? '';
unset($_SESSION['message'], $_SESSION['message_type']);

$nombrePaciente = $_SESSION['usuario_nombre'] ?? $_SESSION['usuario'];

$nombreDoctor = trim(
    (string) ($slot['doctor_nombres'] ?? '') . ' ' .
    (string) ($slot['doctor_apellidos'] ?? '')
);

$csrfToken = csrfToken();

$claseMensaje = $messageType === 'success'
    ? 'solicitud-mensaje-exito'
    : 'solicitud-mensaje-error';

$titulo_pagina_paciente = 'HappyDent — Solicitar cita';
$css_pagina_paciente = '/clinica/assets/css/patient_request_form.css';

require_once __DIR__ . '/../cabecera/cabecera_paciente.php';
?>

<main class="solicitud-main">
    <div class="solicitud-contenedor">

        <a class="solicitud-volver" href="disponibilidad/ver_horarios.php">
            <span aria-hidden="true">←</span>
            Volver a horarios
        </a>

        <div class="solicitud-panel">

            <div class="solicitud-encabezado">
                <span class="solicitud-etiqueta">Solicitud de cita</span>
                <h1>Completa tus datos</h1>
                <p>
                    Revisa el horario elegido y llena el formulario
                    para enviar tu solicitud.
                </p>
            </div>

            <?php if ($message !== ''): ?>
                <div
                    class="solicitud-mensaje <?= $claseMensaje ?>"
                    role="<?= $claseMensaje === 'solicitud-mensaje-error' ? 'alert' : 'status' ?>"
                >
                    <?= htmlspecialchars((string) $message, ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>

            <section
                class="solicitud-resumen"
                aria-labelledby="solicitud-resumen-titulo"
            >
                <h2 id="solicitud-resumen-titulo">Horario seleccionado</h2>

                <div class="solicitud-resumen-grid">
                    <div>
                        <span>Especialidad</span>
                        <strong>
                            <?= htmlspecialchars(
                                (string) ($slot['especialidad'] ?? ''),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </strong>
                    </div>

                    <div>
                        <span>Doctor</span>
                        <strong>
                            <?= htmlspecialchars(
                                $nombreDoctor,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </strong>
                    </div>

                    <?php if ($fecha): ?>
                        <div>
                            <span>Fecha seleccionada</span>
                            <strong>
                                <?= htmlspecialchars(
                                    (string) $fecha,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </strong>
                        </div>
                    <?php endif; ?>
                </div>
            </section>

            <form
                action="store_cita.php"
                method="post"
                class="solicitud-formulario"
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
                    name="disponibilidad_id"
                    value="<?= $disponibilidad_id ?>"
                >

                <input
                    type="hidden"
                    name="doctor_id"
                    value="<?= $doctor_id_pre ?>"
                >

                <h2>Datos del paciente</h2>

                <div class="solicitud-campos">
                    <div class="solicitud-campo solicitud-campo-completo">
                        <label for="nombre_completo">Nombre completo</label>
                        <input
                            type="text"
                            id="nombre_completo"
                            name="insertar_nombre"
                            value="<?= htmlspecialchars(
                                (string) $nombrePaciente,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            readonly
                        >
                    </div>

                    <div class="solicitud-campo">
                        <label for="dni">DNI</label>
                        <input
                            type="text"
                            id="dni"
                            name="dni"
                            required
                            maxlength="20"
                            autocomplete="off"
                            placeholder="Ingresa tu DNI"
                        >
                    </div>

                    <div class="solicitud-campo">
                        <label for="fecha_nacimiento">Fecha de nacimiento</label>
                        <input
                            type="date"
                            id="fecha_nacimiento"
                            name="fecha_nacimiento"
                            required
                            autocomplete="bday"
                        >
                    </div>

                    <div class="solicitud-campo">
                        <label for="sexo">Sexo</label>
                        <select id="sexo" name="sexo" required>
                            <option value="" selected disabled>
                                Selecciona una opción
                            </option>
                            <option value="M">Masculino</option>
                            <option value="F">Femenino</option>
                        </select>
                    </div>

                    <div class="solicitud-campo">
                        <label for="telefono">Teléfono</label>
                        <input
                            type="tel"
                            id="telefono"
                            name="telefono"
                            required
                            maxlength="20"
                            autocomplete="tel"
                            placeholder="Ingresa tu teléfono"
                        >
                    </div>

                    <div class="solicitud-campo solicitud-campo-completo">
                        <label for="direccion">Dirección</label>
                        <input
                            type="text"
                            id="direccion"
                            name="direccion"
                            required
                            maxlength="255"
                            autocomplete="street-address"
                            placeholder="Ingresa tu dirección"
                        >
                    </div>
                </div>

                <div class="solicitud-acciones">
                    <p>Verifica tus datos antes de continuar.</p>

                    <input
                        type="submit"
                        name="request_appointment"
                        value="Solicitar cita"
                        class="solicitud-enviar"
                    >
                </div>
            </form>

        </div>
    </div>
</main>

<?php require_once __DIR__ . '/../cabecera/pie_paciente.php'; ?>

</body>
</html>

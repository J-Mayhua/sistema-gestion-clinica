<?php
session_start();

if (!isset($_SESSION['usuario'], $_SESSION['usuario_id'])) {
    header('Location: login_register.php');
    exit();
}

if (
    $_SERVER['REQUEST_METHOD'] !== 'POST'
    || !isset($_POST['request_appointment'])
) {
    header('Location: patient_dashboard.php?action=calendar');
    exit();
}

require_once __DIR__ . '/../../controllers/UserController.php';
require_once __DIR__ . '/../../models/Disponibilidad.php';
require_once __DIR__ . '/../../config/csrf.php';

if (!validarCsrf($_POST['csrf_token'] ?? null)) {
    http_response_code(403);
    exit('Solicitud no válida.');
}

$disponibilidadId = filter_var(
    $_POST['disponibilidad_id'] ?? null,
    FILTER_VALIDATE_INT
);

if ($disponibilidadId === false || $disponibilidadId === null || $disponibilidadId <= 0) {
    $_SESSION['message'] = 'Selecciona un horario disponible.';
    $_SESSION['message_type'] = 'error';

    header('Location: patient_dashboard.php?action=calendar');
    exit();
}

$availabilityModel = new Disponibilidad();
$slot = $availabilityModel->getDisponibleFuturoById($disponibilidadId);

if (!$slot) {
    $_SESSION['message'] = 'El horario seleccionado ya no está disponible.';
    $_SESSION['message_type'] = 'error';

    header('Location: patient_dashboard.php?action=calendar');
    exit();
}

/*
 * Doctor y especialidad proceden del horario consultado en el servidor,
 * no de los campos ocultos enviados por el navegador.
 */
$doctorId = (int) $slot['doctor_id'];
$especialidad = $slot['especialidad'];

$formularioUrl = 'patient_request_form.php?disponibilidad_id=' . $disponibilidadId;

$campos = ['dni', 'fecha_nacimiento', 'sexo', 'direccion', 'telefono'];
$datos = [];

foreach ($campos as $campo) {
    $valor = $_POST[$campo] ?? null;

    if (!is_string($valor) || trim($valor) === '') {
        $_SESSION['message'] = 'Completa todos los campos requeridos.';
        $_SESSION['message_type'] = 'error';

        header('Location: ' . $formularioUrl);
        exit();
    }

    $datos[$campo] = trim($valor);
}

$fechaNacimiento = DateTime::createFromFormat(
    '!Y-m-d',
    $datos['fecha_nacimiento']
);

$datosValidos =
    $fechaNacimiento !== false
    && $fechaNacimiento->format('Y-m-d') === $datos['fecha_nacimiento']
    && $fechaNacimiento <= new DateTime('today')
    && in_array($datos['sexo'], ['M', 'F'], true)
    && strlen($datos['dni']) <= 20
    && strlen($datos['telefono']) <= 20
    && strlen($datos['direccion']) <= 255;

if (!$datosValidos) {
    $_SESSION['message'] = 'Revisa los datos ingresados.';
    $_SESSION['message_type'] = 'error';

    header('Location: ' . $formularioUrl);
    exit();
}

$userController = new UserController();

$citaGuardada = $userController->requestAppointment(
    [
        'dni'              => $datos['dni'],
        'fecha_nacimiento' => $datos['fecha_nacimiento'],
        'sexo'             => $datos['sexo'],
        'direccion'        => $datos['direccion'],
        'telefono'         => $datos['telefono'],
        'especialidad'     => $especialidad,
    ],
    $disponibilidadId,
    $doctorId
);

if (!$citaGuardada) {
    header('Location: ' . $formularioUrl);
    exit();
}

header('Location: patient_appointment_history.php');
exit();

<?php
session_start();
if (!isset($_SESSION['usuario']) || !isset($_SESSION['usuario_id'])) {
    header("Location: login_register.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Allow: POST');
    http_response_code(405);
    exit('Método no permitido.');
}

require_once __DIR__ . '/../../config/csrf.php';
if (!validarCsrf($_POST['csrf_token'] ?? null)) {
    http_response_code(403);
    exit('Solicitud no válida.');
}

$appointmentId = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
if (!$appointmentId) {
    http_response_code(400);
    exit('Cita no válida.');
}

require_once __DIR__ . '/../../controllers/UserController.php';
$userController = new UserController();
$result = $userController->cancelAppointmentForPatient($appointmentId, (int)$_SESSION['usuario_id']);
$_SESSION['appointment_message'] = $result
    ? 'Cita cancelada.'
    : 'No se pudo cancelar. Verifica propiedad y estado permitido.';
header('Location: patient_appointment_history.php');
exit();

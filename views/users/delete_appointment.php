<?php
session_start();
if (!isset($_SESSION['doctor_id'])) {
    header('Location: ../doctor/login.php');
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

require_once __DIR__ . '/../../controllers/DoctorController.php';
$doctorController = new DoctorController();
$cancelled = $doctorController->cancelAppointmentForDoctor($appointmentId, (int)$_SESSION['doctor_id']);
$_SESSION['appointment_message'] = $cancelled
    ? 'Cita cancelada.'
    : 'No se pudo cancelar. Verifica propiedad y estado permitido.';
header('Location: ../doctor/doctor_appointments.php');
exit();
// Bloque eliminado: código muerto sin ownership ni CSRF que existía
// después del exit() y representaba un riesgo latente de IDOR si se
// reorganizaba el archivo.

<?php
session_start();

if (!isset($_SESSION['doctor_id'])) {
    echo json_encode(["success" => false, "message" => "No autorizado"]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    echo json_encode(["success" => false, "message" => "Método no permitido"]);
    exit();
}

require_once __DIR__ . '/../../config/csrf.php';
if (!validarCsrf($_POST['csrf_token'] ?? null)) {
    http_response_code(403);
    echo json_encode(["success" => false, "message" => "Solicitud no válida"]);
    exit();
}

require_once __DIR__ . '/../../controllers/DoctorController.php';

$doctorController = new DoctorController();

if (isset($_POST['appointment_id'], $_POST['status'])) {
    $appointmentId = filter_var($_POST['appointment_id'], FILTER_VALIDATE_INT);
    $status = $_POST['status'];

    $result = $appointmentId
        && in_array($status, ['Pendiente', 'Confirmada', 'Cancelada', 'Completada'], true)
        && $doctorController->updateAppointmentStatus($appointmentId, $status, (int)$_SESSION['doctor_id']);

    if ($result) {
        echo json_encode(["success" => true, "message" => "Estado actualizado correctamente a: " . $status]);
    } else {
        echo json_encode(["success" => false, "message" => "Error al actualizar el estado de la cita"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Datos insuficientes"]);
}
?>

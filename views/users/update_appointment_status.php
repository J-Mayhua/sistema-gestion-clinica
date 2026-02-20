<?php
session_start();

if (!isset($_SESSION['doctor_id'])) {
    echo json_encode(["success" => false, "message" => "No autorizado"]);
    exit();
}

require_once __DIR__ . '/../../controllers/DoctorController.php';

$doctorController = new DoctorController();

if (isset($_POST['appointment_id']) && isset($_POST['status'])) {
    $appointmentId = $_POST['appointment_id'];
    $status = htmlspecialchars(strip_tags($_POST['status']));

    $result = $doctorController->updateAppointmentStatus($appointmentId, $status);
    
    if ($result) {
        echo json_encode(["success" => true, "message" => "Estado actualizado correctamente a: " . $status]);
    } else {
        echo json_encode(["success" => false, "message" => "Error al actualizar el estado de la cita"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Datos insuficientes"]);
}
?>
<?php
require_once __DIR__ . '/../../controllers/UserController.php';

if (isset($_GET['id'])) {
    $userController = new UserController();
    if ($userController->deleteAppointment($_GET['id'])) {
        header("Location: patient_appointments.php?message=Cita eliminada con éxito");
    } else {
        header("Location: patient_appointments.php?message=Error al eliminar la cita");
    }
} else {
    header("Location: patient_appointments.php?message=ID de cita no proporcionado");
}
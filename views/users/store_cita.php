<?php
session_start();

require_once __DIR__ . '/../../controllers/UserController.php';
require_once __DIR__ . '/../../controllers/DisponibilidadController.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dni = $_POST['dni'];
    $fecha_nacimiento = $_POST['fecha_nacimiento'];
    $sexo = $_POST['sexo'];
    $direccion = $_POST['direccion'];
    $telefono = $_POST['telefono'];
    $especialidad = $_POST['especialidad'];
    $doctor_id = $_POST['doctor_id'];
    $usuario_id = $_SESSION['usuario_id'];
    $insertar_nombre = $_SESSION['usuario_nombre'];
    
    // Guardar cita
    $userController = new UserController();
    $result = $userController->requestAppointment();

    if ($result) {
        // Si hay fecha, marcar horario como ocupado
        if (isset($_POST['fecha'])) {
            $fecha = $_POST['fecha'];
            $disponibilidadModel = new Disponibilidad();
            $horario = $disponibilidadModel->getByFechaAndDoctor($doctor_id, $fecha);

            if ($horario && $horario['estado'] === 'Disponible') {
                $disponibilidadModel->actualizarEstado($horario['disponibilidad_id'], 'Ocupado');
            }
        }

        $_SESSION['message'] = "Cita solicitada exitosamente.";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "cita solicitada correctamente.";
        $_SESSION['message_type'] = "error";
    }

    header("Location: patient_request_form.php");
    exit();
}
?>
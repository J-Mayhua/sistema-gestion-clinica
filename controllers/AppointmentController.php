<?php
require_once __DIR__ . '/../models/Appointment.php';

class AppointmentController {
    public function createAppointment() {
        $appointment = new Appointment();
        $appointment->dni = htmlspecialchars(strip_tags($_POST['dni']));
        $appointment->fecha_nacimiento = htmlspecialchars(strip_tags($_POST['fecha_nacimiento']));
        $appointment->sexo = htmlspecialchars(strip_tags($_POST['sexo']));
        $appointment->direccion = htmlspecialchars(strip_tags($_POST['direccion']));
        $appointment->telefono = htmlspecialchars(strip_tags($_POST['telefono']));
        $appointment->especialidad = htmlspecialchars(strip_tags($_POST['especialidad']));
        $appointment->usuario_id = htmlspecialchars(strip_tags($_POST['usuario_id']));
        $appointment->doctor_id = htmlspecialchars(strip_tags($_POST['doctor_id']));

        if($appointment->create()) {
            echo json_encode(["message" => "Appointment created successfully."]);
        } else {
            echo json_encode(["message" => "Appointment could not be created."]);
        }
    }

    public function getAppointments() {
        $appointment = new Appointment();
        return $appointment->read();
    }
}
?>

<?php
require_once __DIR__ . '/../models/Appointment.php';
require_once __DIR__ . '/../config/csrf.php';

class AppointmentController {

    public function createAppointment() {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        header('Content-Type: application/json');

        if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['usuario'])) {
            http_response_code(401);
            echo json_encode(["message" => "No autorizado."]);
            return;
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !validarCsrf($_POST['csrf_token'] ?? null)) {
            http_response_code(403);
            echo json_encode(["message" => "Solicitud no válida."]);
            return;
        }

        $doctorId       = filter_var($_POST['doctor_id']        ?? null, FILTER_VALIDATE_INT);
        $availabilityId = filter_var($_POST['disponibilidad_id'] ?? null, FILTER_VALIDATE_INT);
        $required       = ['dni', 'fecha_nacimiento', 'sexo', 'direccion', 'telefono', 'especialidad'];
        foreach ($required as $field) {
            if (!isset($_POST[$field]) || trim((string)$_POST[$field]) === '') {
                http_response_code(400);
                echo json_encode(["message" => "Faltan datos requeridos."]);
                return;
            }
        }
        if (!$doctorId || !$availabilityId || !in_array($_POST['sexo'], ['M', 'F'], true)) {
            http_response_code(400);
            echo json_encode(["message" => "Datos no válidos."]);
            return;
        }
        $birthDate = DateTime::createFromFormat('!Y-m-d', $_POST['fecha_nacimiento']);
        if (!$birthDate || $birthDate->format('Y-m-d') !== $_POST['fecha_nacimiento']
            || $birthDate > new DateTime('today')
            || strlen(trim($_POST['dni']))         > 20
            || strlen(trim($_POST['direccion']))   > 255
            || strlen(trim($_POST['telefono']))    > 20
            || strlen(trim($_POST['especialidad'])) > 100) {
            http_response_code(400);
            echo json_encode(["message" => "Datos no válidos."]);
            return;
        }

        $appointment = new Appointment();
        $created = $appointment->createFromAvailableSlot([
            'dni'              => trim($_POST['dni']),
            'fecha_nacimiento' => $_POST['fecha_nacimiento'],
            'sexo'             => $_POST['sexo'],
            'direccion'        => trim($_POST['direccion']),
            'telefono'         => trim($_POST['telefono']),
            'especialidad'     => trim($_POST['especialidad']),
            'usuario_id'       => (int)$_SESSION['usuario_id'],
            'insertar_nombre'  => (string)($_SESSION['usuario_nombre'] ?? ''),
        ], $availabilityId, $doctorId);

        if ($created) {
            echo json_encode(["message" => "Cita creada."]);
        } else {
            http_response_code(409);
            echo json_encode(["message" => "Horario no disponible o no se pudo guardar la cita."]);
        }
    }

    /**
     * Devuelve todas las citas. Solo debe llamarse desde contextos con
     * autorización verificada (rol doctor). Usa readAll() que sí existe en el modelo.
     * CORRECCIÓN #5: antes llamaba a $appointment->read() que no existía → fatal error.
     */
    public function getAppointments(): array {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        // Bloquear acceso si no hay sesión de doctor activa
        if (!isset($_SESSION['doctor_id'])) {
            return [];
        }
        $appointment = new Appointment();
        return $appointment->readAll();
    }

    public function getDoctorAppointments($doctor_id): array {
        $appointment = new Appointment();
        return $appointment->getDoctorAppointments((int)$doctor_id);
    }
}

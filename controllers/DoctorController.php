<?php
require_once __DIR__ . '/../models/Doctor.php';
require_once __DIR__ . '/../models/Appointment.php';

class DoctorController {
    private $doctorModel;
    private $appointmentModel;

    public function __construct() {
        $this->doctorModel = new Doctor();
        $this->appointmentModel = new Appointment();
    }

    public function createDoctor() {
        $doctor = new Doctor();
        $doctor->correo = htmlspecialchars(strip_tags($_POST['correo']));
        $doctor->contrasena = password_hash(htmlspecialchars(strip_tags($_POST['contrasena'])), PASSWORD_BCRYPT);
        $doctor->nombres = htmlspecialchars(strip_tags($_POST['nombres']));
        $doctor->apellidos = htmlspecialchars(strip_tags($_POST['apellidos']));
        $doctor->especialidad = htmlspecialchars(strip_tags($_POST['especialidad']));
        $doctor->telefono = htmlspecialchars(strip_tags($_POST['telefono']));
        $doctor->horario = htmlspecialchars(strip_tags($_POST['horario']));

        if ($doctor->create()) {
            return ["success" => true, "message" => "Doctor created successfully."];
        } else {
            return ["success" => false, "message" => "Doctor could not be created."];
        }
    }

    public function getDoctors() {
        return $this->doctorModel->read();
    }

    public function loginDoctor() {
        $message = '';
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['correo']) && isset($_POST['contrasena'])) {
            $doctor = new Doctor();
            $doctor->correo = htmlspecialchars(strip_tags($_POST['correo']));
            $doctor->contrasena = $_POST['contrasena'];
    
            $result = $doctor->login();
    
            if ($result) {
                if (password_verify($doctor->contrasena, $result['contrasena'])) {
                    session_start();
                    $_SESSION['doctor_id'] = $result['doctor_id'];
                    $_SESSION['doctor_name'] = $result['nombres'];
                    header("Location: ../views/doctor/dashboard.php");
                    exit();
                } else {
                    $message = "Contraseña incorrecta";
                }
            } else {
                $message = "Correo electrónico incorrecto";
            }
        }
    
        // Incluir la vista con el mensaje
        include '../views/doctor/login.php';
    }
    public function dashboard() {
        if (!isset($_SESSION['doctor_id'])) {
            return ["success" => false, "message" => "Not logged in."];
        }

        $doctor_id = $_SESSION['doctor_id'];
        $appointments = $this->appointmentModel->getDoctorAppointments($doctor_id);
        return ["success" => true, "appointments" => $appointments];
    }

    public function updateCalendar() {
        if (!isset($_SESSION['doctor_id'])) {
            return ["success" => false, "message" => "Not logged in."];
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $calendar_data = $_POST['calendar_data'];
            if ($this->doctorModel->updateCalendar($calendar_data)) {
                return ["success" => true, "message" => "Calendar updated successfully."];
            } else {
                return ["success" => false, "message" => "Calendar could not be updated."];
            }
        } else {
            return ["success" => false, "message" => "Invalid request method."];
        }
    }

    public function getAppointmentById($appointmentId) {
        return $this->appointmentModel->getAppointmentByIddd($appointmentId);
    }

    public function getAppointmentsByDoctorId($doctorId) {
        return $this->appointmentModel->getDoctorAppointments($doctorId);
    }

    public function updateAppointmentStatus($appointmentId, $status) {
        try {
            if ($status === 'Cancelada') {
                return $this->appointmentModel->cancelAppointment($appointmentId);
            } else {
                return $this->appointmentModel->updateStatus($appointmentId, $status);
            }
        } catch (Exception $e) {
            error_log("Exception when updating appointment status: " . $e->getMessage());
            return false;
        }
    }

    public function getDoctorById($doctorId) {
        return $this->doctorModel->getDoctorById($doctorId);
    }

    public function createAppointment($appointmentData) {
        $this->appointmentModel->dni = $appointmentData['dni'];
        $this->appointmentModel->fecha_nacimiento = $appointmentData['fecha_nacimiento'];
        $this->appointmentModel->sexo = $appointmentData['sexo'];
        $this->appointmentModel->direccion = $appointmentData['direccion'];
        $this->appointmentModel->telefono = $appointmentData['telefono'];
        $this->appointmentModel->especialidad = $appointmentData['especialidad'];
        $this->appointmentModel->usuario_id = $appointmentData['usuario_id'];
        $this->appointmentModel->doctor_id = $appointmentData['doctor_id'];

        if ($this->appointmentModel->create()) {
            return ["success" => true, "message" => "Appointment created successfully."];
        } else {
            return ["success" => false, "message" => "Failed to create appointment."];
        }
    }

    public function updateAppointment($appointmentData) {
        $this->appointmentModel->cita_id = $appointmentData['cita_id'];
        $this->appointmentModel->dni = $appointmentData['dni'];
        $this->appointmentModel->fecha_nacimiento = $appointmentData['fecha_nacimiento'];
        $this->appointmentModel->sexo = $appointmentData['sexo'];
        $this->appointmentModel->direccion = $appointmentData['direccion'];
        $this->appointmentModel->telefono = $appointmentData['telefono'];
        $this->appointmentModel->especialidad = $appointmentData['especialidad'];
        $this->appointmentModel->doctor_id = $appointmentData['doctor_id'];

        if ($this->appointmentModel->update()) {
            return ["success" => true, "message" => "Appointment updated successfully."];
        } else {
            return ["success" => false, "message" => "Failed to update appointment."];
        }
    }

    public function deleteAppointment($appointmentId) {
        if ($this->appointmentModel->delete($appointmentId)) {
            return ["success" => true, "message" => "Appointment deleted successfully."];
        } else {
            return ["success" => false, "message" => "Failed to delete appointment."];
        }
    }
    public function addPatient() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $dni = $_POST['dni'];
            $nombre = $_POST['nombre'];
            $fecha_nacimiento = $_POST['fecha_nacimiento'];
            $sexo = $_POST['sexo'];
            $direccion = $_POST['direccion'];
            $telefono = $_POST['telefono'];
            $especialidad = $_POST['especialidad'];
    
            $appointment = new Appointment();
            $appointment->dni = $dni;
            $appointment->nombre = $nombre;
            $appointment->fecha_nacimiento = $fecha_nacimiento;
            $appointment->sexo = $sexo;
            $appointment->direccion = $direccion;
            $appointment->telefono = $telefono;
            $appointment->especialidad = $especialidad;
            $appointment->insertar_nombre = $nombre;
    
            if ($appointment->create()) {
                echo "Paciente agregado con éxito.";
            } else {
                echo "Error al agregar paciente.";
            }
        } else {
            // Mostrar el formulario
            include 'views/users/add_patient.php';
        }
    }
    
}
?>

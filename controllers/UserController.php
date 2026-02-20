<?php
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Appointment.php';
require_once __DIR__ . '/../models/Doctor.php'; // Asegúrate de que el modelo Doctor esté incluido

class UserController {
    public function patientDashboard() {
        session_start();
    
        if (!isset($_SESSION['usuario'])) {
            header("Location: login_register.php");
            exit();
        }
    
        $userModel = new User();
        $appointmentModel = new Appointment();
    
        $patient = $userModel->readById($_SESSION['usuario_id']);
    
        $action = isset($_GET['action']) ? $_GET['action'] : '';
        $appointments = [];
    
        if ($action === 'viewAppointments') {
            $appointments = $appointmentModel->getPatientAppointments($_SESSION['usuario_id']);
        }
    
        require_once __DIR__ . '/../views/users/patient_dashboard.php';
    }
    

    public function requestAppointment() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    
        if (!isset($_SESSION['usuario'])) {
            header("Location: login_register.php");
            exit();
        }
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['request_appointment'])) {
            $appointmentModel = new Appointment();
            $appointmentModel->dni = htmlspecialchars(strip_tags($_POST['dni']));
            $appointmentModel->fecha_nacimiento = htmlspecialchars(strip_tags($_POST['fecha_nacimiento']));
            $appointmentModel->sexo = htmlspecialchars(strip_tags($_POST['sexo']));
            $appointmentModel->direccion = htmlspecialchars(strip_tags($_POST['direccion']));
            $appointmentModel->telefono = htmlspecialchars(strip_tags($_POST['telefono']));
            $appointmentModel->especialidad = htmlspecialchars(strip_tags($_POST['especialidad']));
            $appointmentModel->usuario_id = htmlspecialchars(strip_tags($_SESSION['usuario_id']));
            $appointmentModel->doctor_id = htmlspecialchars(strip_tags($_POST['doctor_id']));
            $appointmentModel->insertar_nombre = htmlspecialchars(strip_tags($_SESSION['usuario_nombre']));
    
            if ($appointmentModel->create()) {
                $_SESSION['message'] = "Cita solicitada exitosamente.";
                $_SESSION['message_type'] = "success";
            } else {
                $_SESSION['message'] = "Error al solicitar la cita. Por favor, inténtelo de nuevo.";
                $_SESSION['message_type'] = "error";
            }
        }
    }
    public function createUser() {
        if (isset($_POST['nombre_completo']) && isset($_POST['correo_electronico']) && isset($_POST['usuario']) && isset($_POST['contrasena'])) {
            $user = new User();
            $user->nombre_completo = htmlspecialchars(strip_tags($_POST['nombre_completo']));
            $user->correo_electronico = htmlspecialchars(strip_tags($_POST['correo_electronico']));
            $user->usuario = htmlspecialchars(strip_tags($_POST['usuario']));
            $user->contrasena = htmlspecialchars(strip_tags($_POST['contrasena']));

            if ($user->create()) {
                echo json_encode(["message" => "Usuario creado exitosamente."]);
            } else {
                echo json_encode(["message" => "No se pudo crear el usuario."]);
            }
        } else {
            echo json_encode(["message" => "Entrada inválida."]);
        }
    }

    public function getUsers() {
        $user = new User();
        $users = $user->read();
        echo json_encode($users);
    }

    public function loginUser() {
        session_start();

        if (isset($_POST['usuario']) && isset($_POST['contrasena'])) {
            $usuario = htmlspecialchars(strip_tags($_POST['usuario']));
            $contrasena = htmlspecialchars(strip_tags($_POST['contrasena']));

            $user = new User();
            $authenticatedUser = $user->login($usuario, $contrasena);

            if ($authenticatedUser) {
                $_SESSION['usuario'] = $authenticatedUser['usuario'];
                $_SESSION['usuario_id'] = $authenticatedUser['usuario_id'];
                $_SESSION['usuario_nombre'] = $authenticatedUser['nombre_completo']; // Guardar el nombre del paciente
                header("Location: ../views/users/patient_dashboard.php");
                exit();
            } else {
                echo json_encode(["message" => "Inicio de sesión fallido. Credenciales inválidas."]);
            }
        } else {
            echo json_encode(["message" => "Entrada inválida."]);
        }
    }

    public function getPatientInfo($id) {
        $userModel = new User();
        return $userModel->readById($id);
    }

    public function getAppointmentsByUserId($userId) {
        $appointmentModel = new Appointment();
        return $appointmentModel->getPatientAppointmentsByIdd($userId);
    }

    public function deleteAppointment($cita_id) {
        $appointmentModel = new Appointment();
        return $appointmentModel->delete($cita_id);
    }
    
    public function updateAppointment($cita_id, $data) {
        $appointmentModel = new Appointment();
        $currentAppointment = $appointmentModel->getAppointmentById($cita_id);
    
        $appointmentModel->cita_id = $cita_id;
        $appointmentModel->dni = htmlspecialchars(strip_tags($data['dni']));
        $appointmentModel->fecha_nacimiento = htmlspecialchars(strip_tags($data['fecha_nacimiento']));
        $appointmentModel->sexo = htmlspecialchars(strip_tags($data['sexo']));
        $appointmentModel->direccion = htmlspecialchars(strip_tags($data['direccion']));
        $appointmentModel->telefono = htmlspecialchars(strip_tags($data['telefono']));
        $appointmentModel->especialidad = htmlspecialchars(strip_tags($data['especialidad']));
        $appointmentModel->doctor_id = htmlspecialchars(strip_tags($data['doctor_id']));
        
        // Mantener el nombre original
        $appointmentModel->insertar_nombre = $currentAppointment['insertar_nombre'];
        
        // Mantener el estado original si no se proporciona
        $appointmentModel->estado = isset($data['estado']) ? htmlspecialchars(strip_tags($data['estado'])) : $currentAppointment['estado'];
    
        return $appointmentModel->update();
    }

    public function getAppointmentById($cita_id) {
        $appointmentModel = new Appointment();
        return $appointmentModel->getAppointmentById($cita_id);
    }

    public function getPatientAppointmentsByIdd($userId) {
        $appointmentModel = new Appointment();
        return $appointmentModel->getPatientAppointmentsByIdd($userId);
    }

    public function getAppointmentByIddd($appointmentId) {
        $appointmentModel = new Appointment();
        return $appointmentModel->getAppointmentByIddd($appointmentId);
    }
    
    public function cancelAppointment($appointmentId) {
        $appointmentModel = new Appointment();
        return $appointmentModel->cancelAppointment($appointmentId);
    }
}
?>

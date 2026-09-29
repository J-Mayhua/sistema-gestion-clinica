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


    public function requestAppointment(array $data, int $availabilityId, int $doctorId): bool {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['usuario'])) {
            header("Location: login_register.php");
            exit();
        }

        $appointmentModel = new Appointment();
        $data['usuario_id'] = (int)$_SESSION['usuario_id'];
        $data['insertar_nombre'] = (string)$_SESSION['usuario_nombre'];
        $created = $appointmentModel->createFromAvailableSlot($data, $availabilityId, $doctorId);

        $_SESSION['message'] = $created
            ? 'Cita solicitada exitosamente.'
            : 'El horario ya no está disponible o no se pudo guardar la cita.';
        $_SESSION['message_type'] = $created ? 'success' : 'error';

        return $created;
    }
    public function createUser() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // IMPORTANTE: ajusta esta ruta si tu pantalla de acceso está en otro lugar.
    $accessUrl = '/clinica/views/users/login_register.php';

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: $accessUrl");
        exit();
    }
    unset(
    $_SESSION['acceso_error'],
    $_SESSION['acceso_exito'],
    $_SESSION['acceso_formulario']
);

    $nombre = trim($_POST['nombre_completo'] ?? '');
    $correo = trim($_POST['correo_electronico'] ?? '');
    $usuario = trim($_POST['usuario'] ?? '');
    $contrasena = $_POST['contrasena'] ?? '';

    if (
        $nombre === '' ||
        $correo === '' ||
        $usuario === '' ||
        $contrasena === '' ||
        !filter_var($correo, FILTER_VALIDATE_EMAIL)
    ) {
        $_SESSION['acceso_error'] = 'Completa correctamente todos los campos.';
        $_SESSION['acceso_formulario'] = 'registro';
        header("Location: $accessUrl");
        exit();
    }

    try {
        $user = new User();
        $user->nombre_completo = $nombre;
        $user->correo_electronico = $correo;
        $user->usuario = $usuario;
        $user->contrasena = $contrasena;

        if ($user->create()) {
            $_SESSION['acceso_exito'] = 'Cuenta creada correctamente. Ya puedes iniciar sesión.';
            $_SESSION['acceso_formulario'] = 'login';
        } else {
            $_SESSION['acceso_error'] = 'No se pudo crear la cuenta. Inténtalo nuevamente.';
            $_SESSION['acceso_formulario'] = 'registro';
        }
    } catch (PDOException $e) {
        $driverCode = (int) ($e->errorInfo[1] ?? 0);

        if ($driverCode === 1062) {
            $_SESSION['acceso_error'] = 'El correo electrónico o el usuario ya está registrado.';
        } else {
            // No enviar la consulta ni los detalles de la excepción al navegador.
            error_log('Error al registrar usuario. Código SQLSTATE: ' . $e->getCode());
            $_SESSION['acceso_error'] = 'Ocurrió un problema al crear la cuenta. Inténtalo más tarde.';
        }

        $_SESSION['acceso_formulario'] = 'registro';
    }

    header("Location: $accessUrl");
    exit();
}


    public function getUsers() {
        $user = new User();
        $users = $user->read();
        echo json_encode($users);
    }

    public function loginUser() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // IMPORTANTE: debe ser la misma ruta que en createUser().
    $accessUrl = '/clinica/views/users/login_register.php';

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: $accessUrl");
        exit();
    }
    unset(
    $_SESSION['acceso_error'],
    $_SESSION['acceso_exito'],
    $_SESSION['acceso_formulario']
);

    $usuario = trim($_POST['usuario'] ?? '');
    $contrasena = $_POST['contrasena'] ?? '';

    if ($usuario === '' || $contrasena === '') {
        $_SESSION['acceso_error'] = 'Ingresa tu usuario y contraseña.';
        $_SESSION['acceso_formulario'] = 'login';
        header("Location: $accessUrl");
        exit();
    }

    try {
        $user = new User();
        $authenticatedUser = $user->login($usuario, $contrasena);

        if (!$authenticatedUser) {
            $_SESSION['acceso_error'] = 'Usuario o contraseña incorrectos.';
            $_SESSION['acceso_formulario'] = 'login';
            header("Location: $accessUrl");
            exit();
        }

        session_regenerate_id(true);

        $_SESSION['usuario'] = $authenticatedUser['usuario'];
        $_SESSION['usuario_id'] = $authenticatedUser['usuario_id'];
        $_SESSION['usuario_nombre'] = $authenticatedUser['nombre_completo'];

        // Conserva el destino que ya usaba el inicio de sesión del paciente.
        header('Location: ../views/users/patient_dashboard.php');
        exit();
    } catch (PDOException $e) {
        error_log('Error al iniciar sesión. Código SQLSTATE: ' . $e->getCode());

        $_SESSION['acceso_error'] = 'No se pudo iniciar sesión en este momento. Inténtalo más tarde.';
        $_SESSION['acceso_formulario'] = 'login';
        header("Location: $accessUrl");
        exit();
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

    public function cancelAppointmentForPatient(int $appointmentId, int $userId): bool {
        $appointmentModel = new Appointment();
        return $appointmentModel->cancelForPatient($appointmentId, $userId);
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

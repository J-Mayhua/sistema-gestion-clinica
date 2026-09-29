<?php
require_once __DIR__ . '/../autoload.php';
$controller = isset($_GET['controller']) ? $_GET['controller'] : null;
$action = isset($_GET['action']) ? $_GET['action'] : null;

switch ($controller) {
    case 'doctor':
        require_once __DIR__ . '/../controllers/DoctorController.php';
        $controller = new DoctorController();
        switch ($action) {
            case 'create':
                $controller->createDoctor();
                break;
            case 'login':
                $controller->loginDoctor();
                break;
            case 'getPatients':
                echo json_encode($controller->getPatients());
                break;
            case 'updateCalendar':
                $calendar_data = json_decode(file_get_contents('php://input'), true);
                $controller->updateCalendar($calendar_data);
                break;
            default:
                $controller->getDoctors();
                break;
        }
        break;
    case 'user':
        require_once __DIR__ . '/../controllers/UserController.php';
        $controller = new UserController();
        switch ($action) {
            case 'create':
                $controller->createUser();
                break;
            case 'login':
                $controller->loginUser();
                break;
            case 'index':
                $controller->getUsers();
                break;
            default:
                echo "Invalid action for user.";
                break;
        }
        break;
    case 'appointment':
        require_once __DIR__ . '/../controllers/AppointmentController.php';
        $controller = new AppointmentController();
        switch ($action) {
            case 'create':
                $controller->createAppointment();
                break;
            case 'index':
                $controller->getAppointments();
                break;
            default:
                echo "Invalid action for appointment.";
                break;
        }
        break;
    default:
        echo "Invalid controller.";
        break;
}
?>

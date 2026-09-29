<?php
if (!class_exists('Disponibilidad')) {
    require_once __DIR__ . '/../models/Disponibilidad.php';
}
require_once __DIR__ . '/../config/csrf.php';

class DisponibilidadController {
    private $model;

    public function __construct() {
        $this->model = new Disponibilidad();
    }

    // Mostrar formulario
    public function index() {
        session_start();
        if (!isset($_SESSION['doctor_id'])) {
            header("Location: /clinica/views/doctor/login.php");
            exit();
        }

        $doctor_id = $_SESSION['doctor_id'];
        $horarios = $this->model->getByDoctorId($doctor_id);
        $csrfToken = csrfToken();

        include __DIR__ . '/../views/doctor/disponibilidad/index.php';
    }

    // Guardar horario
    public function guardar() {
        header('Content-Type: application/json');
        session_start();

        if (!isset($_SESSION['doctor_id'])) {
            echo json_encode(["success" => false, "message" => "No autorizado"]);
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !validarCsrf($_POST['csrf_token'] ?? null)) {
            http_response_code(403);
            echo json_encode(["success" => false, "message" => "Solicitud no válida"]);
            exit();
        }

        // Validar datos requeridos
        if (!isset($_POST['fecha']) || empty($_POST['fecha'])) {
            echo json_encode(["success" => false, "message" => "Fecha es requerida"]);
            exit();
        }

        if (!isset($_POST['hora_inicio']) || empty($_POST['hora_inicio'])) {
            echo json_encode(["success" => false, "message" => "Hora de inicio es requerida"]);
            exit();
        }

        if (!isset($_POST['hora_fin']) || empty($_POST['hora_fin'])) {
            echo json_encode(["success" => false, "message" => "Hora de fin es requerida"]);
            exit();
        }

        // Validar que la hora de fin sea después de la hora de inicio
        if ($_POST['hora_inicio'] >= $_POST['hora_fin']) {
            echo json_encode(["success" => false, "message" => "La hora de fin debe ser posterior a la hora de inicio"]);
            exit();
        }

        // Validar que la fecha no sea en el pasado
        if ($_POST['fecha'] < date('Y-m-d')) {
            echo json_encode(["success" => false, "message" => "No se puede crear horarios en fechas pasadas"]);
            exit();
        }

        $data = [
            'doctor_id' => $_SESSION['doctor_id'],
            'fecha' => $_POST['fecha'],
            'hora_inicio' => $_POST['hora_inicio'],
            'hora_fin' => $_POST['hora_fin'],
            'estado' => 'libre'
        ];

        if ($this->model->guardar($data)) {
            echo json_encode(["success" => true, "message" => "Horario guardado exitosamente"]);
        } else {
            echo json_encode(["success" => false, "message" => "Error: Ya existe un horario para esta fecha o no se pudo guardar"]);
        }
        exit();
    }

    // Devolver solo la lista de horarios (para AJAX) - Vista del DOCTOR
    public function obtenerHorariosHtml() {
        session_start();
        if (!isset($_SESSION['doctor_id'])) {
            echo '<div class="empty-state"><p>❌ No autorizado.</p></div>';
            return;
        }

        $doctor_id = $_SESSION['doctor_id'];
        $horarios = $this->model->getByDoctorId($doctor_id);

        if (!empty($horarios)) {
            foreach ($horarios as $h) {
                $fechaFormateada = date('d/m/Y', strtotime($h['fecha']));
                $horaInicio = date('H:i', strtotime($h['hora_inicio']));
                $horaFin = date('H:i', strtotime($h['hora_fin']));

                // Determinar clase de estado
                $estadoClass = 'estado-libre';
                $estadoEmoji = '🟢';
                $estadoTexto = 'Libre';

                if ($h['estado'] == 'ocupado') {
                    $estadoClass = 'estado-ocupado';
                    $estadoEmoji = '🟡';
                    $estadoTexto = 'Ocupado';
                } elseif ($h['estado'] == 'cita') {
                    $estadoClass = 'estado-cita';
                    $estadoEmoji = '🔴';
                    $estadoTexto = 'Con Cita';
                }

                echo '<div class="horario-item">';
                echo '<div class="horario-info">';
                echo '<div class="horario-fecha">📅 ' . $fechaFormateada . '</div>';
                echo '<div class="horario-tiempo">🕒 ' . $horaInicio . ' - ' . $horaFin . '</div>';
                echo '<span class="horario-estado ' . $estadoClass . '" onclick="abrirModalEstado(' . $h['disponibilidad_id'] . ', \'' . $h['fecha'] . '\', \'' . $horaInicio . '\', \'' . $horaFin . '\', \'' . $h['estado'] . '\')" title="Clic para cambiar estado">';
                echo $estadoEmoji . ' ' . $estadoTexto;
                echo '</span>';
                echo '</div>';
                echo '<div class="horario-acciones">';
                echo '<button class="btn-cambiar-estado" onclick="abrirModalEstado(' . $h['disponibilidad_id'] . ', \'' . $h['fecha'] . '\', \'' . $horaInicio . '\', \'' . $horaFin . '\', \'' . $h['estado'] . '\')" title="Cambiar estado">';
                echo '🔄 Estado';
                echo '</button>';
                echo '<button class="btn-eliminar" onclick="eliminarHorario(' . $h['disponibilidad_id'] . ')" title="Eliminar horario">';
                echo '🗑️ Eliminar';
                echo '</button>';
                echo '</div>';
                echo '</div>';
            }
        } else {
            echo '<div class="empty-state">';
            echo '<div style="font-size: 4em; margin-bottom: 20px; opacity: 0.3;">📅</div>';
            echo '<h3>No tienes horarios registrados</h3>';
            echo '<p>Crea tu primer horario usando el formulario de arriba</p>';
            echo '</div>';
        }
    }

    // NUEVA FUNCIÓN: Cambiar estado
    public function cambiarEstado() {
        header('Content-Type: application/json');
        session_start();

        if (!isset($_SESSION['doctor_id'])) {
            echo json_encode(["success" => false, "message" => "No autorizado"]);
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !validarCsrf($_POST['csrf_token'] ?? null)) {
            http_response_code(403);
            echo json_encode(["success" => false, "message" => "Solicitud no válida"]);
            exit();
        }

        if (!isset($_POST['disponibilidad_id']) || !isset($_POST['estado'])) {
            echo json_encode(["success" => false, "message" => "Datos incompletos"]);
            exit();
        }

        $disponibilidad_id = $_POST['disponibilidad_id'];
        $estado = $_POST['estado'];

        // Validar que el estado sea válido
        $estadosValidos = ['libre', 'ocupado', 'cita'];
        if (!in_array($estado, $estadosValidos)) {
            echo json_encode(["success" => false, "message" => "Estado no válido"]);
            exit();
        }

        if ($this->model->actualizarEstado($disponibilidad_id, $estado, (int)$_SESSION['doctor_id'])) {
            $mensajes = [
                'libre' => 'Estado cambiado a Libre exitosamente',
                'ocupado' => 'Estado cambiado a Ocupado exitosamente',
                'cita' => 'Estado cambiado a Con Cita exitosamente'
            ];
            echo json_encode(["success" => true, "message" => $mensajes[$estado]]);
        } else {
            echo json_encode(["success" => false, "message" => "Error al cambiar el estado"]);
        }
        exit();
    }

    // Eliminar horario
    public function eliminar() {
        header('Content-Type: application/json');
        session_start();

        if (!isset($_SESSION['doctor_id'])) {
            echo json_encode(["success" => false, "message" => "No autorizado"]);
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !validarCsrf($_POST['csrf_token'] ?? null)) {
            http_response_code(403);
            echo json_encode(["success" => false, "message" => "Solicitud no válida"]);
            exit();
        }

        if (!isset($_POST['disponibilidad_id'])) {
            echo json_encode(["success" => false, "message" => "ID de horario no válido"]);
            exit();
        }

        $disponibilidad_id = $_POST['disponibilidad_id'];
        $doctor_id = $_SESSION['doctor_id'];

        if ($this->model->eliminar($disponibilidad_id, $doctor_id)) {
            echo json_encode(["success" => true, "message" => "Horario eliminado exitosamente"]);
        } else {
            echo json_encode(["success" => false, "message" => "Error al eliminar el horario"]);
        }
        exit();
    }

    // Para que los pacientes puedan ver horarios disponibles
    public function verDisponibles() {
        $horarios = $this->model->getDisponiblesParaPacientes();
        include __DIR__ . '/../views/paciente/horarios_disponibles.php';
    }
}

// --- Ejecutar solo si se accede directamente ---
if (basename($_SERVER['PHP_SELF']) == 'DisponibilidadController.php') {
    $controller = new DisponibilidadController();

    $action = isset($_GET['action']) ? $_GET['action'] : 'index';

    if (method_exists($controller, $action)) {
        $controller->{$action}();
    } else {
        $controller->index();
    }
}
?>

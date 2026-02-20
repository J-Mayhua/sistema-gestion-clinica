<?php
require_once __DIR__ . '/../config/conexion.php';

class Appointment {
    private $conn;
    private $table = 'registrar_citas';

    public $cita_id;
    public $dni;
    public $fecha_nacimiento;
    public $sexo;
    public $direccion;
    public $telefono;
    public $especialidad;
    public $usuario_id;
    public $doctor_id;
    public $insertar_nombre;
    public $estado;

    public function __construct() {
        $database = new Conexion();
        $this->conn = $database->conectar();
    }

    // Crea una nueva cita
    public function create() {
        $query = "INSERT INTO " . $this->table . " (dni, fecha_nacimiento, sexo, direccion, telefono, especialidad, usuario_id, doctor_id, insertar_nombre, estado) 
                  VALUES (:dni, :fecha_nacimiento, :sexo, :direccion, :telefono, :especialidad, :usuario_id, :doctor_id, :insertar_nombre, 'Pendiente')";
        $stmt = $this->conn->prepare($query);

        // Sanitización de entradas
        $this->dni = htmlspecialchars(strip_tags($this->dni));
        $this->fecha_nacimiento = htmlspecialchars(strip_tags($this->fecha_nacimiento));
        $this->sexo = htmlspecialchars(strip_tags($this->sexo));
        $this->direccion = htmlspecialchars(strip_tags($this->direccion));
        $this->telefono = htmlspecialchars(strip_tags($this->telefono));
        $this->especialidad = htmlspecialchars(strip_tags($this->especialidad));
        $this->usuario_id = htmlspecialchars(strip_tags($this->usuario_id));
        $this->doctor_id = htmlspecialchars(strip_tags($this->doctor_id));
        $this->insertar_nombre = htmlspecialchars(strip_tags($this->insertar_nombre));

        // Asignación de parámetros
        $stmt->bindParam(":dni", $this->dni);
        $stmt->bindParam(":fecha_nacimiento", $this->fecha_nacimiento);
        $stmt->bindParam(":sexo", $this->sexo);
        $stmt->bindParam(":direccion", $this->direccion);
        $stmt->bindParam(":telefono", $this->telefono);
        $stmt->bindParam(":especialidad", $this->especialidad);
        $stmt->bindParam(":usuario_id", $this->usuario_id);
        $stmt->bindParam(":doctor_id", $this->doctor_id);
        $stmt->bindParam(":insertar_nombre", $this->insertar_nombre);

        if ($stmt->execute()) {
            return true;
        }
        printf("Error: %s.\n", $stmt->error);
        return false;
    }

    // Lee las citas de un paciente
    public function getPatientAppointments($usuario_id) {
        $query = "SELECT a.*, u.nombre_completo as nombre_paciente, d.nombre_completo as nombre_doctor, DATE(a.fecha_creacion) as fecha_solicitud, a.estado 
                  FROM " . $this->table . " a
                  JOIN login_usuario u ON a.usuario_id = u.usuario_id
                  JOIN login_usuario d ON a.doctor_id = d.usuario_id  -- Aquí se une la tabla de doctores
                  WHERE a.usuario_id = :usuario_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":usuario_id", $usuario_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    
    public function getDoctorAppointments($doctor_id) {
        $query = "SELECT a.*, u.nombre_completo as nombre_paciente, d.nombre_completo as nombre_doctor, a.estado 
                  FROM " . $this->table . " a
                  JOIN login_usuario u ON a.usuario_id = u.usuario_id
                  JOIN login_usuario d ON a.doctor_id = d.usuario_id
                  WHERE a.doctor_id = :doctor_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":doctor_id", $doctor_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function delete($cita_id) {
        $query = "DELETE FROM " . $this->table . " WHERE cita_id = :cita_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":cita_id", $cita_id);
        return $stmt->execute();
    }

    public function update() {
        $query = "UPDATE " . $this->table . " 
                  SET dni = :dni, fecha_nacimiento = :fecha_nacimiento, sexo = :sexo, 
                      direccion = :direccion, telefono = :telefono, especialidad = :especialidad, 
                      doctor_id = :doctor_id, insertar_nombre = :insertar_nombre, estado = :estado
                  WHERE cita_id = :cita_id";
        
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":dni", $this->dni);
        $stmt->bindParam(":fecha_nacimiento", $this->fecha_nacimiento);
        $stmt->bindParam(":sexo", $this->sexo);
        $stmt->bindParam(":direccion", $this->direccion);
        $stmt->bindParam(":telefono", $this->telefono);
        $stmt->bindParam(":especialidad", $this->especialidad);
        $stmt->bindParam(":doctor_id", $this->doctor_id);
        $stmt->bindParam(":insertar_nombre", $this->insertar_nombre);
        $stmt->bindParam(":estado", $this->estado); // Nuevo parámetro
        $stmt->bindParam(":cita_id", $this->cita_id);
        
        return $stmt->execute();
    }
    
    public function getAppointmentById($cita_id) {
        $query = "SELECT * FROM " . $this->table . " WHERE cita_id = :cita_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":cita_id", $cita_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getPatientAppointmentsByIdd($usuario_id) {
        $query = "SELECT a.*, DATE(a.fecha_creacion) as fecha_solicitud 
                  FROM " . $this->table . " a
                  WHERE a.usuario_id = :usuario_id
                  ORDER BY a.fecha_creacion DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":usuario_id", $usuario_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAppointmentByIddd($appointmentId) {
        $query = "SELECT *, DATE(fecha_creacion) as fecha_solicitud 
                  FROM " . $this->table . " 
                  WHERE cita_id = :cita_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":cita_id", $appointmentId);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function cancelAppointment($appointmentId) {
        $query = "UPDATE " . $this->table . " SET estado = 'Cancelada' WHERE cita_id = :cita_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":cita_id", $appointmentId);
        return $stmt->execute();
    }
    public function updateStatus($cita_id, $nuevo_estado) {
        $query = "UPDATE " . $this->table . " SET estado = :estado WHERE cita_id = :cita_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":estado", $nuevo_estado);
        $stmt->bindParam(":cita_id", $cita_id);
        return $stmt->execute();
    }
}
?>

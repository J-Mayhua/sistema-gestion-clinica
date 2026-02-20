<?php
require_once __DIR__ . '/../config/conexion.php';

class Doctor {
    private $conn;
    private $table = 'doctor'; 

    public $doctor_id;
    public $correo;
    public $contrasena;
    public $nombres;
    public $apellidos;
    public $especialidad;
    public $telefono;
    public $horario;

    public function __construct() {
        $database = new Conexion();
        $this->conn = $database->conectar();
    }

    public function create() {
        $query = "INSERT INTO " . $this->table . " (correo, contrasena, nombres, apellidos, especialidad, telefono, horario) 
                  VALUES (:correo, :contrasena, :nombres, :apellidos, :especialidad, :telefono, :horario)";
        $stmt = $this->conn->prepare($query);

        $this->correo = htmlspecialchars(strip_tags($this->correo));
        $this->contrasena = password_hash(htmlspecialchars(strip_tags($this->contrasena)), PASSWORD_BCRYPT);
        $this->nombres = htmlspecialchars(strip_tags($this->nombres));
        $this->apellidos = htmlspecialchars(strip_tags($this->apellidos));
        $this->especialidad = htmlspecialchars(strip_tags($this->especialidad));
        $this->telefono = htmlspecialchars(strip_tags($this->telefono));
        $this->horario = htmlspecialchars(strip_tags($this->horario));

        $stmt->bindParam(":correo", $this->correo);
        $stmt->bindParam(":contrasena", $this->contrasena);
        $stmt->bindParam(":nombres", $this->nombres);
        $stmt->bindParam(":apellidos", $this->apellidos);
        $stmt->bindParam(":especialidad", $this->especialidad);
        $stmt->bindParam(":telefono", $this->telefono);
        $stmt->bindParam(":horario", $this->horario);

        if ($stmt->execute()) {
            return true;
        }
        printf("Error: %s.\n", $stmt->error);
        return false;
    }

    public function read() {
        $query = "SELECT * FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function login() {
        $query = "SELECT * FROM " . $this->table . " WHERE correo = :correo";
        $stmt = $this->conn->prepare($query);
    
        $this->correo = htmlspecialchars(strip_tags($this->correo));
        $stmt->bindParam(":correo", $this->correo);
    
        $stmt->execute();
    
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (password_verify($this->contrasena, $row['contrasena'])) {
                $this->doctor_id = $row['doctor_id'];
                return $row;
            }
        }
        return false;
    }

    public function getPatients() {
        $query = "SELECT * FROM registrar_citas WHERE doctor_id = :doctor_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':doctor_id', $this->doctor_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateCalendar($calendar_data) {
        foreach ($calendar_data as $date => $availability) {
            $query = "INSERT INTO tabla_disponibilidad (doctor_id, fecha, estado) 
                      VALUES (:doctor_id, :fecha, :estado) 
                      ON DUPLICATE KEY UPDATE estado = :estado";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':doctor_id', $this->doctor_id);
            $stmt->bindParam(':fecha', $date);
            $stmt->bindParam(':estado', $availability);
            $stmt->execute();
        }
        return true;
    }
}
?>

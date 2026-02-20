<?php
require_once __DIR__ . '/../config/conexion.php';

class Disponibilidad {
    private $conn;
    private $table = 'tabla_disponibilidad';

    public function __construct() {
        $database = new Conexion();
        $this->conn = $database->conectar();
    }

    // Guardar un nuevo horario (evita duplicados)
    public function guardar($data) {
        // Verificar si ya existe un horario para esa fecha y doctor
        $check = "SELECT * FROM " . $this->table . " WHERE doctor_id = ? AND fecha = ?";
        $stmt = $this->conn->prepare($check);
        $stmt->execute([$data['doctor_id'], $data['fecha']]);
        if ($stmt->rowCount() > 0) {
            return false; // Ya existe un horario para esa fecha
        }

        // Insertar nuevo horario con hora_inicio y hora_fin
        $query = "INSERT INTO " . $this->table . " (doctor_id, fecha, hora_inicio, hora_fin, estado) 
                  VALUES (:doctor_id, :fecha, :hora_inicio, :hora_fin, :estado)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':doctor_id' => $data['doctor_id'],
            ':fecha' => $data['fecha'],
            ':hora_inicio' => $data['hora_inicio'],
            ':hora_fin' => $data['hora_fin'],
            ':estado' => 'libre'  // Cambiado a 'libre' según tu BD
        ]);
    }

    // Obtener horarios por doctor
    public function getByDoctorId($doctor_id) {
        $query = "SELECT * FROM " . $this->table . " WHERE doctor_id = ? ORDER BY fecha ASC, hora_inicio ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$doctor_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener horarios disponibles para pacientes (corregido con nombres de campos reales)
    public function getDisponiblesParaPacientes() {
        $query = "SELECT d.*, doc.nombres as doctor_nombres, doc.apellidos as doctor_apellidos, doc.especialidad 
                  FROM " . $this->table . " d 
                  INNER JOIN doctor doc ON d.doctor_id = doc.doctor_id 
                  WHERE d.estado = 'libre' AND d.fecha >= CURDATE() 
                  AND d.hora_inicio != '00:00:00' AND d.hora_fin != '00:00:00'
                  ORDER BY d.fecha ASC, d.hora_inicio ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Actualizar estado
    public function actualizarEstado($disponibilidad_id, $estado) {
        $query = "UPDATE " . $this->table . " SET estado = ? WHERE disponibilidad_id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$estado, $disponibilidad_id]);
    }

    // Eliminar horario
    public function eliminar($disponibilidad_id, $doctor_id) {
        $query = "DELETE FROM " . $this->table . " WHERE disponibilidad_id = ? AND doctor_id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$disponibilidad_id, $doctor_id]);
    }

    // Limpiar datos con problemas (método auxiliar)
    public function limpiarDatosProblem() {
        // Eliminar registros con horas 00:00:00 o estados vacíos
        $query = "DELETE FROM " . $this->table . " WHERE 
                  hora_inicio = '00:00:00' OR hora_fin = '00:00:00' OR estado = '' OR estado IS NULL";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute();
    }
}
?>
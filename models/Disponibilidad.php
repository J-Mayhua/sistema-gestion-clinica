<?php
require_once __DIR__ . '/../config/conexion.php';

class Disponibilidad {
    private $conn;
    private $table = 'tabla_disponibilidad';

    public function __construct() {
        $database = new Conexion();
        $this->conn = $database->conectar();
    }

    // Guardar un nuevo horario (evita duplicados por doctor+fecha)
    public function guardar($data) {
        $check = "SELECT disponibilidad_id FROM " . $this->table . "
                  WHERE doctor_id = ? AND fecha = ?";
        $stmt = $this->conn->prepare($check);
        $stmt->execute([$data['doctor_id'], $data['fecha']]);
        if ($stmt->rowCount() > 0) {
            return false;
        }

        $query = "INSERT INTO " . $this->table . "
                    (doctor_id, fecha, hora_inicio, hora_fin, estado)
                  VALUES (:doctor_id, :fecha, :hora_inicio, :hora_fin, :estado)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':doctor_id'   => $data['doctor_id'],
            ':fecha'       => $data['fecha'],
            ':hora_inicio' => $data['hora_inicio'],
            ':hora_fin'    => $data['hora_fin'],
            ':estado'      => 'libre',
        ]);
    }

    // Obtener horarios por doctor
    public function getByDoctorId($doctor_id) {
        $query = "SELECT * FROM " . $this->table . "
                  WHERE doctor_id = ?
                  ORDER BY fecha ASC, hora_inicio ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$doctor_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener horarios disponibles para pacientes
    public function getDisponiblesParaPacientes() {
        $query = "SELECT d.*, doc.nombres AS doctor_nombres,
                 doc.apellidos AS doctor_apellidos, doc.especialidad
                  FROM " . $this->table . " d
                  INNER JOIN doctor doc ON d.doctor_id = doc.doctor_id
                  WHERE d.estado = 'libre'
                    AND d.fecha >= CURDATE()
                    AND d.hora_inicio != '00:00:00'
                    AND d.hora_fin    != '00:00:00'
                  ORDER BY d.fecha ASC, d.hora_inicio ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ─── NUEVO ────────────────────────────────────────────────────────────────
    // Obtener un slot por su ID verificando que pertenece al doctor indicado
    // y que sigue en estado 'libre'. Usado en store_cita.php para validar
    // antes de reservar.
    // ─────────────────────────────────────────────────────────────────────────
    public function getDisponibleById($disponibilidad_id, $doctor_id) {
        $query = "SELECT * FROM " . $this->table . "
                  WHERE disponibilidad_id = ?
                    AND doctor_id = ?
                    AND estado = 'libre'
                  LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$disponibilidad_id, $doctor_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getDisponibleFuturoById($disponibilidad_id) {
        $query = "SELECT d.*, doc.nombres AS doctor_nombres,
                         doc.apellidos AS doctor_apellidos, doc.especialidad
                  FROM " . $this->table . " d
                  INNER JOIN doctor doc ON d.doctor_id = doc.doctor_id
                  WHERE d.disponibilidad_id = ?
                    AND d.estado = 'libre'
                    AND d.fecha >= CURDATE()
                    AND d.hora_inicio <> '00:00:00'
                    AND d.hora_fin <> '00:00:00'
                  LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$disponibilidad_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // ─── NUEVO ────────────────────────────────────────────────────────────────
    // Reservar un slot de forma atómica con transacción:
    //   1. Bloquea la fila con SELECT ... FOR UPDATE
    //   2. Verifica que sigue en 'libre'
    //   3. Lo marca como 'ocupado'
    // Devuelve true si se reservó, false si ya estaba tomado (doble reserva)
    // ─────────────────────────────────────────────────────────────────────────
    public function reservar($disponibilidad_id, $doctor_id) {
        try {
            $this->conn->beginTransaction();

            // Bloqueo a nivel de fila para evitar reserva simultánea
            $lock = "SELECT disponibilidad_id FROM " . $this->table . "
                     WHERE disponibilidad_id = ?
                       AND doctor_id = ?
                       AND estado = 'libre'
                     FOR UPDATE";
            $stmt = $this->conn->prepare($lock);
            $stmt->execute([$disponibilidad_id, $doctor_id]);

            if ($stmt->rowCount() === 0) {
                // Ya fue reservado por otra petición simultánea
                $this->conn->rollBack();
                return false;
            }

            $update = "UPDATE " . $this->table . "
                       SET estado = 'ocupado'
                       WHERE disponibilidad_id = ?";
            $stmt2 = $this->conn->prepare($update);
            $stmt2->execute([$disponibilidad_id]);

            $this->conn->commit();
            return true;

        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log("Disponibilidad::reservar error: " . $e->getMessage());
            return false;
        }
    }

    // Actualizar estado manualmente (uso del doctor)
    public function actualizarEstado($disponibilidad_id, $estado, $doctor_id) {
        $query = "UPDATE " . $this->table . " SET estado = ?
                  WHERE disponibilidad_id = ? AND doctor_id = ?
                    AND NOT EXISTS (
                        SELECT 1 FROM registrar_citas a
                        WHERE a.disponibilidad_id = " . $this->table . ".disponibilidad_id
                    )";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$estado, $disponibilidad_id, $doctor_id]);
        return $stmt->rowCount() === 1;
    }

    // Eliminar horario
    public function eliminar($disponibilidad_id, $doctor_id) {
        $query = "DELETE FROM " . $this->table . "
                  WHERE disponibilidad_id = ? AND doctor_id = ? AND estado = 'libre'
                    AND NOT EXISTS (
                        SELECT 1 FROM registrar_citas a
                        WHERE a.disponibilidad_id = " . $this->table . ".disponibilidad_id
                    )";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$disponibilidad_id, $doctor_id]);
        return $stmt->rowCount() === 1;
    }

    // Limpiar datos con problemas (método auxiliar)
    public function limpiarDatosProblema() {
        $query = "DELETE FROM " . $this->table . "
                  WHERE hora_inicio = '00:00:00'
                     OR hora_fin    = '00:00:00'
                     OR estado IS NULL
                     OR estado = ''";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute();
    }
}
?>

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
        $query = "INSERT INTO " . $this->table . "
                    (dni, fecha_nacimiento, sexo, direccion, telefono,
                     especialidad, usuario_id, doctor_id, insertar_nombre, estado)
                  VALUES
                    (:dni, :fecha_nacimiento, :sexo, :direccion, :telefono,
                     :especialidad, :usuario_id, :doctor_id, :insertar_nombre, 'Pendiente')";
        $stmt = $this->conn->prepare($query);

        // strip_tags: elimina etiquetas HTML antes de persistir.
        // htmlspecialchars se aplica SOLO al mostrar en HTML, no al guardar en BD.
        $this->dni             = strip_tags($this->dni);
        $this->fecha_nacimiento= strip_tags($this->fecha_nacimiento);
        $this->sexo            = strip_tags($this->sexo);
        $this->direccion       = strip_tags($this->direccion);
        $this->telefono        = strip_tags($this->telefono);
        $this->especialidad    = strip_tags($this->especialidad);
        $this->usuario_id      = (int) $this->usuario_id;
        $this->doctor_id       = (int) $this->doctor_id;
        $this->insertar_nombre = strip_tags($this->insertar_nombre);

        $stmt->bindParam(":dni",             $this->dni);
        $stmt->bindParam(":fecha_nacimiento",$this->fecha_nacimiento);
        $stmt->bindParam(":sexo",            $this->sexo);
        $stmt->bindParam(":direccion",       $this->direccion);
        $stmt->bindParam(":telefono",        $this->telefono);
        $stmt->bindParam(":especialidad",    $this->especialidad);
        $stmt->bindParam(":usuario_id",      $this->usuario_id, PDO::PARAM_INT);
        $stmt->bindParam(":doctor_id",       $this->doctor_id,  PDO::PARAM_INT);
        $stmt->bindParam(":insertar_nombre", $this->insertar_nombre);

        return $stmt->execute();
    }

    /**
     * Lectura general de citas (sin filtro de paciente).
     * Reemplaza al inexistente read() que AppointmentController::getAppointments() llamaba.
     * Solo debe usarse desde contextos con autorización verificada (rol doctor/admin).
     */
    public function readAll(): array {
        $query = "SELECT a.*,
                         u.nombre_completo                AS nombre_paciente,
                         CONCAT(d.nombres,' ',d.apellidos) AS nombre_doctor,
                         DATE(a.fecha_creacion)           AS fecha_solicitud
                  FROM " . $this->table . " a
                  JOIN login_usuario u ON a.usuario_id = u.usuario_id
                  JOIN doctor        d ON a.doctor_id  = d.doctor_id
                  ORDER BY a.fecha_creacion DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createFromAvailableSlot(array $data, int $availabilityId, int $doctorId): bool {
        try {
            $this->conn->beginTransaction();

            $slotQuery = "SELECT disponibilidad_id FROM tabla_disponibilidad
                          WHERE disponibilidad_id = :disponibilidad_id
                            AND doctor_id = :doctor_id
                            AND estado = 'libre'
                            AND fecha >= CURDATE()
                            AND hora_inicio <> '00:00:00'
                            AND hora_fin <> '00:00:00'
                          FOR UPDATE";
            $slotStmt = $this->conn->prepare($slotQuery);
            $slotStmt->execute([
                ':disponibilidad_id' => $availabilityId,
                ':doctor_id'         => $doctorId,
            ]);

            if (!$slotStmt->fetchColumn()) {
                $this->conn->rollBack();
                return false;
            }

            $insertQuery = "INSERT INTO registrar_citas
                            (dni, fecha_nacimiento, sexo, direccion, telefono,
                             especialidad, usuario_id, doctor_id, disponibilidad_id,
                             insertar_nombre, estado)
                            VALUES
                            (:dni, :fecha_nacimiento, :sexo, :direccion, :telefono,
                             :especialidad, :usuario_id, :doctor_id, :disponibilidad_id,
                             :insertar_nombre, 'Pendiente')";
            $insertStmt = $this->conn->prepare($insertQuery);
            $insertStmt->execute([
                ':dni'              => strip_tags($data['dni']),
                ':fecha_nacimiento' => strip_tags($data['fecha_nacimiento']),
                ':sexo'             => strip_tags($data['sexo']),
                ':direccion'        => strip_tags($data['direccion']),
                ':telefono'         => strip_tags($data['telefono']),
                ':especialidad'     => strip_tags($data['especialidad']),
                ':usuario_id'       => (int) $data['usuario_id'],
                ':doctor_id'        => $doctorId,
                ':disponibilidad_id'=> $availabilityId,
                ':insertar_nombre'  => strip_tags($data['insertar_nombre']),
            ]);

            $updateQuery = "UPDATE tabla_disponibilidad
                            SET estado = 'ocupado'
                            WHERE disponibilidad_id = :disponibilidad_id
                              AND doctor_id = :doctor_id
                              AND estado = 'libre'";
            $updateStmt = $this->conn->prepare($updateQuery);
            $updateStmt->execute([
                ':disponibilidad_id' => $availabilityId,
                ':doctor_id'         => $doctorId,
            ]);

            if ($updateStmt->rowCount() !== 1) {
                throw new RuntimeException('Availability slot changed during reservation.');
            }

            $this->conn->commit();
            return true;
        } catch (Throwable $exception) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            error_log('Appointment::createFromAvailableSlot failed: ' . $exception->getMessage());
            return false;
        }
    }

    public function getPatientAppointments($usuario_id) {
        $query = "SELECT a.*,
                         u.nombre_completo                AS nombre_paciente,
                         CONCAT(d.nombres,' ',d.apellidos) AS nombre_doctor,
                         DATE(a.fecha_creacion)           AS fecha_solicitud
                  FROM " . $this->table . " a
                  JOIN login_usuario u ON a.usuario_id = u.usuario_id
                  JOIN doctor        d ON a.doctor_id  = d.doctor_id
                  WHERE a.usuario_id = :usuario_id
                  ORDER BY a.fecha_creacion DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":usuario_id", $usuario_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDoctorAppointments($doctor_id) {
        $query = "SELECT a.*,
                         u.nombre_completo                AS nombre_paciente,
                         CONCAT(d.nombres,' ',d.apellidos) AS nombre_doctor,
                         a.estado
                  FROM " . $this->table . " a
                  JOIN login_usuario u ON a.usuario_id = u.usuario_id
                  JOIN doctor        d ON a.doctor_id  = d.doctor_id
                  WHERE a.doctor_id = :doctor_id
                  ORDER BY a.fecha_creacion DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":doctor_id", $doctor_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPatientAppointmentsByIdd($usuario_id) {
        $query = "SELECT a.*,
                         CONCAT(d.nombres,' ',d.apellidos) AS nombre_doctor,
                         DATE(a.fecha_creacion) AS fecha_solicitud
                  FROM " . $this->table . " a
                  JOIN doctor d ON a.doctor_id = d.doctor_id
                  WHERE a.usuario_id = :usuario_id
                  ORDER BY a.fecha_creacion DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":usuario_id", $usuario_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAppointmentByIddd($appointmentId) {
        $query = "SELECT a.*,
                         CONCAT(d.nombres,' ',d.apellidos) AS nombre_doctor,
                         DATE(a.fecha_creacion) AS fecha_solicitud
                  FROM " . $this->table . " a
                  JOIN doctor d ON a.doctor_id = d.doctor_id
                  WHERE a.cita_id = :cita_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":cita_id", $appointmentId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAppointmentById($cita_id) {
        $query = "SELECT * FROM " . $this->table . " WHERE cita_id = :cita_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":cita_id", $cita_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function delete($cita_id) {
        $query = "DELETE FROM " . $this->table . " WHERE cita_id = :cita_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":cita_id", $cita_id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function update() {
        $query = "UPDATE " . $this->table . "
                  SET dni              = :dni,
                      fecha_nacimiento = :fecha_nacimiento,
                      sexo             = :sexo,
                      direccion        = :direccion,
                      telefono         = :telefono,
                      especialidad     = :especialidad,
                      doctor_id        = :doctor_id,
                      insertar_nombre  = :insertar_nombre,
                      estado           = :estado
                  WHERE cita_id = :cita_id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":dni",             $this->dni);
        $stmt->bindParam(":fecha_nacimiento",$this->fecha_nacimiento);
        $stmt->bindParam(":sexo",            $this->sexo);
        $stmt->bindParam(":direccion",       $this->direccion);
        $stmt->bindParam(":telefono",        $this->telefono);
        $stmt->bindParam(":especialidad",    $this->especialidad);
        $stmt->bindParam(":doctor_id",       $this->doctor_id,  PDO::PARAM_INT);
        $stmt->bindParam(":insertar_nombre", $this->insertar_nombre);
        $stmt->bindParam(":estado",          $this->estado);
        $stmt->bindParam(":cita_id",         $this->cita_id,    PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function cancelAppointment($appointmentId) {
        $query = "UPDATE " . $this->table . " SET estado = 'Cancelada' WHERE cita_id = :cita_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":cita_id", $appointmentId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function cancelForPatient(int $appointmentId, int $userId): bool {
        return $this->cancelOwnedAppointment($appointmentId, 'usuario_id', $userId, ['Pendiente']);
    }

    public function cancelForDoctor(int $appointmentId, int $doctorId): bool {
        return $this->cancelOwnedAppointment($appointmentId, 'doctor_id', $doctorId, ['Pendiente', 'Confirmada']);
    }

    private function cancelOwnedAppointment(int $appointmentId, string $ownerColumn, int $ownerId, array $allowedStates): bool {
        if (!in_array($ownerColumn, ['usuario_id', 'doctor_id'], true)) {
            return false;
        }

        try {
            $this->conn->beginTransaction();
            $query = "SELECT disponibilidad_id, estado FROM " . $this->table . "
                      WHERE cita_id = :cita_id AND " . $ownerColumn . " = :owner_id
                      FOR UPDATE";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([':cita_id' => $appointmentId, ':owner_id' => $ownerId]);
            $appointment = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$appointment || !in_array($appointment['estado'], $allowedStates, true)) {
                $this->conn->rollBack();
                return false;
            }

            $update = $this->conn->prepare(
                "UPDATE " . $this->table . " SET estado = 'Cancelada' WHERE cita_id = :cita_id"
            );
            $update->execute([':cita_id' => $appointmentId]);

            if ($appointment['disponibilidad_id'] !== null) {
                $release = $this->conn->prepare(
                    "UPDATE tabla_disponibilidad SET estado = 'libre'
                     WHERE disponibilidad_id = :disponibilidad_id AND estado = 'ocupado'"
                );
                $release->execute([':disponibilidad_id' => $appointment['disponibilidad_id']]);
                if ($release->rowCount() !== 1) {
                    throw new RuntimeException('Reserved availability slot could not be released.');
                }
            }

            $this->conn->commit();
            return true;
        } catch (Throwable $exception) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            error_log('Appointment cancellation failed: ' . $exception->getMessage());
            return false;
        }
    }

    public function updateForDoctor(int $appointmentId, int $doctorId, array $data): bool {
        $transitions = [
            'Pendiente'  => ['Pendiente', 'Confirmada', 'Cancelada'],
            'Confirmada' => ['Confirmada', 'Completada', 'Cancelada'],
            'Cancelada'  => ['Cancelada'],
            'Completada' => ['Completada'],
        ];

        try {
            $this->conn->beginTransaction();
            $stmt = $this->conn->prepare(
                "SELECT estado, disponibilidad_id FROM " . $this->table . "
                 WHERE cita_id = :cita_id AND doctor_id = :doctor_id FOR UPDATE"
            );
            $stmt->execute([':cita_id' => $appointmentId, ':doctor_id' => $doctorId]);
            $current = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$current
                || !in_array($current['estado'], ['Pendiente', 'Confirmada'], true)
                || !isset($transitions[$current['estado']])
                || !in_array($data['estado'], $transitions[$current['estado']], true)) {
                $this->conn->rollBack();
                return false;
            }

            $update = $this->conn->prepare(
                "UPDATE " . $this->table . "
                 SET dni = :dni, fecha_nacimiento = :fecha_nacimiento, sexo = :sexo,
                     direccion = :direccion, telefono = :telefono,
                     especialidad = :especialidad, estado = :estado
                 WHERE cita_id = :cita_id AND doctor_id = :doctor_id"
            );
            $update->execute([
                ':dni'              => $data['dni'],
                ':fecha_nacimiento' => $data['fecha_nacimiento'],
                ':sexo'             => $data['sexo'],
                ':direccion'        => $data['direccion'],
                ':telefono'         => $data['telefono'],
                ':especialidad'     => $data['especialidad'],
                ':estado'           => $data['estado'],
                ':cita_id'          => $appointmentId,
                ':doctor_id'        => $doctorId,
            ]);

            if ($current['estado'] !== 'Cancelada'
                && $data['estado'] === 'Cancelada'
                && $current['disponibilidad_id'] !== null) {
                $release = $this->conn->prepare(
                    "UPDATE tabla_disponibilidad SET estado = 'libre'
                     WHERE disponibilidad_id = :disponibilidad_id AND estado = 'ocupado'"
                );
                $release->execute([':disponibilidad_id' => $current['disponibilidad_id']]);
                if ($release->rowCount() !== 1) {
                    throw new RuntimeException('Reserved availability slot could not be released.');
                }
            }

            $this->conn->commit();
            return true;
        } catch (Throwable $exception) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            error_log('Appointment update failed: ' . $exception->getMessage());
            return false;
        }
    }

    public function updateStatus($cita_id, $nuevo_estado) {
        $query = "UPDATE " . $this->table . " SET estado = :estado WHERE cita_id = :cita_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":estado",  $nuevo_estado);
        $stmt->bindParam(":cita_id", $cita_id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}

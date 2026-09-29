<?php
require_once __DIR__ . '/../config/conexion.php';

class User {
    private $conn;
    private $table = 'login_usuario';

    public $usuario_id;
    public $nombre_completo;
    public $correo_electronico;
    public $usuario;
    public $contrasena;

    public function __construct() {
        $database = new Conexion();
        $this->conn = $database->conectar();
    }

    public function create() {
    $query = "INSERT INTO {$this->table}
              (nombre_completo, correo_electronico, usuario, contrasena)
              VALUES (:nombre_completo, :correo_electronico, :usuario, :contrasena)";

    $stmt = $this->conn->prepare($query);

    $hash = password_hash($this->contrasena, PASSWORD_BCRYPT);

    return $stmt->execute([
        ':nombre_completo' => $this->nombre_completo,
        ':correo_electronico' => $this->correo_electronico,
        ':usuario' => $this->usuario,
        ':contrasena' => $hash,
    ]);
}


    public function read() {
        $query = "SELECT * FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function readById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE usuario_id = :usuario_id";
        $stmt = $this->conn->prepare($query);
        $id = htmlspecialchars(strip_tags($id));
        $stmt->bindParam(":usuario_id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($id) {
        $query = "UPDATE " . $this->table . " SET nombre_completo = :nombre_completo, correo_electronico = :correo_electronico, usuario = :usuario, contrasena = :contrasena WHERE usuario_id = :usuario_id";
        $stmt = $this->conn->prepare($query);

        $this->nombre_completo = htmlspecialchars(strip_tags($this->nombre_completo));
        $this->correo_electronico = htmlspecialchars(strip_tags($this->correo_electronico));
        $this->usuario = htmlspecialchars(strip_tags($this->usuario));
        $this->contrasena = password_hash(htmlspecialchars(strip_tags($this->contrasena)), PASSWORD_BCRYPT);

        $stmt->bindParam(":nombre_completo", $this->nombre_completo);
        $stmt->bindParam(":correo_electronico", $this->correo_electronico);
        $stmt->bindParam(":usuario", $this->usuario);
        $stmt->bindParam(":contrasena", $this->contrasena);
        $stmt->bindParam(":usuario_id", $id);

        if ($stmt->execute()) {
            return true;
        }
        printf("Error: %s.\n", $stmt->error);
        return false;
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table . " WHERE usuario_id = :usuario_id";
        $stmt = $this->conn->prepare($query);
        $id = htmlspecialchars(strip_tags($id));
        $stmt->bindParam(":usuario_id", $id);
        return $stmt->execute();
    }

    public function login($usuario, $contrasena) {
        $query = "SELECT * FROM " . $this->table . " WHERE usuario = :usuario LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":usuario", $usuario);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($contrasena, $user['contrasena'])) {
            return $user;
        }

        return false;
    }
}
?>

<?php
/**
 * Conexion.php
 * Lee credenciales desde variables de entorno; si no existen usa los valores
 * de config.local.php (XAMPP); si tampoco existe ese archivo, cae al fallback
 * codificado para que el entorno de desarrollo siga funcionando sin cambios.
 */

// Cargar config local si existe (nunca debe subirse al repositorio)
$_localCfg = __DIR__ . '/config.local.php';
if (file_exists($_localCfg)) {
    require_once $_localCfg;
}
unset($_localCfg);

class Conexion {
    private string $host;
    private string $db;
    private string $user;
    private string $password;

    public function __construct() {
       $this->host = getenv('DB_HOST') ?: (defined('DB_HOST') ? DB_HOST : '127.0.0.1');

        $this->db       = getenv('DB_NAME')     ?: (defined('DB_NAME')     ? DB_NAME     : 'clinica');
        $this->user     = getenv('DB_USER')     ?: (defined('DB_USER')     ? DB_USER     : 'root');
        $this->password = getenv('DB_PASSWORD') ?: (defined('DB_PASSWORD') ? DB_PASSWORD : '');
    }

    public function conectar(): PDO {
        try {
            $dsn  = "mysql:host={$this->host};dbname={$this->db};charset=utf8mb4";
            $opts = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];
            return new PDO($dsn, $this->user, $this->password, $opts);
        } catch (PDOException $e) {
            error_log('DB connection error: ' . $e->getMessage());
            // Mensaje genérico al exterior; nunca credenciales ni traza
            throw new RuntimeException('No se pudo conectar a la base de datos. Inténtalo más tarde.');
        }
    }
}

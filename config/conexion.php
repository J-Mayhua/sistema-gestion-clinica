<?php
/**
 * config/conexion.php
 *
 * Orden de lectura de credenciales:
 *   1. Variables de entorno (Vercel / Aiven)
 *   2. config.local.php (XAMPP, no se sube a git)
 *   3. Valores por defecto de desarrollo
 */

// Cargar config local si existe (nunca debe subirse al repositorio)
$_localCfg = __DIR__ . '/config.local.php';
if (file_exists($_localCfg)) {
    require_once $_localCfg;
}
unset($_localCfg);

class Conexion {
    private string $host;
    private string $port;
    private string $db;
    private string $user;
    private string $password;
    private bool $usarSsl;

    /**
     * Lee una variable desde $_ENV o getenv(). Devuelve null si no existe o está vacía.
     */
    private function env(string $nombre): ?string {
        $valor = $_ENV[$nombre] ?? getenv($nombre);
        if ($valor === false || $valor === null || $valor === '') {
            return null;
        }
        return (string) $valor;
    }

    public function __construct() {
        $this->host     = $this->env('DB_HOST')     ?? (defined('DB_HOST')     ? DB_HOST     : '127.0.0.1');
        $this->port     = $this->env('DB_PORT')     ?? (defined('DB_PORT')     ? (string) DB_PORT : '3306');
        $this->db       = $this->env('DB_NAME')     ?? (defined('DB_NAME')     ? DB_NAME     : 'clinica');
        $this->user     = $this->env('DB_USER')     ?? (defined('DB_USER')     ? DB_USER     : 'root');
        $this->password = $this->env('DB_PASSWORD') ?? (defined('DB_PASSWORD') ? DB_PASSWORD : '');

        // XAMPP local: DB_SSL no existe o es false.
        // Aiven en Vercel: DB_SSL=true
        $this->usarSsl = strtolower($this->env('DB_SSL') ?? 'false') === 'true';
    }

    public function conectar(): PDO {
        try {
            $puerto = filter_var(
                $this->port,
                FILTER_VALIDATE_INT,
                ['options' => ['min_range' => 1, 'max_range' => 65535]]
            );
            if ($puerto === false) {
                throw new RuntimeException('DB_PORT no es un puerto válido.');
            }

            $dsn = "mysql:host={$this->host};port={$puerto};dbname={$this->db};charset=utf8mb4";

            $opts = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::ATTR_TIMEOUT            => 10,
            ];

            if ($this->usarSsl) {
                $caPath = __DIR__ . '/ca.pem';

                if (!is_file($caPath) || !is_readable($caPath)) {
                    throw new RuntimeException('No se encontró o no se puede leer config/ca.pem.');
                }

                if (PHP_VERSION_ID >= 80500) {
                    $opts[\Pdo\Mysql::ATTR_SSL_CA] = $caPath;
                    $opts[\Pdo\Mysql::ATTR_SSL_VERIFY_SERVER_CERT] = true;
                } else {
                    $opts[\PDO::MYSQL_ATTR_SSL_CA] = $caPath;
                    $opts[\PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = true;
                }
            }

            return new PDO($dsn, $this->user, $this->password, $opts);
        } catch (Throwable $e) {
            // Detalle técnico solo en los logs de Vercel
            error_log('DB connection error: ' . $e->getMessage());
            // Mensaje genérico al exterior; nunca credenciales ni traza
            throw new RuntimeException('No se pudo conectar a la base de datos. Inténtalo más tarde.');
        }
    }
}

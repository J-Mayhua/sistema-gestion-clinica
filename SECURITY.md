# SECURITY.md - Guía de Seguridad

## 🔐 Postura de Seguridad

HappyDent implementa **seguridad en capas (Defense in Depth)** con múltiples niveles de protección contra las vulnerabilidades OWASP Top 10.

```
┌─────────────────────────────────────────────────────────┐
│  Cliente (Browser)                                      │
│  - Validación HTML5 (UX)                                │
│  - HTTPS enforced                                       │
└─────────────────────────────────────────────────────────┘
                        ↓
┌─────────────────────────────────────────────────────────┐
│  HTTP Transport                                         │
│  - HTTPS with TLS 1.2+ (en producción)                 │
│  - HSTS header (en producción)                         │
└─────────────────────────────────────────────────────────┘
                        ↓
┌─────────────────────────────────────────────────────────┐
│  Web Server (Apache)                                    │
│  - restrict .php files visibility                      │
│  - Rate limiting (en producción)                       │
│  - .htaccess rules                                     │
└─────────────────────────────────────────────────────────┘
                        ↓
┌─────────────────────────────────────────────────────────┐
│  Application Layer (PHP)                                │
│  - Input validation & sanitization                     │
│  - Authentication & authorization                      │
│  - Session management                                  │
│  - Error handling (no info disclosure)                 │
└─────────────────────────────────────────────────────────┘
                        ↓
┌─────────────────────────────────────────────────────────┐
│  Database Layer                                         │
│  - Prepared statements (prevent SQL injection)         │
│  - Least privilege (usuarios limitados)                │
│  - Encryption at rest (en producción)                  │
└─────────────────────────────────────────────────────────┘
```

---

## 🛡️ Vulnerabilidades OWASP Top 10 y Mitigaciones

### 1. A01:2021 - Broken Access Control

**Riesgo:** Usuario A accede a datos de Usuario B

**Mitigación Implementada:**
```php
// En UserController::patientDashboard()
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: login_register.php");
    exit();
}

// Solo obtener citas del usuario logeado
$appointments = $appointmentModel->getPatientAppointments($_SESSION['usuario_id']);

// Nunca confiar en $_GET['usuario_id']
// ✗ INCORRECTO:
// $user_id = $_GET['usuario_id'];  // VULNERABILITY!

// ✓ CORRECTO:
// $user_id = $_SESSION['usuario_id'];  // De sesión segura
```

**Validaciones Adicionales:**
```php
// View action requiere que el usuario sea el propietario
public function viewAppointment($cita_id) {
    $cita = $this->appointmentModel->getById($cita_id);
    
    if ($cita['usuario_id'] !== $_SESSION['usuario_id']) {
        die("Acceso denegado");  // 403 Forbidden
    }
    
    // Proceder
}
```

**Checklist para Revisar:**
- [ ] ¿Todas las acciones requieren autenticación?
- [ ] ¿Se valida ownership antes de devolver datos?
- [ ] ¿No hay IDs secuenciales en URLs que puedan ser adivinados?
- [ ] ¿El admin no puede acceder a paciente aleatorio?

---

### 2. A02:2021 - Cryptographic Failures

**Riesgo:** Contraseñas en texto plano, comunicación sin encripción

**Mitigación Implementada:**

#### 2.1 Almacenamiento de Contraseñas
```php
// models/User.php - En create()
$this->contrasena = password_hash(
    htmlspecialchars(strip_tags($this->contrasena)),
    PASSWORD_BCRYPT,  // Algoritmo
    ['cost' => 10]    // Factor de trabajo (por defecto)
);

// Insertar $this->contrasena (hash) en BD
```

**BCrypt Properties:**
- Algoritmo one-way (no reversible)
- Auto-genera salt aleatorio
- Adaptivo: Se ralentiza con Moore's Law
- Ejemplo: 'password123' → '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36P4/KFm'

**Verificación en Login:**
```php
$stored_hash = "..."; // De base de datos
$user_input = $_POST['contrasena'];

if (password_verify($user_input, $stored_hash)) {
    // Contraseña correcta
    $_SESSION['usuario'] = $username;
} else {
    // Contraseña incorrecta
    $_SESSION['error'] = "Email o contraseña inválidos";
}
```

#### 2.2 Comunicación (HTTPS)

**En Producción (OBLIGATORIO):**
```apache
# .htaccess
# Forzar HTTPS
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

# HSTS Header (Google Chrome respeta)
Header set Strict-Transport-Security "max-age=31536000; includeSubDomains"
```

**Configuración Apache:**
```apache
# httpd.conf
<VirtualHost *:443>
    ServerName clinica.com
    SSLEngine on
    SSLCertificateFile /etc/ssl/certs/clinica.crt
    SSLCertificateKeyFile /etc/ssl/private/clinica.key
    SSLCertificateChainFile /etc/ssl/certs/clinica.ca-bundle
</VirtualHost>
```

---

### 3. A03:2021 - Injection (SQL, Command, LDAP)

**Riesgo:** `SELECT * FROM users WHERE username = '" . $_GET['name'] . "'`

**Mitigación Implementada:**

#### 3.1 SQL Injection Prevention
```php
// ✗ VULNERABLE (Nunca usar)
$name = $_GET['name'];
$query = "SELECT * FROM login_usuario WHERE usuario = '$name'";
$stmt = $conn->prepare($query);  // INCORRECTO: Aún vulnerable
$stmt->execute();

// ✓ SEGURO (Prepared statement)
$query = "SELECT * FROM login_usuario WHERE usuario = :usuario";
$stmt = $conn->prepare($query);  // Separación: estructura vs datos
$stmt->bindParam(':usuario', $name, PDO::PARAM_STR);
$stmt->execute();
$result = $stmt->fetch();
```

**Ataque Ejemplo:**
```
Entrada: admin' --
Query vulnerable: 
  SELECT * FROM login_usuario WHERE usuario = 'admin' --'
  (El -- comenta el resto, saltando validación de contraseña)

Query prepared statement:
  SELECT * FROM login_usuario WHERE usuario = ?
  User input "admin' --" se trata como STRING LITERAL
```

#### 3.2 Sanitización de Entrada
```php
// En todos los modelos ANTES de usar en query
$user_input = $_POST['nombre'];

// htmlspecialchars: Convierte <script> en &lt;script&gt;
$safe = htmlspecialchars($user_input);

// strip_tags: Remueve <tag> completamente
$safer = strip_tags(htmlspecialchars($user_input));

// PERO: Prepared statements son la defensa PRINCIPAL
```

---

### 4. A04:2021 - Insecure Design

**Riesgo:** Lógica de negocio flawed (ej: cualquiera puede cambiar estado de cita)

**Protecciones:**
```php
// En AppointmentController::cancelAppointment()
public function cancelAppointment($cita_id) {
    // Validar ownership
    if ($cita['usuario_id'] !== $_SESSION['usuario_id']) {
        return UNAUTHORIZED;
    }
    
    // Validar estado (solo Pendiente puede cancelarse)
    if ($cita['estado'] !== 'Pendiente') {
        $_SESSION['error'] = "Solo citas Pendientes pueden cancelarse";
        return false;
    }
    
    // Estado no viene de user input
    $new_estado = 'Cancelada';  // Hardcoded
    
    // Update
    return $appointmentModel->updateStatus($cita_id, $new_estado);
}

// ✗ INSEGURO:
// $new_estado = $_POST['estado'];  // User puede enviar 'Confirmada'
```

---

### 5. A05:2021 - Broken Authentication

**Riesgo:** Sesiones débiles, contraseñas débiles, sin MFA

**Mitigaciones:**

#### 5.1 Gestión de Sesiones
```php
// Configuración recomendada en php.ini o .htaccess
session_start([
    'cookie_lifetime' => 1800,        // 30 minutos
    'cookie_secure' => true,          // HTTP Only
    'cookie_httponly' => true,        // No accessible desde JS
    'cookie_samesite' => 'Strict',    // CSRF protection
    'use_strict_mode' => true,        // Regenerar ID después de login
]);

// IMPORTANTE: session_regenerate_id() después de autenticación
public function login() {
    // ... validar credenciales ...
    
    session_regenerate_id(true);  // Invalida sesión anterior
    $_SESSION['usuario_id'] = $user['usuario_id'];
    $_SESSION['usuario'] = $user['usuario'];
}
```

#### 5.2 Logout Seguro
```php
// views/users/logout.php
session_start();
$_SESSION = [];  // Vaciar datos
session_destroy();  // Destruir sesión
setcookie(session_name(), '', time()-3600, '/');  // Limpiar cookie
header("Location: login.php");
exit();
```

#### 5.3 Detección de Abuso de Sesión
```php
// Validaciones adicionales (Fase 2)
if (isset($_SESSION['last_activity']) && 
    (time() - $_SESSION['last_activity'] > 1800)) {
    // Session timeout (30 min)
    session_destroy();
    header("Location: login.php");
}

$_SESSION['last_activity'] = time();

// Validar User Agent (no es perfect pero dificulta hijacking)
if (!isset($_SESSION['user_agent'])) {
    $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
}
if ($_SESSION['user_agent'] !== $_SERVER['HTTP_USER_AGENT']) {
    session_destroy();
    die("Session tampering detected");
}
```

---

### 6. A06:2021 - Sensitive Data Exposure

**Riesgo:** Datos PII (Personally Identifiable Information) visibles en logs, errores, backups

**Mitigaciones:**

#### 6.1 Error Handling Seguro
```php
// ✗ INCORRECTO: Expone estructura de BD
try {
    $stmt->execute();
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();  // "SQLSTATE[HY000]: General error: 1030 Got error 28 from..."
}

// ✓ CORRECTO: Mensaje genérico + log privado
try {
    $stmt->execute();
} catch (PDOException $e) {
    error_log("DB Error: " . $e->getMessage());  // Archivo logs
    $_SESSION['error'] = "Error en el sistema. Contacte a soporte.";
}
```

#### 6.2 Logging Seguro
```php
// config/logging.php (crear)
function logSecurely($action, $user_id, $data = []) {
    $log_entry = [
        'timestamp' => date('Y-m-d H:i:s'),
        'action' => $action,
        'user_id' => $user_id,  // Pseudonimizar: hash user_id?
        'data' => [
            'patient_id' => $data['patient_id'] ?? null,
            // NO incluir: contraseña, DNI, credit card
        ],
        'ip' => $_SERVER['REMOTE_ADDR'],
    ];
    
    file_put_contents('logs/actions.log', json_encode($log_entry) . "\n", FILE_APPEND);
}

// En UserController::requestAppointment()
logSecurely('APPOINTMENT_CREATED', $_SESSION['usuario_id'], 
    ['patient_id' => $appointmentModel->cita_id]);
```

#### 6.3 Proteger Archivos Sensibles
```apache
# .htaccess
# Prevenir acceso directo a archivos config
<FilesMatch "\.php$">
    Order Deny,Allow
    Deny from all
    Allow from 127.0.0.1
</FilesMatch>

# Permitir solo archivos públicos
<Directory "/var/www/html/clinica1/public">
    Order Allow,Deny
    Allow from all
</Directory>
```

---

### 7. A07:2021 - Identification and Authentication Failures

**Mitigaciones:**

#### 7.1 Validación de Email
```php
// models/User.php
public function create() {
    // Validar formato email
    if (!filter_var($this->correo_electronico, FILTER_VALIDATE_EMAIL)) {
        return false;
    }
    
    // Verificar email no exista (prevenir duplicados)
    $query = "SELECT usuario_id FROM " . $this->table . 
             " WHERE correo_electronico = :email";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':email', $this->correo_electronico);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        $_SESSION['error'] = "Email ya registrado";
        return false;
    }
    
    // Continuar con INSERT
}
```

#### 7.2 Validación de Contraseña (Futuro)
```php
// Recomendación NIST 800-63B
function validatePassword($password) {
    $errors = [];
    
    // Mínimo 12 caracteres
    if (strlen($password) < 12) {
        $errors[] = "Mínimo 12 caracteres";
    }
    
    // NO usar diccionario de palabras comunes
    $common = ['password', '123456', 'qwerty', 'admin', ...];
    if (in_array(strtolower($password), $common)) {
        $errors[] = "Contraseña muy común";
    }
    
    // NO requerirquiere caracteres especiales (mayúsculas/números)
    // Los usuarios usan débiles (P@ss123)
    
    return $errors;
}
```

---

### 8. A08:2021 - Software and Data Integrity Failures

**Riesgo:** Dependencies vulnerables, actualizaciones sin revisar

**Mitigaciones:**

#### 8.1 Auditar Dependencias
```bash
# Si usa Composer (PHP dependency manager)
composer show --outdated

# Verificar vulnerabilidades
composer audit
```

#### 8.2 Versiones de Software
```
# Mantener actualizado
- PHP 7.4+ (EOL Noviembre 2022, usar 8.0+)
- MySQL 5.7 mínimo (EOL Octubre 2023)
- Apache 2.4+ (LTS)
```

#### 8.3 Revision Control
```bash
# Revisar cambios antes de commit
git diff

# No committear credenciales
# Usar .env para config sensible
```

---

### 9. A09:2021 - Logging and Monitoring Failures

**Riesgo:** Nadie se da cuenta de breach en progreso

**Implementación:**
```php
// config/security_log.php
function securityAlert($level, $message, $context = []) {
    $alert = [
        'timestamp' => date(DATE_ISO8601),
        'level' => $level,  // INFO, WARNING, CRITICAL
        'message' => $message,
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'CLI',
        'user_id' => $_SESSION['usuario_id'] ?? 'GUEST',
        'context' => $context,
    ];
    
    // Log a archivo
    error_log(json_encode($alert), 3, 'logs/security.log');
    
    // Si es CRITICAL, enviar email
    if ($level === 'CRITICAL') {
        mail('admin@clinica.com', 'Security Alert!', json_encode($alert));
    }
}

// Ejemplos de uso
securityAlert('WARNING', 'Failed login attempt', 
    ['email' => $email, 'attempts' => 3]);

securityAlert('CRITICAL', 'SQL Injection attempt detected', 
    ['query' => $_GET['search']]);
```

---

### 10. A10:2021 - Server-Side Request Forgery (SSRF)

**Riesgo:** Atacante hace que servidor haga requests a internos (ej: `file://`)

**Mitigation:**
```php
// Si implementas funcionalidad de upload de archivos:
// ✗ VULNERABLE:
$file = fopen($_FILES['report']['tmp_name'], 'r');
// Atacante envía: file:///etc/passwd

// ✓ SEGURO:
// - Validar MIME type
// - Almacenar en carpeta fuera de web root
// - Usar storage service (AWS S3, etc.)
```

---

## 🔍 Checklist de Seguridad Pre-Deployment

### Antes de Producción:
- [ ] **HTTPS/TLS** configurado y válido
- [ ] **php.ini** hardened:
  ```ini
  display_errors = off
  error_reporting = E_ALL
  log_errors = on
  error_log = /var/log/php_errors.log
  ```
- [ ] **Database credentials** en variables de entorno (.env)
- [ ] **Contraseña root MySQL** cambiada de default
- [ ] **Archivos config** no accesibles desde web
- [ ] **Backups automáticos** configurados
- [ ] **WAF (Web Application Firewall)** como ModSecurity
- [ ] **Rate limiting** en login
- [ ] **CORS headers** configurados correctamente
- [ ] **Password hints** removidos (ej: "Min 8 caracteres")

### Testing:
- [ ] **OWASP ZAP scan** (herramienta libre)
- [ ] **SQL Injection test** (sqlmap tool)
- [ ] **XSS test** (enviar `<script>alert('test')</script>`)
- [ ] **CSRF test** (form tampering)
- [ ] **Penetration testing** con profesional

### Operacional:
- [ ] **Log monitoring** activo
- [ ] **Incident response plan** documentado
- [ ] **Seguro cyber** contratado
- [ ] **Política de privacidad** actualizada (GDPR, CCPA)
- [ ] **Términos de servicio** con disclaimers

---

## 📋 Incidentes de Seguridad: Procedimiento

Si ocurre un breach o incidente:

1. **Contener (0-1 hora)**
   ```bash
   # Aislar servidor afectado
   # Desconectar de internet si es crítico
   # Preservar logs
   ```

2. **Investigar (1-4 horas)**
   ```bash
   # ¿Cuándo comenzó?
   # ¿Quién fue afectado?
   # ¿Qué datos se expusieron?
   grep -r "injection attempt" /var/log/apache2/*
   ```

3. **Notificar (4-24 horas)**
   - Usuarios afectados (requerido por ley)
   - Autoridades (si datos personales)
   - Aseguradora

4. **Corregir (24-48 horas)**
   - Patch de seguridad
   - Cambio de credenciales
   - Deploy en producción

5. **Aprender (Posterior)**
   - Post-mortem análisis
   - Actualizar arquitectura
   - Entrenar equipo

---

## 🔐 Secrets Management

### Variables Sensibles (NO en código)

**Crear archivo `.env` en raíz del proyecto:**
```env
# .env (NO committerear a Git)
DB_HOST=localhost
DB_NAME=clinica
DB_USER=clinica_app
DB_PASS=XyZ9@9#kL2mNoPq$
ADMIN_EMAIL=admin@clinica.com
SMTP_PASS=Tu_Gmail_App_Password
API_KEY=sk-proj-xxxxxxxxxxxx
```

**Cargar en config/conexion.php:**
```php
<?php
// Cargar .env
if (file_exists(__DIR__ . '/../.env')) {
    $env = parse_ini_file(__DIR__ . '/../.env');
    define('DB_HOST', $env['DB_HOST']);
    define('DB_NAME', $env['DB_NAME']);
    define('DB_USER', $env['DB_USER']);
    define('DB_PASS', $env['DB_PASS']);
}

class Conexion {
    private $host = DB_HOST;
    private $db = DB_NAME;
    private $user = DB_USER;
    private $password = DB_PASS;
    // ...
}
```

**En .gitignore:**
```
.env
.env.local
config/conexion.php (si contiene credenciales)
logs/
```

---

## 🚨 Test de Seguridad Manual

### Test 1: SQL Injection
```
Ir a: http://localhost/clinica1/views/doctor/login.php
Email: admin@gmail.com' OR '1'='1
Contraseña: anything
Resultado esperado: Rechazado (mensaje "Email o contraseña inválidos")
```

### Test 2: XSS
```
Crear cita con:
Nombre: <script>alert('XSS')</script>

Resultado esperado: 
- Si se muestra alerta = VULNERABLE
- Si se muestra como texto = SEGURO
```

### Test 3: CSRF (Simulado)
```
1. Logearse como paciente
2. Abrir DevTools (F12) → Console
3. Ejecutar:
   fetch('clinica1/controllers/AppointmentController.php?action=cancel&cita_id=1', {
     method: 'GET'
   })

Resultado esperado: Rechazado (requiere POST + token)
```

---

**Versión:** 2.0  
**Última actualización:** Febrero 2026  
**Responsable de Seguridad:** [Tu nombre]  
**Contacto:** security@clinica.com

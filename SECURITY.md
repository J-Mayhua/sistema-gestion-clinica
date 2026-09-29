# Política de seguridad — HappyDent

Este documento describe cómo reportar vulnerabilidades, qué medidas de seguridad tiene HappyDent y qué falta configurar antes de publicarlo en producción.

## Contenido

- [Reportar una vulnerabilidad](#reportar-una-vulnerabilidad)
- [Estado de la seguridad](#estado-de-la-seguridad)
- [Controles implementados](#controles-implementados)
- [Recomendaciones para producción](#recomendaciones-para-producción)
- [Gestión de secretos](#gestión-de-secretos)
- [Pruebas de seguridad manuales](#pruebas-de-seguridad-manuales)
- [Respuesta a incidentes](#respuesta-a-incidentes)

## Reportar una vulnerabilidad

Si encuentras una vulnerabilidad, **no la publiques en un issue abierto**. Envía un correo a **[correo-de-seguridad]** con:

- Descripción del problema y su impacto.
- Pasos para reproducirlo (ruta afectada, datos de entrada, resultado obtenido).
- Versión o commit donde lo detectaste.

Responderemos para confirmar la recepción y coordinar la corrección antes de cualquier divulgación pública.

## Estado de la seguridad

HappyDent aplica seguridad en capas. La tabla distingue lo que **ya está implementado** de lo que **se recomienda** añadir para producción.

| Capa | Control | Estado |
|------|---------|--------|
| Aplicación | Comprobación de sesión y de propiedad del recurso en cada ruta protegida | Implementado |
| Aplicación | Tokens CSRF en formularios POST | Implementado |
| Aplicación | Validación de tipos, fechas y longitudes en el servidor | Implementado |
| Aplicación | Escape de salida con `htmlspecialchars()` | Implementado |
| Aplicación | Reservas y cancelaciones con transacciones y `FOR UPDATE` | Implementado |
| Base de datos | Consultas preparadas con PDO | Implementado |
| Base de datos | Contraseñas con hash bcrypt | Implementado |
| Servidor | Cabeceras `X-Frame-Options` y `X-Content-Type-Options` | Implementado |
| Transporte | HTTPS con TLS 1.2 o superior y cabecera HSTS | Recomendado |
| Sesión | Cookies `HttpOnly`, `Secure`, `SameSite`; regeneración del ID al iniciar sesión; caducidad por inactividad | Recomendado |
| Servidor | `Content-Security-Policy` | Recomendado |
| Aplicación | Límite de intentos de inicio de sesión | Recomendado |
| Aplicación | Registro de eventos de seguridad | Recomendado |
| Base de datos | Usuario de MySQL con permisos mínimos | Recomendado |

> Los elementos **Recomendados** no están confirmados como implementados. Verifícalos en el código y en la configuración del servidor antes de publicar.

## Controles implementados

Esta sección sigue la referencia **OWASP Top 10 (edición 2021)**. Los controles de acceso, CSRF y concurrencia se revisaron ruta por ruta; esa revisión fue interna y no sustituye una auditoría independiente.

### A01 — Control de acceso

**Riesgo:** un usuario accede a datos de otro (IDOR) o ejecuta acciones que no le corresponden.

**Medidas:**

- Toda ruta protegida comprueba la sesión antes de procesar la solicitud.
- El ID del usuario sale siempre de la sesión, nunca de `$_GET` ni `$_POST`.
- Toda ruta que recibe un ID de cita comprueba que pertenezca al usuario o doctor de la sesión.

```php
$appointment = $userController->getAppointmentByIddd((int) $_GET['id']);

if (!$appointment || (int) $appointment['usuario_id'] !== (int) $_SESSION['usuario_id']) {
    header("Location: patient_appointment_history.php");
    exit();
}
```

Compara siempre con conversión a entero: `"5" !== 5` es `true` en PHP, y una comparación estricta entre cadena y entero deniega el acceso a usuarios legítimos.

| Rol | Rutas | Comprobación |
|-----|-------|--------------|
| Paciente | `view_appointment.php`, `cancel_appointment.php`, `patient_appointment_history.php` | `usuario_id` de la cita igual al de la sesión |
| Doctor | `edit_appointment.php`, `delete_appointment.php`, `doctor_appointments.php` | `doctor_id` de la cita igual al de la sesión |

### A02 — Fallos criptográficos

**Contraseñas:** se almacenan con bcrypt. La contraseña se hashea tal como la escribe el usuario, sin `strip_tags()` ni `htmlspecialchars()`, que alterarían el valor.

```php
// Registro
$hash = password_hash($contrasena, PASSWORD_BCRYPT);

// Inicio de sesión
if (password_verify($contrasena, $hashGuardado)) {
    // credenciales correctas
}
```

**Mensajes de error:** el inicio de sesión devuelve siempre el mismo mensaje ("Correo o contraseña inválidos"), sin indicar cuál de los dos falló.

**Transporte:** ver [Recomendaciones para producción](#recomendaciones-para-producción).

### A03 — Inyección

**SQL:** todas las consultas usan sentencias preparadas de PDO. Los datos del usuario nunca se concatenan en la consulta.

```php
// Correcto
$stmt = $conn->prepare("SELECT * FROM login_usuario WHERE usuario = :usuario");
$stmt->bindParam(':usuario', $usuario, PDO::PARAM_STR);
$stmt->execute();

// Incorrecto: preparar una consulta que ya contiene datos no la protege
$stmt = $conn->prepare("SELECT * FROM login_usuario WHERE usuario = '$usuario'");
```

**XSS:** los datos se escapan con `htmlspecialchars()` al mostrarlos en HTML. Además, se aplica `strip_tags()` al guardarlos. El escape de salida es la defensa principal.

**Validación de entrada:** tipos con `filter_var(..., FILTER_VALIDATE_INT)`, correos con `FILTER_VALIDATE_EMAIL`, fechas con `DateTime::createFromFormat()` y longitudes máximas. Se valida siempre en el servidor, aunque el navegador también valide.

### A04 — Diseño inseguro

**Riesgo:** fallos en la lógica de negocio, como cambiar el estado de una cita sin autorización.

**Medidas:**

- El estado nuevo lo decide el servidor, nunca el formulario.
- Un paciente solo puede cancelar citas en estado `Pendiente`.
- Las transiciones de estado del doctor se validan dentro de una transacción (`updateForDoctor()`).
- Cada operación crítica exige un token CSRF válido.

```php
// Incorrecto: el cliente decide el estado
$nuevoEstado = $_POST['estado'];

// Correcto: el servidor lo fija
$nuevoEstado = 'Cancelada';
```

**Reservas sin doble asignación:** `createFromAvailableSlot()` bloquea el horario con `SELECT ... FOR UPDATE` dentro de una transacción. `cancelForPatient()` y `cancelForDoctor()` usan el mismo mecanismo.

```php
$this->conn->beginTransaction();

// 1. Bloquear el horario hasta el commit
$slotQuery = "SELECT disponibilidad_id FROM tabla_disponibilidad
              WHERE disponibilidad_id = :disponibilidad_id
                AND doctor_id = :doctor_id
                AND estado = 'libre'
              FOR UPDATE";

// 2. Insertar la cita
// 3. Marcar el horario como 'ocupado'
// 4. commit(), o rollBack() si algo falla

$this->conn->commit();
```

### A05 — Configuración de seguridad incorrecta

**Implementado:** cabeceras HTTP en `.htaccess` (requiere el módulo `mod_headers` de Apache).

```apache
Header always set X-Frame-Options "SAMEORIGIN"
Header always set X-Content-Type-Options "nosniff"
```

**Recomendado en producción:**

```ini
; php.ini
display_errors = Off
log_errors = On
error_reporting = E_ALL
error_log = /var/log/php_errors.log
```

Mantén los archivos de configuración y los registros fuera de la carpeta pública del servidor.

### A06 — Componentes vulnerables y desactualizados

- Usa versiones de PHP, MySQL/MariaDB y Apache que sigan recibiendo parches de seguridad. PHP 7.4 y MySQL 5.7 ya no los reciben, por lo que no son adecuados para producción aunque el proyecto funcione con ellos.
- Si añades dependencias con Composer, revisa las vulnerabilidades conocidas con `composer audit`.
- Font Awesome se carga desde una versión fija (5.15.4). Revisa periódicamente las actualizaciones.

### A07 — Fallos de identificación y autenticación

**Implementado:** validación de formato de correo y comprobación de duplicados al registrarse; hash bcrypt; mensaje de error genérico.

**Recomendado:**

```php
// Configurar la cookie de sesión antes de session_start()
session_set_cookie_params([
    'lifetime' => 0,
    'path'     => '/',
    'secure'   => true,     // solo con HTTPS
    'httponly' => true,     // no accesible desde JavaScript
    'samesite' => 'Lax',
]);
ini_set('session.use_strict_mode', '1');
session_start();
```

```php
// Al iniciar sesión, invalidar el ID anterior
session_regenerate_id(true);
$_SESSION['usuario_id'] = $usuario['usuario_id'];
$_SESSION['usuario']    = $usuario['usuario'];
```

```php
// Caducidad por inactividad (30 minutos)
if (isset($_SESSION['last_activity']) && time() - $_SESSION['last_activity'] > 1800) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
}
$_SESSION['last_activity'] = time();
```

```php
// Cierre de sesión completo
session_start();
$_SESSION = [];
session_destroy();
setcookie(session_name(), '', time() - 3600, '/');
header("Location: login.php");
exit();
```

**Contraseñas:** prioriza la longitud sobre la complejidad (por ejemplo, mínimo 12 caracteres) y rechaza contraseñas comunes como `123456` o `qwerty`. Limita los intentos de inicio de sesión fallidos.

### A08 — Fallos de integridad de software y datos

- Revisa los cambios con `git diff` antes de cada commit.
- No incluyas credenciales en el repositorio (ver [Gestión de secretos](#gestión-de-secretos)).
- Descarga las librerías de terceros solo desde fuentes oficiales.

### A09 — Fallos de registro y monitoreo

**Recomendado:** registrar los eventos relevantes (inicios de sesión fallidos, accesos denegados, errores de base de datos) sin incluir datos sensibles.

```php
function registrarEventoSeguridad(string $nivel, string $mensaje, array $contexto = []): void
{
    $evento = [
        'fecha'      => date(DATE_ATOM),
        'nivel'      => $nivel,                              // INFO, WARNING, CRITICAL
        'mensaje'    => $mensaje,
        'ip'         => $_SERVER['REMOTE_ADDR'] ?? 'CLI',
        'usuario_id' => $_SESSION['usuario_id'] ?? 'invitado',
        'contexto'   => $contexto,
    ];

    error_log(json_encode($evento) . "\n", 3, '/var/log/happydent/security.log');
}

registrarEventoSeguridad('WARNING', 'Intento de inicio de sesión fallido', ['intentos' => 3]);
```

Reglas para los registros:

- Guárdalos **fuera de la carpeta pública** del servidor.
- No registres contraseñas, DNI ni datos clínicos.
- Muestra al usuario un mensaje genérico y deja el detalle técnico solo en el registro:

```php
try {
    $stmt->execute();
} catch (PDOException $e) {
    error_log("Error de base de datos: " . $e->getMessage());
    $_SESSION['error'] = "Error en el sistema. Inténtalo de nuevo más tarde.";
}
```

### A10 — Server-Side Request Forgery (SSRF)

HappyDent no realiza peticiones HTTP a URLs proporcionadas por el usuario, por lo que este riesgo no aplica actualmente. Si en el futuro añades subida de archivos o integraciones externas: valida el tipo MIME real, guarda los archivos fuera de la carpeta pública y no uses nunca una URL del usuario como destino de una petición del servidor.

## Recomendaciones para producción

### HTTPS y cabeceras

```apache
# .htaccess — forzar HTTPS
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

# HSTS (solo cuando HTTPS funcione correctamente en todo el sitio)
Header always set Strict-Transport-Security "max-age=31536000; includeSubDomains"
```

```apache
# Configuración del servidor
<VirtualHost *:443>
    ServerName tu-dominio.com
    SSLEngine on
    SSLCertificateFile    /ruta/al/certificado.crt
    SSLCertificateKeyFile /ruta/a/la/clave.key
</VirtualHost>
```

### Lista de verificación previa al despliegue

**Configuración**

- [ ] HTTPS activo con certificado válido y HSTS.
- [ ] `display_errors = Off` y registro de errores activo.
- [ ] Credenciales fuera del repositorio.
- [ ] Contraseña de `root` de MySQL cambiada; la aplicación usa un usuario limitado.
- [ ] Archivos de configuración y registros fuera de la carpeta pública.
- [ ] Cookies de sesión con `HttpOnly`, `Secure` y `SameSite`.
- [ ] `Content-Security-Policy` configurada.
- [ ] Límite de intentos de inicio de sesión.

**Operación**

- [ ] Copias de seguridad automáticas, con restauración probada.
- [ ] Monitoreo de los registros de seguridad.
- [ ] Procedimiento de respuesta a incidentes documentado.
- [ ] Política de privacidad conforme a la normativa de protección de datos aplicable.

**Verificación**

- [ ] Pruebas manuales de la sección siguiente superadas.
- [ ] Análisis con una herramienta como OWASP ZAP.
- [ ] Revisión por un profesional de seguridad antes de manejar datos reales de pacientes.

## Gestión de secretos

Las credenciales nunca deben estar en el código ni en Git. HappyDent las lee en este orden de prioridad:

1. Variables de entorno.
2. Constantes de `config/config.local.php`.
3. Valores predeterminados de desarrollo.

```php
<?php
// config/config.local.php
define('DB_HOST',     '127.0.0.1');
define('DB_NAME',     'clinica');
define('DB_USER',     'clinica_app');
define('DB_PASSWORD', '<contraseña-segura>');
```

Añade a `.gitignore`:

```text
config/config.local.php
logs/
.env
```

Crea un usuario de base de datos con permisos mínimos para la aplicación:

```sql
CREATE USER 'clinica_app'@'localhost' IDENTIFIED BY '<contraseña-segura>';
GRANT SELECT, INSERT, UPDATE, DELETE ON clinica.* TO 'clinica_app'@'localhost';
```

Si una credencial se sube por error a Git, cámbiala de inmediato: borrar el archivo del historial no basta.

## Pruebas de seguridad manuales

Ejecútalas en un entorno de prueba, nunca en producción.

| Prueba | Cómo realizarla | Resultado esperado |
|--------|-----------------|--------------------|
| **Inyección SQL** | En `views/doctor/login.php`, usa como correo `admin@example.com' OR '1'='1` y cualquier contraseña. | Rechazo con el mensaje genérico de credenciales inválidas. |
| **XSS** | Registra o crea una cita con el nombre `<script>alert('XSS')</script>`. | El texto se muestra literalmente; no aparece ninguna alerta. |
| **CSRF** | Envía el formulario de cancelar o editar una cita eliminando el campo `csrf_token` (o alterando su valor) desde las herramientas de desarrollo. | La operación se rechaza y la cita no cambia. |
| **IDOR (paciente)** | Inicia sesión como paciente A y abre `view_appointment.php?id=` con el ID de una cita del paciente B. | Redirección al historial; no se muestran datos. |
| **IDOR (doctor)** | Inicia sesión como doctor A e intenta editar o cancelar una cita del doctor B. | La operación se rechaza. |
| **Concurrencia** | Desde dos sesiones, reserva el mismo horario casi a la vez. | Solo una reserva se confirma. |
| **Acceso sin sesión** | Abre directamente una vista protegida sin haber iniciado sesión. | Redirección al login. |

## Respuesta a incidentes

Si sospechas de una brecha:

| Fase | Acciones |
|------|----------|
| **1. Contener** | Aísla el servidor afectado, bloquea el acceso comprometido y conserva los registros. |
| **2. Investigar** | Determina cuándo empezó, qué cuentas y qué datos se vieron afectados y por qué vía se entró. |
| **3. Notificar** | Avisa a las personas afectadas, a la aseguradora si la hay, y a las autoridades cuando la normativa aplicable lo exija (los plazos varían según el país). |
| **4. Corregir** | Aplica el parche, cambia credenciales y claves, y cierra las sesiones activas. |
| **5. Aprender** | Documenta lo ocurrido, ajusta los controles y actualiza esta política. |

---

**Versión:** 2.0 · **Última actualización:** febrero 2026
**Responsable de seguridad:** [Jose Adolfo Mayhua] · **Contacto:** [joseadolmayhua01@gmail.com]

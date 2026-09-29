# HappyDent

Aplicación web para la gestión de citas de una clínica dental. Permite a los pacientes solicitar citas y consultar su estado, y a los doctores administrar sus citas, su disponibilidad horaria y su lista de pacientes.

Desarrollada en **PHP** con **MySQL**, siguiendo el patrón **MVC** (Modelo-Vista-Controlador).

## Contenido

- [Funcionalidades](#funcionalidades)
- [Tecnologías](#tecnologías)
- [Arquitectura](#arquitectura)
- [Base de datos](#base-de-datos)
- [Instalación](#instalación)
- [Configuración](#configuración)
- [Guía de uso](#guía-de-uso)
- [Seguridad](#seguridad)
- [Rendimiento](#rendimiento)
- [Limitaciones conocidas](#limitaciones-conocidas)
- [Mejoras futuras](#mejoras-futuras)
- [Preguntas frecuentes](#preguntas-frecuentes)
- [Contribuciones](#contribuciones)
- [Autor y licencia](#autor-y-licencia)

## Funcionalidades

**Pacientes**

- Registro e inicio de sesión.
- Solicitud de citas eligiendo especialidad y doctor.
- Historial de citas con su estado: `Pendiente`, `Confirmada`, `Cancelada` o `Completada`.
- Cancelación de citas en estado `Pendiente`.
- Consulta de la disponibilidad de los doctores en un calendario interactivo.

**Doctores**

- Inicio de sesión.
- Lista de citas asignadas, con datos del paciente (nombre, DNI, teléfono) y especialidad.
- Gestión de bloques de disponibilidad (fecha, hora de inicio y hora de fin) con estado `libre`, `ocupado` o `cita`.
- Lista de pacientes asociados a sus citas, con el número de citas de cada uno.
- Registro directo de pacientes.

## Tecnologías

| Capa | Tecnología |
|------|------------|
| Backend | PHP 7.4+, PDO, sesiones de PHP |
| Base de datos | MySQL 5.7+ / MariaDB 10.2+ (InnoDB, `utf8mb4`) |
| Frontend | HTML5, CSS3 (Flexbox y Grid), JavaScript sin frameworks, Font Awesome 5.15.4 |
| Entorno de desarrollo | XAMPP (Apache, MySQL, PHP), Git |

## Arquitectura

Cada capa tiene una responsabilidad concreta:

| Capa | Responsabilidad |
|------|-----------------|
| **Vistas** | Generan las páginas HTML que ve el usuario. No contienen lógica de negocio. |
| **Controladores** | Reciben las solicitudes, validan los datos de entrada y coordinan modelos y vistas. |
| **Modelos** | Se conectan a MySQL mediante PDO y ejecutan las consultas. |
| **Configuración** | Define los datos de conexión a la base de datos. |

### Estructura del proyecto

```text
clinica1/
├── controllers/            # Lógica de aplicación
│   ├── UserController.php
│   ├── DoctorController.php
│   ├── AppointmentController.php
│   └── DisponibilidadController.php
├── models/                 # Acceso a datos
│   ├── User.php
│   ├── Doctor.php
│   ├── Appointment.php
│   └── Disponibilidad.php
├── views/
│   ├── users/              # Vistas del paciente
│   ├── doctor/             # Vistas del doctor
│   ├── appointments/       # Gestión de citas
│   └── cabecera/           # Componentes comunes (cabeceras y pies)
├── config/
│   ├── conexion.php        # Conexión PDO
│   └── config.local.php    # Credenciales locales (no subir a Git)
├── assets/
│   ├── css/
│   ├── js/
│   └── images/
├── public/                 # Punto de entrada (index.php)
├── autoload.php            # Carga automática de clases
└── BASE DE DATOS/
    └── base_de_datos.sql   # Esquema inicial
```

### Ejemplo de flujo: lista de pacientes del doctor

1. La vista `views/doctor/doctor_patient_list.php` inicia la sesión de PHP.
2. Comprueba que exista `$_SESSION['doctor_id']`; si no existe, redirige a `login.php`.
3. Crea una instancia de `DoctorController`, que llama a `getPatientsByDoctorId()`.
4. El modelo `Doctor` consulta `registrar_citas` y `login_usuario`, agrupa por paciente y ordena por nombre.
5. La vista muestra nombre, correo y número de citas. Si no hay resultados, muestra un mensaje indicándolo.

## Base de datos

Nombre de la base de datos: `clinica`.

| Tabla | Contenido |
|-------|-----------|
| `login_usuario` | Pacientes: datos de acceso y nombre. |
| `doctor` | Doctores: datos personales, especialidad, horario y acceso. |
| `registrar_citas` | Citas: datos del paciente, doctor asignado, estado y fecha de creación. |
| `tabla_disponibilidad` | Bloques horarios por doctor y su estado. |

### Relaciones

```text
login_usuario (1) ──< registrar_citas >── (1) doctor
                                              │
                                              └──< tabla_disponibilidad
```

- Una cita pertenece a un paciente (`usuario_id`) y a un doctor (`doctor_id`).
- Un doctor puede tener muchos bloques de disponibilidad.

### Esquema

```sql
CREATE TABLE login_usuario (
  usuario_id         INT PRIMARY KEY AUTO_INCREMENT,
  nombre_completo    VARCHAR(255) NOT NULL,
  correo_electronico VARCHAR(255) UNIQUE NOT NULL,
  usuario            VARCHAR(50)  UNIQUE NOT NULL,
  contrasena         VARCHAR(255) NOT NULL          -- hash bcrypt
);

CREATE TABLE doctor (
  doctor_id    INT PRIMARY KEY AUTO_INCREMENT,
  nombres      VARCHAR(255) NOT NULL,
  apellidos    VARCHAR(255) NOT NULL,
  especialidad VARCHAR(100) NOT NULL,
  telefono     VARCHAR(20)  NOT NULL,
  horario      VARCHAR(255) NOT NULL,
  correo       VARCHAR(255) UNIQUE NOT NULL,
  contrasena   VARCHAR(255) NOT NULL                -- hash bcrypt
);

CREATE TABLE registrar_citas (
  cita_id          INT PRIMARY KEY AUTO_INCREMENT,
  dni              VARCHAR(20)  NOT NULL,
  fecha_nacimiento DATE         NOT NULL,
  sexo             ENUM('M','F') NOT NULL,
  direccion        VARCHAR(255) NOT NULL,
  telefono         VARCHAR(20)  NOT NULL,
  especialidad     VARCHAR(100) NOT NULL,
  usuario_id       INT NOT NULL,
  doctor_id        INT NOT NULL,
  insertar_nombre  VARCHAR(255) NOT NULL,
  fecha_creacion   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  estado           ENUM('Pendiente','Confirmada','Cancelada','Completada') DEFAULT 'Pendiente',
  FOREIGN KEY (usuario_id) REFERENCES login_usuario(usuario_id),
  FOREIGN KEY (doctor_id)  REFERENCES doctor(doctor_id)
);

CREATE TABLE tabla_disponibilidad (
  disponibilidad_id INT PRIMARY KEY AUTO_INCREMENT,
  doctor_id         INT NOT NULL,
  fecha             DATE NOT NULL,
  hora_inicio       TIME NOT NULL,
  hora_fin          TIME NOT NULL,
  estado            ENUM('libre','ocupado','cita') DEFAULT 'libre',
  FOREIGN KEY (doctor_id) REFERENCES doctor(doctor_id)
);
```

## Instalación

### Requisitos

- PHP 7.4 o superior
- MySQL 5.7+ o MariaDB 10.2+
- Apache 2.4+ (incluido en XAMPP)
- Navegador moderno (Chrome, Firefox o Edge)

### Pasos

**1. Preparar el entorno.** Instala [XAMPP](https://www.apachefriends.org) e inicia **Apache** y **MySQL** desde su panel de control.

**2. Obtener el proyecto.** La carpeta debe llamarse `clinica1` y estar dentro de `htdocs`:

```bash
cd C:\xampp\htdocs
git clone https://github.com/tu-usuario/clinica-dental.git clinica1
```

**3. Crear la base de datos.** Con phpMyAdmin (`http://localhost/phpmyadmin`):

1. Crea una base de datos llamada `clinica` con cotejamiento `utf8mb4_general_ci`.
2. Selecciónala, ve a **Importar** y elige `BASE DE DATOS/base_de_datos.sql`.

O desde la terminal:

```bash
mysql -u root -p < "BASE DE DATOS/base_de_datos.sql"
```

**4. Configurar las credenciales.** Consulta la sección [Configuración](#configuración).

**5. Abrir la aplicación.**

| Acceso | URL |
|--------|-----|
| Inicio | `http://localhost/clinica1` |
| Login de paciente | `http://localhost/clinica1/views/users/login.php` |
| Login de doctor | `http://localhost/clinica1/views/doctor/login.php` |

### Crear un doctor de prueba

Las contraseñas se guardan con bcrypt, por lo que no pueden insertarse en texto plano. Genera primero el hash:

```bash
php -r "echo password_hash('TuContraseña', PASSWORD_BCRYPT);"
```

Después inserta el doctor usando ese hash:

```sql
INSERT INTO doctor (nombres, apellidos, especialidad, telefono, horario, correo, contrasena)
VALUES ('Nombre', 'Apellido', 'Dentista', '999999999', 'Lun-Vie 9:00-17:00',
        'doctor@example.com', '<HASH_GENERADO>');
```

## Configuración

La conexión se define en `config/conexion.php` (PDO). Los datos de conexión se toman en este orden de prioridad:

1. **Variables de entorno**, si están configuradas.
2. **Constantes** definidas en `config/config.local.php`.
3. **Valores predeterminados de desarrollo** definidos en `conexion.php`.

Por ejemplo, si existe la variable de entorno `DB_HOST`, tiene prioridad sobre la constante `DB_HOST` de `config.local.php`.

Ejemplo de `config/config.local.php` para XAMPP:

```php
<?php

define('DB_HOST',     '127.0.0.1');  // usar 127.0.0.1 en lugar de localhost (ver "Rendimiento")
define('DB_NAME',     'clinica');
define('DB_USER',     'root');
define('DB_PASSWORD', '');           // XAMPP: vacía por defecto
```

> **Importante:** `config/config.local.php` es propio de cada equipo y **no debe subirse a Git**. Verifica que esté en `.gitignore`. En producción, usa un usuario de MySQL con permisos limitados (nunca `root`) y no incluyas credenciales en el código fuente.

## Guía de uso

### Paciente

| Acción | Página | Pasos |
|--------|--------|-------|
| Registrarse | `views/users/login_register.php` | Clic en "Registrarse", completar el formulario y enviar. Se inicia sesión automáticamente. |
| Solicitar cita | `views/users/patient_request_form.php` | Elegir especialidad y doctor, ingresar DNI, fecha de nacimiento y dirección, y confirmar. La cita queda en estado `Pendiente`. |
| Ver citas | `views/users/patient_dashboard.php` | Consultar el estado de cada cita y cancelar las que estén `Pendiente`. |
| Ver disponibilidad | `views/users/disponibilidad/ver_horarios.php` | Consultar los horarios libres de cada doctor en el calendario. |

### Doctor

| Acción | Página | Descripción |
|--------|--------|-------------|
| Iniciar sesión | `views/doctor/login.php` | Acceso con correo y contraseña. |
| Ver citas | `views/doctor/doctor_appointments.php` | Lista de citas asignadas con datos del paciente. |
| Gestionar disponibilidad | `views/doctor/disponibilidad/index.php` | Agregar bloques horarios y cambiar su estado. |
| Ver pacientes | `views/doctor/doctor_patient_list.php` | Pacientes vinculados al doctor y número de citas de cada uno. |
| Registrar paciente | `views/doctor/doctor_add_patient.php` | Alta directa de un paciente. |

## Seguridad

Esta sección resume las medidas implementadas y el resultado de una revisión interna de las rutas críticas (autorización, IDOR, CSRF y concurrencia). No sustituye una auditoría de seguridad independiente.

### Protección de rutas

**Pacientes**

| Ruta | Protección |
|------|------------|
| `patient_dashboard.php` | Valida la sesión del paciente. |
| `patient_request_form.php` | Valida la sesión y comprueba con `getDisponibleFuturoById()` que el horario siga disponible. |
| `store_cita.php` | Token CSRF, reserva atómica con `FOR UPDATE` y validación del horario. |
| `view_appointment.php` | Verifica que `usuario_id` de la cita coincida con el de la sesión. |
| `cancel_appointment.php` | Token CSRF y `cancelForPatient()`, que valida la propiedad de la cita. |
| `patient_appointment_history.php` | Valida la sesión y filtra por `usuario_id`. |

**Doctores**

| Ruta | Protección |
|------|------------|
| `doctor/dashboard.php` | Valida `$_SESSION['doctor_id']`. |
| `doctor/doctor_appointments.php` | Token CSRF, filtro por `doctor_id` y control de transiciones de estado. |
| `edit_appointment.php` | Verifica que `doctor_id` de la cita coincida con el de la sesión, más token CSRF. |
| `delete_appointment.php` | `cancelAppointmentForDoctor()` valida la propiedad de la cita, más token CSRF. |

**Públicas**

`index.php`, `especialidades.php` y `nosotros.php` no requieren sesión. Reciben las cabeceras de seguridad definidas en `.htaccess`.

### Mecanismos implementados

| Amenaza | Medida |
|---------|--------|
| **IDOR** (acceso a recursos de otros usuarios) | Cada ruta que recibe un ID comprueba que el recurso pertenezca al usuario de la sesión antes de mostrarlo o modificarlo. |
| **Condiciones de carrera** en reservas y cancelaciones | Transacciones con `SELECT ... FOR UPDATE` en `createFromAvailableSlot()`, `cancelForPatient()` y `cancelForDoctor()`. `updateForDoctor()` valida las transiciones de estado dentro de la transacción. |
| **CSRF** | Todos los formularios POST incluyen `csrf_token`, verificado con `validarCsrf()` antes de cada operación crítica. |
| **Inyección SQL** | Consultas preparadas de PDO con parámetros enlazados. |
| **XSS** | `htmlspecialchars()` al mostrar datos y `strip_tags()` al guardarlos. |
| **Clickjacking** | Cabecera `X-Frame-Options: SAMEORIGIN`. |
| **MIME sniffing** | Cabecera `X-Content-Type-Options: nosniff`. |
| **Contraseñas expuestas** | Hash bcrypt con `password_hash()` y verificación con `password_verify()`. |
| **Datos inválidos** | Validación de tipos (`filter_var` con `FILTER_VALIDATE_INT`), de fechas (`DateTime::createFromFormat()`) y de longitudes máximas. Se repite siempre en el servidor, aunque exista validación en el navegador. |

Cabeceras configuradas en `.htaccess`:

```apache
Header always set X-Frame-Options "SAMEORIGIN"
Header always set X-Content-Type-Options "nosniff"
```

### Código clave

**Reserva atómica de una cita** (`Appointment::createFromAvailableSlot`)

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

**Validación de propiedad** (`view_appointment.php`)

```php
$appointment = $userController->getAppointmentByIddd((int) $_GET['id']);

if (!$appointment || (int) $appointment['usuario_id'] !== (int) $_SESSION['usuario_id']) {
    header("Location: patient_appointment_history.php");
    exit();
}
```

### Recomendaciones antes de publicar en producción

Estas medidas no forman parte de la revisión anterior; se recomienda evaluarlas:

- Servir toda la aplicación por **HTTPS** y añadir la cabecera `Strict-Transport-Security`.
- Configurar las cookies de sesión con `HttpOnly`, `Secure` y `SameSite`.
- Llamar a `session_regenerate_id(true)` al iniciar sesión.
- Añadir una política `Content-Security-Policy`.
- Limitar los intentos de inicio de sesión fallidos.
- Usar en producción un usuario de MySQL con permisos mínimos.

## Rendimiento

Durante las pruebas, la lista de pacientes del doctor tardaba varios segundos en cargar. Se midieron por separado la conexión, la consulta y la generación de la página:

| Medición | Con `localhost` | Con `127.0.0.1` |
|----------|-----------------|-----------------|
| Conexión a MySQL | ~2,413 s | ~0,002 s |
| Consulta SQL | ~0,001 s | ~0,001 s |
| Tiempo PHP total de la página | varios segundos | ~0,007 s |

**Causa:** el retraso estaba en abrir la conexión usando `localhost` como host, no en la consulta.
**Solución:** configurar `DB_HOST` como `127.0.0.1`.

Estos valores corresponden al entorno probado; en otros equipos pueden variar.

**Regla práctica:** mide la conexión, la consulta y la generación de la página por separado antes de optimizar. Una página lenta no implica necesariamente una consulta lenta.

## Limitaciones conocidas

- **Sesiones:** dependen de PHP; si Apache se reinicia, los usuarios deben iniciar sesión de nuevo.
- **Pruebas:** las pruebas realizadas son manuales; no hay pruebas automatizadas.

## Mejoras futuras

**Corto plazo**

- [ ] Índice en `registrar_citas (usuario_id, estado)`.
- [ ] Registro de errores de conexión en un archivo de log.
- [ ] Pruebas automatizadas.

**Mediano plazo**

- [ ] API REST con respuestas JSON.
- [ ] Recordatorios de cita por correo o WhatsApp.
- [ ] Panel de administración (usuarios, especialidades, reportes de ocupación).
- [ ] Historial clínico digital (notas por cita, archivos adjuntos).
- [ ] Facturación con generación de PDF.

**Largo plazo**

- [ ] Soporte para varias sucursales.
- [ ] Autenticación con tokens (JWT) si se expone una API.
- [ ] Interfaz con un framework moderno (React o Vue).

## Preguntas frecuentes

**¿Cómo cambio la contraseña de un doctor?**
Genera un hash bcrypt y actualiza la tabla. No uses la función `PASSWORD()` de MySQL: no es compatible con `password_verify()`.

```bash
php -r "echo password_hash('NuevaContraseña', PASSWORD_BCRYPT);"
```

```sql
UPDATE doctor SET contrasena = '<HASH_GENERADO>' WHERE doctor_id = 1;
```

**¿Cómo hago una copia de seguridad de la base de datos?**

```bash
mysqldump -u root -p clinica > backup_clinica.sql
```

**¿Por qué la aplicación tarda en cargar en local?**
Cambia `DB_HOST` de `localhost` a `127.0.0.1`. Ver [Rendimiento](#rendimiento).

**¿Cómo activo HTTPS?**
Obtén un certificado (por ejemplo, con Let's Encrypt), configúralo en Apache y redirige todo el tráfico HTTP a HTTPS.

## Contribuciones

1. Haz un fork del repositorio.
2. Crea una rama: `git checkout -b feature/nombre-de-la-mejora`.
3. Haz commit de tus cambios: `git commit -m "Descripción del cambio"`.
4. Sube la rama: `git push origin feature/nombre-de-la-mejora`.
5. Abre un Pull Request.

Antes de enviarlo, verifica que la documentación esté actualizada y que los cambios hayan sido revisados por al menos otra persona.

## Autor y licencia

**Autor:** [Tu nombre]
**Contacto:** [correo] · [GitHub](https://github.com/tu-usuario) · [LinkedIn](https://linkedin.com/in/tu-perfil)

Proyecto distribuido bajo la licencia **MIT**. Consulta el archivo `LICENSE` para más detalles.

---

**Versión:** 1.0.0 · **Última actualización:** febrero 2026

# ARCHITECTURE.md - Arquitectura Técnica Detallada

## 📐 Visión General de la Arquitectura

HappyDent implementa un **patrón MVC tradicional** con capas claramente separadas:

```
┌─────────────────────────────────────────────────────────────┐
│                      PRESENTATION LAYER                       │
│  ┌──────────────────────────────────────────────────────┐   │
│  │ Views (PHP Templates + HTML5 + CSS3 + JavaScript)   │   │
│  │ - patient_dashboard.php                             │   │
│  │ - doctor_appointments.php                           │   │
│  │ - patient_request_form.php                          │   │
│  └──────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────┘
                              ↕
┌─────────────────────────────────────────────────────────────┐
│                    BUSINESS LOGIC LAYER                       │
│  ┌──────────────────────────────────────────────────────┐   │
│  │ Controllers (Orchestration & Validation)            │   │
│  │ - UserController::requestAppointment()              │   │
│  │ - DoctorController::updateDisponibilidad()          │   │
│  │ - AppointmentController::cancelAppointment()        │   │
│  └──────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────┘
                              ↕
┌─────────────────────────────────────────────────────────────┐
│                   DATA ACCESS LAYER (DAL)                    │
│  ┌──────────────────────────────────────────────────────┐   │
│  │ Models (ORM-style Data Operations)                  │   │
│  │ - User::readById()                                  │   │
│  │ - Appointment::create()                             │   │
│  │ - Doctor::getSpecialties()                          │   │
│  │ - Disponibilidad::getAvailableSlots()               │   │
│  └──────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────┘
                              ↕
┌─────────────────────────────────────────────────────────────┐
│                   PERSISTENCE LAYER                          │
│  ┌──────────────────────────────────────────────────────┐   │
│  │ PDO (Database Abstraction)                          │   │
│  │ MySQL 5.7+ with InnoDB Storage Engine              │   │
│  └──────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────┘
```

---

## 🗂️ Estructura de Carpetas Detallada

```
clinica1/
│
├── autoload.php                      # PSR-4 Autoloader
│   └─ Registra rutas de clases automáticamente
│      require_once 'Appointment.php' innecesario
│
├── index.php                         # Front Controller (raíz)
│   └─ Punto de entrada principal
│      Incluye header, renderiza HTML
│
├── public/
│   └── index.php                     # Punto de entrada alternativo
│
├── config/
│   └── conexion.php
│       ├─ private $host = 'localhost'
│       ├─ private $db = 'clinica'
│       ├─ private $user = 'root'
│       ├─ private $password = ''
│       └─ public function conectar(): PDO
│
├── controllers/                      # Business Logic
│   ├── UserController.php
│   │   ├─ patientDashboard()         # GET /dashboard
│   │   ├─ requestAppointment()       # POST /cita
│   │   ├─ login()                    # POST /login
│   │   ├─ register()                 # POST /register
│   │   ├─ logout()                   # GET /logout
│   │   ├─ updateProfile()            # POST /profile
│   │   └─ searchDoctor()             # GET /search
│   │
│   ├── DoctorController.php
│   │   ├─ doctorDashboard()          # GET /doctor/dashboard
│   │   ├─ viewAppointments()         # GET /doctor/citas
│   │   ├─ updateAppointmentStatus()  # POST /doctor/cita/{id}/status
│   │   ├─ setAvailability()          # POST /doctor/disponibilidad
│   │   ├─ getSchedule()              # GET /doctor/horario
│   │   └─ addPatientNote()           # POST /doctor/patient/{id}/nota
│   │
│   ├── AppointmentController.php
│   │   ├─ createAppointment()        # POST /cita/crear
│   │   ├─ updateAppointment()        # PUT /cita/{id}
│   │   ├─ cancelAppointment()        # DELETE /cita/{id}
│   │   ├─ getAppointmentDetails()    # GET /cita/{id}
│   │   └─ listAppointments()         # GET /citas
│   │
│   └── DisponibilidadController.php
│       ├─ addTimeSlot()              # POST /disponibilidad
│       ├─ removeTimeSlot()           # DELETE /disponibilidad/{id}
│       ├─ getAvailableSlots()        # GET /disponibilidad
│       └─ blockTimeSlot()            # POST /disponibilidad/bloquear
│
├── models/                           # Data Access Layer (DAL)
│   ├── User.php
│   │   private $table = 'login_usuario'
│   │   ├─ create()      : bool       # INSERT
│   │   ├─ read()        : array      # SELECT *
│   │   ├─ readById()    : object     # SELECT by ID
│   │   ├─ update()      : bool       # UPDATE
│   │   ├─ delete()      : bool       # DELETE
│   │   ├─ authenticate(): object     # Verifica contraseña
│   │   └─ findByEmail() : object     # SELECT by correo
│   │
│   ├── Doctor.php
│   │   private $table = 'doctor'
│   │   ├─ create()      : bool
│   │   ├─ update()      : bool
│   │   ├─ getSpecialties(): array    # Especialidades únicas
│   │   ├─ getDoctorsBySpecialty(): array
│   │   └─ authenticate(): object
│   │
│   ├── Appointment.php
│   │   private $table = 'registrar_citas'
│   │   ├─ create()      : bool
│   │   ├─ update()      : bool
│   │   ├─ delete()      : bool
│   │   ├─ getPatientAppointments(): array
│   │   ├─ getDoctorAppointments(): array
│   │   ├─ getAppointmentById(): object
│   │   ├─ updateStatus(): bool
│   │   └─ getByDateRange(): array
│   │
│   └── Disponibilidad.php
│       private $table = 'tabla_disponibilidad'
│       ├─ create()      : bool
│       ├─ getByDoctor(): array
│       ├─ getByDate()  : array
│       ├─ updateStatus(): bool
│       └─ hasConflict(): bool
│
├── views/                            # Presentation Layer
│   ├── cabecera/
│   │   ├── cabecera.php             # Navbar (incluido en todas)
│   │   └── pie.php                  # Footer (incluido en todas)
│   │
│   ├── users/                        # Paciente UI
│   │   ├── login_register.php       # Login + Register form
│   │   ├── login.php                # Login-only view
│   │   ├── patient_dashboard.php    # Home de paciente
│   │   ├── patient_request_form.php # Formulario de solicitud
│   │   ├── patient_appointments.php # Lista de citas
│   │   ├── patient_calendar.php     # Calendario interactivo
│   │   ├── view_appointment.php     # Detalles de cita
│   │   ├── cancel_appointment.php   # Confirmación cancelación
│   │   ├── edit_appointment.php     # Editar cita
│   │   ├── update_appointment_status.php
│   │   ├── patient_appointment_history.php
│   │   ├── store_cita.php          # Procesar creación (deprecated)
│   │   ├── logout.php               # Cerrar sesión
│   │   └── disponibilidad/
│   │       └── ver_horarios.php     # Ver disponibilidad doctores
│   │
│   ├── doctor/                       # Doctor UI
│   │   ├── login.php                # Doctor login
│   │   ├── dashboard.php            # Home de doctor
│   │   ├── doctor_appointments.php  # Citas del doctor
│   │   ├── doctor_calendar.php      # Calendario doctor
│   │   ├── doctor_patient_list.php  # Pacientes del doctor
│   │   ├── disponibilidad/
│   │   │   └── index.php            # Gestionar horarios
│   │   ├── doctor_add_patient.php   # Agregar paciente
│   │   ├── create.php               # Crear doctor (admin)
│   │   ├── index.php                # Listar doctores
│   │   └── logout.php
│   │
│   ├── appointments/                 # Gestión de citas
│   │   ├── index.php                # Listar citas (admin)
│   │   ├── create.php               # Crear cita (admin)
│   │   └── edit.php                 # Editar cita (admin)
│   │
│   ├── especialidades/
│   │   └── especialidades.php       # Catálogo de servicios
│   │
│   └── nosotros/
│       └── nosotros.php             # Página informativa
│
├── assets/                           # Recursos Estáticos
│   ├── css/
│   │   ├── global.css               # Estilos globales (reset, variables)
│   │   ├── styles.css               # Estilos principales
│   │   ├── cabecera.css             # Navbar + footer
│   │   ├── patient_styles.css       # Dashboard paciente
│   │   ├── doctor_styles.css        # Dashboard doctor
│   │   ├── formulario.css           # Formularios
│   │   ├── patient_calendar.css     # Calendario paciente
│   │   ├── disponibilidaddoctor.css # Calendario doctor
│   │   ├── lista.css                # Listas y tablas
│   │   ├── docestilos.css           # Estilos doctor
│   │   ├── principalfondo.css       # Página inicio
│   │   ├── nosotros.css             # Página nosotros
│   │   ├── mision.css               # Sección misión
│   │   ├── accesoestilo.css         # Acceso/login
│   │   └── pie.css                  # Footer
│   │
│   ├── js/
│   │   ├── script_menu.js           # Hamburger menu (mobile)
│   │   ├── cabecera.js              # Header interactions
│   │   ├── patient_dashboard.js     # Patient UI logic
│   │   ├── patient_calendar.js      # Calendar widget (AJAX)
│   │   ├── doctor_dashboard.js      # Doctor UI logic
│   │   ├── calendar.js              # Calendario genérico
│   │   ├── acceso.js                # Login form validation
│   │   └── styles.js                # Theme switching (si aplica)
│   │
│   └── images/
│       ├── doc.jfif                 # Foto doctor
│       ├── endodoncia.jfif          # Servicio
│       ├── profilaxis.jfif          # Servicio
│       ├── log1.jfif                # Logo
│       ├── fondo_principal1.jfif    # Hero background
│       └── [otros posters/imágenes]
│
├── BASE DE DATOS/
│   └── base_de_datos.sql           # Script de inicialización BD
│       ├─ CREATE DATABASE clinica
│       ├─ CREATE TABLE doctor
│       ├─ CREATE TABLE login_usuario
│       ├─ CREATE TABLE registrar_citas
│       ├─ CREATE TABLE tabla_disponibilidad
│       └─ Seeding con datos de ejemplo
│
└── .gitignore                       # Archivos ignorados en Git
    ├─ config/conexion.php (producción)
    ├─ .env (variables sensibles)
    └─ node_modules/ (si en futuro usa npm)
```

---

## 🔄 Flujos de Datos Críticos

### Flujo 1: Registro e Inicio de Sesión de Paciente

```
[Cliente]
    │
    ├─→ GET /views/users/login_register.php
    │   └─ Renderiza HTML: Formulario POST
    │
    └─→ POST /views/users/login_register.php
        ├─ Captura: nombre, email, usuario, contraseña
        │
        ├─→ [UserController::register()]
        │   ├─ Sanitización: htmlspecialchars(strip_tags($input))
        │   ├─ Validación: email, usuario único
        │   │
        │   ├─→ [User::create()]
        │   │   ├─ Hash: password_hash(..., PASSWORD_BCRYPT)
        │   │   ├─ Prepared Statement:
        │   │   │  INSERT INTO login_usuario 
        │   │   │  (nombre_completo, correo_electronico, usuario, contrasena)
        │   │   │  VALUES (?, ?, ?, ?)
        │   │   │
        │   │   └─ return true/false
        │   │
        │   ├─ Session: $_SESSION['usuario'] = $usuario
        │   ├─ Session: $_SESSION['usuario_id'] = $id
        │   └─ Redirección: header("Location: ..patient_dashboard.php")
        │
        └─→ [Cliente]
            └─ GET /views/users/patient_dashboard.php
                └─ Renderiza: Citas, próximas citas, opciones
```

**Puntos de Seguridad:**
1. Sanitización antes de almacenar (htmlspecialchars)
2. Hashing one-way (BCrypt no es reversible)
3. Prepared statement previene SQL injection
4. Session token aleatorio generado por PHP

---

### Flujo 2: Solicitud de Cita

```
[Cliente: Paciente logeado]
    │
    ├─→ GET /views/users/patient_request_form.php
    │   └─ Renderiza formulario con:
    │      - Dropdown: select Doctor (from DB)
    │      - Input: DNI, fecha_nacimiento, sexo, teléfono
    │      - Dropdown: select Especialidad
    │
    └─→ POST /views/users/patient_request_form.php
        ├─ Parámetros POST:
        │  {dni, fecha_nacimiento, sexo, teléfono,
        │   especialidad, doctor_id}
        │
        ├─→ [UserController::requestAppointment()]
        │   ├─ Check: if (!isset($_SESSION['usuario'])) 
        │   │          return UNAUTHORIZED
        │   │
        │   ├─ Sanitización: htmlspecialchars(strip_tags(...))
        │   │
        │   ├─ Validación:
        │   │  - dni matches patron ^[0-9]{8}$
        │   │  - fecha_nacimiento < hoy
        │   │  - doctor_id EXISTS in doctor table
        │   │  - sexo IN ('M', 'F')
        │   │
        │   ├─→ [Appointment::create()]
        │   │   ├─ Prepared Statement:
        │   │   │  INSERT INTO registrar_citas
        │   │   │  (dni, fecha_nacimiento, sexo, direccion, 
        │   │   │   telefono, especialidad, usuario_id, 
        │   │   │   doctor_id, insertar_nombre, estado)
        │   │   │  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pendiente')
        │   │   │
        │   │   ├─ ON CONFLICT (NULL): No hay error, UNIQUE es en doctor_id + fecha
        │   │   └─ return true/false
        │   │
        │   ├─ Set $_SESSION['message'] = "Cita solicitada"
        │   └─ Redirección: header("Location: patient_dashboard.php")
        │
        └─→ [Cliente]
            └─ GET /views/users/patient_dashboard.php
                ├─ Muestra mensaje de éxito
                └─ Lista citas actualizada con nueva entrada
```

**Puntos de Seguridad:**
1. Validación de sesión (usuario logeado)
2. Sanitización de entrada
3. Validación de integridad referencial (doctor_id existe)
4. Estado inicial SIEMPRE 'Pendiente' (en DB, no en POST)

---

### Flujo 3: Gestión de Disponibilidad (Doctor)

```
[Cliente: Doctor logeado]
    │
    ├─→ GET /views/doctor/disponibilidad/index.php
    │   └─ Muestra:
    │      - Calendario actual
    │      - Bloques de disponibilidad existentes
    │      - Formulario para agregar nuevos
    │
    └─→ POST /views/doctor/disponibilidad/index.php
        ├─ Parámetros:
        │  {fecha, hora_inicio, hora_fin}
        │
        ├─→ [DisponibilidadController::addTimeSlot()]
        │   ├─ Check: if (!isset($_SESSION['doctor'])) return UNAUTHORIZED
        │   │
        │   ├─ Validación:
        │   │  - fecha >= hoy
        │   │  - hora_inicio < hora_fin
        │   │  - No overlap con existentes
        │   │
        │   ├─ Conflicto Check:
        │   │  SELECT COUNT(*) FROM tabla_disponibilidad
        │   │  WHERE doctor_id = ? 
        │   │    AND fecha = ? 
        │   │    AND (
        │   │      (hora_inicio < ? AND hora_fin > ?) OR
        │   │      (hora_inicio >= ? AND hora_fin <= ?)
        │   │    )
        │   │
        │   ├─→ [Disponibilidad::create()]
        │   │   ├─ INSERT INTO tabla_disponibilidad
        │   │   │  (doctor_id, fecha, hora_inicio, hora_fin, estado)
        │   │   │  VALUES (?, ?, ?, ?, 'libre')
        │   │   │
        │   │   └─ return true/false
        │   │
        │   └─ Set $_SESSION['message'] = "Disponibilidad agregada"
        │
        └─→ GET /views/doctor/disponibilidad/index.php
            └─ Recarga página con nuevos bloques visibles
```

**Lógica de Conflictos (Sobreposición):**
```
Bloque existente:  [09:00 ─── 10:30]
Intento nuevo:
  ✗ [08:00 ─── 09:30]  (overlap parcial)
  ✗ [09:15 ─── 10:00]  (completamente dentro)
  ✗ [10:00 ─── 11:00]  (overlap parcial)
  ✓ [07:00 ─── 09:00]  (termina antes)
  ✓ [10:30 ─── 12:00]  (comienza después)
```

---

## 🗄️ Detalles del Modelo de Datos

### Tabla `login_usuario` - Pacientes
```sql
┌─────────────────────────────────┬──────────────┬──────────────┐
│ Columna                         │ Tipo         │ Restricción  │
├─────────────────────────────────┼──────────────┼──────────────┤
│ usuario_id (PK)                 │ INT          │ AUTO_INCREMENT
│ nombre_completo                 │ VARCHAR(255) │ NOT NULL     │
│ correo_electronico              │ VARCHAR(255) │ UNIQUE, NOT NULL
│ usuario                         │ VARCHAR(50)  │ UNIQUE, NOT NULL
│ contrasena                      │ VARCHAR(255) │ NOT NULL     │
│                                 │              │ BCrypt(60)   │
└─────────────────────────────────┴──────────────┴──────────────┘

Índices:
- PRIMARY KEY (usuario_id)
- UNIQUE KEY (correo_electronico)
- UNIQUE KEY (usuario)

Casos de Uso:
- SELECT * FROM login_usuario WHERE correo_electronico = ? (Login)
- INSERT ... quando registra paciente
- SELECT ... FROM login_usuario lu
  INNER JOIN registrar_citas rc 
  ON lu.usuario_id = rc.usuario_id (Dashboard)
```

---

### Tabla `doctor` - Proveedores
```sql
┌─────────────────────────────────┬──────────────┬──────────────┐
│ Columna                         │ Tipo         │ Restricción  │
├─────────────────────────────────┼──────────────┼──────────────┤
│ doctor_id (PK)                  │ INT          │ AUTO_INCREMENT
│ nombres                         │ VARCHAR(255) │ NOT NULL     │
│ apellidos                       │ VARCHAR(255) │ NOT NULL     │
│ especialidad                    │ VARCHAR(100) │ NOT NULL     │
│ telefono                        │ VARCHAR(20)  │ NOT NULL     │
│ horario                         │ VARCHAR(255) │ NOT NULL     │
│ correo                          │ VARCHAR(255) │ UNIQUE, NOT NULL
│ contrasena                      │ VARCHAR(255) │ NOT NULL     │
│                                 │              │ BCrypt(60)   │
└─────────────────────────────────┴──────────────┴──────────────┘

Índices:
- PRIMARY KEY (doctor_id)
- UNIQUE KEY (correo)

Normalización Issues (Future):
- especialidad debería ser FK a tabla especialidad
- horario debería numerar (09:00-17:00)
- Repetir "nombre + apellido" es redundante
```

---

### Tabla `registrar_citas` - Transacciones Core
```sql
┌─────────────────────────────────┬──────────────┬──────────────┐
│ Columna                         │ Tipo         │ Restricción  │
├─────────────────────────────────┼──────────────┼──────────────┤
│ cita_id (PK)                    │ INT          │ AUTO_INCREMENT
│ dni                             │ VARCHAR(20)  │ NOT NULL     │
│ fecha_nacimiento                │ DATE         │ NOT NULL     │
│ sexo                            │ ENUM         │ IN ('M','F') │
│ direccion                       │ VARCHAR(255) │ NOT NULL     │
│ telefono                        │ VARCHAR(20)  │ NOT NULL     │
│ especialidad                    │ VARCHAR(100) │ NOT NULL     │
│ usuario_id (FK)                │ INT          │ NOT NULL     │
│ doctor_id (FK)                 │ INT          │ NOT NULL     │
│ insertar_nombre                │ VARCHAR(255) │ NOT NULL     │
│ fecha_creacion                 │ TIMESTAMP    │ DEFAULT NOW()
│ estado                         │ ENUM         │ DEFAULT 'Pendiente'
│                                 │              │ IN (Pendiente, Confirmada,
│                                 │              │     Cancelada, Completada)
└─────────────────────────────────┴──────────────┴──────────────┘

Índices:
- PRIMARY KEY (cita_id)
- FOREIGN KEY (usuario_id) REFERENCES login_usuario(usuario_id)
- FOREIGN KEY (doctor_id) REFERENCES doctor(doctor_id)
- INDEX (usuario_id) - Búsqueda frecuente por usuario
- INDEX (doctor_id) - Búsqueda frecuente por doctor
- INDEX (estado) - Filtrado por estado

Critical Queries (Optimizadas):
- SELECT * FROM registrar_citas WHERE usuario_id = ? 
  ORDER BY fecha_creacion DESC (O(n) con INDEX)
  
- SELECT COUNT(*) FROM registrar_citas 
  WHERE doctor_id = ? AND estado = 'Confirmada'
  (Contador de citas confirmadas)
```

---

### Tabla `tabla_disponibilidad` - Ocupación
```sql
┌─────────────────────────────────┬──────────────┬──────────────┐
│ Columna                         │ Tipo         │ Restricción  │
├─────────────────────────────────┼──────────────┼──────────────┤
│ disponibilidad_id (PK)          │ INT          │ AUTO_INCREMENT
│ doctor_id (FK)                 │ INT          │ NOT NULL     │
│ fecha                          │ DATE         │ NOT NULL     │
│ hora_inicio                    │ TIME         │ NOT NULL     │
│ hora_fin                       │ TIME         │ NOT NULL     │
│ estado                         │ ENUM         │ DEFAULT 'libre'
│                                 │              │ IN (libre, ocupado, cita)
└─────────────────────────────────┴──────────────┴──────────────┘

Índices:
- PRIMARY KEY (disponibilidad_id)
- FOREIGN KEY (doctor_id) REFERENCES doctor(doctor_id)
- UNIQUE INDEX (doctor_id, fecha, hora_inicio, hora_fin)
  Previene duplicados exactos

Validaciones de Aplicación:
- hora_inicio < hora_fin
- fecha >= CURDATE()
- No sobreposición (verificado en app layer)

Query Típica (Calendario):
SELECT * FROM tabla_disponibilidad 
WHERE doctor_id = ? 
  AND fecha BETWEEN ? AND ?  -- Mes actual
  AND estado = 'libre'
ORDER BY fecha, hora_inicio
```

---

## 🔐 Matriz de Seguridad

| Amenaza | Técnica | Implementación | Estado |
|---------|---------|-----------------|--------|
| **SQL Injection** | Prepared Statements | PDO bound params `:param` | ✅ Implementado |
| **XSS** | Output Sanitization | htmlspecialchars(), strip_tags() | ✅ Implementado |
| **CSRF** | CSRF Token | NO (sesiones PHP nativas) | ⚠️ Necesario en v2 |
| **Weak Passwords** | Password Rules | NO (sin validación minLength) | ⚠️ Mejorar |
| **Credential Stuffing** | Rate Limiting | NO | ⚠️ Requiere Redis |
| **Session Hijacking** | HTTPOnly Cookies | Default PHP (revisar .htaccess) | ⚠️ Requiere HTTPS |
| **Broken Auth** | Multi-Factor Auth | NO | ⚠️ Futuro |

---

## 📊 Principios de Diseño

### 1. Separación de Responsabilidades (SoC)
```php
// ✓ CORRECTO: Cada clase una responsabilidad
class User {
    public function create() { /* INSERT */ }
    public function authenticate() { /* PASSWORD_VERIFY */ }
}

class UserController {
    public function register() {
        // Orquesta User + validación + sesión
        $user = new User();
        $user->create();
        $_SESSION['usuario'] = ...;
    }
}

// ✗ INCORRECTO: Mezclar lógica
class User {
    public function register() {
        // INSERT + $_SESSION + header() + HTML rendering
    }
}
```

### 2. DRY (Don't Repeat Yourself)
```php
// ✓ CORRECTO: Sanitización centralizada en modelo
class User {
    public function create() {
        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        // INSERT
    }
}

// ✗ INCORRECTO: Repetir en cada controller
$nombre = htmlspecialchars(strip_tags($_POST['nombre']));
// ... en 10 controllers diferentes
```

### 3. Fail Secure (Fallar Seguro)
```php
// ✓ CORRECTO: Negar por defecto
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit(); // Crítico: exit() después de header()
}

// ✗ INCORRECTO: Asumir autenticación
$user = $_SESSION['usuario_id'] ?? 'guest'; // Sigue adelante
```

---

## 🚀 Performance Considerations

### Índices Críticos
```sql
-- Búsquedas más frecuentes
CREATE INDEX idx_login_usuario_email 
  ON login_usuario(correo_electronico);

CREATE INDEX idx_registrar_citas_usuario 
  ON registrar_citas(usuario_id);

CREATE INDEX idx_registrar_citas_doctor 
  ON registrar_citas(doctor_id);

CREATE INDEX idx_registrar_citas_estado 
  ON registrar_citas(estado);

CREATE INDEX idx_tabla_disponibilidad_doctor_fecha
  ON tabla_disponibilidad(doctor_id, fecha);
```

### Query Optimization
```php
// ✗ LENTO: N+1 problem
$users = $db->query("SELECT * FROM login_usuario");
foreach ($users as $user) {
    $citas = $db->query("SELECT * FROM registrar_citas WHERE usuario_id = {$user['id']}");
    // Query ejecutada N veces
}

// ✓ RÁPIDO: Join
$result = $db->query("
    SELECT u.*, c.cita_id
    FROM login_usuario u
    LEFT JOIN registrar_citas c ON u.usuario_id = c.usuario_id
");
// Una sola query
```

---

## 📚 Referencias de Código Clave

Consulta los siguientes archivos para ejemplos detallados:
- [models/User.php](../models/User.php) - CRUD básico
- [controllers/UserController.php](../controllers/UserController.php) - Orquestación
- [config/conexion.php](../config/conexion.php) - Pool de conexiones

---

**Última actualización:** Febrero 2026

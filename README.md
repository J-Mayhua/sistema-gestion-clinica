# HappyDent - Sistema de Gestión de Clínica Dental

## 📋 Tabla de Contenidos
- [Descripción](#descripción-del-proyecto)
- [Contexto del Problema](#contexto-del-problema)
- [Stack Técnico](#stack-técnico)
- [Arquitectura del Sistema](#arquitectura-del-sistema)
- [Diseño Técnico](#diseño-técnico)
- [Metodología](#metodología)
- [Instalación](#instalación-y-configuración)
- [Uso](#guía-de-uso)
- [Retos Técnicos](#retos-técnicos-y-soluciones)
- [Mejoras Futuras](#mejoras-futuras)
- [Decisiones Técnicas](#decisiones-técnicas-clave)
- [Autor](#autor)

---

## Descripción del Proyecto

**HappyDent** es un sistema integral de gestión de citas para clínicas dentales, diseñado bajo el patrón arquitectónico **MVC (Model-View-Controller)**. Proporciona a pacientes y doctores una plataforma web para gestionar citas, disponibilidad horaria y servicios especializados de manera eficiente.

El sistema facilita la automatización del flujo de trabajo clínico, reduciendo tiempos de espera y mejorando la experiencia del paciente mediante:
- Registro y autenticación segura
- Solicitud y seguimiento de citas
- Gestión de disponibilidad en tiempo real
- Calendarios interactivos (AJAX)
- Control de estatus de citas

---

## Contexto del Problema

### Desafío Clínico Original
Las clínicas dentales tradicionalmente enfrentan:
1. **Gestión manual de citas** - Propenso a errores y conflictos de horarios
2. **Sobrecarga administrativa** - Personal ocupado en tareas manuales
3. **Experiencia del paciente** - Tiempos de espera para consultas y confirmaciones
4. **Visibilidad de disponibilidad** - Pacientes sin acceso a horarios reales
5. **Historial desorganizado** - Registros fragmentados y difíciles de auditar

### Solución Implementada
HappyDent automatiza el ciclo completo de gestión de citas con:
- **Backend robusto** con validación en múltiples capas
- **Frontend intuitivo** con calendarios interactivos
- **Base de datos normalizada** garantizando integridad referencial
- **Roles diferenciados** (Paciente, Doctor, Admin)
- **Trazabilidad completa** de todas las transacciones

---

## Stack Técnico

### Backend
| Tecnología | Versión | Propósito |
|-----------|---------|----------|
| **PHP** | 7.4+ | Lenguaje base del servidor |
| **PDO** | Nativo | Abstracción para base de datos (prepared statements) |
| **Sessions PHP** | Nativo | Gestión de autenticación y estado |
| **Password Hashing** | BCrypt | Criptografía de contraseñas |

### Frontend
| Tecnología | Propósito |
|-----------|----------|
| **HTML5** | Estructura semántica |
| **CSS3** | Diseño responsivo (FlexBox, Grid) |
| **JavaScript (Vanilla)** | Interactividad sin dependencias |
| **Font Awesome 5.15.4** | Iconografía |

### Base de Datos
| Componente | Especificación |
|-----------|----------------|
| **Motor** | MySQL 5.7+ / MariaDB 10.2+ |
| **Juego de caracteres** | UTF-8 MB4 (soporte multilingual) |
| **Integridad** | Claves foráneas y restricciones |
| **Transacciones** | InnoDB ENGINE |

### Herramientas de Desarrollo
- **XAMPP**: Stack Apache + MySQL + PHP
- **Git**: Control de versiones
- **VS Code**: IDE recomendado
- **Postman** (opcional): Pruebas de endpoints

---

## Arquitectura del Sistema

### Patrón Arquitectónico: MVC

```
clinica1/
├── controllers/          # Lógica de negocio y orquestación
│   ├── UserController.php
│   ├── DoctorController.php
│   ├── AppointmentController.php
│   └── DisponibilidadController.php
├── models/              # Abstracción de datos (Data Access Layer)
│   ├── User.php
│   ├── Doctor.php
│   ├── Appointment.php
│   └── Disponibilidad.php
├── views/               # Presentación (templates)
│   ├── users/           # Vistas de pacientes
│   ├── doctor/          # Vistas de doctores
│   ├── appointments/    # Gestión de citas
│   └── cabecera/        # Componentes comunes
├── config/              # Configuración central
│   └── conexion.php     # Pool de conexiones (singleton pattern)
├── assets/              # Recursos estáticos
│   ├── css/             # Estilos organizados por módulo
│   ├── js/              # Scripts funcionales
│   └── images/          # Imágenes del sitio
├── public/              # Punto de entrada (index.php)
├── autoload.php         # PSR-4 Autoloader
└── BASE DE DATOS/       # Scripts de inicialización
    └── base_de_datos.sql
```

### Justificación Técnica de la Estructura

**`models/` - Data Access Layer**
- **Responsabilidad única**: CRUD operations y lógica de datos
- **Reutilización**: Controllers pueden usar múltiples modelos
- **Testabilidad**: Lógica de datos independiente de HTTP
- **Ejemplo**: `Appointment::create()` encapsula INSERT sin conocer $_POST

**`controllers/` - Business Logic & Orchestration**
- **Mediadores**: Coordinan interacciones entre modelos y vistas
- **Validación**: Procesa entrada de usuario antes de persistencia
- **Flujo**: Maneja redirecciones y sesiones
- **Ejemplo**: `AppointmentController::requestAppointment()` valida, crea cita y actualiza sesión

**`views/` - Presentation Layer**
- **HTML puro**: Sin lógica de negocio, solo presentación
- **Alcance limitado**: Acceso a variables pasadas por controlador
- **Reutilización**: Componentes como `cabecera.php` incluidos múltiples veces
- **Ejemplo**: `patient_dashboard.php` recibe `$appointments` y renderiza HTML

**`config/`**
- **Patrón Singleton**: `Conexion` genera una única instancia por request
- **PDO**: Prepared statements previenen SQL injection
- **Centralización**: Cambios en credenciales en un único punto

**`autoload.php`**
- **PSR-4 Autoloading**: Carga automática de clases sin `require_once` repetitivo
- **Performance**: Carga lazy (solo cuando se usan)
- **Mantenibilidad**: Agregar nuevos modelos sin cambiar código existente

---

## Diseño Técnico

### 1. Modelo de Base de Datos

#### Diagrama Entidad-Relación (ER)

```
┌─────────────────────┐
│   login_usuario     │
├─────────────────────┤
│ PK usuario_id       │
│    nombre_completo  │
│    correo_electr.   │
│    usuario          │
│    contrasena       │
└─────────────────────┘
         │
         │ FK usuario_id
         │
         ▼
┌─────────────────────┐         ┌─────────────────────┐
│  registrar_citas    │────────▶│      doctor         │
├─────────────────────┤         ├─────────────────────┤
│ PK cita_id          │         │ PK doctor_id        │
│    dni              │         │    nombres          │
│    fecha_nac.       │         │    apellidos        │
│    sexo             │         │    especialidad     │
│    direccion        │         │    telefono         │
│    telefono         │         │    horario          │
│    especialidad     │         │    correo           │
│ FK usuario_id       │         │    contrasena       │
│ FK doctor_id        │         └─────────────────────┘
│    estado           │                  │
│    fecha_creacion   │                  │
└─────────────────────┘                  │
                                         │ FK doctor_id
         ┌───────────────────────────────┘
         │
         ▼
   ┌─────────────────────────┐
   │ tabla_disponibilidad    │
   ├─────────────────────────┤
   │ PK disponibilidad_id    │
   │ FK doctor_id            │
   │    fecha                │
   │    hora_inicio          │
   │    hora_fin             │
   │    estado               │
   └─────────────────────────┘
```

#### Tabla `login_usuario` (Pacientes)
```sql
CREATE TABLE login_usuario (
  usuario_id INT PRIMARY KEY AUTO_INCREMENT,
  nombre_completo VARCHAR(255) NOT NULL,
  correo_electronico VARCHAR(255) UNIQUE NOT NULL,
  usuario VARCHAR(50) UNIQUE NOT NULL,
  contrasena VARCHAR(255) NOT NULL -- BCrypt hash
)
```
**Índices**: PK usuario_id, UNIQUE correo_electronico
**Propósito**: Registro y autenticación de pacientes

#### Tabla `doctor` (Proveedores de Servicios)
```sql
CREATE TABLE doctor (
  doctor_id INT PRIMARY KEY AUTO_INCREMENT,
  nombres VARCHAR(255) NOT NULL,
  apellidos VARCHAR(255) NOT NULL,
  especialidad VARCHAR(100) NOT NULL,
  telefono VARCHAR(20) NOT NULL,
  horario VARCHAR(255) NOT NULL,
  correo VARCHAR(255) UNIQUE NOT NULL,
  contrasena VARCHAR(255) NOT NULL -- BCrypt hash
)
```
**Índices**: PK doctor_id, UNIQUE correo
**Propósito**: Registro y autenticación de doctores

#### Tabla `registrar_citas` (Transacciones)
```sql
CREATE TABLE registrar_citas (
  cita_id INT PRIMARY KEY AUTO_INCREMENT,
  dni VARCHAR(20) NOT NULL,
  fecha_nacimiento DATE NOT NULL,
  sexo ENUM('M','F') NOT NULL,
  direccion VARCHAR(255) NOT NULL,
  telefono VARCHAR(20) NOT NULL,
  especialidad VARCHAR(100) NOT NULL,
  usuario_id INT NOT NULL,
  doctor_id INT NOT NULL,
  insertar_nombre VARCHAR(255) NOT NULL,
  fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  estado ENUM('Pendiente','Confirmada','Cancelada','Completada') DEFAULT 'Pendiente',
  FOREIGN KEY (usuario_id) REFERENCES login_usuario(usuario_id),
  FOREIGN KEY (doctor_id) REFERENCES doctor(doctor_id)
)
```
**Índices**: PK cita_id, FK usuario_id, FK doctor_id
**Propósito**: Registro de citas con trazabilidad completa

#### Tabla `tabla_disponibilidad` (Ocupación)
```sql
CREATE TABLE tabla_disponibilidad (
  disponibilidad_id INT PRIMARY KEY AUTO_INCREMENT,
  doctor_id INT NOT NULL,
  fecha DATE NOT NULL,
  hora_inicio TIME NOT NULL,
  hora_fin TIME NOT NULL,
  estado ENUM('libre','ocupado','cita') DEFAULT 'libre',
  FOREIGN KEY (doctor_id) REFERENCES doctor(doctor_id)
)
```
**Índices**: PK disponibilidad_id, FK doctor_id
**Propósito**: Bloqueos de tiempo para prevenir overbooking

### 2. Arquitectura de API y Flujos

#### Diagrama de Flujo: Solicitud de Cita
```
[Paciente] 
    │
    ├─→ [GET /views/users/patient_request_form.php]
    │   (Formulario con doctores y especialidades)
    │
    └─→ [POST /controllers/UserController->requestAppointment()]
        ├─ Validación de entrada (sanitización)
        ├─ Creación de instancia Appointment
        ├─ $appointment->create() [PDO prepared statement]
        ├─ INSERT INTO registrar_citas (estado='Pendiente')
        └─ SET $_SESSION['message'] = "Éxito"
            └─→ [GET /views/users/patient_dashboard.php]
                (Redisplayed con nueva cita)
```

#### Diagrama de Flujo: Login de Usuario
```
[Form POST]
    ├─ email, password
    │
    └─→ [UserController->login()]
        ├─ readByEmail() [SELECT * WHERE correo = ?]
        ├─ password_verify($input, $hash)
        ├─ SET $_SESSION['usuario_id'], $_SESSION['usuario']
        └─→ [Redirección a patient_dashboard.php]
```

### 3. Seguridad Implementada

#### Capas de Protección

| Nivel | Mecanismo | Implementación |
|------|-----------|----------------|
| **Transporte** | HTTPS | Recomendado en producción |
| **Autenticación** | BCrypt + Salt | `password_hash(..., PASSWORD_BCRYPT)` |
| **Sesiones** | PHP Sessions | `session_start()` con validación |
| **SQL Injection** | Prepared Statements | PDO bound parameters `:param` |
| **XSS** | Output Sanitization | `htmlspecialchars()`, `strip_tags()` |
| **Access Control** | Session Guards | `if (!isset($_SESSION['usuario']))` |

#### Ejemplos de Código Seguro

**Hashing de contraseña (User.php)**
```php
$this->contrasena = password_hash(
    htmlspecialchars(strip_tags($this->contrasena)), 
    PASSWORD_BCRYPT  // Algoritmo bcrypt con salt automático
);
```

**Prepared Statement (Appointment.php)**
```php
$query = "SELECT * FROM registrar_citas WHERE usuario_id = :usuario_id";
$stmt = $this->conn->prepare($query);  // Separación: estructura vs datos
$stmt->bindParam(":usuario_id", $id, PDO::PARAM_INT);
```

**Sanitización de entrada (UserController.php)**
```php
$appointmentModel->dni = htmlspecialchars(strip_tags($_POST['dni']));
// htmlspecialchars: Convierte caracteres a entidades HTML
// strip_tags: Elimina cualquier etiqueta HTML/PHP
```

---

## Metodología

### Enfoque de Desarrollo

**Waterfall Modificado** - Iterativo en ciclos cortos adaptado a requisitos clínicos

1. **Análisis de Requisitos** (Semana 1)
   - Reunión con stakeholders (doctores, administrativos)
   - Documentación de flujos manuales actuales
   - Definición de casos de uso prioritarios

2. **Diseño Arquitectónico** (Semana 2)
   - Modelo ER de base de datos
   - Estructura MVC
   - Especificación de endpoints

3. **Implementación Modular** (Semanas 3-6)
   - **Sprint 1**: Autenticación (Login/Register)
   - **Sprint 2**: Gestión de citas (CRUD)
   - **Sprint 3**: Calendarios interactivos
   - **Sprint 4**: Sistema de disponibilidad

4. **Testing Integrado** (Semana 7)
   - Pruebas funcionales manuales
   - Validación de seguridad
   - Testing en navegadores (Chrome, Firefox, Edge)

5. **Deployment** (Semana 8)
   - Setup en servidor de producción
   - Configuración de variables de entorno
   - Documentación operacional

### Prácticas de Código
- **Code Review**: Al menos una persona revisa PRs
- **Naming Conventions**: PSR-12 (camelCase para métodos, PascalCase para clases)
- **Documentation**: Docblocks en métodos clave
- **Error Handling**: Try-catch en operaciones de BD

---

## Instalación y Configuración

### Requisitos Previos
```bash
- PHP 7.4 o superior
- MySQL 5.7+ o MariaDB 10.2+
- Apache 2.4+ (incluido en XAMPP)
- Git (opcional, para control de versiones)
- Navegador moderno (Chrome 90+, Firefox 88+)
```

### Paso 1: Preparación del Entorno

**En Windows con XAMPP:**
```powershell
# 1. Descargar XAMPP desde https://www.apachefriends.org
# 2. Instalar en C:\xampp
# 3. Iniciar XAMPP Control Panel
Start-Process "C:\xampp\xampp-control.exe"

# 4. Activar Apache y MySQL
# (Click en botones "Start" en la interfaz gráfica)
```

### Paso 2: Clonar/Descargar Proyecto

```bash
cd C:\xampp\htdocs

# Opción A: Clonar repositorio
git clone https://github.com/tu-usuario/clinica-dental.git clinica1
cd clinica1

# Opción B: Descargar ZIP y extraer
# Asegurarse que la carpeta sea "clinica1"
```

### Paso 3: Configurar Base de Datos

```bash
# A través de phpMyAdmin
1. Abrir navegador: http://localhost/phpmyadmin
2. Login: usuario="root", contraseña="" (vacío por defecto)
3. Click en "New" o "Nueva"
4. Nombre de base de datos: "clinica"
5. Collation: "utf8mb4_general_ci"
6. Click en "Create"

# Importar esquema
7. Seleccionar base de datos "clinica"
8. Click en pestaña "Import"
9. Seleccionar archivo: BASE DE DATOS/base_de_datos.sql
10. Click en "Go"
```

**Alternativa vía terminal MySQL:**
```bash
mysql -u root -p < "BASE DE DATOS\base_de_datos.sql"
# Presionar ENTER cuando pida contraseña (vacía)
```

### Paso 4: Configurar PHP

**Verificar en /config/conexion.php**
```php
private $host = 'localhost';     // Verificar IP correcta
private $db = 'clinica';         // Nombre exacto de base de datos
private $user = 'root';          // Usuario MySQL
private $password = '';          // Contraseña (XAMPP: vacía)
```

**Modificar si es necesario:**
```php
// Ejemplo para servidor remoto
private $host = '192.168.1.100';
private $db = 'clinica_prod';
private $user = 'clinica_user';
private $password = 'MiContraseñaSegura123';
```

### Paso 5: Verificar Permisos de Carpeta

```powershell
# En Windows, IIS/Apache necesita permisos de lectura-escritura
icacls "C:\xampp\htdocs\clinica1" /grant Users:F /t

# En Linux/macOS
chmod -R 755 /var/www/html/clinica1
chmod -R 777 /var/www/html/clinica1/assets  # Si se suben imágenes
```

### Paso 6: Acceder a la Aplicación

```
URL local:     http://localhost/clinica1
URL alternativa: http://127.0.0.1/clinica1

Pantalla de inicio: Carrusel de servicios
Login paciente: http://localhost/clinica1/views/users/login.php
Login doctor: http://localhost/clinica1/views/doctor/login.php
```

### Paso 7: Crear Cuenta de Prueba (Opcional)

**Registro de Paciente:**
```
Nombre: Juan Pérez
Correo: juan@example.com
Usuario: juan123
Contraseña: Segura123
```

**Doctor Pre-registrado (en BD):**
```
Correo: jose@gmail.com
Contraseña: (usar contraseña hasheada de tabla doctor)
Especialidad: Dentista
```

---

## Guía de Uso

### Para Pacientes

#### 1. Registro
```
Página: /views/users/login_register.php
Pasos:
1. Click en "Registrarse"
2. Completar formulario (nombre, email, usuario, contraseña)
3. Presionar "Registrar"
4. Automáticamente logeado y redirigido a dashboard
```

#### 2. Solicitar Cita
```
Página: /views/users/patient_request_form.php
Pasos:
1. Dashboard → "Solicitar Cita"
2. Seleccionar especialidad (Dentista, Ortodoxia, etc.)
3. Seleccionar doctor disponible
4. Ingresar datos personales (DNI, fecha nacimiento, dirección)
5. Click "Confirmar Solicitud"
6. Cita aparece en estado "Pendiente"
```

#### 3. Ver Historial de Citas
```
Página: /views/users/patient_dashboard.php
Opciones:
- Ver todas las citas (filtrada por usuario_id)
- Ver estado: Pendiente → Confirmada → Completada
- Cancelar cita (solo si estado es Pendiente)
- Ver detalles de doctor asignado
```

#### 4. Ver Disponibilidad
```
Página: /views/users/disponibilidad/ver_horarios.php
Funcionalidad:
- Calendario interactivo
- Visualizar huecos libres por doctor
- Horarios bloqueados por doctor_id
```

### Para Doctores

#### 1. Login
```
Página: /views/doctor/login.php
Credenciales pre-registradas:
- Email: jose@gmail.com
- Contraseña: (del sistema)
```

#### 2. Ver Citas Asignadas
```
Página: /views/doctor/doctor_appointments.php
Información:
- Lista de citas con estado (Pendiente, Confirmada, Completada)
- Datos del paciente (nombre, DNI, teléfono)
- Especialidad de la cita
```

#### 3. Gestionar Disponibilidad
```
Página: /views/doctor/disponibilidad/index.php
Acciones:
- Agregar bloques de disponibilidad (fecha, hora inicio, hora fin)
- Marcar como "libre", "ocupado", "cita"
- Ver calendario de ocupación
```

#### 4. Registrar Paciente
```
Página: /views/doctor/doctor_add_patient.php
Uso:
- Agregar paciente directamente (admin)
- Vincular con citas existentes
```

---

## Retos Técnicos y Soluciones

### Reto 1: Prevención de Overbooking (Sobrecarga de Citas)

**Problema:** Múltiples pacientes pueden reservar el mismo horario simultáneamente

**Solución Implementada:**
```php
// tabla_disponibilidad actúa como "calendar lock"
// Proceso:
1. SELECT * FROM tabla_disponibilidad 
   WHERE doctor_id = ? AND fecha = ? AND estado != 'libre'
2. Si hay registro con hora_inicio <= nueva_hora AND hora_fin >= nueva_hora
   → Rechazar cita
3. Si no hay conflicto → INSERT nuevo bloque con estado='cita'
```

**Mejora Futura:** Implementar transacciones ACID:
```php
$this->conn->beginTransaction();
try {
    // SELECT FOR UPDATE para lock pessimistic
    // INSERT disponibilidad
    // INSERT cita
    $this->conn->commit();
} catch (Exception $e) {
    $this->conn->rollBack();
}
```

---

### Reto 2: Gestión de Sesiones en Diferente Contexto (Paciente vs Doctor)

**Problema:** Doctores y pacientes login en mismo sistema, pero con diferentes módulos

**Solución Implementada:**
```php
// Usuario se autentica contra tabla login_usuario O login_doctor
// Se guarda $_SESSION['tipo_usuario'] = 'paciente' | 'doctor'

if ($_SESSION['tipo_usuario'] === 'paciente') {
    header("Location: /views/users/patient_dashboard.php");
} else {
    header("Location: /views/doctor/doctor_dashboard.php");
}
```

**Mejora Futura:** Usar JWT tokens:
```php
// Token contiene: {user_id, tipo, exp: timestamp}
// Validación: verify(token) en cada request
// Beneficio: Stateless, escalable a microservicios
```

---

### Reto 3: Validación Doble Capa (Frontend + Backend)

**Problema:** JavaScript validation es fácil de bypasear (modificar código en inspect element)

**Solución Implementada:**

**Frontend (JavaScript - UX):**
```javascript
// /assets/js/patient_dashboard.js
if (!email.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
    alert("Email inválido");
    return false;
}
```

**Backend (PHP - Seguridad):**
```php
// /controllers/UserController.php
$this->correo_electronico = filter_var($_POST['correo'], FILTER_VALIDATE_EMAIL);

if (!$this->correo_electronico) {
    $_SESSION['error'] = "Email inválido";
    return false;
}
```

**Justificación:** 
- Frontend: Feedback inmediato al usuario
- Backend: Garantiza integridad sin confiar en cliente

---

### Reto 4: Rendimiento con N usuarios Concurrentes

**Problema:** Query `SELECT * FROM registrar_citas WHERE usuario_id = ?` se ejecuta múltiples veces por request

**Solución Implementada:**
```php
// Caché en sesión (request-level)
if (!isset($_SESSION['appointments_cache'])) {
    $_SESSION['appointments_cache'] = $appointmentModel->getPatientAppointments($user_id);
}
$appointments = $_SESSION['appointments_cache'];
```

**Mejora Futura:**
```php
// Implementar Redis u OP-Cache:
// - Redis: Caché compartido entre requests (TTL de 5 min)
// - OP-Cache: Caché de scripts PHP compilados
// - Base de datos: Indexes en (usuario_id, estado) para queries
```

---

### Reto 5: Manejo de Errores de Conexión BD

**Problema:** Si MySQL está offline, la aplicación crashea sin mensaje útil

**Solución Implementada:**
```php
// /config/conexion.php
try {
    $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db, 
                          $this->user, $this->password);
} catch(PDOException $exception) {
    echo "Connection error: " . $exception->getMessage();
    // Loguear error en archivo separado
    file_put_contents("logs/db_errors.log", date('Y-m-d H:i:s') . " - " . $exception, FILE_APPEND);
}
```

**Mejora Futura:**
```php
// Implementar retry logic con exponential backoff
// Y fallback a BD replica si es disponible
$retries = 3;
while ($retries-- > 0) {
    try {
        // conectar
        break;
    } catch (PDOException $e) {
        if ($retries === 0) {
            // Conectar a BD replica
            // O mostrar página de "servicio en mantenimiento"
        }
        sleep(2 ** (3 - $retries)); // Backoff: 2s, 4s
    }
}
```

---

## Mejoras Futuras

### Fase 2: Escalabilidad (Roadmap 3-6 meses)

#### Backend
- [ ] **RESTful API completa** (JSON endpoints)
  - `POST /api/v1/appointments` - Crear cita
  - `GET /api/v1/appointments/{id}` - Obtener detalles
  - `PATCH /api/v1/appointments/{id}` - Actualizar estado
  
- [ ] **Autenticación moderna**
  - JWT tokens (sin dependencia de sesiones PHP)
  - OAuth2 con Google/Microsoft (login social)
  
- [ ] **Caché distribuido**
  - Redis para sesiones y datos frecuentes
  - Invalidación inteligente (event-driven)

- [ ] **Logging y Monitoreo**
  - Monolog para logs estructurados
  - Sentry o New Relic para APM (Application Performance Monitoring)

#### Frontend
- [ ] **Framework moderno**
  - React.js o Vue.js 3
  - TypeScript para type-safety
  - Vite como bundler
  
- [ ] **PWA (Progressive Web App)**
  - Service Workers para offline-first
  - Push notifications para recordatorio de citas
  
- [ ] **Oficios mejorados**
  - Calendario tipo Google Calendar (drag-drop)
  - Autocomplete en búsqueda de doctores

#### Base de Datos
- [ ] **Denormalización inteligente**
  - Tabla `appointments_summary` para dashboards
  - Columna `doctor_name` en `registrar_citas` (optimización lectura)
  
- [ ] **Particionamiento temporal**
  - Tabla `registrar_citas_2024`, `registrar_citas_2025` (por año)
  - Mejora de queries históricas
  
- [ ] **Auditoría completa**
  - Tabla `audit_log` con cambios históricos
  - Triggers para registrar WHO, WHAT, WHEN

#### Características Clínicas
- [ ] **Historiales médicos digitales**
  - Notas de doctores por cita
  - Adjuntos (radiografías, presupuestos)
  - Historial de tratamientos
  
- [ ] **Facturación integrada**
  - Generación de facturas PDF
  - Integración con sistemas contables
  
- [ ] **Recordatorios automáticos**
  - Email 24h antes de cita
  - SMS (WhatsApp API)
  
- [ ] **Panel de Administración**
  - Gestión de usuarios y permisos
  - Reportes de ocupación y ingresos
  - Configuración de especialidades

### Fase 3: Enterprise (6-12 meses)

- [ ] **Multi-sucursal**
  - Diferentes clínicas con BD compartida
  - Sincronización de doctores entre sedes
  
- [ ] **Integración con sistemas externo**
  - HL7 para interoperabilidad con historiales
  - FHIR estándar healthcare
  
- [ ] **Machine Learning**
  - Predicción de no-shows (cancelaciones)
  - Recomendación de tratamientos basados en historial

---

## Decisiones Técnicas Clave

### 1. **MVC Monolítico vs Microservicios**

**Decisión:** Monolítico MVC

**Justificación:**
- **Complejidad**: Clínica pequeña (1-3 doctores inicialmente)
- **Costo operacional**: Microservicios requiere DevOps expertise
- **Mantenimiento**: Una única codebase es simpler
- **Deployment**: Un single `git push` deploya todo

**Punto de inflexión para refactor:**
- > 100 citas simultáneas por día
- > 10 doctores en múltiples sucursales
- > 5 PM para mantenimiento

---

### 2. **PDO vs ORM (Eloquent, Doctrine)**

**Decisión:** PDO nativo sin ORM

**Justificación:**
```
Ventajas:
✓ Cero overhead (ORM agrega 10-20% latencia)
✓ Control total sobre queries (optimización fácil)
✓ Prepared statements nativas (seguridad sin abstracciones)
✓ Menos dependencias (composer packages)

Desventajas:
✗ Más código boilerplate
✗ Migraciones manuales

Cuándo cambiar:
- Si tablas > 20, cambiar a Doctrine
- Si queries muy complejas, cambiar a Dapper (C#) o Django ORM
```

---

### 3. **Sesiones PHP vs JWT**

**Decisión:** Sesiones PHP (actual)

**Justificación:**
```
Actual (Sesiones):
- Simple para monolítico
- Historial de logout fácil
- CSRF protection nativa

Futuro (JWT):
- Requerido si escalamos a API
- Stateless (mejor para load balancing)
- Compatible con múltiples dominios
```

**Plan de migración:**
```php
// Fase 2: Mantener ambos
// if (request.header('Authorization')) {
//   use JWT
// } else {
//   use $_SESSION
// }
```

---

### 4. **Base de Datos Relacional vs NoSQL**

**Decisión:** MySQL Relacional

**Justificación:**
```
SQL:
✓ Integridad referencial (doctor_id existente garantizado)
✓ ACID compliance (citas atómicas)
✓ Queries complejas (JOIN doctor + citas + disponibilidad)

NoSQL (MongoDB):
✗ Citas duplicadas posibles (sin PK)
✗ Inconsistencias (sin TX)
✓ Escalabilidad horizontal (no necesaria aún)
```

---

### 5. **Validación HTML5 vs JavaScript Complejo**

**Decisión:** HTML5 Input Validation + Revalidación Backend

**Justificación:**
```php
<!-- Frontend (HTML5) -->
<input type="email" required>
<input type="date" required>

// Backend (PHP)
$email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
$date = DateTime::createFromFormat('Y-m-d', $_POST['fecha']);
```

**Por qué:**
- HTML5: Browsers entienden, soporte móvil
- Backend: Seguridad (cliente no es confiable)
- Resultado: UX rápida + seguridad garantizada

---

---

## Autor

**Nombre:** [Tu Nombre]  
**Puesto:** Senior Backend Engineer  
**Stack:** PHP 7+, MySQL, JavaScript (Vanilla)  
**Ubicación:** [Tu Ciudad, País]

### Contacto
- **Email:** [tu-email@example.com]
- **GitHub:** [github.com/tu-usuario](https://github.com/tu-usuario)
- **LinkedIn:** [linkedin.com/in/tu-perfil](https://linkedin.com/in/tu-perfil)
- **Portfolio:** [tu-portfolio.com](https://tu-portfolio.com)

### Sobre el Proyecto
Este proyecto fue desarrollado como demostración de:
- **Arquitectura robusta**: Patrón MVC implementado correctamente
- **Seguridad**: Sanitización, hashing, prepared statements
- **Escalabilidad**: Estructura modular lista para evolución
- **Calidad de código**: Naming conventions, error handling, documentación

Disponible para:
- 💼 Posiciones **Backend Senior** en equipos ágiles
- 🏗️ Proyectos de **arquitectura y refactoring** de sistemas legacy
- 📚 Mentoría en **patrones de diseño** y **seguridad web**

---

## Licencia

Este proyecto está bajo licencia **MIT**. Siéntete libre de usarlo, modificarlo y distribuirlo según los términos de la licencia.

```
MIT License

Copyright (c) 2024 [Tu Nombre]

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction...
```

---

## Contribuciones

Las contribuciones son bienvenidas. Para cambios mayores:

1. Fork el repositorio
2. Crea una rama: `git checkout -b feature/AmazingFeature`
3. Commit tus cambios: `git commit -m 'Add AmazingFeature'`
4. Push a la rama: `git push origin feature/AmazingFeature`
5. Abre un Pull Request

Se requiere:
- [ ] Tests unitarios
- [ ] Documentación actualizada
- [ ] Code review aprobado

---

## FAQ

### ¿Cómo cambio la contraseña de un doctor?
```sql
UPDATE doctor 
SET contrasena = PASSWORD('nueva_contraseña')
WHERE doctor_id = 1;
```

### ¿Cómo exporto un backup de la BD?
```bash
mysqldump -u root -p clinica > backup_clinica_2024.sql
```

### ¿Qué pasa si se cae Apache?
Se pierden las sesiones PHP activas. Usuarios deben re-logearse. 
Usa Redis para sesiones persistentes en Fase 2.

### ¿Cómo hago que funcione en HTTPS?
```
1. Obtener certificado SSL (Let's Encrypt gratuito)
2. Configurar Apache en C:\xampp\apache\conf\httpd.conf
3. Cambiar todas las URLs de http:// a https://
```

---

**Última actualización:** Febrero 2026  
**Versión:** 1.0.0 (MVP)  
**Status:** En producción ✅

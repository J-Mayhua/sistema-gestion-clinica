<?php
session_start();

// Verificar que el usuario esté logueado
if (!isset($_SESSION['usuario']) || !isset($_SESSION['usuario_id'])) {
    header("Location: login_register.php");
    exit();
}

// Obtener doctor_id y fecha desde la URL (si vienen)
$doctor_id = isset($_GET['doctor_id']) ? intval($_GET['doctor_id']) : null;
$fecha = isset($_GET['fecha']) ? $_GET['fecha'] : null;

// Cargar doctores
require_once __DIR__ . '/../../controllers/DoctorController.php';
$doctorController = new DoctorController();
$doctors = $doctorController->getDoctors(); // Asegúrate de que este método devuelva todos los doctores

// Cargar mensaje de sesión (éxito o error)
$message = '';
$messageType = '';

if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    $messageType = $_SESSION['message_type'];
    unset($_SESSION['message']);
    unset($_SESSION['message_type']);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Solicitar Cita</title>
    <link rel="stylesheet" href="../../assets/css/formulario.css">
    <link rel="stylesheet" href="../../assets/css/patient_styles.css">
    <style>
        .message {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
<header>
    <div class="logo">HappyDent</div>
    <div class="user-actions">
        <a href="logout.php">SALIR</a>
    </div>
</header>
<main>
    <div class="column left">
        <img src="<?php echo htmlspecialchars($_SESSION['profile_image'] ?? '../../assets/images/tarjeta.jpg'); ?>" alt="Foto de perfil" class="profile-image">
        <a href="patient_dashboard.php" class="action-button">INICIO</a>
        <a href="patient_appointment_history.php" class="action-button">MIS CITAS</a>
    </div>
    <form action="store_cita.php" method="post" class="appointment-form">
        <h2>Formulario de Solicitud de Cita</h2>
        <?php if ($message): ?>
            <div class="message <?= $messageType ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <input type="hidden" name="patient_id" value="<?= htmlspecialchars($_SESSION['usuario_id']) ?>">
        <input type="hidden" name="estado" value="Pendiente">

        <div class="form-group">
            <label for="nombre_completo">Nombre Completo:</label>
            <input type="text" id="nombre_completo" name="insertar_nombre"
                   value="<?= htmlspecialchars($_SESSION['usuario_nombre']) ?>" readonly>
        </div>

        <div class="form-group">
            <label for="dni">DNI:</label>
            <input type="text" id="dni" name="dni" required>
        </div>

        <div class="form-group">
            <label for="fecha_nacimiento">Fecha de Nacimiento:</label>
            <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" required>
        </div>

        <div class="form-group">
            <label for="sexo">Sexo:</label>
            <select id="sexo" name="sexo" required>
                <option value="M">Masculino</option>
                <option value="F">Femenino</option>
            </select>
        </div>

        <div class="form-group">
            <label for="direccion">Dirección:</label>
            <input type="text" id="direccion" name="direccion" required>
        </div>

        <div class="form-group">
            <label for="telefono">Teléfono:</label>
            <input type="text" id="telefono" name="telefono" required>
        </div>

        <div class="form-group">
            <label for="especialidad">Especialidad a la que quiera solicitar:</label>
            <input type="text" id="especialidad" name="especialidad" required>
        </div>

        <div class="form-group">
            <label for="doctor_id">Seleccionar Doctor:</label>
            <select id="doctor_id" name="doctor_id" required>
                <?php foreach ($doctors as $doctor): ?>
                    <option value="<?= htmlspecialchars($doctor['doctor_id']) ?>"
                        <?= $doctor['doctor_id'] == $doctor_id ? 'selected' : '' ?>>
                        <?= htmlspecialchars($doctor['nombres'] . ' ' . $doctor['apellidos']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <?php if ($fecha): ?>
            <input type="hidden" name="fecha" value="<?= htmlspecialchars($fecha) ?>">
            <div class="form-group">
                <label>Fecha seleccionada:</label>
                <input type="text" value="<?= htmlspecialchars($fecha) ?>" readonly>
            </div>
        <?php endif; ?>

        <div class="form-group">
            <input type="submit" name="request_appointment" value="Solicitar Cita" class="submit-btn">
        </div>
    </form>
</main>
</body>
</html>
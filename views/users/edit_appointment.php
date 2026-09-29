<?php
session_start();
if (!isset($_SESSION['doctor_id'])) {
    header('Location: ../doctor/login.php');
    exit();
}

require_once __DIR__ . '/../../controllers/DoctorController.php';
require_once __DIR__ . '/../../config/csrf.php';
$doctorController = new DoctorController();
$appointmentId = filter_var($_POST['cita_id'] ?? $_GET['id'] ?? null, FILTER_VALIDATE_INT);
if (!$appointmentId) {
    http_response_code(400);
    exit('Cita no válida.');
}

$appointment = $doctorController->getAppointmentById($appointmentId);
if (!$appointment || (int)$appointment['doctor_id'] !== (int)$_SESSION['doctor_id']) {
    http_response_code(404);
    exit('Cita no encontrada.');
}
if (!in_array($appointment['estado'], ['Pendiente', 'Confirmada'], true)) {
    http_response_code(403);
    exit('Esta cita ya no se puede modificar.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validarCsrf($_POST['csrf_token'] ?? null)) {
        http_response_code(403);
        exit('Solicitud no válida.');
    }

    $required = ['dni', 'fecha_nacimiento', 'sexo', 'direccion', 'telefono', 'especialidad'];
    foreach ($required as $field) {
        if (!isset($_POST[$field]) || trim((string)$_POST[$field]) === '') {
            http_response_code(400);
            exit('Completa los datos requeridos.');
        }
    }
    $birthDate = DateTime::createFromFormat('!Y-m-d', $_POST['fecha_nacimiento']);
    if (!$birthDate || $birthDate->format('Y-m-d') !== $_POST['fecha_nacimiento']
        || $birthDate > new DateTime('today')
        || !in_array($_POST['sexo'], ['M', 'F'], true)
        || strlen(trim($_POST['dni'])) > 20
        || strlen(trim($_POST['direccion'])) > 255
        || strlen(trim($_POST['telefono'])) > 20
        || strlen(trim($_POST['especialidad'])) > 100) {
        http_response_code(400);
        exit('Datos no válidos.');
    }

    $updated = $doctorController->updateAppointmentForDoctor($appointmentId, (int)$_SESSION['doctor_id'], [
        'dni' => trim($_POST['dni']),
        'fecha_nacimiento' => $_POST['fecha_nacimiento'],
        'sexo' => $_POST['sexo'],
        'direccion' => trim($_POST['direccion']),
        'telefono' => trim($_POST['telefono']),
        'especialidad' => trim($_POST['especialidad']),
        'estado' => $appointment['estado'],
    ]);
    if ($updated) {
        header('Location: ../doctor/doctor_appointments.php');
        exit();
    }
    $error = 'No se pudo actualizar la cita.';
}

$csrfToken = csrfToken();
$doctor = $doctorController->getDoctorById((int)$appointment['doctor_id']);
function escapar($value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar cita</title>
    <link rel="stylesheet" href="../../assets/css/doctor_styles.css">
</head>
<body>
    <main>
        <h1>Editar cita</h1>
        <?php if (isset($error)): ?><p><?php echo escapar($error); ?></p><?php endif; ?>
        <form method="post">
            <input type="hidden" name="csrf_token" value="<?php echo escapar($csrfToken); ?>">
            <input type="hidden" name="cita_id" value="<?php echo (int)$appointment['cita_id']; ?>">
            <label>Paciente <input value="<?php echo escapar($appointment['insertar_nombre']); ?>" readonly></label>
            <label>DNI <input name="dni" maxlength="20" value="<?php echo escapar($appointment['dni']); ?>" required></label>
            <label>Fecha de nacimiento <input type="date" name="fecha_nacimiento" value="<?php echo escapar($appointment['fecha_nacimiento']); ?>" required></label>
            <label>Sexo
                <select name="sexo" required>
                    <option value="M" <?php echo $appointment['sexo'] === 'M' ? 'selected' : ''; ?>>Masculino</option>
                    <option value="F" <?php echo $appointment['sexo'] === 'F' ? 'selected' : ''; ?>>Femenino</option>
                </select>
            </label>
            <label>Dirección <input name="direccion" maxlength="255" value="<?php echo escapar($appointment['direccion']); ?>" required></label>
            <label>Teléfono <input name="telefono" maxlength="20" value="<?php echo escapar($appointment['telefono']); ?>" required></label>
            <label>Especialidad <input name="especialidad" maxlength="100" value="<?php echo escapar($appointment['especialidad']); ?>" required></label>
            <label>Doctor <input value="<?php echo escapar(trim(($doctor['nombres'] ?? '') . ' ' . ($doctor['apellidos'] ?? ''))); ?>" readonly></label>
            <button type="submit">Guardar cambios</button>
            <a href="../doctor/doctor_appointments.php">Volver</a>
        </form>
    </main>
</body>
</html>

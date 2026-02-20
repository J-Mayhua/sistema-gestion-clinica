<?php
session_start();
if (!isset($_SESSION['usuario']) || !isset($_SESSION['usuario_id'])) {
    header("Location: login_register.php");
    exit();
}

require_once __DIR__ . '/../../controllers/UserController.php';

$userController = new UserController();

if (!isset($_GET['id'])) {
    header("Location: patient_appointment_history.php");
    exit();
}

$appointment = $userController->getAppointmentByIddd($_GET['id']);

// Verifica que la cita pertenezca al usuario actual
if ($appointment['usuario_id'] != $_SESSION['usuario_id']) {
    header("Location: patient_appointment_history.php");
    exit();
}

// Aquí iría la lógica para cancelar la cita
$result = $userController->cancelAppointment($_GET['id']);

if ($result) {
    $message = "Cita cancelada exitosamente.";
} else {
    $message = "Error al cancelar la cita.";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cancelar Cita</title>
    <link rel="stylesheet" href="../../assets/css/patient_styles.css">
</head>
<body>
    <header>
        <h1>Cancelar Cita</h1>
    </header>
    <main>
        <section>
            <h2><?php echo $message; ?></h2>
            <a href="patient_appointment_history.php">Volver al Historial de Citas</a>
        </section>
    </main>
</body>
</html>
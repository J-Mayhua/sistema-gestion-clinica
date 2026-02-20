<?php
session_start();
if (!isset($_SESSION['usuario']) || !isset($_SESSION['usuario_id'])) {
    header("Location: login_register.php");
    exit();
}

require_once __DIR__ . '/../../controllers/UserController.php';

$userController = new UserController();
$appointments = $userController->getPatientAppointmentsByIdd($_SESSION['usuario_id']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Historial de Citas</title>
    <link rel="stylesheet" href="../../assets/css/patient_styles.css">
    <link rel="stylesheet" href="../../assets/css/lista.css">
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
        <a href="patient_request_form.php" class="action-button">REALIZAR CITA</a>
    </div>
    <main>
        <section>
            <h2>Mis Citas</h2>
            <?php if (empty($appointments)): ?>
                <p class="empty-message">No tienes citas registradas.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Fecha de Solicitud</th>
                            <th>Especialidad</th>
                            <th>Doctor</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($appointments as $appointment): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($appointment['fecha_solicitud']); ?></td>
                                <td><?php echo htmlspecialchars($appointment['especialidad']); ?></td>
                                <td><?php echo htmlspecialchars($appointment['doctor_id']); ?></td>

                                <td class="status-<?php echo strtolower($appointment['estado'] ?? 'pendiente'); ?>">
                                    <?php echo htmlspecialchars($appointment['estado'] ?? 'Pendiente'); ?>
                                </td>
                                <td>
                                    <a href="view_appointment.php?id=<?php echo $appointment['cita_id']; ?>">Ver Detalles</a>
                                    <?php if (($appointment['estado'] ?? 'Pendiente') == 'Pendiente'): ?>
                                        <a href="cancel_appointment.php?id=<?php echo $appointment['cita_id']; ?>" onclick="return confirm('¿Estás seguro de que quieres cancelar esta cita?');" class="cancel-link">Cancelar</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>

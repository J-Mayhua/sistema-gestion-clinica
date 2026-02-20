<?php
session_start();

if (!isset($_SESSION['doctor_id'])) {
    header("Location: ../login_register.php");
    exit();
}

require_once __DIR__ . '/../../controllers/DoctorController.php';

$doctorController = new DoctorController();

// Obtener información del doctor
$doctor_id = $_SESSION['doctor_id'];

// Obtener citas del doctor
$appointments = $doctorController->getAppointmentsByDoctorId($doctor_id);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Citas</title>
    <link rel="stylesheet" href="../../assets/css/doctor_styles.css">
    <link rel="stylesheet" href="../../assets/css/docestilos.css">
</head>
<body>
    <header>
        <h1>Historial de Citas</h1>
        <nav>
            <a href="../doctor/dashboard.php"><i class="fas fa-home"></i> INICIO</a>
            <a href="../users/patient_appointments.php"><i class="fas fa-list"></i> Historia de Citas del Paciente</a>
            <a href="../doctor/logout.php"><i class="fas fa-sign-out-alt"></i> SALIR</a>
        </nav>
    </header>
    <main>
        <section>
            <h2>Lista de Citas</h2>
            <?php if (empty($appointments)): ?>
                <p class="no-appointments">No hay citas.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Nombre del Paciente</th>
                            <th>DNI</th>
                            <th>Fecha de Nacimiento</th>
                            <th>Sexo</th>
                            <th>Dirección</th>
                            <th>Teléfono</th>
                            <th>Especialidad</th>
                            <th>ID del Doctor</th>
                            <th>Fecha de Solicitud</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($appointments as $appointment): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($appointment['insertar_nombre']); ?></td>
                                <td><?php echo htmlspecialchars($appointment['dni']); ?></td>
                                <td><?php echo htmlspecialchars($appointment['fecha_nacimiento']); ?></td>
                                <td><?php echo htmlspecialchars($appointment['sexo']); ?></td>
                                <td><?php echo htmlspecialchars($appointment['direccion']); ?></td>
                                <td><?php echo htmlspecialchars($appointment['telefono']); ?></td>
                                <td><?php echo htmlspecialchars($appointment['especialidad']); ?></td>
                                <td><?php echo htmlspecialchars($appointment['doctor_id']); ?></td>
                                <td><?php echo htmlspecialchars($appointment['fecha_creacion']); ?></td>
                                <td>
                                    <form action="update_appointment_status.php" method="post">
                                        <input type="hidden" name="appointment_id" value="<?php echo htmlspecialchars($appointment['cita_id']); ?>">
                                        <select name="status" onchange="updateAppointmentStatus(this, <?php echo htmlspecialchars($appointment['cita_id']); ?>)">
                                            <option value="Pendiente" <?php echo ($appointment['estado'] == 'Pendiente') ? 'selected' : ''; ?>>Pendiente</option>
                                            <option value="Confirmada" <?php echo ($appointment['estado'] == 'Confirmada') ? 'selected' : ''; ?>>Confirmada</option>
                                            <option value="Cancelada" <?php echo ($appointment['estado'] == 'Cancelada') ? 'selected' : ''; ?>>Cancelada</option>
                                        </select>
                                    </form>
                                </td>
                                <td class="actions">
                                    <a href="edit_appointment.php?id=<?php echo htmlspecialchars($appointment['cita_id']); ?>">Editar</a>
                                    <a href="delete_appointment.php?id=<?php echo htmlspecialchars($appointment['cita_id']); ?>" onclick="return confirm('¿Estás seguro de que quieres eliminar esta cita?');">Eliminar</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </section>
    </main>
    <script>
        function updateAppointmentStatus(selectElement, appointmentId) {
            var status = selectElement.value;
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "update_appointment_status.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function() {
                if (this.readyState === XMLHttpRequest.DONE && this.status === 200) {
                    var response = JSON.parse(this.responseText);
                    if (response.success) {
                        alert("Estado actualizado correctamente a: " + status);
                    } else {
                        alert("Error al actualizar el estado: " + response.message);
                    }
                }
            }
            xhr.send("appointment_id=" + appointmentId + "&status=" + status);
        }
    </script>
</body>
</html>

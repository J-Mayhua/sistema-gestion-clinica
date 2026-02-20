<!-- views/users/doctor_appointments.php -->

<?php
session_start();

if (!isset($_SESSION['usuario']) || !isset($_SESSION['doctor_id'])) {
    header("Location: login_register.php");
    exit();
}

require_once __DIR__ . '/../../controllers/AppointmentController.php';

$appointmentController = new AppointmentController();
$appointments = $appointmentController->getDoctorAppointments($_SESSION['doctor_id']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Citas</title>
    <link rel="stylesheet" href="../../assets/css/doctor_styles.css">
</head>
<body>
    <header>
        <h1>Gestión de Citas</h1>
    </header>
    <main>
        <section>
            <h2>Todas las Citas</h2>
            <table border="1">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>DNI</th>
                        <th>Fecha de Nacimiento</th>
                        <th>Sexo</th>
                        <th>Dirección</th>
                        <th>Teléfono</th>
                        <th>Especialidad</th>
                        <th>ID del Paciente</th>
                        <th>ID del Doctor</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($appointments as $appointment): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($appointment['cita_id']); ?></td>
                        <td><?php echo htmlspecialchars($appointment['dni']); ?></td>
                        <td><?php echo htmlspecialchars($appointment['fecha_nacimiento']); ?></td>
                        <td><?php echo htmlspecialchars($appointment['sexo']); ?></td>
                        <td><?php echo htmlspecialchars($appointment['direccion']); ?></td>
                        <td><?php echo htmlspecialchars($appointment['telefono']); ?></td>
                        <td><?php echo htmlspecialchars($appointment['especialidad']); ?></td>
                        <td><?php echo htmlspecialchars($appointment['usuario_id']); ?></td>
                        <td><?php echo htmlspecialchars($appointment['doctor_id']); ?></td>
                        <td>
                            <form action="doctor_appointments.php" method="post" style="display:inline;">
                                <input type="hidden" name="cita_id" value="<?php echo htmlspecialchars($appointment['cita_id']); ?>">
                                <input type="submit" name="delete_appointment" value="Eliminar">
                            </form>
                            <button onclick="document.getElementById('updateModal<?php echo $appointment['cita_id']; ?>').style.display='block'">Modificar</button>

                            <!-- Modal para modificar -->
                            <div id="updateModal<?php echo $appointment['cita_id']; ?>" style="display:none;">
                                <form action="doctor_appointments.php" method="post">
                                    <input type="hidden" name="cita_id" value="<?php echo htmlspecialchars($appointment['cita_id']); ?>">
                                    <label for="dni">DNI:</label>
                                    <input type="text" name="dni" value="<?php echo htmlspecialchars($appointment['dni']); ?>" required>
                                    <label for="fecha_nacimiento">Fecha de Nacimiento:</label>
                                    <input type="date" name="fecha_nacimiento" value="<?php echo htmlspecialchars($appointment['fecha_nacimiento']); ?>" required>
                                    <label for="sexo">Sexo:</label>
                                    <select name="sexo" required>
                                        <option value="M" <?php if($appointment['sexo'] == 'M') echo 'selected'; ?>>Masculino</option>
                                        <option value="F" <?php if($appointment['sexo'] == 'F') echo 'selected'; ?>>Femenino</option>
                                    </select>
                                    <label for="direccion">Dirección:</label>
                                    <input type="text" name="direccion" value="<?php echo htmlspecialchars($appointment['direccion']); ?>" required>
                                    <label for="telefono">Teléfono:</label>
                                    <input type="text" name="telefono" value="<?php echo htmlspecialchars($appointment['telefono']); ?>" required>
                                    <label for="especialidad">Especialidad:</label>
                                    <input type="text" name="especialidad" value="<?php echo htmlspecialchars($appointment['especialidad']); ?>" required>
                                    <label for="doctor_id">ID del Doctor:</label>
                                    <input type="text" name="doctor_id" value="<?php echo htmlspecialchars($appointment['doctor_id']); ?>" required>
                                    <input type="submit" name="update_appointment" value="Guardar Cambios">
                                </form>
                                <button onclick="document.getElementById('updateModal<?php echo $appointment['cita_id']; ?>').style.display='none'">Cancelar</button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
        <section>
            <h2>Agregar Nueva Cita</h2>
            <form action="doctor_appointments.php" method="post">
                <label for="dni">DNI:</label>
                <input type="text" name="dni" required>
                <label for="fecha_nacimiento">Fecha de Nacimiento:</label>
                <input type="date" name="fecha_nacimiento" required>
                <label for="sexo">Sexo:</label>
                <select name="sexo" required>
                    <option value="M">Masculino</option>
                    <option value="F">Femenino</option>
                </select>
                <label for="direccion">Dirección:</label>
                <input type="text" name="direccion" required>
                <label for="telefono">Teléfono:</label>
                <input type="text" name="telefono" required>
                <label for="especialidad">Especialidad:</label>
                <input type="text" name="especialidad" required>
                <label for="usuario_id">ID del Paciente:</label>
                <input type="text" name="usuario_id" required>
                <label for="doctor_id">ID del Doctor:</label>
                <input type="text" name="doctor_id" required>
                <input type="submit" name="create_appointment" value="Agregar Cita">
            </form>
        </section>
    </main>
</body>
</html>

<?php
require_once '../../controllers/AppointmentController.php';
$controller = new AppointmentController();
$appointments = $controller->getAppointments();

if ($appointments === null) {
    $appointments = []; // Asegúrate de que $appointments sea un array si json_decode devuelve null
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Lista de Citas</title>
</head>
<body>
    <h1>Lista de Citas</h1>
    <table border="1">
        <tr>
            <th>DNI</th>
            <th>Fecha de Nacimiento</th>
            <th>Sexo</th>
            <th>Dirección</th>
            <th>Teléfono</th>
            <th>Especialidad</th>
        </tr>
        <?php foreach ($appointments as $appointment): ?>
        <tr>
            <td><?php echo htmlspecialchars($appointment['dni']); ?></td>
            <td><?php echo htmlspecialchars($appointment['fecha_nacimiento']); ?></td>
            <td><?php echo htmlspecialchars($appointment['sexo']); ?></td>
            <td><?php echo htmlspecialchars($appointment['direccion']); ?></td>
            <td><?php echo htmlspecialchars($appointment['telefono']); ?></td>
            <td><?php echo htmlspecialchars($appointment['especialidad']); ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>

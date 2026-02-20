<?php
require_once '../../controllers/DoctorController.php';
$controller = new DoctorController();
$doctors = $controller->getDoctors(); // Obtener datos como array

if ($doctors === null) {
    $doctors = []; // Asegúrate de que $doctors sea un array si json_decode devuelve null
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Lista de Doctores</title>
</head>
<body>
    <h1>Lista de Doctores</h1>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Correo</th>
            <th>Nombres</th>
            <th>Apellidos</th>
            <th>Especialidad</th>
            <th>Telefono</th>
            <th>Horario</th>
        </tr>
        <?php foreach ($doctors as $doctor): ?>
        <tr>
            <td><?php echo htmlspecialchars($doctor['doctor_id']); ?></td>
            <td><?php echo htmlspecialchars($doctor['correo']); ?></td>
            <td><?php echo htmlspecialchars($doctor['nombres']); ?></td>
            <td><?php echo htmlspecialchars($doctor['apellidos']); ?></td>
            <td><?php echo htmlspecialchars($doctor['especialidad']); ?></td>
            <td><?php echo htmlspecialchars($doctor['telefono']); ?></td>
            <td><?php echo htmlspecialchars($doctor['horario']); ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>


</html>

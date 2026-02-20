<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: login_register.php");
    exit();
}

require_once __DIR__ . '/../../controllers/DoctorController.php';

$doctorController = new DoctorController();
$patients = $doctorController->getPatients(); // Método que deberás implementar en tu controlador

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Pacientes</title>
    <link rel="stylesheet" href="../../assets/css/doctor_styles.css">
</head>
<body>
    <header>
        <h1>Lista de Pacientes</h1>
        <nav>
            <a href="dashboard.php">Inicio</a>
            <a href="doctor_calendar.php">Modificar Calendario</a>
            <a href="doctor_add_patient.php">Agregar Paciente</a>
            <a href="logout.php">Salir</a>
        </nav>
    </header>
    <main>
        <h2>Pacientes</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Correo</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($patients as $patient) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($patient['id']); ?></td>
                        <td><?php echo htmlspecialchars($patient['nombre']); ?></td>
                        <td><?php echo htmlspecialchars($patient['correo']); ?></td>
                        <td>
                            <a href="doctor_edit_patient.php?id=<?php echo htmlspecialchars($patient['id']); ?>">Modificar</a>
                            <a href="doctor_delete_patient.php?id=<?php echo htmlspecialchars($patient['id']); ?>" onclick="return confirm('¿Estás seguro de eliminar este paciente?');">Eliminar</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </main>
</body>
</html>

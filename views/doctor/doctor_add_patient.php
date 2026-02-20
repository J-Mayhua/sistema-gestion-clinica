<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: login_register.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Paciente</title>
    <link rel="stylesheet" href="../../assets/css/doctor_styles.css">
</head>
<body>
    <header>
        <h1>Agregar Paciente</h1>
        <nav>
            <a href="dashboard.php">Inicio</a>
            <a href="doctor_patient_list.php">Lista de Pacientes</a>
            <a href="doctor_calendar.php">Modificar Calendario</a>
            <a href="logout.php">Salir</a>
        </nav>
    </header>
    <main>
        <h2>Formulario de Registro de Paciente</h2>
        <form action="doctor_process_add_patient.php" method="post">
            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" required>

            <label for="correo">Correo Electrónico:</label>
            <input type="email" id="correo" name="correo" required>

            <label for="telefono">Teléfono:</label>
            <input type="text" id="telefono" name="telefono" required>

            <input type="submit" value="Agregar Paciente">
        </form>
    </main>
</body>
</html>

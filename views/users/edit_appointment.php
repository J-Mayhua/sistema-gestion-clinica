<?php
require_once __DIR__ . '/../../controllers/UserController.php';

$userController = new UserController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $appointmentData = [
        'dni' => $_POST['dni'],
        'fecha_nacimiento' => $_POST['fecha_nacimiento'],
        'sexo' => $_POST['sexo'],
        'direccion' => $_POST['direccion'],
        'telefono' => $_POST['telefono'],
        'especialidad' => $_POST['especialidad'],
        'doctor_id' => $_POST['doctor_id']
        // No incluyas 'insertar_nombre' aquí
    ];

    if ($userController->updateAppointment($_POST['cita_id'], $appointmentData)) {
        header("Location: patient_appointments.php?message=Cita actualizada con éxito");
        exit();
    } else {
        $error = "Error al actualizar la cita";
    }
} elseif (isset($_GET['id'])) {
    $appointment = $userController->getAppointmentById($_GET['id']);
} else {
    header("Location: patient_appointments.php?message=ID de cita no proporcionado");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Cita</title>
    <style>
        @import url("https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap");

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Poppins", sans-serif;
        }

        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background: url('../../assets/images/DOC1.jpg') no-repeat center center fixed;
            background-size: cover;
            color: #333;
            justify-content: center;
            align-items: center;
        }

        header {
            background: rgba(0, 0, 0, 0.8);
            color: #fff;
            padding: 20px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
        }

        header h1 {
            font-size: 2em;
            margin-bottom: 10px;
        }

        main {
            flex: 1;
            padding: 30px;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            width: 100%;
            margin: 20px;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            margin-bottom: 5px;
            font-weight: 600;
        }

        input[type="text"],
        input[type="date"],
        select {
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1em;
        }

        .form-actions {
            display: flex;
            justify-content: space-between;
        }

        input[type="submit"], 
        .back-button {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: #fff;
            border: none;
            border-radius: 5px;
            font-size: 1em;
            cursor: pointer;
            transition: background-color 0.3s ease;
            text-decoration: none;
            text-align: center;
        }

        input[type="submit"]:hover,
        .back-button:hover {
            background-color: #45a049;
        }

        .error {
            color: red;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <header>
        <h1>Editar Cita</h1>
    </header>
    <main>
        <?php if (isset($error)): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <form method="POST">
            <input type="hidden" name="cita_id" value="<?php echo htmlspecialchars($appointment['cita_id']); ?>">
            <label for="nombre_paciente">Nombre del Paciente:</label>
            <input type="text" id="nombre_paciente" name="nombre_paciente" value="<?php echo htmlspecialchars($appointment['insertar_nombre']); ?>" readonly>
            <label for="dni">DNI:</label>
            <input type="text" id="dni" name="dni" value="<?php echo htmlspecialchars($appointment['dni']); ?>" required>
            <label for="fecha_nacimiento">Fecha de Nacimiento:</label>
            <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" value="<?php echo htmlspecialchars($appointment['fecha_nacimiento']); ?>" required>
            <label for="sexo">Sexo:</label>
            <input type="text" id="sexo" name="sexo" value="<?php echo htmlspecialchars($appointment['sexo']); ?>" required>
            <label for="direccion">Dirección:</label>
            <input type="text" id="direccion" name="direccion" value="<?php echo htmlspecialchars($appointment['direccion']); ?>" required>
            <label for="telefono">Teléfono:</label>
            <input type="text" id="telefono" name="telefono" value="<?php echo htmlspecialchars($appointment['telefono']); ?>" required>
            <label for="especialidad">Especialidad:</label>
            <input type="text" id="especialidad" name="especialidad" value="<?php echo htmlspecialchars($appointment['especialidad']); ?>" required>
            <label for="doctor_id">ID del Doctor:</label>
            <input type="text" id="doctor_id" name="doctor_id" value="<?php echo htmlspecialchars($appointment['doctor_id']); ?>" required>
            <div class="form-actions">
                <input type="submit" value="Actualizar Cita">
                <a href="patient_appointments.php" class="back-button">Volver Atrás</a>
            </div>
        </form>
    </main>
</body>
</html>

<?php
session_start();
if (!isset($_SESSION['usuario']) || !isset($_SESSION['usuario_id'])) {
    header("Location: login_register.php");
    exit();
}

require_once  '../../controllers/UserController.php';

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
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles de la Cita</title>
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
        }

        header {
            background: rgba(0, 0, 0, 0.8);
            color: #fff;
            padding: 20px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
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
            margin: 20px;
            max-width: 800px;
            margin: 40px auto;
        }

        section {
            margin-bottom: 20px;
        }

        section h2 {
            font-size: 1.8em;
            margin-bottom: 15px;
            color: #333;
        }

        p {
            margin-bottom: 10px;
            font-size: 1.1em;
        }

        strong {
            font-weight: 600;
        }

        a {
            display: inline-block;
            margin-top: 15px;
            padding: 10px 20px;
            color: #fff;
            background-color: #4CAF50;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        a:hover {
            background-color: #45a049;
        }

        a.cancel {
            background-color: #f44336;
        }

        a.cancel:hover {
            background-color: #d32f2f;
        }
    </style>
</head>
<body>
    <header>
        <h1>Detalles de la Cita</h1>
    </header>
    <main>
        <section>
            <h2>Información de la Cita</h2>
            <p><strong>Fecha de Solicitud:</strong> <?php echo isset($appointment['fecha_solicitud']) ? htmlspecialchars($appointment['fecha_solicitud']) : 'No disponible'; ?></p>
            <p><strong>Especialidad:</strong> <?php echo htmlspecialchars($appointment['especialidad']); ?></p>
            <p><strong>Doctor:</strong> <?php echo htmlspecialchars($appointment['doctor_id']); ?></p>
            <p><strong>Estado:</strong> <?php echo htmlspecialchars($appointment['estado'] ?? 'Pendiente'); ?></p>
            <p><strong>DNI:</strong> <?php echo htmlspecialchars($appointment['dni']); ?></p>
            <p><strong>Fecha de Nacimiento:</strong> <?php echo htmlspecialchars($appointment['fecha_nacimiento']); ?></p>
            <p><strong>Sexo:</strong> <?php echo htmlspecialchars($appointment['sexo']); ?></p>
            <p><strong>Dirección:</strong> <?php echo htmlspecialchars($appointment['direccion']); ?></p>
            <p><strong>Teléfono:</strong> <?php echo htmlspecialchars($appointment['telefono']); ?></p>

            <?php if (($appointment['estado'] ?? 'Pendiente') == 'Pendiente'): ?>
                <a href="cancel_appointment.php?id=<?php echo $appointment['cita_id']; ?>" onclick="return confirm('¿Estás seguro de que quieres cancelar esta cita?');" class="cancel">Cancelar Cita</a>
            <?php endif; ?>

            <a href="patient_appointment_history.php">Volver al Historial de Citas</a>
        </section>
    </main>
</body>
</html>

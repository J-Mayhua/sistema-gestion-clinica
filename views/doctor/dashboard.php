<?php
session_start();
if (!isset($_SESSION['doctor_id'])) {
    header("Location: login.php");
    exit();
}

$doctor_name = $_SESSION['doctor_name'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Dashboard</title>
    <link rel="stylesheet" href="../../assets/css/docestilos.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <header>
        <h1>Bienvenido, Dr. <?php echo htmlspecialchars($doctor_name); ?></h1>
        <nav>
            <a href="add_patient.php"><i class="fas fa-user-plus"></i> Agregar Paciente</a>
            
             <!-- <a href="dashboard.php?action=calendar"><i class="fas fa-calendar-alt"></i> Modificar Calendario</a>-->
            <a href="../users/patient_appointments.php"><i class="fas fa-list"></i> Historia de cital del paciente</a>
            <a href="<?= '/clinica1/controllers/DisponibilidadController.php' ?>">Gestionar Horarios</a>
            <a href="logout.php"><i class="fas fa-lis"></i> SALIR</a>
        </nav>
    </header>
    <main>
        <section class="welcome-section">
            <h2>Especialidades Médicas</h2>
            <div class="specialties-grid">
                <div class="specialty">
                    <img src="../../assets/images/protesis.png" alt="Especialidad 1">
                    <h3>Protesis</h3>
                </div>
                <div class="specialty">
                    <img src="../../assets/images/ortodoncia.jpg" alt="Especialidad 2">
                    <h3>Ortodoncia</h3>
                </div>
                <div class="specialty">
                    <img src="../../assets/images/exodoncia.jpg" alt="Especialidad 3">
                    <h3>Exodoncia</h3>
                </div>
                <div class="specialty">
                    <img src="../../assets/images/endodoncia.jfif" alt="Especialidad 4">
                    <h3>Endodoncia</h3>
                </div>
            </div>
        </section>
    </main>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
</body>
</html>

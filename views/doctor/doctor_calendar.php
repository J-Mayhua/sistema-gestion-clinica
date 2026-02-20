<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: login_register.php");
    exit();
}

require_once __DIR__ . '/../../controllers/DoctorController.php';
require_once __DIR__ . '/../../models/Appointment.php';

$doctorController = new DoctorController();
$appointmentModel = new Appointment();

$availability = $appointmentModel->getDoctorAppointments($_SESSION['usuario_id']);

$daysOccupied = [];
foreach ($availability as $entry) {
    $daysOccupied[] = $entry['fecha'];
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificar Calendario</title>
    <link rel="stylesheet" href="../../assets/css/doctor_styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/5.10.1/main.min.css">
</head>
<body>
    <header>
        <h1>Modificar Calendario</h1>
        <nav>
            <a href="dashboard.php">Inicio</a>
            <a href="doctor_patient_list.php">Lista de Pacientes</a>
            <a href="doctor_add_patient.php">Agregar Paciente</a>
            <a href="logout.php">Salir</a>
        </nav>
    </header>
    <main>
        <h2>Calendario de Disponibilidad</h2>
        <div id="calendar"></div>
    </main>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/5.10.1/main.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');

            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                selectable: true,
                events: <?php echo json_encode(array_map(function($date) {
                    return [
                        'title' => 'Ocupado',
                        'start' => $date,
                        'backgroundColor' => 'red'
                    ];
                }, $daysOccupied)); ?>,
                dateClick: function(info) {
                    // Implementa lógica para agregar/modificar disponibilidad
                    var isOccupied = info.event ? true : false;
                    if (!isOccupied) {
                        var date = info.dateStr;
                        var time = prompt('Selecciona la hora disponible (HH:mm):');
                        if (time) {
                            // Envía la fecha y hora al servidor para actualizar la disponibilidad
                            // Puedes usar AJAX para enviar la solicitud
                        }
                    }
                }
            });

            calendar.render();
        });
    </script>
</body>
</html>

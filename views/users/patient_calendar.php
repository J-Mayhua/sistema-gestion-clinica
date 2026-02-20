<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Dashboard</title>
    <link rel="stylesheet" href="../../assets/css/patient_styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/5.10.1/main.min.css">
</head>
<body>
    <header>
        <h1>Bienvenido, <?php echo htmlspecialchars($patient['nombre_completo']); ?></h1>
        <nav>
            <a href="patient_dashboard.php?action=viewAppointments">Ver Citas</a>
            <a href="patient_dashboard.php?action=calendar">Solicitar Cita</a>
            <a href="logout.php">Salir</a>
        </nav>
    </header>
    <main>
        <section>
            <h2>Información del Paciente</h2>
            <p><strong>Nombre:</strong> <?php echo htmlspecialchars($patient['nombre_completo']); ?></p>
            <p><strong>Correo Electrónico:</strong> <?php echo htmlspecialchars($patient['correo_electronico']); ?></p>
        </section>
        <section>
            <?php if ($action === 'calendar') { ?>
                <h2>Calendario</h2>
                <div id="calendar"></div>
                <h2>Solicitar Cita</h2>
                <form id="request-form" action="patient_request_form.php" method="post" style="display: none;">
                    <input type="hidden" name="patient_id" value="<?php echo htmlspecialchars($_SESSION['usuario_id']); ?>">
                    <input type="hidden" id="selected-date" name="date">
                    <label for="time">Seleccionar Hora:</label>
                    <input type="time" id="time" name="time" required>
                    <input type="submit" name="request_appointment" value="Solicitar Cita">
                </form>
            <?php } elseif ($action === 'viewAppointments') { ?>
                <h2>Historial de Citas</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Hora</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($appointments as $appointment) { ?>
                            <tr>
                                <td><?php echo htmlspecialchars($appointment['fecha']); ?></td>
                                <td><?php echo htmlspecialchars($appointment['hora']); ?></td>
                                <td><?php echo htmlspecialchars($appointment['estado']); ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            <?php } ?>
        </section>
    </main>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/5.10.1/main.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');

            if (calendarEl) {
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
                        if (!info.event) { // Solo mostrar el formulario si el día no está ocupado
                            document.getElementById('selected-date').value = info.dateStr;
                            document.getElementById('request-form').style.display = 'block';
                        }
                    }
                });

                calendar.render();
            }
        });
    </script>
</body>
</html>

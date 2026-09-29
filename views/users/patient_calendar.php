<?php
session_start();

if (!isset($_SESSION['usuario']) || !isset($_SESSION['usuario_id'])) {
    header("Location: login_register.php");
    exit();
}

require_once __DIR__ . '/../../controllers/UserController.php';

$userController = new UserController();
$userId         = (int)$_SESSION['usuario_id'];
$patient        = $userController->getPatientInfo($userId);
$appointments   = $userController->getAppointmentsByUserId($userId);
$action         = $_GET['action'] ?? 'calendar';
$daysOccupied   = [];
?>
<?php require_once __DIR__ . '/../cabecera/cabecera_paciente.php'; ?>

<!-- FullCalendar CSS (específico de esta vista) -->
<link rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/5.10.1/main.min.css">

<main>
    <section>
        <h2>Información del Paciente</h2>
        <p><strong>Nombre:</strong>
            <?php echo htmlspecialchars($patient['nombre_completo'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
        <p><strong>Correo Electrónico:</strong>
            <?php echo htmlspecialchars($patient['correo_electronico'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
    </section>

    <section>
        <?php if ($action === 'calendar'): ?>
            <h2>Calendario</h2>
            <div id="calendar"></div>
            <h2>Solicitar Cita</h2>
            <form id="request-form" action="patient_request_form.php" method="post" style="display:none;">
                <input type="hidden" name="patient_id"
                       value="<?php echo htmlspecialchars((string)$_SESSION['usuario_id'], ENT_QUOTES, 'UTF-8'); ?>">
                <input type="hidden" id="selected-date" name="date">
                <label for="time">Seleccionar Hora:</label>
                <input type="time" id="time" name="time" required>
                <input type="submit" name="request_appointment" value="Solicitar Cita">
            </form>

        <?php elseif ($action === 'viewAppointments'): ?>
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
                    <?php foreach ($appointments as $appointment): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($appointment['fecha'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($appointment['hora']  ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($appointment['estado'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </section>
</main>

<!-- Scripts FullCalendar (específicos de esta vista) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/5.10.1/main.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var calendarEl = document.getElementById('calendar');
        if (calendarEl) {
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                selectable: true,
                events: <?php echo json_encode(array_map(function ($date) {
                    return [
                        'title'           => 'Ocupado',
                        'start'           => $date,
                        'backgroundColor' => 'red'
                    ];
                }, $daysOccupied)); ?>,
                dateClick: function (info) {
                    if (!info.event) {
                        document.getElementById('selected-date').value = info.dateStr;
                        document.getElementById('request-form').style.display = 'block';
                    }
                }
            });
            calendar.render();
        }
    });
</script>

<?php require_once __DIR__ . '/../cabecera/pie_paciente.php'; ?>
</body>
</html>

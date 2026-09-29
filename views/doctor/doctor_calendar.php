<?php
session_start();

if (!isset($_SESSION['doctor_id'])) {
    header("Location: login.php");
    exit();
}

require_once __DIR__ . '/../../controllers/DoctorController.php';

$doctorController = new DoctorController();
$events = $doctorController->getCalendarEvents((int)$_SESSION['doctor_id']);
?>
<?php require_once __DIR__ . '/../cabecera/cabecera_doctor.php'; ?>

<!-- FullCalendar CSS (específico de esta vista) -->
<link rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/5.10.1/main.min.css">

<main>
    <h2>Calendario de Disponibilidad</h2>
    <div id="calendar"></div>
</main>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/5.10.1/main.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var calendarEl = document.getElementById('calendar');
        if (calendarEl) {
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                events: <?php echo json_encode($events, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>
            });
            calendar.render();
        }
    });
</script>

<?php require_once __DIR__ . '/../cabecera/pie_paciente.php'; ?>
</body>
</html>

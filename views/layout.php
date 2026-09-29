<?php
$nombreVista = isset($viewFile) ? basename($viewFile) : '';

$scriptsPorVista = [
    'login_register.php' => ['acceso.js'],
    'patient_dashboard.php' => ['patient_dashboard.js'],
    'ver_horarios.php' => ['patient_calendar.js'],
    'doctor_dashboard.php' => ['doctor_dashboard.js'],
];
?>

<?php foreach ($scriptsPorVista[$nombreVista] ?? [] as $script): ?>
    <script
        src="/clinica/assets/js/<?= htmlspecialchars($script, ENT_QUOTES, 'UTF-8') ?>"
        defer
    ></script>
<?php endforeach; ?>

</body>
</html>

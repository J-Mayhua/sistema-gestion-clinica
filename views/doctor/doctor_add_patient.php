<?php
session_start();

// Corregido: el original usaba $_SESSION['usuario'] (variable del área de paciente).
// El área del doctor usa $_SESSION['doctor_id'].
if (!isset($_SESSION['doctor_id'])) {
    header("Location: login.php");
    exit();
}
?>
<?php require_once __DIR__ . '/../cabecera/cabecera_doctor.php'; ?>

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

<?php require_once __DIR__ . '/../cabecera/pie_paciente.php'; ?>
</body>
</html>

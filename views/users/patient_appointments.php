<?php
session_start();

if (!isset($_SESSION['usuario']) || !isset($_SESSION['usuario_id'])) {
    header("Location: login_register.php");
    exit();
}

require_once __DIR__ . '/../../controllers/UserController.php';
require_once __DIR__ . '/../../config/csrf.php';

$userController = new UserController();
$userId = (int)$_SESSION['usuario_id'];
$csrfToken = csrfToken();

$appointments = $userController->getAppointmentsByUserId($userId);
?>
<?php require_once __DIR__ . '/../cabecera/cabecera_paciente.php'; ?>

<main>
    <section>
        <h2>Lista de Citas</h2>
        <?php if (empty($appointments)): ?>
            <p class="no-appointments">No hay citas.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Nombre del Paciente</th>
                        <th>DNI</th>
                        <th>Fecha de Nacimiento</th>
                        <th>Sexo</th>
                        <th>Dirección</th>
                        <th>Teléfono</th>
                        <th>Especialidad</th>
                        <th>Doctor</th>
                        <th>Fecha de Solicitud</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($appointments as $appointment): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($appointment['insertar_nombre'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($appointment['dni']); ?></td>
                            <td><?php echo htmlspecialchars($appointment['fecha_nacimiento']); ?></td>
                            <td><?php echo htmlspecialchars($appointment['sexo']); ?></td>
                            <td><?php echo htmlspecialchars($appointment['direccion']); ?></td>
                            <td><?php echo htmlspecialchars($appointment['telefono']); ?></td>
                            <td><?php echo htmlspecialchars($appointment['especialidad']); ?></td>
                            <td><?php echo htmlspecialchars($appointment['nombre_doctor'] ?? 'Sin asignar', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($appointment['fecha_creacion']); ?></td>
                            <td><?php echo htmlspecialchars($appointment['estado'] ?? 'Pendiente', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td class="actions">
                                <a href="view_appointment.php?id=<?php echo (int)$appointment['cita_id']; ?>">Ver detalles</a>
                                <?php if (($appointment['estado'] ?? '') === 'Pendiente'): ?>
                                    <form action="cancel_appointment.php" method="post" style="display:inline;">
                                        <input type="hidden" name="csrf_token"
                                               value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
                                        <input type="hidden" name="id"
                                               value="<?php echo (int)$appointment['cita_id']; ?>">
                                        <button type="submit"
                                                onclick="return confirm('¿Cancelar esta cita?');">Cancelar</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </section>
</main>

<?php require_once __DIR__ . '/../cabecera/pie_paciente.php'; ?>
</body>
</html>

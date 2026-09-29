<?php
session_start();

if (!isset($_SESSION['doctor_id'])) {
    header('Location: login.php');
    exit();
}

require_once __DIR__ . '/../../controllers/DoctorController.php';

$doctorController = new DoctorController();
$patients = $doctorController->getPatientsByDoctorId(
    (int) $_SESSION['doctor_id']
);

$titulo_pagina_doctor = 'HappyDent — Lista de pacientes';
$css_pagina_doctor = '/clinica/assets/css/doctor_patient_list.css';

require_once __DIR__ . '/../cabecera/cabecera_doctor.php';
?>

<main class="doctor-pacientes-main">
    <div class="doctor-pacientes-contenedor">

        <div class="doctor-pacientes-intro">
            <span class="doctor-pacientes-etiqueta">
                <i class="fas fa-users" aria-hidden="true"></i>
                Área del doctor
            </span>

            <h1>Lista de pacientes</h1>

            <p>
                Consulta los pacientes asociados a tus citas y revisa
                cuántas citas tiene cada uno.
            </p>
        </div>

        <section
            class="doctor-pacientes-panel"
            aria-labelledby="doctor-pacientes-titulo"
        >
            <div class="doctor-pacientes-panel-encabezado">
                <div>
                    <span class="doctor-pacientes-subtitulo">
                        Tus pacientes
                    </span>

                    <h2 id="doctor-pacientes-titulo">
                        Pacientes registrados
                    </h2>
                </div>

                <span class="doctor-pacientes-contador">
                    <?= count($patients) ?>
                    <?= count($patients) === 1 ? 'paciente' : 'pacientes' ?>
                </span>
            </div>

            <?php if (empty($patients)): ?>
                <div class="doctor-pacientes-vacio">
                    <span
                        class="doctor-pacientes-vacio-icono"
                        aria-hidden="true"
                    >
                        <i class="fas fa-user-friends"></i>
                    </span>

                    <h3>Aún no hay pacientes para mostrar</h3>

                    <p>
                        Los pacientes asociados a tus citas aparecerán aquí.
                    </p>
                </div>
            <?php else: ?>
                <div
                    class="doctor-pacientes-tabla-contenedor"
                    role="region"
                    aria-label="Lista de pacientes; desplázate horizontalmente para ver todas las columnas"
                    tabindex="0"
                >
                    <table class="doctor-pacientes-tabla">
                        <thead>
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">Nombre</th>
                                <th scope="col">Correo</th>
                                <th scope="col">Citas</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($patients as $patient): ?>
                                <tr>
                                    <td>
                                        <?= (int) $patient['id'] ?>
                                    </td>

                                    <td class="doctor-pacientes-nombre">
                                        <?= htmlspecialchars(
                                            (string) ($patient['nombre'] ?? ''),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            (string) ($patient['correo'] ?? ''),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </td>

                                    <td>
                                        <span class="doctor-pacientes-citas">
                                            <?= (int) $patient['citas'] ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </section>

    </div>
</main>

<?php require_once __DIR__ . '/../cabecera/pie_paciente.php'; ?>

</body>
</html>

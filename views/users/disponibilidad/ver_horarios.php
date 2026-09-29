<?php
// views/users/disponibilidad/ver_horarios.php
session_start();

if (!class_exists('Disponibilidad')) {
    require_once __DIR__ . '/../../../models/Disponibilidad.php';
}

$disponibilidadModel = new Disponibilidad();
$horarios = $disponibilidadModel->getDisponiblesParaPacientes();

$meses = [
    1 => 'ENE', 2 => 'FEB', 3 => 'MAR', 4 => 'ABR',
    5 => 'MAY', 6 => 'JUN', 7 => 'JUL', 8 => 'AGO',
    9 => 'SEP', 10 => 'OCT', 11 => 'NOV', 12 => 'DIC',
];

$titulo_pagina_paciente = 'HappyDent — Horarios disponibles';
$css_pagina_paciente = '/clinica/assets/css/ver_horarios_paciente.css';

require_once __DIR__ . '/../../cabecera/cabecera_paciente.php';
?>

<main class="horarios-main">
    <div class="horarios-contenedor">

        <section class="horarios-hero" aria-labelledby="horarios-titulo">
            <div class="horarios-hero-texto">
                <span class="horarios-etiqueta">
                    <i class="fas fa-clock" aria-hidden="true"></i>
                    Tu espacio de salud dental
                </span>

                <h1 id="horarios-titulo">Horarios disponibles</h1>

                <p>
                    Elige el especialista y el horario que mejor se adapten a ti.
                </p>
            </div>

            <div class="horarios-hero-icono" aria-hidden="true">
                <i class="fas fa-calendar-alt"></i>
            </div>
        </section>

        <div
            id="mensajes"
            class="horarios-mensajes"
            role="status"
            aria-live="polite"
        ></div>

        <section class="horarios-panel" aria-labelledby="horarios-listado">
            <div class="horarios-panel-header">
                <div>
                    <span class="horarios-subtitulo">Organiza tu atención</span>
                    <h2 id="horarios-listado">Elige un horario</h2>
                </div>

                <span class="horarios-contador">
                    <?= count($horarios) ?>
                    <?= count($horarios) === 1 ? 'horario' : 'horarios' ?>
                </span>
            </div>

            <?php if (!empty($horarios)): ?>
                <div class="horarios-grid" id="horarios-container">
                    <?php foreach ($horarios as $horario): ?>
                        <?php
                        $doctorNombre = trim(
                            (string) ($horario['doctor_nombres'] ?? '') . ' ' .
                            (string) ($horario['doctor_apellidos'] ?? '')
                        );

                        $especialidad = (string) ($horario['especialidad'] ?? '');
                        $fecha = (string) ($horario['fecha'] ?? '');
                        $horaInicio = (string) ($horario['hora_inicio'] ?? '');
                        $horaFin = (string) ($horario['hora_fin'] ?? '');

                        $fechaTimestamp = strtotime($fecha);
                        $inicioTimestamp = strtotime($horaInicio);
                        $finTimestamp = strtotime($horaFin);

                        $dia = $fechaTimestamp !== false
                            ? date('d', $fechaTimestamp)
                            : '--';

                        $mesNumero = $fechaTimestamp !== false
                            ? (int) date('n', $fechaTimestamp)
                            : 0;

                        $mes = $meses[$mesNumero] ?? '---';

                        $fechaLegible = $fechaTimestamp !== false
                            ? date('d/m/Y', $fechaTimestamp)
                            : 'Fecha no disponible';

                        $inicioLegible = $inicioTimestamp !== false
                            ? date('H:i', $inicioTimestamp)
                            : '--:--';

                        $finLegible = $finTimestamp !== false
                            ? date('H:i', $finTimestamp)
                            : '--:--';
                        ?>

                        <article class="horario-card">
                            <div class="horario-card-superior">
                                <div class="horario-calendario" aria-hidden="true">
                                    <span class="horario-calendario-mes">
                                        <?= $mes ?>
                                    </span>
                                    <strong class="horario-calendario-dia">
                                        <?= $dia ?>
                                    </strong>
                                </div>

                                <div class="horario-card-info">
                                    <span class="horario-disponible">
                                        <span
                                            class="horario-disponible-punto"
                                            aria-hidden="true"
                                        ></span>
                                        Disponible
                                    </span>

                                    <h3>
                                        <?= htmlspecialchars(
                                            $doctorNombre !== ''
                                                ? 'Dr. ' . $doctorNombre
                                                : 'Doctor por confirmar',
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </h3>

                                    <span class="horario-especialidad">
                                        <?= htmlspecialchars(
                                            $especialidad,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </span>
                                </div>
                            </div>

                            <div class="horario-card-fecha">
                                <span>
                                    <i class="far fa-calendar-alt" aria-hidden="true"></i>
                                    <?= htmlspecialchars(
                                        $fechaLegible,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </span>

                                <span>
                                    <i class="far fa-clock" aria-hidden="true"></i>
                                    <?= htmlspecialchars(
                                        $inicioLegible . ' – ' . $finLegible,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </span>
                            </div>

                            <button
                                type="button"
                                class="horario-agendar"
                                data-disponibilidad-id="<?= (int) $horario['disponibilidad_id'] ?>"
                                aria-label="Solicitar cita con <?= htmlspecialchars(
                                    $doctorNombre !== ''
                                        ? $doctorNombre
                                        : 'el especialista',
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?> para el <?= htmlspecialchars(
                                    $fechaLegible,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?> a las <?= htmlspecialchars(
                                    $inicioLegible,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                            >
                                <i class="fas fa-calendar-plus" aria-hidden="true"></i>
                                Solicitar cita
                            </button>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="horarios-vacio">
                    <span class="horarios-vacio-icono" aria-hidden="true">
                        <i class="far fa-calendar"></i>
                    </span>

                    <h3>No hay horarios disponibles</h3>

                    <p>
                        Vuelve a consultar más tarde o contacta con la clínica.
                    </p>
                </div>
            <?php endif; ?>
        </section>
    </div>

    <dialog
        id="confirmar-cita"
        class="horarios-dialog"
        aria-labelledby="confirmar-cita-titulo"
        aria-describedby="confirmar-cita-descripcion"
    >
        <div class="horarios-dialog-icono" aria-hidden="true">
            <i class="fas fa-calendar-check"></i>
        </div>

        <h2 id="confirmar-cita-titulo">¿Continuamos con tu cita?</h2>

        <p id="confirmar-cita-descripcion">
            Te llevaremos al formulario para completar tu solicitud.
            El horario aún no quedará reservado.
        </p>

        <div class="horarios-dialog-acciones">
            <button
                type="button"
                id="cerrar-confirmacion"
                class="horarios-dialog-volver"
            >
                Volver
            </button>

            <button
                type="button"
                id="aceptar-confirmacion"
                class="horarios-dialog-aceptar"
            >
                Continuar
                <i class="fas fa-arrow-right" aria-hidden="true"></i>
            </button>
        </div>
    </dialog>
</main>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const mensajes = document.getElementById('mensajes');
    const dialogo = document.getElementById('confirmar-cita');
    const cerrar = document.getElementById('cerrar-confirmacion');
    const aceptar = document.getElementById('aceptar-confirmacion');
    const usuarioAutenticado = <?= isset($_SESSION['usuario']) ? 'true' : 'false' ?>;

    let disponibilidadSeleccionada = null;
    let temporizadorMensaje;

    function mostrarMensaje(texto, tipo) {
        if (!mensajes) return;

        window.clearTimeout(temporizadorMensaje);
        mensajes.replaceChildren();

        const aviso = document.createElement('div');
        aviso.className = tipo === 'error'
            ? 'horarios-mensaje horarios-mensaje-error'
            : 'horarios-mensaje horarios-mensaje-exito';

        aviso.textContent = texto;
        mensajes.appendChild(aviso);

        temporizadorMensaje = window.setTimeout(function () {
            mensajes.replaceChildren();
        }, 5000);
    }

    document.querySelectorAll('.horario-agendar').forEach(function (boton) {
        boton.addEventListener('click', function () {
            const id = Number(boton.dataset.disponibilidadId);

            if (!Number.isSafeInteger(id) || id <= 0) {
                mostrarMensaje('No se pudo identificar este horario.', 'error');
                return;
            }

            if (!usuarioAutenticado) {
                mostrarMensaje(
                    'Debes iniciar sesión para solicitar una cita.',
                    'error'
                );

                window.setTimeout(function () {
                    window.location.href =
                        '/clinica/views/users/login_register.php';
                }, 1500);

                return;
            }

            disponibilidadSeleccionada = id;
            dialogo.showModal();
        });
    });

    cerrar.addEventListener('click', function () {
        dialogo.close();
    });

    dialogo.addEventListener('close', function () {
        disponibilidadSeleccionada = null;
    });

    aceptar.addEventListener('click', function () {
        if (disponibilidadSeleccionada === null) return;

        aceptar.disabled = true;

        window.location.href =
            '/clinica/views/users/patient_request_form.php?disponibilidad_id=' +
            encodeURIComponent(disponibilidadSeleccionada);
    });
});
</script>

<?php require_once __DIR__ . '/../../cabecera/pie_paciente.php'; ?>

</body>
</html>

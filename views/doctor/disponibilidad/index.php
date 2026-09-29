<?php
session_start();

if (!isset($_SESSION['doctor_id'])) {
    header('Location: ../login.php');
    exit();
}

require_once __DIR__ . '/../../../config/csrf.php';

$titulo_pagina_doctor = 'HappyDent — Gestionar horarios';
$css_pagina_doctor = '/clinica/assets/css/disponibilidaddoctor.css';
$csrfToken = csrfToken();

require_once __DIR__ . '/../../cabecera/cabecera_doctor.php';
?>

<main class="doctor-horarios-main">
    <div class="doctor-horarios-contenedor">

        <section
            class="doctor-horarios-intro"
            aria-labelledby="doctor-horarios-titulo"
        >
            <span class="doctor-horarios-etiqueta">
                <i class="fas fa-calendar-alt" aria-hidden="true"></i>
                Área del doctor
            </span>

            <h1 id="doctor-horarios-titulo">Gestionar horarios</h1>

            <p>
                Crea horarios de atención y administra tu disponibilidad
                desde un solo lugar.
            </p>
        </section>

        <section
            class="doctor-horarios-panel"
            aria-labelledby="doctor-horarios-crear-titulo"
        >
            <div class="doctor-horarios-panel-encabezado">
                <span class="doctor-horarios-panel-icono" aria-hidden="true">
                    <i class="fas fa-calendar-plus"></i>
                </span>

                <div>
                    <span class="doctor-horarios-subtitulo">
                        Disponibilidad
                    </span>

                    <h2 id="doctor-horarios-crear-titulo">
                        Crear nuevo horario
                    </h2>
                </div>
            </div>

            <form id="form-horario" class="doctor-horarios-formulario">
                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= htmlspecialchars(
                        (string) $csrfToken,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>"
                >

                <div class="doctor-horarios-form-grid">
                    <div class="doctor-horarios-form-group">
                        <label for="fecha">Fecha de atención</label>
                        <input
                            type="date"
                            name="fecha"
                            id="fecha"
                            min="<?= date('Y-m-d') ?>"
                            required
                        >
                    </div>

                    <div class="doctor-horarios-form-group">
                        <label for="hora_inicio">Hora de inicio</label>
                        <input
                            type="time"
                            name="hora_inicio"
                            id="hora_inicio"
                            value="08:00"
                            required
                        >
                    </div>

                    <div class="doctor-horarios-form-group">
                        <label for="hora_fin">Hora de fin</label>
                        <input
                            type="time"
                            name="hora_fin"
                            id="hora_fin"
                            value="09:00"
                            required
                        >
                    </div>
                </div>

                <div class="doctor-horarios-form-acciones">
                    <p>
                        La hora de fin debe ser posterior a la hora de inicio.
                    </p>

                    <button
                        type="submit"
                        class="doctor-horarios-btn-primary"
                    >
                        <i class="fas fa-plus" aria-hidden="true"></i>
                        Crear horario
                    </button>
                </div>
            </form>
        </section>

        <div
            id="mensajes"
            class="doctor-horarios-mensajes"
            role="status"
            aria-live="polite"
        ></div>

        <section
            class="doctor-horarios-panel doctor-horarios-listado"
            aria-labelledby="doctor-horarios-lista-titulo"
        >
            <div class="doctor-horarios-listado-encabezado">
                <div>
                    <span class="doctor-horarios-subtitulo">
                        Planificación
                    </span>

                    <h2 id="doctor-horarios-lista-titulo">
                        Mis horarios programados
                    </h2>
                </div>

                <span
                    class="doctor-horarios-listado-icono"
                    aria-hidden="true"
                >
                    <i class="fas fa-calendar-week"></i>
                </span>
            </div>

            <div id="lista-horarios" class="doctor-horarios-lista">
                <p class="doctor-horarios-aviso-lista">
                    Cargando horarios…
                </p>
            </div>
        </section>

        <dialog
            id="modal-estado"
            class="doctor-horarios-dialog"
            aria-labelledby="doctor-horarios-dialog-titulo"
        >
            <div class="doctor-horarios-dialog-encabezado">
                <span
                    class="doctor-horarios-dialog-icono"
                    aria-hidden="true"
                >
                    <i class="fas fa-sync-alt"></i>
                </span>

                <h2 id="doctor-horarios-dialog-titulo">
                    Cambiar estado del horario
                </h2>

                <p id="info-horario"></p>
            </div>

            <div class="doctor-horarios-form-group">
                <label for="nuevo-estado">Nuevo estado</label>

                <select
                    id="nuevo-estado"
                    class="doctor-horarios-select-estado"
                >
                    <option value="libre">Libre</option>
                    <option value="ocupado">Ocupado</option>
                    <option value="cita">Con cita</option>
                </select>
            </div>

            <div class="doctor-horarios-dialog-acciones">
                <button
                    id="cancelar-cambio"
                    type="button"
                    class="doctor-horarios-btn-secondary"
                >
                    Volver
                </button>

                <button
                    id="confirmar-cambio"
                    type="button"
                    class="doctor-horarios-btn-primary"
                >
                    Confirmar cambio
                </button>
            </div>
        </dialog>

    </div>
</main>

<script>
(() => {
    const form = document.getElementById('form-horario');
    const fechaInput = document.getElementById('fecha');
    const horaInicioInput = document.getElementById('hora_inicio');
    const horaFinInput = document.getElementById('hora_fin');
    const listaHorarios = document.getElementById('lista-horarios');
    const mensajes = document.getElementById('mensajes');
    const modal = document.getElementById('modal-estado');
    const nuevoEstado = document.getElementById('nuevo-estado');
    const confirmarCambio = document.getElementById('confirmar-cambio');

    const csrfToken = form.elements.namedItem('csrf_token').value;
    const controllerUrl =
        '/clinica/controllers/DisponibilidadController.php';

    let disponibilidadIdActual = null;
    let temporizadorMensaje = null;

    function fechaLocalHoy() {
        const hoy = new Date();

        const anio = hoy.getFullYear();
        const mes = String(hoy.getMonth() + 1).padStart(2, '0');
        const dia = String(hoy.getDate()).padStart(2, '0');

        return `${anio}-${mes}-${dia}`;
    }

    function mostrarMensaje(mensaje, tipo) {
        clearTimeout(temporizadorMensaje);
        mensajes.replaceChildren();

        const aviso = document.createElement('div');

        aviso.className = tipo === 'success'
            ? 'doctor-horarios-mensaje doctor-horarios-mensaje-exito'
            : 'doctor-horarios-mensaje doctor-horarios-mensaje-error';

        aviso.textContent = mensaje;
        mensajes.appendChild(aviso);

        temporizadorMensaje = setTimeout(() => {
            mensajes.replaceChildren();
        }, 5000);
    }

    async function enviarAccion(action, formData) {
        const respuesta = await fetch(
            `${controllerUrl}?action=${encodeURIComponent(action)}`,
            {
                method: 'POST',
                body: formData,
                credentials: 'same-origin'
            }
        );

        if (!respuesta.ok) {
            throw new Error(`Error HTTP ${respuesta.status}`);
        }

        return respuesta.json();
    }

    async function cargarHorarios() {
        try {
            const respuesta = await fetch(
                `${controllerUrl}?action=obtenerHorariosHtml`,
                {
                    credentials: 'same-origin'
                }
            );

            if (!respuesta.ok) {
                throw new Error(`Error HTTP ${respuesta.status}`);
            }

            // Este endpoint existente devuelve HTML.
            listaHorarios.innerHTML = await respuesta.text();
        } catch (error) {
            console.error('Error al cargar horarios:', error);

            listaHorarios.replaceChildren();

            const aviso = document.createElement('p');
            aviso.className =
                'doctor-horarios-aviso-lista doctor-horarios-error-lista';
            aviso.textContent =
                'No se pudieron cargar los horarios.';

            listaHorarios.appendChild(aviso);
        }
    }

    form.addEventListener('submit', async (event) => {
        event.preventDefault();

        if (horaInicioInput.value >= horaFinInput.value) {
            mostrarMensaje(
                'La hora de fin debe ser posterior a la hora de inicio.',
                'error'
            );
            return;
        }

        const boton = form.querySelector('button[type="submit"]');
        boton.disabled = true;

        try {
            const data = await enviarAccion(
                'guardar',
                new FormData(form)
            );

            if (!data.success) {
                mostrarMensaje(
                    data.message || 'No se pudo crear el horario.',
                    'error'
                );
                return;
            }

            mostrarMensaje(
                data.message || 'Horario creado correctamente.',
                'success'
            );

            form.reset();
            fechaInput.min = fechaLocalHoy();
            horaInicioInput.value = '08:00';
            horaFinInput.value = '09:00';

            await cargarHorarios();
        } catch (error) {
            console.error('Error al guardar:', error);

            mostrarMensaje(
                'Error de conexión al guardar el horario.',
                'error'
            );
        } finally {
            boton.disabled = false;
        }
    });

    function cerrarModal() {
        if (modal.open) {
            modal.close();
        }

        disponibilidadIdActual = null;
    }

    /*
     * Se mantienen globales porque los botones del HTML generado
     * por obtenerHorariosHtml pueden llamarlas directamente.
     */
    window.abrirModalEstado = function (
        disponibilidadId,
        fechaActual,
        horaInicio,
        horaFin,
        estadoActual
    ) {
        disponibilidadIdActual = disponibilidadId;

        const fecha = new Date(`${fechaActual}T00:00:00`);

        const fechaFormateada = Number.isNaN(fecha.getTime())
            ? fechaActual
            : fecha.toLocaleDateString('es-PE');

        document.getElementById('info-horario').textContent =
            `${fechaFormateada} · ${horaInicio} a ${horaFin}`;

        nuevoEstado.value = estadoActual;
        modal.showModal();
    };

    window.eliminarHorario = async function (disponibilidadId) {
        if (!window.confirm(
            '¿Eliminar este horario? Esta acción no se puede deshacer.'
        )) {
            return;
        }

        const formData = new FormData();
        formData.append('disponibilidad_id', disponibilidadId);
        formData.append('csrf_token', csrfToken);

        try {
            const data = await enviarAccion(
                'eliminar',
                formData
            );

            mostrarMensaje(
                data.message || (
                    data.success
                        ? 'Horario eliminado.'
                        : 'No se pudo eliminar el horario.'
                ),
                data.success ? 'success' : 'error'
            );

            if (data.success) {
                await cargarHorarios();
            }
        } catch (error) {
            console.error('Error al eliminar:', error);

            mostrarMensaje(
                'Error de conexión al eliminar el horario.',
                'error'
            );
        }
    };

    document.getElementById('cancelar-cambio')
        .addEventListener('click', cerrarModal);

    modal.addEventListener('close', () => {
        disponibilidadIdActual = null;
    });

    confirmarCambio.addEventListener('click', async () => {
        if (disponibilidadIdActual === null) {
            return;
        }

        const formData = new FormData();
        formData.append(
            'disponibilidad_id',
            disponibilidadIdActual
        );
        formData.append('estado', nuevoEstado.value);
        formData.append('csrf_token', csrfToken);

        confirmarCambio.disabled = true;

        try {
            const data = await enviarAccion(
                'cambiarEstado',
                formData
            );

            mostrarMensaje(
                data.message || (
                    data.success
                        ? 'Estado actualizado.'
                        : 'No se pudo cambiar el estado.'
                ),
                data.success ? 'success' : 'error'
            );

            if (data.success) {
                cerrarModal();
                await cargarHorarios();
            }
        } catch (error) {
            console.error('Error al cambiar el estado:', error);

            mostrarMensaje(
                'Error de conexión al cambiar el estado.',
                'error'
            );
        } finally {
            confirmarCambio.disabled = false;
        }
    });

    fechaInput.min = fechaLocalHoy();
    cargarHorarios();
})();
</script>

<?php require_once __DIR__ . '/../../cabecera/pie_paciente.php'; ?>

</body>
</html>

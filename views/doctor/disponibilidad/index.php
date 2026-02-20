<?php

if (!isset($_SESSION['doctor_id'])) {
    header("Location: login.php");
    exit();
}
$doctor_name = $_SESSION['doctor_name'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Mi Disponibilidad - Doctor</title>
    <link rel="stylesheet" href="../../../clinica1/assets/css/disponibilidaddoctor.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        @import url("https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap");

        /* Estilos del header del doctor */
        .doctor-header {
            background: rgba(0, 0, 0, 0.8);
            color: #fff;
            padding: 20px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 0;
        }

        .doctor-header h1 {
            font-size: 2em;
            margin-bottom: 10px;
            font-family: "Poppins", sans-serif;
        }

        .doctor-nav a {
            color: #fff;
            text-decoration: none;
            margin: 0 15px;
            font-weight: 500;
            display: inline-block;
            padding: 10px 20px;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .doctor-nav a:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }

        .doctor-nav a i {
            margin-right: 8px;
        }

        /* Ajuste para el contenedor principal */
        .container {
            margin-top: 0;
            border-radius: 0 0 15px 15px;
        }

        .header {
            border-radius: 0;
        }

        body {
            padding: 0;
        }
    </style>
</head>
<body>
    <!-- Header del Doctor -->
    <header class="doctor-header">
        <h1>Bienvenido, Dr. <?php echo htmlspecialchars($doctor_name); ?></h1>
        <nav class="doctor-nav">
                  <a href="../../../clinica1/views/doctor/add_patient.php"><i class="fas fa-user-plus"></i> Agregar Paciente</a>
            
             <!-- <a href="dashboard.php?action=calendar"><i class="fas fa-calendar-alt"></i> Modificar Calendario</a>-->
            <a href="../../../clinica1/views/users/patient_appointments.php"><i class="fas fa-list"></i> Historia de cital del paciente</a>
            <a href="<?= '../../../clinica1/controllers/DisponibilidadController.php' ?>">Gestionar Horarios</a>
            <a href="../../../clinica1/views/doctor/logout.php"><i class="fas fa-lis"></i> SALIR</a>
        </nav>
        </nav>
    </header>

    <!-- Contenido de Disponibilidad -->
    <div class="container">
        <div class="header">
            <h1>🩺 Panel del Doctor</h1>
            <p>Gestiona tu disponibilidad y horarios de atención</p>
        </div>

        <div class="content">
            <!-- Formulario para crear horarios -->
            <div class="form-section">
                <h2>📅 Crear Nuevo Horario</h2>
                <form id="form-horario">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="fecha">Fecha de Atención</label>
                            <input type="date" name="fecha" id="fecha" required min="<?php echo date('Y-m-d'); ?>">
                        </div>

                        <div class="form-group">
                            <label for="hora_inicio">Hora de Inicio</label>
                            <input type="time" name="hora_inicio" id="hora_inicio" value="08:00" required>
                        </div>

                        <div class="form-group">
                            <label for="hora_fin">Hora de Fin</label>
                            <input type="time" name="hora_fin" id="hora_fin" value="09:00" required>
                        </div>
                    </div>

                    <button type="submit" class="btn-primary">
                        ➕ Crear Horario
                    </button>
                </form>
            </div>

            <!-- Mensajes -->
            <div id="mensajes"></div>

            <!-- Lista de horarios -->
            <div class="horarios-section">
                <div class="horarios-header">
                    <h3>📋 Mis Horarios Programados</h3>
                </div>
                <div id="lista-horarios">
                    <!-- Aquí se cargarán los horarios -->
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para cambiar estado -->
    <div id="modal-estado" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>🔄 Cambiar Estado del Horario</h3>
                <span class="close">&times;</span>
            </div>
            <div class="modal-body">
                <p id="info-horario"></p>
                <div class="form-group">
                    <label for="nuevo-estado">Nuevo Estado:</label>
                    <select id="nuevo-estado" class="select-estado">
                        <option value="libre">🟢 Libre</option>
                        <option value="ocupado">🟡 Ocupado</option>
                        <option value="cita">🔴 Con Cita</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button id="cancelar-cambio" class="btn-secondary">Cancelar</button>
                <button id="confirmar-cambio" class="btn-primary">Confirmar Cambio</button>
            </div>
        </div>
    </div>

    <script>
    // Variables globales para el modal
let disponibilidadIdActual = null;
const modal = document.getElementById('modal-estado');
const closeModal = document.querySelector('.close');
const cancelarCambio = document.getElementById('cancelar-cambio');
const confirmarCambio = document.getElementById('confirmar-cambio');

// Función para mostrar mensajes
function mostrarMensaje(mensaje, tipo) {
    const div = document.getElementById('mensajes');
    div.innerHTML = `<div class="mensaje ${tipo}">${mensaje}</div>`;
    setTimeout(() => {
        div.innerHTML = '';
    }, 5000);
}

// Manejar envío del formulario
document.getElementById("form-horario").addEventListener("submit", function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    
    // Validar que hora_fin sea mayor a hora_inicio
    const horaInicio = document.getElementById('hora_inicio').value;
    const horaFin = document.getElementById('hora_fin').value;
    
    if (horaInicio >= horaFin) {
        mostrarMensaje('⚠️ La hora de fin debe ser posterior a la hora de inicio', 'error');
        return;
    }

    fetch("/clinica1/controllers/DisponibilidadController.php?action=guardar", {
        method: "POST",
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            mostrarMensaje('✅ ' + data.message, 'success');
            document.getElementById("form-horario").reset();
            document.getElementById('fecha').min = new Date().toISOString().split('T')[0];
            document.getElementById('hora_inicio').value = '08:00';
            document.getElementById('hora_fin').value = '09:00';
            cargarHorarios();
        } else {
            mostrarMensaje('❌ ' + data.message, 'error');
        }
    })
    .catch(err => {
        console.error("Error:", err);
        mostrarMensaje("❌ Error de conexión al guardar", 'error');
    });
});

// Cargar lista de horarios
function cargarHorarios() {
    fetch("/clinica1/controllers/DisponibilidadController.php?action=obtenerHorariosHtml")
        .then(res => res.text())
        .then(html => {
            document.getElementById("lista-horarios").innerHTML = html;
        })
        .catch(err => {
            console.error("Error al cargar horarios:", err);
            document.getElementById("lista-horarios").innerHTML = 
                '<div class="empty-state"><p>❌ Error al cargar los horarios</p></div>';
        });
}

// Función para abrir modal de cambio de estado
function abrirModalEstado(disponibilidadId, fechaActual, horaInicio, horaFin, estadoActual) {
    disponibilidadIdActual = disponibilidadId;
    
    // Formatear la información del horario
    const fechaFormateada = new Date(fechaActual + 'T00:00:00').toLocaleDateString('es-ES');
    const infoHorario = `📅 ${fechaFormateada} - 🕒 ${horaInicio} - ${horaFin}`;
    
    document.getElementById('info-horario').textContent = infoHorario;
    document.getElementById('nuevo-estado').value = estadoActual;
    
    modal.style.display = 'block';
}

// Función para cerrar modal
function cerrarModal() {
    modal.style.display = 'none';
    disponibilidadIdActual = null;
}

// Event listeners para el modal
closeModal.onclick = cerrarModal;
cancelarCambio.onclick = cerrarModal;

// Cerrar modal al hacer clic fuera de él
window.onclick = function(event) {
    if (event.target === modal) {
        cerrarModal();
    }
}

// Confirmar cambio de estado
confirmarCambio.onclick = function() {
    if (!disponibilidadIdActual) return;
    
    const nuevoEstado = document.getElementById('nuevo-estado').value;
    cambiarEstado(disponibilidadIdActual, nuevoEstado);
};

// Función para cambiar estado
function cambiarEstado(disponibilidadId, nuevoEstado) {
    const formData = new FormData();
    formData.append('disponibilidad_id', disponibilidadId);
    formData.append('estado', nuevoEstado);

    fetch("/clinica1/controllers/DisponibilidadController.php?action=cambiarEstado", {
        method: "POST",
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            mostrarMensaje('✅ ' + data.message, 'success');
            cargarHorarios();
            cerrarModal();
        } else {
            mostrarMensaje('❌ ' + data.message, 'error');
        }
    })
    .catch(err => {
        console.error("Error:", err);
        mostrarMensaje("❌ Error al cambiar el estado", 'error');
    });
}

// Función para eliminar horario
function eliminarHorario(disponibilidadId) {
    if (!confirm('🗑️ ¿Estás seguro de que quieres eliminar este horario?\n\nEsta acción no se puede deshacer.')) {
        return;
    }

    const formData = new FormData();
    formData.append('disponibilidad_id', disponibilidadId);

    fetch("/clinica1/controllers/DisponibilidadController.php?action=eliminar", {
        method: "POST",
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            mostrarMensaje('✅ ' + data.message, 'success');
            cargarHorarios();
        } else {
            mostrarMensaje('❌ ' + data.message, 'error');
        }
    })
    .catch(err => {
        console.error("Error:", err);
        mostrarMensaje("❌ Error al eliminar el horario", 'error');
    });
}

// Cargar horarios al iniciar la página
window.onload = function() {
    // Establecer fecha mínima (hoy)
    document.getElementById('fecha').min = new Date().toISOString().split('T')[0];
    cargarHorarios();
};
    </script>
</body>
</html>
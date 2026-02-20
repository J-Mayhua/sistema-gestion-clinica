<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: login_register.php");
    exit();
}

require_once __DIR__ . '/../../controllers/UserController.php';
require_once __DIR__ . '/../../controllers/DoctorController.php';
require_once __DIR__ . '/../../models/Disponibilidad.php';
require_once __DIR__ . '/../../models/Appointment.php';

$userController = new UserController();
$doctorController = new DoctorController();
$patient = $userController->getPatientInfo($_SESSION['usuario_id']);

$action = isset($_GET['action']) ? $_GET['action'] : '';
$appointments = [];

// Obtener citas del paciente
if ($action === 'viewAppointments' || $action === 'calendar') {
    $appointmentModel = new Appointment();
    $appointments = $appointmentModel->getPatientAppointments($_SESSION['usuario_id']);
}

// Obtener horarios disponibles para todos los doctores
$disponibilidadModel = new Disponibilidad();
$horarios = $disponibilidadModel->getDisponiblesParaPacientes();

// Obtener lista única de especialidades y doctores para filtros
$especialidades = array_unique(array_column($horarios, 'especialidad'));
$doctores = [];
foreach ($horarios as $horario) {
    $doctorNombre = trim($horario['doctor_nombres'] . ' ' . $horario['doctor_apellidos']);
    $doctores[$doctorNombre] = $horario['especialidad'];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HappyDent - Dashboard del Paciente</title>
    <link rel="stylesheet" href="../../assets/css/patient_styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/5.10.1/main.min.css">
    <style>
        /* Estilos del calendario y lista de horarios */
        .calendario-container {
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 30px;
            margin-top: 20px;
        }
        
        .calendar-section {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        .horarios-section {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            max-height: 800px;
            overflow-y: auto;
        }
        
        .filters-section {
            display: none;
        }
        
        .filter-group {
            display: none;
        }
        
        .btn-filtrar, .btn-limpiar {
            display: none;
        }
        
        .mensaje {
            display: none;
        }
        
        .horarios-list {
            max-height: 500px;
            overflow-y: auto;
        }
        
        .horario-card {
            background: white;
            border-radius: 12px;
            padding: 18px;
            margin-bottom: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            border: 1px solid #f0f0f0;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }
        
        .horario-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.12);
            border-color: #74b9ff;
        }
        
        .horario-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(135deg, #74b9ff 0%, #0984e3 100%);
        }
        
        .doctor-info {
            margin-bottom: 12px;
        }
        
        .doctor-nombre {
            font-size: 1.1em;
            font-weight: 700;
            color: #333;
            margin-bottom: 5px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .doctor-especialidad {
            background: #e3f2fd;
            color: #1976d2;
            padding: 4px 10px;
            border-radius: 15px;
            font-size: 0.8em;
            font-weight: 600;
            display: inline-block;
        }
        
        .horario-detalles {
            background: #f8f9fa;
            padding: 12px;
            border-radius: 8px;
            margin: 10px 0;
        }
        
        .horario-fecha {
            font-size: 1em;
            font-weight: 600;
            color: #333;
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        
        .horario-tiempo {
            color: #666;
            font-size: 0.95em;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        
        .estado-badge {
            position: absolute;
            top: 12px;
            right: 12px;
            background: #4caf50;
            color: white;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.7em;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .btn-agendar {
    display: inline-block;
    width: 100%;
    background: linear-gradient(135deg, #4caf50 0%, #45a049 100%);
    color: white;
    padding: 10px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-size: 13px;
    font-weight: 600;
    transition: all 0.3s ease;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-top: 10px;
    text-decoration: none;
    text-align: center;
    box-sizing: border-box;
}

.btn-agendar:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(76, 175, 80, 0.4);
    color: white;
    text-decoration: none;
}

.btn-agendar:active {
    transform: translateY(0);
    box-shadow: 0 4px 10px rgba(76, 175, 80, 0.3);
}

.btn-agendar:focus {
    outline: none;
    box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.3);
}

.btn-agendar i {
    margin-right: 6px;
}
        
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #666;
        }
        
        .empty-state-icon {
            font-size: 3em;
            margin-bottom: 15px;
            opacity: 0.3;
        }
        
        .empty-state h4 {
            font-size: 1.2em;
            margin-bottom: 10px;
            color: #333;
        }
        
        .empty-state p {
            font-size: 0.95em;
            line-height: 1.4;
        }
        
        .mensaje {
            padding: 12px 15px;
            margin: 15px 0;
            border-radius: 8px;
            font-weight: 500;
            animation: slideIn 0.3s ease;
        }
        
        .mensaje.success {
            background: #d4edda;
            color: #155724;
            border-left: 4px solid #28a745;
        }
        
        .mensaje.error {
            background: #f8d7da;
            color: #721c24;
            border-left: 4px solid #dc3545;
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @media (max-width: 1024px) {
            .calendario-container {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            
            .horarios-section {
                max-height: 600px;
            }
        }
        
        /* Estilos para FullCalendar */
        .fc-event {
            border-radius: 6px !important;
            border: none !important;
            padding: 2px 4px !important;
        }
        
        .fc-event-title {
            font-weight: 600 !important;
            font-size: 12px !important;
        }
        
        .fc-daygrid-day:hover {
            background-color: rgba(116, 185, 255, 0.1) !important;
        }
        
        .fc-day-today {
            background-color: rgba(116, 185, 255, 0.15) !important;
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">HappyDent</div>
        <div class="user-actions">
            <span>Bienvenido, <?= htmlspecialchars($patient['nombre_completo']) ?></span>
            <a href="logout.php">SALIR</a>
        </div>
    </header>

    <main>
        <div class="column left">
            <img src="<?= htmlspecialchars($patient['profile_image'] ?? '../../assets/images/tarjeta.jpg') ?>" alt="Foto de perfil" class="profile-image">
            
            <a href="patient_appointment_history.php" class="action-button">MIS CITAS</a>
            <a href="patient_dashboard.php?action=calendar" class="action-button">CALENDARIO</a>
        </div>

        <div class="column right">
            <?php if ($action === 'calendar'): ?>
                <h2><i class="fas fa-calendar-alt"></i> Calendario de Citas y Horarios Disponibles</h2>
                
              
                    <!-- Sección de Lista de Horarios -->
                    <div class="horarios-section">
                        <h3><i class="fas fa-clock"></i> Horarios Disponibles</h3>
                        
                        <!-- Lista de horarios -->
                        <div class="horarios-list">
                            <?php if (!empty($horarios)): ?>
                                <?php foreach ($horarios as $horario): ?>
                                    <div class="horario-card">
                                        
                                        <div class="estado-badge">Disponible</div>
                                        
                                        <div class="doctor-info">
                                            <div class="doctor-nombre">
                                                <i class="fas fa-user-md"></i> Dr. <?php echo htmlspecialchars(trim($horario['doctor_nombres'] . ' ' . $horario['doctor_apellidos'])); ?>
                                            </div>
                                            <div class="doctor-especialidad">
                                                <?php echo htmlspecialchars($horario['especialidad']); ?>
                                            </div>
                                        </div>

                                        <div class="horario-detalles">
                                            <div class="horario-fecha">
                                                <i class="fas fa-calendar-day"></i> <?php echo date('d/m/Y', strtotime($horario['fecha'])); ?>
                                            </div>
                                            <div class="horario-tiempo">
                                                <i class="fas fa-clock"></i> <?php echo date('H:i', strtotime($horario['hora_inicio'])); ?> - 
                                                <?php echo date('H:i', strtotime($horario['hora_fin'])); ?>
                                            </div>
                                        </div>

                                        <button class="btn-agendar" onclick="agendarCita(<?php echo $horario['disponibilidad_id']; ?>)">
                                          <a href="patient_request_form.php?disponibilidad_id=<?php echo $horario['disponibilidad_id']; ?>" class="btn-agendar">
    <i class="fas fa-calendar-plus"></i> Agendar Cita
</a>

                                        </button>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="empty-state">
                                    <div class="empty-state-icon"><i class="fas fa-calendar-times"></i></div>
                                    <h4>No hay horarios disponibles</h4>
                                    <p>En este momento no hay horarios disponibles.<br>Por favor, inténtalo más tarde.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

            <?php elseif ($action === 'viewAppointments'): ?>
                <h2>Historial de Citas</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Especialidad</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($appointments as $appointment): ?>
                            <tr>
                                <td><?= htmlspecialchars($appointment['fecha_creacion']) ?></td>
                                <td><?= htmlspecialchars($appointment['especialidad']) ?></td>
                                <td><?= htmlspecialchars($appointment['estado']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

            <?php else: ?>
                <h2>Información del Paciente</h2>
                <p><strong>Nombre:</strong> <?= htmlspecialchars($patient['nombre_completo']) ?></p>
                <p><strong>Correo Electrónico:</strong> <?= htmlspecialchars($patient['correo_electronico']) ?></p>
            <?php endif; ?>
        </div>
    </main>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/5.10.1/main.min.js"></script>
    
    <script>
        // Función para agendar cita


        // Inicializar calendario
        document.addEventListener('DOMContentLoaded', function () {
            var calendarEl = document.getElementById('calendar');

            if (calendarEl) {
                var calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    selectable: true,
                    locale: 'es',
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek'
                    },
                    events: [
                        // Citas del paciente
                        <?php foreach ($appointments as $appointment): ?>
                            {
                                title: 'Mi Cita: <?= htmlspecialchars($appointment['especialidad']) ?>',
                                start: '<?= substr($appointment['fecha_creacion'], 0, 10) ?>',
                                backgroundColor: '#dc3545',
                                borderColor: '#dc3545',
                                textColor: 'white'
                            },
                        <?php endforeach; ?>

                        // Horarios disponibles
                        <?php foreach ($horarios as $h): ?>
                            {
                                title: 'Disponible - Dr. <?= htmlspecialchars(trim($h['doctor_nombres'] . ' ' . $h['doctor_apellidos'])) ?>',
                                start: '<?= $h['fecha'] ?>T<?= $h['hora_inicio'] ?>',
                                end: '<?= $h['fecha'] ?>T<?= $h['hora_fin'] ?>',
                                backgroundColor: '#28a745',
                                borderColor: '#28a745',
                                textColor: 'white',
                                extendedProps: {
                                    disponibilidad_id: <?= $h['disponibilidad_id'] ?>,
                                    doctor: 'Dr. <?= htmlspecialchars(trim($h['doctor_nombres'] . ' ' . $h['doctor_apellidos'])) ?>',
                                    especialidad: '<?= htmlspecialchars($h['especialidad']) ?>'
                                }
                            },
                        <?php endforeach; ?>
                    ],
                    eventClick: function(info) {
                        if (info.event.extendedProps.disponibilidad_id) {
                            const doctor = info.event.extendedProps.doctor;
                            const especialidad = info.event.extendedProps.especialidad;
                            const fecha = info.event.startStr.split('T')[0];
                            const hora = info.event.startStr.split('T')[1].substring(0,5);
                            
                            if (confirm(`¿Deseas agendar cita con ${doctor}?\n\nEspecialidad: ${especialidad}\nFecha: ${fecha}\nHora: ${hora}`)) {
                                agendarCita(info.event.extendedProps.disponibilidad_id);
                            }
                        }
                    },
                    dateClick: function(info) {
                        // Solo mostrar información sobre la fecha seleccionada
                        alert('Fecha seleccionada: ' + info.dateStr);
                    }
                });

                calendar.render();
            }
        });
    </script>
</body>
</html>
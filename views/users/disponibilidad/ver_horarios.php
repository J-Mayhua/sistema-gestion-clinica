<?php
// views/users/disponibilidad/ver_horario.php - Vista para PACIENTES
session_start();

// Incluir el modelo directamente
if (!class_exists('Disponibilidad')) {
    require_once __DIR__ . '/../../../models/Disponibilidad.php';
}

// Crear instancia del modelo directamente
$disponibilidadModel = new Disponibilidad();
$horarios = $disponibilidadModel->getDisponiblesParaPacientes();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Horarios Disponibles - Clínica</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #74b9ff 0%, #0984e3 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 25px 50px rgba(0,0,0,0.15);
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(135deg, #74b9ff 0%, #0984e3 100%);
            color: white;
            padding: 40px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: repeating-linear-gradient(
                45deg,
                transparent,
                transparent 2px,
                rgba(255,255,255,0.1) 2px,
                rgba(255,255,255,0.1) 4px
            );
            animation: float 20s infinite linear;
        }
        
        @keyframes float {
            0% { transform: translate(-50%, -50%) rotate(0deg); }
            100% { transform: translate(-50%, -50%) rotate(360deg); }
        }
        
        .header-content {
            position: relative;
            z-index: 1;
        }
        
        .header h1 {
            font-size: 3em;
            margin-bottom: 15px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        
        .header p {
            font-size: 1.3em;
            opacity: 0.95;
        }
        
        .content {
            padding: 40px;
        }
        
        .filters-section {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 15px;
            margin-bottom: 35px;
            border: 1px solid #e9ecef;
        }
        
        .filters-title {
            color: #333;
            font-size: 1.4em;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .filters-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .filter-group {
            display: flex;
            flex-direction: column;
        }
        
        .filter-group label {
            font-weight: 600;
            margin-bottom: 8px;
            color: #555;
        }
        
        .filter-group input, .filter-group select {
            padding: 12px 15px;
            border: 2px solid #e0e6ed;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        
        .filter-group input:focus, .filter-group select:focus {
            outline: none;
            border-color: #74b9ff;
            box-shadow: 0 0 0 3px rgba(116, 185, 255, 0.1);
        }
        
        .btn-filtrar {
            background: linear-gradient(135deg, #74b9ff 0%, #0984e3 100%);
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .btn-filtrar:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(116, 185, 255, 0.4);
        }
        
        .horarios-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 25px;
        }
        
        .horario-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            border: 1px solid #f0f0f0;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }
        
        .horario-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
            border-color: #74b9ff;
        }
        
        .horario-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(135deg, #74b9ff 0%, #0984e3 100%);
        }
        
        .doctor-info {
            margin-bottom: 20px;
        }
        
        .doctor-nombre {
            font-size: 1.4em;
            font-weight: 700;
            color: #333;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .doctor-especialidad {
            background: #e3f2fd;
            color: #1976d2;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.9em;
            font-weight: 600;
            display: inline-block;
        }
        
        .horario-detalles {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 12px;
            margin: 15px 0;
        }
        
        .horario-fecha {
            font-size: 1.2em;
            font-weight: 600;
            color: #333;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .horario-tiempo {
            color: #666;
            font-size: 1.1em;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .estado-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: #4caf50;
            color: white;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8em;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .btn-agendar {
            width: 100%;
            background: linear-gradient(135deg, #4caf50 0%, #45a049 100%);
            color: white;
            padding: 15px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 15px;
        }
        
        .btn-agendar:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(76, 175, 80, 0.4);
        }
        
        .btn-agendar:active {
            transform: translateY(0);
        }
        
        .empty-state {
            text-align: center;
            padding: 80px 40px;
            color: #666;
            grid-column: 1 / -1;
        }
        
        .empty-state-icon {
            font-size: 5em;
            margin-bottom: 25px;
            opacity: 0.3;
        }
        
        .empty-state h3 {
            font-size: 1.8em;
            margin-bottom: 15px;
            color: #333;
        }
        
        .empty-state p {
            font-size: 1.1em;
            line-height: 1.6;
        }
        
        .loading {
            text-align: center;
            padding: 40px;
            color: #666;
            grid-column: 1 / -1;
        }
        
        .loading-spinner {
            width: 40px;
            height: 40px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #74b9ff;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .mensaje {
            padding: 15px 20px;
            margin: 20px 0;
            border-radius: 10px;
            font-weight: 500;
            animation: slideIn 0.3s ease;
        }
        
        .mensaje.success {
            background: #d4edda;
            color: #155724;
            border-left: 5px solid #28a745;
        }
        
        .mensaje.error {
            background: #f8d7da;
            color: #721c24;
            border-left: 5px solid #dc3545;
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
        
        @media (max-width: 768px) {
            .header h1 {
                font-size: 2.2em;
            }
            
            .header p {
                font-size: 1.1em;
            }
            
            .content {
                padding: 20px;
            }
            
            .horarios-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            
            .filters-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="header-content">
                <h1>🏥 Horarios Disponibles</h1>
                <p>Encuentra y agenda tu cita con nuestros especialistas</p>
            </div>
        </div>

        <div class="content">
           

            <!-- Mensajes -->
            <div id="mensajes"></div>

            <!-- Lista de horarios -->
            <div class="horarios-grid" id="horarios-container">
                <?php if (!empty($horarios)): ?>
                    <?php foreach ($horarios as $horario): ?>
                        <div class="horario-card" data-fecha="<?php echo $horario['fecha']; ?>" 
                             data-especialidad="<?php echo htmlspecialchars($horario['especialidad']); ?>"
                             data-doctor="<?php echo htmlspecialchars(trim($horario['doctor_nombres'] . ' ' . $horario['doctor_apellidos'])); ?>">
                            
                            <div class="estado-badge">Disponible</div>
                            
                            <div class="doctor-info">
                                <div class="doctor-nombre">
                                    👨‍⚕️ Dr. <?php echo htmlspecialchars(trim($horario['doctor_nombres'] . ' ' . $horario['doctor_apellidos'])); ?>
                                </div>
                                <div class="doctor-especialidad">
                                    <?php echo htmlspecialchars($horario['especialidad']); ?>
                                </div>
                            </div>

                            <div class="horario-detalles">
                                <div class="horario-fecha">
                                    📅 <?php echo date('d/m/Y', strtotime($horario['fecha'])); ?>
                                </div>
                                <div class="horario-tiempo">
                                    🕒 <?php echo date('H:i', strtotime($horario['hora_inicio'])); ?> - 
                                    <?php echo date('H:i', strtotime($horario['hora_fin'])); ?>
                                </div>
                            </div>

                            <button class="btn-agendar" onclick="agendarCita(<?php echo $horario['disponibilidad_id']; ?>)">
                                📝 Agendar Cita
                            </button>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <div class="empty-state-icon">📅</div>
                        <h3>No hay horarios disponibles</h3>
                        <p>En este momento no hay horarios disponibles.<br>Por favor, inténtalo más tarde o contacta con nosotros.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        // Función para mostrar mensajes
        function mostrarMensaje(mensaje, tipo) {
            const div = document.getElementById('mensajes');
            div.innerHTML = `<div class="mensaje ${tipo}">${mensaje}</div>`;
            setTimeout(() => {
                div.innerHTML = '';
            }, 5000);
        }

        // Función para aplicar filtros
        function aplicarFiltros() {
            const fecha = document.getElementById('filtro-fecha').value;
            const especialidad = document.getElementById('filtro-especialidad').value;
            const doctor = document.getElementById('filtro-doctor').value;
            
            const cards = document.querySelectorAll('.horario-card');
            let visibleCount = 0;
            
            cards.forEach(card => {
                let mostrar = true;
                
                // Filtro por fecha
                if (fecha && card.dataset.fecha !== fecha) {
                    mostrar = false;
                }
                
                // Filtro por especialidad
                if (especialidad && card.dataset.especialidad !== especialidad) {
                    mostrar = false;
                }
                
                // Filtro por doctor
                if (doctor && card.dataset.doctor !== doctor) {
                    mostrar = false;
                }
                
                if (mostrar) {
                    card.style.display = 'block';
                    card.style.animation = 'slideIn 0.3s ease';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });
            
            // Mostrar mensaje si no hay resultados
            const container = document.getElementById('horarios-container');
            const emptyState = container.querySelector('.empty-state');
            
            if (visibleCount === 0 && !emptyState) {
                container.innerHTML += `
                    <div class="empty-state">
                        <div class="empty-state-icon">🔍</div>
                        <h3>No se encontraron horarios</h3>
                        <p>No hay horarios que coincidan con los filtros seleccionados.<br>Intenta ajustar los criterios de búsqueda.</p>
                    </div>
                `;
            } else if (visibleCount > 0 && emptyState) {
                emptyState.remove();
            }
            
            mostrarMensaje(`🔍 Filtros aplicados. ${visibleCount} horario(s) encontrado(s).`, 'success');
        }

        // Función para limpiar filtros
        function limpiarFiltros() {
            document.getElementById('filtro-fecha').value = '';
            document.getElementById('filtro-especialidad').value = '';
            document.getElementById('filtro-doctor').value = '';
            
            const cards = document.querySelectorAll('.horario-card');
            cards.forEach(card => {
                card.style.display = 'block';
            });
            
            // Remover mensaje de "no encontrados" si existe
            const emptyState = document.querySelector('.empty-state');
            if (emptyState && emptyState.textContent.includes('No se encontraron')) {
                emptyState.remove();
            }
            
            mostrarMensaje('✨ Filtros limpiados. Mostrando todos los horarios.', 'success');
        }

        // Función para agendar cita
        function agendarCita(disponibilidadId) {
            // Verificar si el usuario está logueado
            <?php if (!isset($_SESSION['user_id']) && !isset($_SESSION['paciente_id'])): ?>
                mostrarMensaje('⚠️ Debes iniciar sesión para agendar una cita.', 'error');
                setTimeout(() => {
                    window.location.href = '/clinica1/views/users/login.php';
                }, 2000);
                return;
            <?php endif; ?>

            if (confirm('📝 ¿Confirmas que deseas agendar esta cita?\n\nUna vez confirmada, el horario será reservado para ti.')) {
                // Aquí puedes redirigir a la página de agendamiento o hacer una petición AJAX
                mostrarMensaje('⏳ Procesando tu solicitud...', 'success');
                
                // Simulación de redirección (ajusta según tu estructura)
                setTimeout(() => {
                    window.location.href = `/clinica1/views/users/citas/agendar.php?disponibilidad_id=${disponibilidadId}`;
                }, 1500);
            }
        }

        // Inicializar fecha mínima en filtros
        window.onload = function() {
            document.getElementById('filtro-fecha').min = new Date().toISOString().split('T')[0];
        };

        // Agregar botón para limpiar filtros
        document.addEventListener('DOMContentLoaded', function() {
            const btnFiltrar = document.querySelector('.btn-filtrar');
            const btnLimpiar = document.createElement('button');
            btnLimpiar.className = 'btn-filtrar';
            btnLimpiar.style.background = 'linear-gradient(135deg, #6c757d 0%, #5a6268 100%)';
            btnLimpiar.style.marginLeft = '10px';
            btnLimpiar.innerHTML = '🧹 Limpiar Filtros';
            btnLimpiar.onclick = limpiarFiltros;
            btnFiltrar.parentNode.appendChild(btnLimpiar);
        });
    </script>
</body>
</html>
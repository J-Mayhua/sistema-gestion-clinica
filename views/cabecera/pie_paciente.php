<?php
// views/cabecera/pie_paciente.php
// Pie de página para el área del paciente.
// Se incluye al final del <body> de cada vista de paciente,
// antes del </body></html> de cierre.
?>

<footer class="footer">

    <!-- Ola decorativa superior -->
    <div class="footer-wave">
        <svg viewBox="0 0 1440 80" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M0,40 C360,80 1080,0 1440,40 L1440,80 L0,80 Z" fill="#1a1a2e"/>
        </svg>
    </div>

    <div class="footer-body">

        <!-- ── Columna 1: Logo + descripción ── -->
        <div class="footer-col footer-brand">
            <div class="footer-logo">
                <span class="logo-icon">🦷</span>
                <span>Happy<b>Dent</b></span>
            </div>
            <p>Clínica dental especializada comprometida con tu salud bucal. Tecnología de vanguardia y trato humano.</p>
            <div class="redes">
                <a href="#" class="red facebook"  aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                <a href="#" class="red instagram" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                <a href="#" class="red whatsapp"  aria-label="WhatsApp"><i class="fab fa-whatsapp"></i></a>
                <a href="#" class="red tiktok"    aria-label="TikTok"><i class="fab fa-tiktok"></i></a>
            </div>
        </div>

        <!-- ── Columna 2: Links rápidos ── -->
        <div class="footer-col">
            <h5><i class="fas fa-link"></i> Enlaces rápidos</h5>
            <ul>
                <li><a href="/clinica/views/users/patient_dashboard.php"><i class="fas fa-chevron-right"></i> Inicio</a></li>
                <li><a href="/clinica/views/especialidades/especialidades.php"><i class="fas fa-chevron-right"></i> Especialidades</a></li>
                <li><a href="/clinica/views/users/patient_appointment_history.php"><i class="fas fa-chevron-right"></i> Mis Citas</a></li>
                <li><a href="/clinica/views/users/disponibilidad/ver_horarios.php"><i class="fas fa-chevron-right"></i> Horarios</a></li>
                <li><a href="/clinica/views/users/logout.php"><i class="fas fa-chevron-right"></i> Salir</a></li>
            </ul>
        </div>

        <!-- ── Columna 3: Servicios ── -->
        <div class="footer-col">
            <h5><i class="fas fa-tooth"></i> Servicios</h5>
            <ul>
                <li><a href="#"><i class="fas fa-chevron-right"></i> Ortodoncia</a></li>
                <li><a href="#"><i class="fas fa-chevron-right"></i> Endodoncia</a></li>
                <li><a href="#"><i class="fas fa-chevron-right"></i> Profilaxis</a></li>
                <li><a href="#"><i class="fas fa-chevron-right"></i> Implantes</a></li>
                <li><a href="#"><i class="fas fa-chevron-right"></i> Prótesis</a></li>
            </ul>
        </div>

        <!-- ── Columna 4: Contacto + Horario ── -->
        <div class="footer-col">
            <h5><i class="fas fa-map-marker-alt"></i> Contacto</h5>
            <ul class="contacto-lista">
                <li>
                    <i class="fas fa-map-marker-alt"></i>
                    <span>Jr. Bolognesi 116, 2do Piso<br>Pampas - Tayacaja</span>
                </li>
                <li>
                    <i class="fas fa-phone-alt"></i>
                    <a href="tel:+51925758041">925 758 041</a>
                </li>
                <li>
                    <i class="fab fa-whatsapp"></i>
                    <a href="https://wa.me/51925758041" target="_blank">WhatsApp directo</a>
                </li>
            </ul>
            <div class="horario">
                <h6><i class="fas fa-clock"></i> Horario de atención</h6>
                <div class="horario-fila"><span>Lun - Vie</span><span class="badge-open">9am – 7pm</span></div>
                <div class="horario-fila"><span>Sábados</span><span class="badge-open">9am – 1pm</span></div>
                <div class="horario-fila"><span>Domingos</span><span class="badge-closed">Cerrado</span></div>
            </div>
        </div>

    </div>

    <!-- ── Barra inferior ── -->
    <div class="footer-bottom">
        <p>© <span id="footer-year"></span> <strong>HappyDent</strong> — Todos los derechos reservados</p>
        <p>Hecho con <span class="heart">mayhua</span> 925-758-041</p>
    </div>

</footer>

<link rel="stylesheet" href="/clinica/assets/css/pie.css">
<script>
    document.getElementById('footer-year').textContent = new Date().getFullYear();
    const footerCols = document.querySelectorAll('.footer-col');
    const obs = new IntersectionObserver((entries) => {
        entries.forEach((entry, i) => {
            if (entry.isIntersecting) {
                setTimeout(() => entry.target.classList.add('visible'), i * 120);
            }
        });
    }, { threshold: 0.1 });
    footerCols.forEach(col => obs.observe(col));
</script>
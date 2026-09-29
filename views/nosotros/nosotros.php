<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HappyDent — Nosotros</title>
    <link rel="stylesheet" href="/clinica/assets/css/cabecera.css">
    <link rel="stylesheet" href="/clinica/assets/css/nosotros.css">
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<?php require_once __DIR__ . '/../cabecera/cabecera.php'; ?>


<div class="container-all" id="move-content">

    <!-- ══ HERO NOSOTROS ══ -->
    <section class="hero-nosotros">
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <span class="tag"><i class="fas fa-star"></i> Quiénes somos</span>
            <h1>Conoce a <span>HappyDent</span></h1>
            <p>Más de 5 años cuidando sonrisas con pasión, tecnología y un equipo humano excepcional.</p>
        </div>
        <div class="scroll-indicator">
            <span>Desliza hacia abajo</span>
            <i class="fas fa-chevron-down"></i>
        </div>
    </section>

    <!-- ══ MISIÓN & VISIÓN ══ -->
    <section class="mv-section fade-in">

        <!-- MISIÓN -->
        <div class="mv-card">
            <div class="mv-img-wrap">
                <img src="/clinica/assets/images/mision.jpg" alt="Misión HappyDent" loading="lazy">
                <div class="mv-badge">
                    <i class="fas fa-bullseye"></i>
                </div>
            </div>
            <div class="mv-body">
                <span class="mv-label">01</span>
                <h2>Nuestra <span>Misión</span></h2>
                <p>Contribuir al cuidado de la vida y la recuperación de la salud, a través de la
                   prestación de servicios de alta complejidad, centrados en la persona, con un equipo
                   humano cálido y calificado para alcanzar desenlaces clínicos superiores, promoviendo
                   el cuidado del medio ambiente y la sostenibilidad económica.</p>
                <ul class="mv-lista">
                    <li><i class="fas fa-check-circle"></i> Atención centrada en el paciente</li>
                    <li><i class="fas fa-check-circle"></i> Equipo humano cálido y calificado</li>
                    <li><i class="fas fa-check-circle"></i> Sostenibilidad y responsabilidad</li>
                </ul>
            </div>
        </div>

        <!-- VISIÓN -->
        <div class="mv-card mv-card--reverse">
            <div class="mv-img-wrap">
                <img src="/clinica/assets/images/vision.jpg" alt="Visión HappyDent" loading="lazy">
                <div class="mv-badge mv-badge--verde">
                    <i class="fas fa-eye"></i>
                </div>
            </div>
            <div class="mv-body">
                <span class="mv-label">02</span>
                <h2>Nuestra <span>Visión</span></h2>
                <p>Para el año 2025, la Clínica HappyDent será reconocida por el desarrollo de centros
                   de cuidado clínico con enfoque de atención basada en valor, consolidándose como una
                   institución con estándares superiores de calidad, innovación y desarrollo tecnológico.</p>
                <ul class="mv-lista">
                    <li><i class="fas fa-check-circle"></i> Estándares superiores de calidad</li>
                    <li><i class="fas fa-check-circle"></i> Innovación y tecnología de vanguardia</li>
                    <li><i class="fas fa-check-circle"></i> Referente regional en salud bucal</li>
                </ul>
            </div>
        </div>

    </section>

    <!-- ══ VALORES ══ -->
    <section class="valores-section fade-in">
        <div class="seccion-header">
            <span class="tag">Lo que nos define</span>
            <h2>Nuestros <span>Valores</span></h2>
            <p>Los principios que guían cada decisión y cada sonrisa que cuidamos</p>
        </div>
        <div class="valores-grid">
            <div class="valor-card">
                <div class="valor-icon"><i class="fas fa-heart"></i></div>
                <h3>Vocación</h3>
                <p>Amamos lo que hacemos. Cada paciente es una persona, no un número.</p>
            </div>
            <div class="valor-card">
                <div class="valor-icon"><i class="fas fa-shield-alt"></i></div>
                <h3>Confianza</h3>
                <p>Transparencia total en diagnósticos, tratamientos y precios.</p>
            </div>
            <div class="valor-card">
                <div class="valor-icon"><i class="fas fa-microscope"></i></div>
                <h3>Innovación</h3>
                <p>Equipos de última generación para resultados precisos y duraderos.</p>
            </div>
            <div class="valor-card">
                <div class="valor-icon"><i class="fas fa-users"></i></div>
                <h3>Trabajo en equipo</h3>
                <p>Especialistas que colaboran para ofrecerte el mejor plan de tratamiento.</p>
            </div>
        </div>
    </section>

    <!-- ══ CTA FINAL ══ -->
    <section class="cta-nosotros fade-in">
        <div class="cta-diente">🦷</div>
        <h2>¿Listo para conocernos en persona?</h2>
        <p>Agenda tu primera consulta de evaluación sin costo.</p>
        <a href="/clinica/views/users/login_register.php" class="btn-primary btn-grande">
            <i class="fas fa-calendar-check"></i> Agendar mi cita gratis
        </a>
    </section>

    <div id="button-up"><i class="fas fa-chevron-up"></i></div>

</div><!-- /container-all -->

<script src="/clinica/assets/js/script_menu.js"></script>
<script src="/clinica/assets/js/cabecera.js"></script>
<script>
document.documentElement.classList.add('js-ready');

const esMobil = window.innerWidth <= 600;

/* ── Fade-in observer ── */
const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) entry.target.classList.add('visible');
    });
}, { threshold: esMobil ? 0 : 0.12, rootMargin: '0px 0px -30px 0px' });

document.querySelectorAll('.fade-in').forEach(el => observer.observe(el));

/* ── Cards valores ── */
const cardObs = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('visible');
            cardObs.unobserve(entry.target);
        }
    });
}, { threshold: esMobil ? 0 : 0.1 });

document.querySelectorAll('.valor-card').forEach(c => cardObs.observe(c));

/* ── Scroll suave ── */
document.querySelectorAll('a[href^="#"]').forEach(a => {
    a.addEventListener('click', function(e) {
        const t = document.querySelector(this.getAttribute('href'));
        if (t) { e.preventDefault(); t.scrollIntoView({ behavior: 'smooth' }); }
    });
});

/* ── Botón arriba ── */
const btnUp = document.getElementById('button-up');
window.addEventListener('scroll', () => {
    btnUp.classList.toggle('show', window.scrollY > 300);
});
btnUp.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));
</script>

</body>
</html>
<?php require_once __DIR__ . '/../cabecera/pie.php'; ?>

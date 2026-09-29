<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HappyDent — Especialidades</title>
    <link rel="stylesheet" href="/clinica/assets/css/cabecera.css">
    <link rel="stylesheet" href="/clinica/assets/css/especialidades.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<?php include $_SERVER['DOCUMENT_ROOT'] . '/clinica/views/cabecera/cabecera.php'; ?>

<div class="container-all" id="move-content">

        <!-- ══ HERO ESPECIALIDADES ══ -->
    <section class="hero-especialidades">

        <!-- Partículas decorativas -->
        <div class="hero-particles">
            <span class="particle p1">🦷</span>
            <span class="particle p2">⭐</span>
            <span class="particle p3">✨</span>
            <span class="particle p4">🦷</span>
        </div>

        <div class="hero-overlay"></div>

        <div class="hero-content">

            <!-- Tag superior -->
            <div class="hero-tag-wrap">
                <span class="hero-tag">
                    <i class="fas fa-tooth"></i> Lo que ofrecemos
                </span>
            </div>

            <!-- Título principal -->
            <h1>
                Nuestras<br>
                <span class="titulo-acento">Especialidades</span>
            </h1>

            <!-- Línea decorativa -->
            <div class="hero-linea">
                <span></span><i class="fas fa-tooth"></i><span></span>
            </div>

            <!-- Descripción -->
            <p class="hero-desc">
                Tecnología de vanguardia y especialistas certificados<br>
                para cada tratamiento que necesitas.
            </p>

            <!-- Badges de confianza -->
            <div class="hero-trust">
                <div class="trust-item">
                    <i class="fas fa-user-md"></i>
                    <span>+8 Especialistas</span>
                </div>
                <div class="trust-sep"></div>
                <div class="trust-item">
                    <i class="fas fa-award"></i>
                    <span>Certificados</span>
                </div>
                <div class="trust-sep"></div>
                <div class="trust-item">
                    <i class="fas fa-star"></i>
                    <span>5 Estrellas</span>
                </div>
            </div>

        </div>

        <!-- Scroll indicator -->
        <div class="scroll-indicator">
            <span>Desliza hacia abajo</span>
            <i class="fas fa-chevron-down"></i>
        </div>

    </section>


    <!-- ══ GRID ESPECIALIDADES ══ -->
    <section class="especialidades-section">
        <div class="seccion-header fade-in">
            <span class="tag">Tratamientos</span>
            <h2>¿Qué <span>tratamos</span>?</h2>
            <p>Contamos con las últimas tecnologías para cuidar tu salud bucal de forma integral</p>
        </div>

        <div class="esp-grid">

            <div class="esp-card fade-in">
                <div class="esp-img-wrap">
                    <img src="/clinica/assets/images/ortodoncia.jpg" alt="Ortodoncia" loading="lazy">
                    <div class="esp-icon"><i class="fas fa-teeth"></i></div>
                </div>
                <div class="esp-body">
                    <h3>Ortodoncia</h3>
                    <p>Se encarga de los problemas de los dientes y la mandíbula, usando dispositivos como aparatos y frenos para enderezar los dientes y lograr una sonrisa armoniosa.</p>
                    <span class="esp-tag">Correctiva</span>
                </div>
            </div>

            <div class="esp-card fade-in">
                <div class="esp-img-wrap">
                    <img src="/clinica/assets/images/endodoncia.jfif" alt="Endodoncia" loading="lazy">
                    <div class="esp-icon"><i class="fas fa-tooth"></i></div>
                </div>
                <div class="esp-body">
                    <h3>Endodoncia</h3>
                    <p>Preserva las piezas dañadas extrayendo la pulpa dental y sellando la cavidad con material biocompatible, evitando la pérdida del diente.</p>
                    <span class="esp-tag">Conservadora</span>
                </div>
            </div>

            <div class="esp-card fade-in">
                <div class="esp-img-wrap">
                    <img src="/clinica/assets/images/restauracion dental.png" alt="Restauración Dental" loading="lazy">
                    <div class="esp-icon"><i class="fas fa-fill-drip"></i></div>
                </div>
                <div class="esp-body">
                    <h3>Restauración Dental</h3>
                    <p>Devuelve al diente dañado su forma y función original mediante técnicas y materiales de alta calidad adaptados a cada caso.</p>
                    <span class="esp-tag">Estética</span>
                </div>
            </div>

            <div class="esp-card fade-in">
                <div class="esp-img-wrap">
                    <img src="/clinica/assets/images/profilaxis.jfif" alt="Profilaxis" loading="lazy">
                    <div class="esp-icon"><i class="fas fa-shield-alt"></i></div>
                </div>
                <div class="esp-body">
                    <h3>Profilaxis</h3>
                    <p>Previene patologías periodontales mediante limpieza bucal profesional, eliminando sarro y bacterias para mantener una boca completamente sana.</p>
                    <span class="esp-tag">Preventiva</span>
                </div>
            </div>

            <div class="esp-card fade-in">
                <div class="esp-img-wrap">
                    <img src="/clinica/assets/images/protesis.png" alt="Prótesis" loading="lazy">
                    <div class="esp-icon"><i class="fas fa-teeth-open"></i></div>
                </div>
                <div class="esp-body">
                    <h3>Prótesis</h3>
                    <p>Estructura con dientes artificiales que se ancla a los dientes naturales para reponer piezas ausentes y recuperar la función masticatoria completa.</p>
                    <span class="esp-tag">Rehabilitadora</span>
                </div>
            </div>

            <div class="esp-card fade-in">
                <div class="esp-img-wrap">
                    <img src="/clinica/assets/images/implante.jpg" alt="Implantes" loading="lazy">
                    <div class="esp-icon"><i class="fas fa-plus-circle"></i></div>
                </div>
                <div class="esp-body">
                    <h3>Implantes</h3>
                    <p>Reemplaza raíces dentales con pernos de titanio y dientes artificiales de aspecto y función idénticos a los naturales. La solución más duradera.</p>
                    <span class="esp-tag">Avanzada</span>
                </div>
            </div>


        </div>
    </section>

    <!-- ══ CTA ══ -->
    <section class="cta-esp fade-in">
        <div class="cta-diente">🦷</div>
        <h2>¿No sabes qué tratamiento necesitas?</h2>
        <p>Agenda una evaluación gratuita y nuestros especialistas te orientarán.</p>
        <a href="/clinica/views/users/login_register.php" class="btn-primary btn-grande">
            <i class="fas fa-calendar-check"></i> Agendar evaluación gratis
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
        if (entry.isIntersecting) {
            entry.target.classList.add('visible');
            observer.unobserve(entry.target);
        }
    });
}, { threshold: esMobil ? 0 : 0.1, rootMargin: '0px 0px -30px 0px' });

document.querySelectorAll('.fade-in').forEach(el => observer.observe(el));

/* ── Botón arriba ── */
const btnUp = document.getElementById('button-up');
window.addEventListener('scroll', () => {
    btnUp.classList.toggle('show', window.scrollY > 300);
});
btnUp.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));
</script>

</body>
</html>
<?php include $_SERVER['DOCUMENT_ROOT'] . '/clinica/views/cabecera/pie.php'; ?>

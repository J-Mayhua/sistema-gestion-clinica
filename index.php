
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HappyDent - Clínica Dental Especializada</title>
    <link rel="stylesheet" href="assets/css/principalfondo.css">
    <link rel="stylesheet" href="/clinica/assets/css/cabecera.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<?php include 'views/cabecera/cabecera.php'; ?>
<div class="container-all" id="move-content">

    <!-- HERO -->
    <section class="hero">
        <div class="hero-video-wrapper"
             style="background: url('/clinica/assets/images/hero-poster.jpg') center/cover no-repeat;">
            <!--
                ✅ FIX: el poster va en style INLINE con ruta absoluta /clinica/...
                Así funciona sin importar desde qué carpeta se sirva el PHP
            -->
            <video
                class="hero-video"
                autoplay muted loop playsinline
                preload="metadata"
            >
                <source src="/clinica/assets/video/clinica.webm" type="video/webm">
                <source src="/clinica/assets/video/clinica.mp4"  type="video/mp4">
            </video>
            <div class="hero-overlay"></div>
        </div>

        <div class="hero-inner">
            <div class="hero-text">
                <p class="hero-sub"><i class="fas fa-star"></i> Tu sonrisa, nuestra pasión</p>
                <h1>Bienvenido a <span>HappyDent</span></h1>
                <p class="hero-desc">Clínica dental especializada con los tratamientos más avanzados para que luzcas una sonrisa radiante y saludable.</p>
                <div class="hero-btns">
                    <a href="views/users/login_register.php" class="btn-primary">
                        <i class="fas fa-calendar-check"></i> Agendar cita
                    </a>
                    <a href="#servicios" class="btn-outline">
                        <i class="fas fa-play-circle"></i> Ver servicios
                    </a>
                </div>
                <div class="hero-badges">
                    <span><i class="fas fa-shield-alt"></i> Certificados</span>
                    <span><i class="fas fa-user-md"></i> +8 especialistas</span>
                    <span><i class="fas fa-star"></i> 5 estrellas</span>
                </div>
            </div>

            <!-- MASCOTA -->
            <div class="mascota-wrapper">
                <div class="mascota">
                    <svg class="diente-svg" viewBox="0 0 120 140" xmlns="http://www.w3.org/2000/svg">
                        <ellipse cx="60" cy="135" rx="35" ry="6" fill="rgba(0,0,0,0.15)"/>
                        <path d="M20,40 Q10,10 30,5 Q45,0 55,15 Q60,22 65,15 Q75,0 90,5 Q110,10 100,40 Q95,70 90,100 Q85,125 75,128 Q65,130 60,120 Q55,130 45,128 Q35,125 30,100 Q25,70 20,40 Z"
                              fill="#ffffff" stroke="#e0e0e0" stroke-width="2"/>
                        <path d="M35,15 Q40,8 50,12" stroke="rgba(255,255,255,0.9)" stroke-width="3" fill="none" stroke-linecap="round"/>
                        <circle class="ojo-izq" cx="45" cy="55" r="7" fill="#333"/>
                        <circle cx="43" cy="53" r="2.5" fill="white"/>
                        <circle class="ojo-der" cx="75" cy="55" r="7" fill="#333"/>
                        <circle cx="73" cy="53" r="2.5" fill="white"/>
                        <path d="M45,75 Q60,90 75,75" stroke="#333" stroke-width="3" fill="none" stroke-linecap="round"/>
                        <ellipse cx="38" cy="68" rx="8" ry="5" fill="rgba(255,150,150,0.4)"/>
                        <ellipse cx="82" cy="68" rx="8" ry="5" fill="rgba(255,150,150,0.4)"/>
                    </svg>
                    <div class="manita manita-izq">
                        <svg viewBox="0 0 40 50" xmlns="http://www.w3.org/2000/svg">
                            <rect x="8"  y="5"  width="10" height="20" rx="5" fill="#fff" stroke="#e0e0e0" stroke-width="1.5"/>
                            <rect x="20" y="8"  width="9"  height="18" rx="4.5" fill="#fff" stroke="#e0e0e0" stroke-width="1.5"/>
                            <rect x="5"  y="20" width="28" height="20" rx="8" fill="#fff" stroke="#e0e0e0" stroke-width="1.5"/>
                        </svg>
                    </div>
                    <div class="manita manita-der">
                        <svg viewBox="0 0 40 50" xmlns="http://www.w3.org/2000/svg">
                            <rect x="22" y="5"  width="10" height="20" rx="5" fill="#fff" stroke="#e0e0e0" stroke-width="1.5"/>
                            <rect x="11" y="8"  width="9"  height="18" rx="4.5" fill="#fff" stroke="#e0e0e0" stroke-width="1.5"/>
                            <rect x="7"  y="20" width="28" height="20" rx="8" fill="#fff" stroke="#e0e0e0" stroke-width="1.5"/>
                        </svg>
                    </div>
                </div>
                <div class="burbuja b1">🦷</div>
                <div class="burbuja b2">⭐</div>
                <div class="burbuja b3">✨</div>
            </div>
        </div>

        <div class="scroll-indicator">
            <span>Desliza hacia abajo</span>
            <i class="fas fa-chevron-down"></i>
        </div>
    </section>

    <!-- STATS -->
    <section class="stats fade-in">
        <div class="stat-item">
            <i class="fas fa-user-friends"></i>
            <h3 class="count" data-target="2000">0</h3>
            <p>Pacientes felices</p>
        </div>
        <div class="stat-item">
            <i class="fas fa-tooth"></i>
            <h3 class="count" data-target="10">0</h3>
            <p>Especialidades</p>
        </div>
        <div class="stat-item">
            <i class="fas fa-user-md"></i>
            <h3 class="count" data-target="8">0</h3>
            <p>Doctores expertos</p>
        </div>
        <div class="stat-item">
            <i class="fas fa-calendar-check"></i>
            <h3 class="count" data-target="5">0</h3>
            <p>Años de experiencia</p>
        </div>
    </section>

  <!-- SERVICIOS — CARRUSEL -->
<section class="seccion-servicios" id="servicios">
    <div class="seccion-header fade-in">
        <span class="tag">Lo que hacemos</span>
        <h2>Nuestros <span>Tratamientos</span></h2>
        <p>Contamos con las últimas tecnologías para cuidar tu salud bucal</p>
    </div>

    <div class="carrusel-wrapper">
        <!-- Botón anterior -->
        <button class="carrusel-btn carrusel-btn--prev" aria-label="Anterior">
            <i class="fas fa-chevron-left"></i>
        </button>

        <div class="carrusel-track-outer">
            <div class="carrusel-track" id="carrusel-track">

                <div class="carrusel-slide">
                    <div class="servicio-card">
                        <img src="/clinica/assets/images/ortodoncia.jpg" alt="Ortodoncia" loading="lazy">
                        <div class="carrusel-label">
                            <div class="servicio-icon"><i class="fas fa-teeth"></i></div>
                            <h3>Ortodoncia</h3>
                        </div>
                    </div>
                </div>

                <div class="carrusel-slide">
                    <div class="servicio-card">
                        <img src="/clinica/assets/images/endodoncia.jfif" alt="Endodoncia" loading="lazy">
                        <div class="carrusel-label">
                            <div class="servicio-icon"><i class="fas fa-tooth"></i></div>
                            <h3>Endodoncia</h3>
                        </div>
                    </div>
                </div>

                <div class="carrusel-slide">
                    <div class="servicio-card">
                        <img src="/clinica/assets/images/profilaxis.jfif" alt="Profilaxis" loading="lazy">
                        <div class="carrusel-label">
                            <div class="servicio-icon"><i class="fas fa-shield-alt"></i></div>
                            <h3>Profilaxis</h3>
                        </div>
                    </div>
                </div>

                <div class="carrusel-slide">
                    <div class="servicio-card">
                        <img src="/clinica/assets/images/restauracion dental.png" alt="Restauración" loading="lazy">
                        <div class="carrusel-label">
                            <div class="servicio-icon"><i class="fas fa-fill-drip"></i></div>
                            <h3>Restauración</h3>
                        </div>
                    </div>
                </div>

                <div class="carrusel-slide">
                    <div class="servicio-card">
                        <img src="/clinica/assets/images/protesis.png" alt="Prótesis" loading="lazy">
                        <div class="carrusel-label">
                            <div class="servicio-icon"><i class="fas fa-teeth-open"></i></div>
                            <h3>Prótesis</h3>
                        </div>
                    </div>
                </div>

                <div class="carrusel-slide">
                    <div class="servicio-card">
                        <img src="/clinica/assets/images/implante.jpg" alt="Implantes" loading="lazy">
                        <div class="carrusel-label">
                            <div class="servicio-icon"><i class="fas fa-plus-circle"></i></div>
                            <h3>Implantes</h3>
                        </div>
                    </div>
                </div>

            

            </div>
        </div>

        <!-- Botón siguiente -->
        <button class="carrusel-btn carrusel-btn--next" aria-label="Siguiente">
            <i class="fas fa-chevron-right"></i>
        </button>
    </div>

    <!-- Dots -->
    <div class="carrusel-dots" id="carrusel-dots"></div>

    <!-- CTA hacia especialidades -->
    <div class="carrusel-cta fade-in">
        <a href="views/especialidades/especialidades.php" class="btn-outline">
            <i class="fas fa-th-list"></i> Ver todas las especialidades
        </a>
    </div>
</section>


    <!-- MISIÓN -->
    <section class="mision fade-in">
        <div class="mision-img">
            <img src="/clinica/assets/images/tarjeta.jpg" alt="HappyDent equipo" loading="lazy">
            <div class="mision-badge"><i class="fas fa-award"></i><span>Clínica certificada</span></div>
        </div>
        <div class="mision-texto">
            <span class="tag">¿Por qué elegirnos?</span>
            <h2>Tu salud bucal es <span>nuestra misión</span></h2>
            <p>En HappyDent nos dedicamos a brindar atención dental de la más alta calidad, combinando tecnología de vanguardia con un trato humano y cercano.</p>
            <ul class="mision-lista">
                <li><i class="fas fa-check-circle"></i> Profesionales altamente calificados</li>
                <li><i class="fas fa-check-circle"></i> Equipos de última generación</li>
                <li><i class="fas fa-check-circle"></i> Ambiente cómodo y sin estrés</li>
                <li><i class="fas fa-check-circle"></i> Precios accesibles y planes de pago</li>
                <li><i class="fas fa-check-circle"></i> Atención personalizada</li>
            </ul>
            <a href="views/nosotros/nosotros.php" class="btn-primary">Conoce al equipo</a>
        </div>
    </section>

    <!-- CTA -->
    <section class="cta fade-in">
        <div class="cta-diente">🦷</div>
        <h2>¿Listo para tener la sonrisa que siempre soñaste?</h2>
        <p>Agenda tu cita hoy mismo. Primera consulta de evaluación sin costo.</p>
        <a href="views/users/login_register.php" class="btn-primary btn-grande">
            <i class="fas fa-calendar-check"></i> Agendar mi cita gratis
        </a>
    </section>

    <div id="button-up"><i class="fas fa-chevron-up"></i></div>
</div>

<script src="/clinica/assets/js/script_menu.js"></script>
<script src="/clinica/assets/js/cabecera.js"></script>
<script>
// ✅ Activar animaciones solo si JS carga
document.documentElement.classList.add('js-ready');

const esMobil = window.innerWidth <= 600;

// ── Observers ────────────────────────────────────────────────
const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('visible');
            if (entry.target.classList.contains('stats')) animarContadores();
        }
    });
}, { threshold: esMobil ? 0 : 0.15, rootMargin: '0px 0px -30px 0px' });

document.querySelectorAll('.fade-in').forEach(el => observer.observe(el));

/* ── Carrusel ─────────────────────────────────────────────── */
(function () {
    const track   = document.getElementById('carrusel-track');
    const dotsBox = document.getElementById('carrusel-dots');
    const btnPrev = document.querySelector('.carrusel-btn--prev');
    const btnNext = document.querySelector('.carrusel-btn--next');
    if (!track) return;

    const slides = track.querySelectorAll('.carrusel-slide');
    let current  = 0;

    /* Cuántas slides se ven según el ancho */
    function visibles() {
        if (window.innerWidth <= 600) return 1;
        if (window.innerWidth <= 900) return 2;
        return 3;
    }

    /* Total de "páginas" */
    function totalPaginas() {
        return Math.ceil(slides.length / visibles());
    }

    /* Crear dots */
    function crearDots() {
        dotsBox.innerHTML = '';
        for (let i = 0; i < totalPaginas(); i++) {
            const d = document.createElement('button');
            d.className = 'carrusel-dot' + (i === 0 ? ' active' : '');
            d.setAttribute('aria-label', `Página ${i + 1}`);
            d.addEventListener('click', () => irA(i));
            dotsBox.appendChild(d);
        }
    }

    /* Mover al índice de página */
    function irA(pagina) {
        const total = totalPaginas();
        current = Math.max(0, Math.min(pagina, total - 1));

        /* Ancho de un slide + gap */
        const slideEl  = slides[0];
        const gap      = 24;
        const slideW   = slideEl.getBoundingClientRect().width + gap;
        const offset   = current * visibles() * slideW;

        track.style.transform = `translateX(-${offset}px)`;

        /* Actualizar dots */
        dotsBox.querySelectorAll('.carrusel-dot').forEach((d, i) => {
            d.classList.toggle('active', i === current);
        });

        /* Botones */
        btnPrev.disabled = current === 0;
        btnNext.disabled = current >= total - 1;
    }

    btnPrev.addEventListener('click', () => irA(current - 1));
    btnNext.addEventListener('click', () => irA(current + 1));

    /* Swipe táctil */
    let startX = 0;
    track.addEventListener('touchstart', e => {
        startX = e.touches[0].clientX;
    }, { passive: true });
    track.addEventListener('touchend', e => {
        const diff = startX - e.changedTouches[0].clientX;
        if (Math.abs(diff) > 50) irA(diff > 0 ? current + 1 : current - 1);
    }, { passive: true });

    /* Auto-play cada 4s */
    let autoplay = setInterval(() => {
        irA(current + 1 < totalPaginas() ? current + 1 : 0);
    }, 4000);

    /* Pausar autoplay al interactuar */
    [btnPrev, btnNext, track].forEach(el => {
        el.addEventListener('pointerdown', () => {
            clearInterval(autoplay);
        });
    });

    /* Recalcular en resize */
    window.addEventListener('resize', () => {
        crearDots();
        irA(0);
    });

    /* Init */
    crearDots();
    irA(0);
})();


document.querySelectorAll('.servicio-card').forEach(card => cardObserver.observe(card));

// ✅ Touch en móvil para las cards (simula hover)
document.querySelectorAll('.servicio-card').forEach(card => {
    card.addEventListener('touchstart', () => {
        // Quitar touched de todas
        document.querySelectorAll('.servicio-card.touched')
            .forEach(c => c.classList.remove('touched'));
        card.classList.add('touched');
    }, { passive: true });
});
// Quitar touched al tocar fuera
document.addEventListener('touchstart', (e) => {
    if (!e.target.closest('.servicio-card')) {
        document.querySelectorAll('.servicio-card.touched')
            .forEach(c => c.classList.remove('touched'));
    }
}, { passive: true });

// ── Contadores ───────────────────────────────────────────────
let contadoresYaAnimados = false;
function animarContadores() {
    if (contadoresYaAnimados) return;
    contadoresYaAnimados = true;
    document.querySelectorAll('.count').forEach(el => {
        const target = +el.dataset.target;
        const step   = target / (1800 / 16);
        let current  = 0;
        const timer  = setInterval(() => {
            current += step;
            if (current >= target) { current = target; clearInterval(timer); }
            el.textContent = (target >= 100 ? '+' : '') + Math.floor(current).toLocaleString();
        }, 16);
    });
}

// ── Scroll suave ─────────────────────────────────────────────
document.querySelectorAll('a[href^="#"]').forEach(a => {
    a.addEventListener('click', function(e) {
        const t = document.querySelector(this.getAttribute('href'));
        if (t) { e.preventDefault(); t.scrollIntoView({ behavior: 'smooth' }); }
    });
});

// ── Pausa video fuera de viewport ────────────────────────────
const video = document.querySelector('.hero-video');
if (video) {
    new IntersectionObserver(([e]) => {
        e.isIntersecting ? video.play() : video.pause();
    }, { threshold: 0.1 }).observe(video);
}
</script>


</body>
</html>
<?php include 'views/cabecera/pie.php'; ?>

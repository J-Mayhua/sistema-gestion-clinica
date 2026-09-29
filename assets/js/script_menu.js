document.addEventListener('DOMContentLoaded', function () {

    const iconMenu = document.getElementById('icon-menu');
    const menu = document.getElementById('show-menu');
    const buttonUp = document.getElementById('button-up');

    let menuAbierto = false; // ✅ Control manual del estado

    // ── Abrir / cerrar menú hamburguesa ──────────────────────────────
    if (iconMenu && menu) {
        iconMenu.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation(); // ✅ CRÍTICO: evita que llegue al document

            menuAbierto = !menuAbierto;

            if (menuAbierto) {
                menu.classList.add('show-lateral');
            } else {
                menu.classList.remove('show-lateral');
            }

            // Cambiar ícono ☰ ↔ ✕
            const icon = iconMenu.querySelector('i');
            if (icon) {
                if (menuAbierto) {
                    icon.classList.remove('fa-bars');
                    icon.classList.add('fa-times');
                } else {
                    icon.classList.remove('fa-times');
                    icon.classList.add('fa-bars');
                }
            }

            console.log('Menú abierto:', menuAbierto, '| Clases:', menu.className);
        });
    }

    // ── Cerrar al hacer clic fuera del menú ──────────────────────────
    document.addEventListener('click', function (event) {
        if (!menuAbierto) return; // ✅ Si está cerrado, no hace nada

        const clickDentroMenu = menu && menu.contains(event.target);
        const clickEnIcono = iconMenu && iconMenu.contains(event.target);

        if (!clickDentroMenu && !clickEnIcono) {
            menuAbierto = false;
            menu.classList.remove('show-lateral');
            const icon = iconMenu ? iconMenu.querySelector('i') : null;
            if (icon) {
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            }
        }
    });

    // ── Cerrar al hacer clic en un enlace (solo móvil) ───────────────
    const menuLinks = document.querySelectorAll('.menu nav ul li a');
    menuLinks.forEach(function (link) {
        link.addEventListener('click', function () {
            if (window.innerWidth <= 800) {
                menuAbierto = false;
                menu.classList.remove('show-lateral');
                const icon = iconMenu ? iconMenu.querySelector('i') : null;
                if (icon) {
                    icon.classList.remove('fa-times');
                    icon.classList.add('fa-bars');
                }
            }
        });
    });

    // ── Cerrar al redimensionar a escritorio ─────────────────────────
    window.addEventListener('resize', function () {
        if (window.innerWidth > 800) {
            menuAbierto = false;
            if (menu) menu.classList.remove('show-lateral');
            const icon = iconMenu ? iconMenu.querySelector('i') : null;
            if (icon) {
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            }
        }
    });

    // ── Botón volver arriba ───────────────────────────────────────────
    if (buttonUp) {
        window.addEventListener('scroll', function () {
            buttonUp.classList.toggle('show', window.scrollY > 300);
        });
        buttonUp.addEventListener('click', function () {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    // ── Año actual en footer ──────────────────────────────────────────
    const currentYearElement = document.getElementById('current-year');
    if (currentYearElement) {
        currentYearElement.textContent = new Date().getFullYear();
    }
});

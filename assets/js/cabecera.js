// assets/js/cabecera.js
document.addEventListener('DOMContentLoaded', function () {
    const header = document.querySelector('header');
    const botonMenu = document.getElementById('icon-menu');
    const menu = document.getElementById('show-menu');

    if (!header) return;

    const showThreshold = 50;
    let isScrolling = false;

    function cerrarMenu() {
        if (!menu || !botonMenu) return;

        menu.classList.remove('show-lateral');
        botonMenu.setAttribute('aria-expanded', 'false');
    }

    function mostrarCabecera() {
        header.classList.remove('header-hidden');
        header.classList.add('header-visible');
    }

    function handleScroll() {
        if (isScrolling) return;

        isScrolling = true;

        window.requestAnimationFrame(function () {
            const scrollActual =
                window.pageYOffset || document.documentElement.scrollTop;

            // No ocultar la cabecera mientras el menú móvil esté abierto.
            if (menu && menu.classList.contains('show-lateral')) {
                mostrarCabecera();
            } else if (scrollActual <= showThreshold) {
                mostrarCabecera();
            } else {
                header.classList.add('header-hidden');
                header.classList.remove('header-visible');
            }

            isScrolling = false;
        });
    }

    if (botonMenu && menu) {
        botonMenu.setAttribute('aria-expanded', 'false');
        botonMenu.setAttribute('aria-controls', 'show-menu');

        botonMenu.addEventListener('click', function () {
            const abierto = menu.classList.toggle('show-lateral');

            botonMenu.setAttribute('aria-expanded', String(abierto));
            mostrarCabecera();
        });

        document.addEventListener('keydown', function (evento) {
            if (evento.key === 'Escape') cerrarMenu();
        });

        window.addEventListener('resize', function () {
            if (window.innerWidth > 800) cerrarMenu();
        });
    }

    window.addEventListener('scroll', handleScroll, { passive: true });
    mostrarCabecera();
});

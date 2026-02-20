// Script para ocultar/mostrar cabecera al hacer scroll
document.addEventListener('DOMContentLoaded', function() {
    const header = document.querySelector('header');
    const showThreshold = 50; // Píxeles desde el top para mostrar header
    let isScrolling = false;

    // Función para manejar el scroll
    function handleScroll() {
        if (!isScrolling) {
            window.requestAnimationFrame(function() {
                const currentScrollTop = window.pageYOffset || document.documentElement.scrollTop;
                
                // Solo mostrar header cuando estemos muy cerca del top
                if (currentScrollTop <= showThreshold) {
                    header.classList.remove('header-hidden');
                    header.classList.add('header-visible');
                }
                // En cualquier otra posición, ocultar header completamente
                else {
                    header.classList.add('header-hidden');
                    header.classList.remove('header-visible');
                }
                
                isScrolling = false;
            });
        }
        isScrolling = true;
    }

    // Agregar el event listener para scroll
    window.addEventListener('scroll', handleScroll, { passive: true });
    
    // Asegurar que el header esté visible al cargar la página
    header.classList.add('header-visible');
});
/**
 * Script para controlar el menú responsivo y el botón de volver arriba
 */

document.addEventListener('DOMContentLoaded', function() {
    // Variables
    const iconMenu = document.getElementById('icon-menu');
    const menu = document.getElementById('show-menu');
    const contentContainer = document.getElementById('move-content');
    const buttonUp = document.getElementById('button-up');
    
    // Función para mostrar/ocultar menú en móvil
    if (iconMenu) {
        iconMenu.addEventListener('click', function() {
            menu.classList.toggle('show-lateral');
            contentContainer.classList.toggle('move-container-all');
        });
    }
    
    // Botón para volver arriba
    if (buttonUp) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 300) {
                buttonUp.classList.add('show');
            } else {
                buttonUp.classList.remove('show');
            }
        });
        
        buttonUp.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }
    
    // Cerrar el menú al hacer clic en un enlace
    const menuLinks = document.querySelectorAll('.menu nav ul li a');
    menuLinks.forEach(function(link) {
        link.addEventListener('click', function() {
            // Solo si estamos en modo móvil
            if (window.innerWidth <= 800) {
                menu.classList.remove('show-lateral');
                contentContainer.classList.remove('move-container-all');
            }
        });
    });
    
    // Cerrar el menú al hacer clic fuera de él
    document.addEventListener('click', function(event) {
        // Si el menú está abierto y el clic no fue dentro del menú o en el ícono del menú
        if (menu.classList.contains('show-lateral') && 
            !menu.contains(event.target) && 
            event.target !== iconMenu) {
            menu.classList.remove('show-lateral');
            contentContainer.classList.remove('move-container-all');
        }
    });
    
    // Ajustar menú en resize
    window.addEventListener('resize', function() {
        if (window.innerWidth > 800) {
            menu.classList.remove('show-lateral');
            contentContainer.classList.remove('move-container-all');
        }
    });
    
    // Actualizar el año actual en el footer
    const currentYearElement = document.getElementById('current-year');
    if (currentYearElement) {
        currentYearElement.textContent = new Date().getFullYear();
    }
});
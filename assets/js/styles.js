document.addEventListener('DOMContentLoaded', function() {
    // Mobile menu toggle
    const iconMenu = document.getElementById('icon-menu');
    const menu = document.getElementById('show-menu');
    const containerAll = document.getElementById('move-content');
    
    if (iconMenu) {
        iconMenu.addEventListener('click', function() {
            menu.classList.toggle('show-lateral');
            containerAll.classList.toggle('move-container-all');
        });
    }
    
    // Back to top button
    const buttonUp = document.getElementById('button-up');
    
    window.addEventListener('scroll', function() {
        if (window.scrollY > 300) {
            buttonUp.classList.add('show');
        } else {
            buttonUp.classList.remove('show');
        }
    });
    
    if (buttonUp) {
        buttonUp.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }
    
    // Carousel functionality
    let currentSlide = 0;
    const track = document.querySelector('.carousel-track');
    const slides = document.querySelectorAll('.carousel-item');
    
    if (track && slides.length > 0) {
        const slideCount = slides.length;
        const slidesToShow = window.innerWidth <= 768 ? 1 : 2; // Responsive: 1 en móvil, 2 en desktop
        
        // Set initial position
        function setupCarousel() {
            // Determinar cuántos slides mostrar según el ancho de la pantalla
            const currentSlidesToShow = window.innerWidth <= 768 ? 1 : 2;
            
            const carouselWidth = document.querySelector('.carousel').offsetWidth;
            const slideWidth = carouselWidth / currentSlidesToShow;
            
            // Configurar el ancho total del track
            track.style.width = (slideWidth * slideCount) + 'px';
            
            // Configurar el ancho de cada slide
            slides.forEach((slide) => {
                slide.style.width = slideWidth + 'px';
            });
            
            // Actualizar la posición actual para mantener la vista correcta
            updateSlidePosition();
        }
        
        function updateSlidePosition() {
            const currentSlidesToShow = window.innerWidth <= 768 ? 1 : 2;
            const maxPosition = slideCount - currentSlidesToShow;
            
            // Asegurarse que la posición actual es válida
            if (currentSlide > maxPosition) {
                currentSlide = maxPosition;
            }
            
            const carouselWidth = document.querySelector('.carousel').offsetWidth;
            const slideWidth = carouselWidth / currentSlidesToShow;
            track.style.transform = `translateX(-${currentSlide * slideWidth}px)`;
        }

        function goToSlide(index) {
            const currentSlidesToShow = window.innerWidth <= 768 ? 1 : 2;
            const maxPosition = slideCount - currentSlidesToShow;
            
            if (index < 0) {
                currentSlide = maxPosition;
            } else if (index > maxPosition) {
                currentSlide = 0;
            } else {
                currentSlide = index;
            }
            
            updateSlidePosition();
        }

        window.prevSlide = function() {
            goToSlide(currentSlide - 1);
        };

        window.nextSlide = function() {
            goToSlide(currentSlide + 1);
        };

        // Initialize carousel
        setupCarousel();
        
        // Auto slide every 5 seconds
        const autoSlideInterval = setInterval(() => {
            window.nextSlide();
        }, 5000);
        
        // Detener autoplay cuando el usuario interactúa con el carrusel
        const carouselContainer = document.querySelector('.carousel-container');
        if (carouselContainer) {
            carouselContainer.addEventListener('mouseenter', () => {
                clearInterval(autoSlideInterval);
            });
            
            carouselContainer.addEventListener('mouseleave', () => {
                // Reiniciar autoplay cuando el mouse sale
                clearInterval(autoSlideInterval);
                const newInterval = setInterval(() => {
                    window.nextSlide();
                }, 5000);
            });
        }
        
        // Recalculate carousel on window resize
        window.addEventListener('resize', setupCarousel);
    }
    
    // Add animation to sections when they come into view
    const animateOnScroll = function() {
        const sections = document.querySelectorAll('.tratamiento, .carousel-container, .intro-box, h4');
        
        sections.forEach(section => {
            const sectionTop = section.getBoundingClientRect().top;
            const windowHeight = window.innerHeight;
            
            if (sectionTop < windowHeight - 100) {
                section.style.animation = 'slide-up 0.8s ease forwards';
            }
        });
    };
    
    // Initial check and add event listener
    animateOnScroll();
    window.addEventListener('scroll', animateOnScroll);
    
    // Update current year in footer copyright
    const yearSpan = document.getElementById('current-year');
    if (yearSpan) {
        yearSpan.textContent = new Date().getFullYear();
    }
});

// Add subtle parallax effect to the background
window.addEventListener('scroll', function() {
    const cover = document.querySelector('.article-container-cover');
    if (cover) {
        const scrollPosition = window.scrollY;
        cover.style.backgroundPosition = `center ${scrollPosition * 0.4}px`;
    }
});
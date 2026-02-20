<?php include 'views/cabecera/cabecera.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HappyDent - Clínica Dental Especializada</title>
    
    <link rel="stylesheet" href="assets/css/principalfondo.css">   
   
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>    
    <!-- Main Content -->
    <div class="container-all" id="move-content">
        <!-- Hero Banner -->
        <div class="article-container-cover">
          
        </div>
        
        <!-- Introduction Text -->
        <div class="container-content">
            <div class="intro-container">
                <div class="intro-box">
                    <h2 class="intro-title">Bienvenidos a <span>HappyDent</span></h2>
                    <p class="intro-text">Somos una clínica dental comprometida con su salud bucal. Nuestro equipo de profesionales está altamente calificado para atender todas sus necesidades. Ofrecemos los tratamientos más avanzados para que usted pueda lucir una sonrisa radiante y saludable.</p>
                </div>
            </div>
        </div>

        <!-- Services Carousel -->
        <div class="carousel-container" id="servicios">
            <button class="carousel-button left" onclick="prevSlide()">&#9664;</button>
            <div class="carousel">
                <div class="carousel-track">
                    <div class="carousel-item"><img src="assets/images/ortodoncia.jpg" alt="Ortodoncia"></div>
                    <div class="carousel-item"><img src="assets/images/endodoncia.jfif" alt="Endodoncia"></div>
                    <div class="carousel-item"><img src="assets/images/profilaxis.jfif" alt="Profilaxis"></div>
                    <div class="carousel-item"><img src="assets/images/restauracion dental.png" alt="Restauración dental"></div>
                    <div class="carousel-item"><img src="assets/images/protesis.png" alt="Prótesis"></div>
                    <div class="carousel-item"><img src="assets/images/implante.jpg" alt="Implantes"></div>
                    <div class="carousel-item"><img src="assets/images/exodoncia.jpg" alt="Exodoncia"></div>
                </div>
            </div>
            <button class="carousel-button right" onclick="nextSlide()">&#9654;</button>
        </div>
        
        <!-- Services Section -->
        <main>
            <div class="tratamiento">
                <img src="assets/images/ortodoncia.jpg" alt="Ortodoncia">
                <h3>Ortodoncia</h3>
                <p>Corregimos la posición de tus dientes para lograr una sonrisa armoniosa y saludable.</p>
            </div>
            <div class="tratamiento">
                <img src="assets/images/endodoncia.jfif" alt="Endodoncia">
                <h3>Endodoncia</h3>
                <p>Tratamiento especializado para salvar dientes afectados por caries profundas.</p>
            </div>
            <div class="tratamiento">
                <img src="assets/images/profilaxis.jfif" alt="Profilaxis">
                <h3>Profilaxis</h3>
                <p>Limpieza dental profesional para mantener tus dientes sanos y libres de sarro.</p>
            </div>
        </main>
        
        <!-- Back to Top Button -->
        <div id="button-up">
            <i class="fas fa-chevron-up"></i>
        </div>
    </div>

    <!-- Scripts -->
    <script src="assets/js/script_menu.js"></script>
    <script src="/clinica1/assets/js/cabecera.js"></script>
   
</body>
</html>
<?php include 'views/cabecera/pie.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Especialidades - HappyDent</title>
    
    <link rel="stylesheet" href="../../assets/css/nosotros.css">   
   
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>    
    <!-- Header - menu -->
    <header>
     <div class="hora">HORARIO: LUNES A VIERNES - 9am a 7pm / SÁBADOS - 9am a 1pm</div>
    <div class="header-content">
    <div class="logo">
        <img src="../../assets/images/tarjeta.jpg" alt="Logo" class="logo-img" width="80" height="80">
        <a href="../../index.php"> <h1>Happy<b>Dent</b></h1></a>
    </div>
                
    <div class="menu" id="show-menu">
                <nav>
                    <ul>
                    <li><a href="../../index.php"><div class="icon-square"><i class="fas fa-home"></i></div> INICIO</a></li>
                   <li><a href="../especialidades/especialidades.php"><div class="icon-square"><i class="fab fa-youtube"></i></div> ESPECIALIDADES</a></li>

<li><a href="../doctor/login.php"><div class="icon-square"><i class="fas fa-headset"></i></div> DOCTOR</a></li>
<li><a href="../users/login_register.php"><div class="icon-square"><i class="fas fa-home"></i></div> ACCEDER</a></li>
                    </ul>
                </nav>
            </div>
        </div>
        <div id="icon-menu">
            <i class="fas fa-bars"></i>
        </div>
    </header>

    <!-- Contenido Principal -->
    <div class="container-all" id="move-content">
        <div class="article-container-cover">
            <h1 id="titulopag"></h1>
        </div>
        
        <main>
            <div class="especialidades-container">
                <h1 class="especialidades-title">ESPECIALIDADES</h1>
            </div>
            <br>
            
            <div class="tratamiento">           
                <img src="../../assets/images/ortodoncia.jpg" alt="Ortodoncia">
                <h2>Ortodoncia</h2>
                <p>Se encarga de los problemas de los dientes y la mandíbula. La atención dental con ortodoncia incluye el uso de dispositivos, tales como aparatos (frenos), para enderezar los dientes.</p>
            </div>
            
            <div class="tratamiento">
                <img src="../../assets/images/endodoncia.jfif" alt="Endodoncia">
                <h2>Endodoncia</h2>
                <p>Es un procedimiento que tiene como finalidad preservar las piezas dañadas, evitando así su pérdida. Para ello se extrae la pulpa dental y la cavidad resultante, se rellena y sella con material inerte y biocompatible.</p>
            </div>
            
            <div class="tratamiento">
                <img src="../../assets/images/restauracion dental.png" alt="Restauración Dental">
                <h2>Restauración Dental</h2>
                <p>Es para poder devolver al diente dañado la forma y la función perdida mediante el uso de técnicas y materiales específicos.</p>
            </div>
            
            <div class="tratamiento">
                <img src="../../assets/images/profilaxis.jfif" alt="Profilaxis">
                <h2>Profilaxis</h2>
                <p>Su objetivo es prevenir patologías periodontales potencialmente graves. De esta manera se encarga de la limpieza bucal, eliminando el sarro y las bacterias del paciente.</p>
            </div>
            
            <div class="tratamiento">
                <img src="../../assets/images/protesis.png" alt="Prótesis">
                <h2>Prótesis</h2>
                <p>Es una estructura metálica con varios dientes artificiales que se ancla a los dientes y sirve para reponer las piezas ausentes o estructuras óseas que se han reabsorbido a lo largo del tiempo con la pérdida de los dientes naturales.</p>
            </div>
            
            <div class="tratamiento">
                <img src="../../assets/images/implante.jpg" alt="Implantes">
                <h2>Implante</h2>
                <p>Es un procedimiento que reemplaza las raíces de los dientes con pernos metálicos que parecen tornillos y reemplaza el diente faltante, o dañado, con un diente artificial que tiene el mismo aspecto y que cumple la misma función que los dientes reales.</p>
            </div>
            
            <div class="tratamiento">
                <img src="../../assets/images/exodoncia.jpg" alt="Exodoncia">
                <h2>Exodoncia</h2>
                <p>Es una técnica odontológica que consiste en la extracción de un diente dañado o que presenta problemas para la salud bucodental del paciente. Se trata de una intervención quirúrgica basada en la extracción de una pieza dental de la cavidad bucal.</p>
            </div>
        </main>
    </div>

    <!-- Botón ir arriba -->
    <div id="button-up">
        <i class="fas fa-chevron-up"></i>
    </div>

    <!-- Scripts -->
    <script src="../../assets/js/script_menu.js"></script>
    <script src="../../assets/js/especialidades.js"></script>
    <script>
        // Script para el menú móvil
document.getElementById('icon-menu').addEventListener('click', function() {
    var menu = document.getElementById('show-menu');
    var container = document.getElementById('move-content');
    
    menu.classList.toggle('show-lateral');
    container.classList.toggle('move-container-all');
});

// Script para botón ir arriba
window.addEventListener('scroll', function() {
    var buttonUp = document.getElementById('button-up');
    if (window.pageYOffset > 600) {
        buttonUp.classList.add('show');
    } else {
        buttonUp.classList.remove('show');
    }
});

document.getElementById('button-up').addEventListener('click', function() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
});

// Script MODIFICADO para ocultar/mostrar header SOLO cuando esté arriba
var header = document.querySelector('header');

window.addEventListener('scroll', function() {
    var scrollTop = window.pageYOffset || document.documentElement.scrollTop;
    
    // Solo mostrar header cuando el scroll esté en la parte superior (primeros 50px)
    if (scrollTop <= 50) {
        header.classList.add('header-visible');
        header.classList.remove('header-hidden');
    } else {
        // Ocultar header cuando se baje del top
        header.classList.add('header-hidden');
        header.classList.remove('header-visible');
    }
});
        </script>
</body>
</html>
<?php include '../cabecera/pie.php'; ?>
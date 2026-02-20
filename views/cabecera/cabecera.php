<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Header - HappyDent</title>
    
    <link rel="stylesheet" href="/clinica1/assets/css/cabecera.css">   
   
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>    
    <!-- Header - menu -->
    <header>
        <div class="hora">HORARIO: LUNES A VIERNES - 9am a 7pm / SÁBADOS - 9am a 1pm</div>
        <div class="header-content">
            <div class="logo">
                <img src="assets/images/tarjeta.jpg" alt="Logo HappyDent" class="logo-img" width="80" height="80">
                <h1>Happy<b>Dent</b></h1>
            </div>
                    
            <div class="menu" id="show-menu">
                <nav>
                    <ul>
                        <li><a href="views/especialidades/especialidades.php"><i class="fas fa-tooth"></i> ESPECIALIDADES</a></li>
                        <li><a href="views/nosotros/nosotros.php"><i class="fas fa-users"></i> NOSOTROS</a></li>
                        <li><a href="views/doctor/login.php"><i class="fas fa-user-md"></i> DOCTOR</a></li>
                        <li><a href="views/users/login_register.php"><i class="fas fa-sign-in-alt"></i> ACCEDER</a></li>
                    </ul>
                </nav>
            </div>
        </div>
        <div id="icon-menu">
            <i class="fas fa-bars"></i>
        </div>
    </header>

    <!-- Scripts -->
    <script src="/clinica1/assets/js/script_menu.js"></script>
    <script src="/clinica1/assets/js/cabecera.js"></script>
</body>
</html>
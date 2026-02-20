<?php
session_start(); // Inicia la sesión para manejar el estado del usuario
if (isset($_SESSION['usuario'])) {
    header("Location: patient_dashboard.php"); // Redirige si ya está autenticado
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login y Register - MagtimusPro</title>
    <link rel="stylesheet" href="../../assets/css/encabezamiento.css">  
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;300;400;500;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/accesoestilo.css">
</head>
<body>
<header>
    <div class="logo">
        <a href="../../index.php"><img src="../../assets/images/tarjeta.jpg" alt="Logo" class="logo-img" width="80" height="80"></a>
    </div>
</header>
<main>
    <div class="contenedor__todo">
        <div class="caja__trasera">
            <div class="caja__trasera-login">
                <h3>¿Ya tienes una cuenta?</h3>
                <p>Inicia sesión para entrar en la página</p>
                <button id="btn__iniciar-sesion">Iniciar Sesión</button>
            </div>
            <div class="caja__trasera-register">
                <h3>¿Aún no tienes una cuenta?</h3>
                <p>Regístrate para que puedas iniciar sesión</p>
                <button id="btn__registrarse">Regístrarse</button>
            </div>
        </div>

        <!--Formulario de Login y registro-->
        <div class="contenedor__login-register">
            <!--Login-->
            <form action="../../public/index.php?controller=user&action=login" method="POST" class="formulario__login">
                <h2>Iniciar Sesión</h2>
                <input type="text" placeholder="Usuario" name="usuario" required>
                <input type="password" placeholder="Contraseña" name="contrasena" required>
                <button>Entrar</button>
            </form>

            <!--Register-->
            <form action="../../public/index.php?controller=user&action=create" method="POST" class="formulario__register">
                <h2>Regístrarse</h2>
                <input type="text" placeholder="Nombre completo" name="nombre_completo" required>
                <input type="email" placeholder="Correo Electrónico" name="correo_electronico" required>
                <input type="text" placeholder="Usuario" name="usuario" required>
                <input type="password" placeholder="Contraseña" name="contrasena" required>
                <button>Regístrarse</button>
            </form>
        </div>
    </div>
</main>
<script src="../../assets/js/acceso.js"></script>
</body>
</html>

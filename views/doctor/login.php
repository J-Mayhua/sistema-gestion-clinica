<!DOCTYPE html>
<html>
<head>
    <title>Login Doctor</title>
    <style>
        body, html {
            height: 100%;
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            background: url('../../assets/images/DOC1.jpg') no-repeat center center fixed;
            background-size: cover;
        }
        .login-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
        }
        .login-box {
            background-color: rgba(255, 255, 255, 0.1); /* Transparencia del cuadro */
            padding: 40px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .login-box h1 {
            margin-bottom: 20px;
            font-size: 32px;
            color: white; /* Color blanco para el texto */
            font-weight: bold;
        }
        .login-box a {
            color: white; /* Letra blanca */
            text-decoration: none; /* Sin subrayado */
            font-size: 36px; /* Tamaño de fuente aumentado */
        }
        .login-box a:hover {
            text-decoration: underline; /* Subrayado al pasar el mouse */
        }
        .login-box label {
            display: none; /* Ocultar etiquetas para simplificar el diseño */
        }
        .login-box input[type="email"],
        .login-box input[type="password"] {
            width: 80%;
            padding: 15px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }
        .login-box input[type="submit"] {
            background-color: white; /* Fondo blanco */
            color: black; /* Letra negra */
            padding: 15px 30px;
            border: 1px solid black; /* Borde negro */
            border-radius: 25px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
            margin-top: 20px;
        }
        .login-box input[type="submit"]:hover {
            background-color: #f0f0f0; /* Color de fondo ligeramente gris para el hover */
        }
        .login-box .input-container {
            position: relative;
            width: 80%;
            margin: 10px auto;
        }
        .login-box .input-container input {
            width: 100%;
            padding: 15px;
            border: 1px solid #ccc;
            border-radius: 25px;
            font-size: 16px;
            padding-left: 50px; /* Espacio para el ícono */
        }
        .login-box .input-container .icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 20px;
            color: #aaa;
            
        }
        .message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #f5c6cb;
            border-radius: 5px;
            text-align: center;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
<div class="login-container">
        <div class="login-box">
            <h1><a href="../../index.php">Iniciar Sesión</a></h1><br><br>
            <?php if (!empty($message)): ?>
                <div class="message"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>
            <form action="../../public/index.php?controller=doctor&action=login" method="POST">
                <div class="input-container">
                    <i class="fas fa-user icon"></i>
                    <input type="email" name="correo" placeholder="Correo" required>
                </div>
                <div class="input-container">
                    <i class="fas fa-lock icon"></i>
                    <input type="password" name="contrasena" placeholder="Contraseña" required>
                </div>
                <input type="submit" value="Acceder">
            </form>
        </div>
    </div>
</body>
</html>
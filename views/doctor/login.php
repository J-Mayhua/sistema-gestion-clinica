<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HappyDent — Acceso Doctor</title>
    <link rel="stylesheet" href="/clinica/assets/css/cabecera.css">
    <link rel="stylesheet" href="/clinica/assets/css/login-doctor.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="/clinica/assets/js/cabecera.js"></script>

</head>
<body class="login-page">

<?php require_once __DIR__ . '/../cabecera/cabecera.php'; ?>

<div class="login-wrapper">

    <!-- Panel izquierdo — decorativo -->
    <div class="login-panel-left">
        <div class="panel-content">
            <div class="panel-logo">🦷</div>
            <h2>Portal <span>Médico</span></h2>
            <p>Accede a tu panel de gestión de citas, pacientes y tratamientos.</p>
            <ul class="panel-features">
                <li><i class="fas fa-calendar-check"></i> Gestión de citas</li>
                <li><i class="fas fa-users"></i> Historial de pacientes</li>
                <li><i class="fas fa-chart-line"></i> Reportes clínicos</li>
                <li><i class="fas fa-shield-alt"></i> Acceso seguro</li>
            </ul>
        </div>
    </div>

    <!-- Panel derecho — formulario -->
    <div class="login-panel-right">
        <div class="login-box">

            <div class="login-header">
                <div class="login-icon">
                    <i class="fas fa-user-md"></i>
                </div>
                <h1>Acceso Doctor</h1>
                <p>Ingresa tus credenciales para continuar</p>
                <div class="credenciales-prueba">
                <strong>Credenciales de prueba:</strong>
                <span><b>Correo:</b> doctor@gmail.com</span>
                <span><b>Contraseña:</b> Doctor1234</span>
            </div>
            </div>

            <?php if (!empty($message)): ?>
                <div class="login-alert">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <form action="/clinica/public/index.php?controller=doctor&action=login"
                  method="POST" class="login-form">

                <div class="input-group">
                    <label for="correo">Correo electrónico</label>
                    <div class="input-wrap">
                        <i class="fas fa-envelope"></i>
                        <input
                            type="email"
                            id="correo"
                            name="correo"
                            placeholder="doctor@happydent.com"
                            required
                            autocomplete="email"
                        >
                    </div>
                </div>

                <div class="input-group">
                    <label for="contrasena">Contraseña</label>
                    <div class="input-wrap">
                        <i class="fas fa-lock"></i>
                        <input
                            type="password"
                            id="contrasena"
                            name="contrasena"
                            placeholder="••••••••"
                            required
                            autocomplete="current-password"
                        >
                        <button type="button" class="toggle-pass" tabindex="-1"
                                onclick="togglePass()">
                            <i class="fas fa-eye" id="eye-icon"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn-login">
                    <i class="fas fa-sign-in-alt"></i> Ingresar al panel
                </button>

            </form>



        </div>
    </div>

</div><!-- /login-wrapper -->

<?php require_once __DIR__ . '/../cabecera/pie.php'; ?>

<script>
function togglePass() {
    const input   = document.getElementById('contrasena');
    const icon    = document.getElementById('eye-icon');
    const visible = input.type === 'password';
    input.type    = visible ? 'text' : 'password';
    icon.className = visible ? 'fas fa-eye-slash' : 'fas fa-eye';
}
</script>

</body>
</html>

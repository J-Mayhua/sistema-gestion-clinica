<?php
session_start();

if (isset($_SESSION['usuario'])) {
    header("Location: patient_dashboard.php");
    exit();
}

/*
 * Mensajes de una sola lectura: el controlador los guarda en la sesión
 * antes de redirigir a esta página.
 */
$accesoError = $_SESSION['acceso_error'] ?? null;
$accesoExito = $_SESSION['acceso_exito'] ?? null;
$accesoFormulario = $_SESSION['acceso_formulario'] ?? 'login';

unset(
    $_SESSION['acceso_error'],
    $_SESSION['acceso_exito'],
    $_SESSION['acceso_formulario']
);

function escaparAcceso($valor): string
{
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HappyDent — Acceder</title>
    <link rel="stylesheet" href="/clinica/assets/css/cabecera.css">
    <link rel="stylesheet" href="/clinica/assets/css/accesoestilo.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="/clinica/assets/js/cabecera.js"></script>

</head>
<body class="acceso-page">

<?php require_once __DIR__ . '/../cabecera/cabecera.php'; ?>

<main class="acceso-main">

    <?php if ($accesoError !== null || $accesoExito !== null): ?>
        <div class="acceso-aviso-wrap">
            <?php if ($accesoError !== null): ?>
                <div class="acceso-aviso acceso-aviso--error" role="alert">
                    <i class="fas fa-circle-exclamation" aria-hidden="true"></i>
                    <span><?= escaparAcceso($accesoError) ?></span>
                </div>
            <?php endif; ?>

            <?php if ($accesoExito !== null): ?>
                <div class="acceso-aviso acceso-aviso--exito" role="status">
                    <i class="fas fa-circle-check" aria-hidden="true"></i>
                    <span><?= escaparAcceso($accesoExito) ?></span>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="contenedor__todo">

        <!-- Caja trasera -->
        <div class="caja__trasera">
            <div class="caja__trasera-login">
                <div class="caja-icon"><i class="fas fa-user-circle"></i></div>
                <h3>¿Ya tienes cuenta?</h3>
                <p>Inicia sesión para gestionar tus citas y tratamientos</p>
                <button type="button" id="btn__iniciar-sesion">Iniciar Sesión</button>
            </div>
            <div class="caja__trasera-register">
                <div class="caja-icon"><i class="fas fa-user-plus"></i></div>
                <h3>¿Aún no tienes cuenta?</h3>
                <p>Regístrate y agenda tu primera consulta gratis</p>
                <button type="button" id="btn__registrarse">Registrarse</button>
            </div>
        </div>

        <!-- Formularios -->
        <div class="contenedor__login-register">

            <!-- Login -->
            <form action="/clinica/public/index.php?controller=user&action=login"
                  method="POST" class="formulario__login">
                <div class="form-header">
                    <div class="form-icon"><i class="fas fa-sign-in-alt"></i></div>
                    <h2>Iniciar Sesión</h2>
                    <p>Bienvenido de vuelta</p>
                </div>
                <div class="input-group">
                    <i class="fas fa-user"></i>
                    <input type="text" placeholder="Usuario" name="usuario"
                           required autocomplete="username">
                </div>
                <div class="input-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" placeholder="Contraseña" name="contrasena"
                           id="pass-login" required autocomplete="current-password">
                    <button type="button" class="toggle-pass"
                            onclick="togglePass('pass-login','eye-login')"
                            aria-label="Mostrar u ocultar contraseña">
                        <i class="fas fa-eye" id="eye-login"></i>
                    </button>
                </div>
                <button type="submit" class="btn-submit">
                    <i class="fas fa-arrow-right"></i> Entrar
                </button>
            </form>

            <!-- Registro -->
            <form action="/clinica/public/index.php?controller=user&action=create"
                  method="POST" class="formulario__register">
                <div class="form-header">
                    <div class="form-icon form-icon--verde"><i class="fas fa-user-plus"></i></div>
                    <h2>Crear Cuenta</h2>
                    <p>Es rápido y gratuito</p>
                </div>
                <div class="input-group">
                    <i class="fas fa-id-card"></i>
                    <input type="text" placeholder="Nombre completo"
                           name="nombre_completo" required autocomplete="name">
                </div>
                <div class="input-group">
                    <i class="fas fa-envelope"></i>
                    <input type="email" placeholder="Correo electrónico"
                           name="correo_electronico" required autocomplete="email">
                </div>
                <div class="input-group">
                    <i class="fas fa-user"></i>
                    <input type="text" placeholder="Usuario"
                           name="usuario" required autocomplete="username">
                </div>
                <div class="input-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" placeholder="Contraseña"
                           name="contrasena" id="pass-reg"
                           required autocomplete="new-password">
                    <button type="button" class="toggle-pass"
                            onclick="togglePass('pass-reg','eye-reg')"
                            aria-label="Mostrar u ocultar contraseña">
                        <i class="fas fa-eye" id="eye-reg"></i>
                    </button>
                </div>
                <button type="submit" class="btn-submit btn-submit--verde">
                    <i class="fas fa-check"></i> Registrarse
                </button>
            </form>

        </div>
    </div>
</main>

<?php require_once __DIR__ .  '/clinica/views/cabecera/pie.php'; ?>

<script src="/clinica/assets/js/acceso.js"></script>
<script>
function togglePass(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(iconId);
    const show = input.type === 'password';

    input.type = show ? 'text' : 'password';
    icon.className = show ? 'fas fa-eye-slash' : 'fas fa-eye';
}

<?php if ($accesoFormulario === 'registro'): ?>
// Tras un error de registro, volver a mostrar ese formulario.
document.getElementById('btn__registrarse')?.click();
<?php endif; ?>
</script>

<style>
.acceso-aviso-wrap {
    width: min(92%, 800px);
    margin: 20px auto 0;
}

.acceso-aviso {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 16px;
    margin-bottom: 10px;
    border-radius: 8px;
    font-size: 14px;
    line-height: 1.4;
}

.acceso-aviso--error {
    color: #842029;
    background: #f8d7da;
    border: 1px solid #f5c2c7;
}

.acceso-aviso--exito {
    color: #0f5132;
    background: #d1e7dd;
    border: 1px solid #badbcc;
}
</style>

</body>
</html>

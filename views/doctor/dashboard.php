<?php
session_start();

if (!isset($_SESSION['doctor_id'])) {
    header('Location: login.php');
    exit();
}

$titulo_pagina_doctor = 'HappyDent — Panel del doctor';
$css_pagina_doctor = '/clinica/assets/css/doctor_dashboard.css';

require_once __DIR__ . '/../cabecera/cabecera_doctor.php';
?>

<main class="doctor-dashboard-main">
    <div class="doctor-dashboard-contenedor">

        <section
            class="doctor-dashboard-hero"
            aria-labelledby="doctor-dashboard-titulo"
        >
            <div class="doctor-dashboard-hero-texto">
                <span class="doctor-dashboard-etiqueta">
                    <i class="fas fa-user-md" aria-hidden="true"></i>
                    Área del doctor
                </span>

                <h1 id="doctor-dashboard-titulo">
                    Bienvenido a HappyDent
                </h1>

                <p>
                    Desde aquí puedes acceder a las herramientas para gestionar
                    tus pacientes, citas y horarios.
                </p>
            </div>

            <div class="doctor-dashboard-hero-icono" aria-hidden="true">
                <i class="fas fa-tooth"></i>
            </div>
        </section>

        <section
            class="doctor-dashboard-panel"
            aria-labelledby="especialidades-titulo"
        >
            <div class="doctor-dashboard-panel-encabezado">
                <div>
                    <span class="doctor-dashboard-subtitulo">
                        Nuestra atención
                    </span>

                    <h2 id="especialidades-titulo">
                        Especialidades odontológicas
                    </h2>
                </div>

                <p>
                    Conoce las áreas de atención de HappyDent.
                </p>
            </div>

            <div class="doctor-dashboard-grid">
                <article class="doctor-dashboard-tarjeta">
                    <div class="doctor-dashboard-imagen">
                        <img
                            src="/clinica/assets/images/protesis.png"
                            alt=""
                            loading="lazy"
                        >
                    </div>

                    <div class="doctor-dashboard-tarjeta-contenido">
                        <h3>Prótesis</h3>
                        <p>
                            Soluciones para recuperar la función y la apariencia
                            de la sonrisa.
                        </p>
                    </div>
                </article>

                <article class="doctor-dashboard-tarjeta">
                    <div class="doctor-dashboard-imagen">
                        <img
                            src="/clinica/assets/images/ortodoncia.jpg"
                            alt=""
                            loading="lazy"
                        >
                    </div>

                    <div class="doctor-dashboard-tarjeta-contenido">
                        <h3>Ortodoncia</h3>
                        <p>
                            Atención orientada a la alineación de los dientes
                            y la mordida.
                        </p>
                    </div>
                </article>

                <article class="doctor-dashboard-tarjeta">
                    <div class="doctor-dashboard-imagen">
                        <img
                            src="/clinica/assets/images/exodoncia.jpg"
                            alt=""
                            loading="lazy"
                        >
                    </div>

                    <div class="doctor-dashboard-tarjeta-contenido">
                        <h3>Exodoncia</h3>
                        <p>
                            Procedimientos de extracción dental según
                            la evaluación clínica.
                        </p>
                    </div>
                </article>

                <article class="doctor-dashboard-tarjeta">
                    <div class="doctor-dashboard-imagen">
                        <img
                            src="/clinica/assets/images/endodoncia.jfif"
                            alt=""
                            loading="lazy"
                        >
                    </div>

                    <div class="doctor-dashboard-tarjeta-contenido">
                        <h3>Endodoncia</h3>
                        <p>
                            Tratamientos enfocados en el interior del diente
                            para conservarlo cuando sea posible.
                        </p>
                    </div>
                </article>
            </div>
        </section>

    </div>
</main>

<?php require_once __DIR__ . '/../cabecera/pie_paciente.php'; ?>

</body>
</html>

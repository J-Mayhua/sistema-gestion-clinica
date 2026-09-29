<?php
session_start();

if (!isset($_SESSION['doctor_id'])) {
    header('Location: login.php');
    exit();
}

$titulo_pagina_doctor = 'HappyDent — Agregar paciente';
$css_pagina_doctor = '/clinica/assets/css/doctor_add_patient.css';

require_once __DIR__ . '/../cabecera/cabecera_doctor.php';
?>

<main class="doctor-agregar-main">
    <div class="doctor-agregar-contenedor">

        <div class="doctor-agregar-intro">
            <span class="doctor-agregar-etiqueta">
                <i class="fas fa-user-plus" aria-hidden="true"></i>
                Área del doctor
            </span>

            <h1>Agregar nuevo paciente</h1>

            <p>
                Completa los datos del paciente y revisa la información
                antes de registrarla.
            </p>
        </div>

        <section
            class="doctor-agregar-panel"
            aria-labelledby="doctor-agregar-formulario-titulo"
        >
            <div class="doctor-agregar-panel-titulo">
                <h2 id="doctor-agregar-formulario-titulo">
                    Datos del paciente
                </h2>
                <p>Todos los campos son obligatorios.</p>
            </div>

            <form
                action="add_patient.php"
                method="post"
                class="doctor-agregar-formulario"
            >
                <div class="doctor-agregar-campos">
                    <div class="doctor-agregar-campo">
                        <label for="dni">DNI</label>
                        <input
                            type="text"
                            id="dni"
                            name="dni"
                            required
                            autocomplete="off"
                        >
                    </div>

                    <div class="doctor-agregar-campo">
                        <label for="nombre">Nombre</label>
                        <input
                            type="text"
                            id="nombre"
                            name="nombre"
                            required
                            autocomplete="name"
                        >
                    </div>

                    <div class="doctor-agregar-campo">
                        <label for="fecha_nacimiento">
                            Fecha de nacimiento
                        </label>
                        <input
                            type="date"
                            id="fecha_nacimiento"
                            name="fecha_nacimiento"
                            required
                            autocomplete="bday"
                        >
                    </div>

                    <div class="doctor-agregar-campo">
                        <label for="sexo">Sexo</label>
                        <select id="sexo" name="sexo" required>
                            <option value="" selected disabled>
                                Selecciona una opción
                            </option>
                            <option value="M">Masculino</option>
                            <option value="F">Femenino</option>
                        </select>
                    </div>

                    <div class="doctor-agregar-campo doctor-agregar-campo-completo">
                        <label for="direccion">Dirección</label>
                        <input
                            type="text"
                            id="direccion"
                            name="direccion"
                            required
                            autocomplete="street-address"
                        >
                    </div>

                    <div class="doctor-agregar-campo">
                        <label for="telefono">Teléfono</label>
                        <input
                            type="tel"
                            id="telefono"
                            name="telefono"
                            required
                            autocomplete="tel"
                        >
                    </div>

                    <div class="doctor-agregar-campo">
                        <label for="especialidad">Especialidad</label>
                        <input
                            type="text"
                            id="especialidad"
                            name="especialidad"
                            required
                        >
                    </div>
                </div>

                <div class="doctor-agregar-acciones">
                    <p>Verifica que los datos sean correctos antes de continuar.</p>

                    <button type="submit" class="doctor-agregar-enviar">
                        <i class="fas fa-user-plus" aria-hidden="true"></i>
                        Agregar paciente
                    </button>
                </div>
            </form>
        </section>

    </div>
</main>

<?php require_once __DIR__ . '/../cabecera/pie_paciente.php'; ?>

</body>
</html>

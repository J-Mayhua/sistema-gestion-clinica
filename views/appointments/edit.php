<!DOCTYPE html>
<html>
<head>
    <title>Editar Cita</title>
</head>
<body>
    <h1>Editar Cita</h1>
    <form action="index.php?controller=appointment&action=edit&id=<?php echo $appointment['cita_id']; ?>" method="post">
        <label for="dni">DNI:</label>
        <input type="text" id="dni" name="dni" value="<?php echo $appointment['dni']; ?>" required><br>

        <label for="fecha_nacimiento">Fecha de Nacimiento:</label>
        <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" value="<?php echo $appointment['fecha_nacimiento']; ?>" required><br>

        <label for="sexo">Sexo:</label>
        <select id="sexo" name="sexo" required>
            <option value="M" <?php echo ($appointment['sexo'] == 'M') ? 'selected' : ''; ?>>Masculino</option>
            <option value="F" <?php echo ($appointment['sexo'] == 'F') ? 'selected' : ''; ?>>Femenino</option>
        </select><br>

        <label for="direccion">Dirección:</label>
        <input type="text" id="direccion" name="direccion" value="<?php echo $appointment['direccion']; ?>" required><br>

        <label for="telefono">Teléfono:</label>
        <input type="text" id="telefono" name="telefono" value="<?php echo $appointment['telefono']; ?>" required><br>

        <label for="especialidad">Especialidad:</label>
        <input type="text" id="especialidad" name="especialidad" value="<?php echo $appointment['especialidad']; ?>" required><br>

        <label for="usuario_id">ID de Usuario:</label>
        <input type="number" id="usuario_id" name="usuario_id" value="<?php echo $appointment['usuario_id']; ?>" required><br>

        <label for="doctor_id">ID de Doctor:</label>
        <input type="number" id="doctor_id" name="doctor_id" value="<?php echo $appointment['doctor_id']; ?>" required><br>

        <input type="submit" value="Actualizar Cita">
    </form>
    <a href="index.php?controller=appointment&action=index">Volver a la lista</a>
</body>
</html>
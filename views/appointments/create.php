<!DOCTYPE html>
<html>
<head>
    <title>Crear Cita</title>
</head>
<body>
    <h1>Crear Cita</h1>
    <form action="../../public/index.php?controller=appointment&action=create" method="POST">
        <label for="dni">DNI:</label>
        <input type="text" name="dni" required><br>

        <label for="fecha_nacimiento">Fecha de Nacimiento:</label>
        <input type="date" name="fecha_nacimiento" required><br>

        <label for="sexo">Sexo:</label>
        <input type="text" name="sexo" required><br>

        <label for="direccion">Dirección:</label>
        <input type="text" name="direccion" required><br>

        <label for="telefono">Teléfono:</label>
        <input type="text" name="telefono" required><br>

        <label for="especialidad">Especialidad:</label>
        <input type="text" name="especialidad" required><br>

        <label for="usuario_id">Usuario ID:</label>
        <input type="text" name="usuario_id" required><br>

        <label for="doctor_id">Doctor ID:</label>
        <input type="text" name="doctor_id" required><br>

        <input type="submit" value="Crear">
    </form>
</body>
</html>

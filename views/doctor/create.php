<!DOCTYPE html>
<html>
<head>
    <title>Crear Doctor</title>
</head>
<body>
    <h1>Crear Doctor</h1>
    <form action="../../public/index.php?controller=doctor&action=create" method="POST">
        <label for="correo">Correo:</label>
        <input type="email" name="correo" required><br>

        <label for="contrasena">Contraseña:</label>
        <input type="password" name="contrasena" required><br>

        <label for="nombres">Nombres:</label>
        <input type="text" name="nombres" required><br>

        <label for="apellidos">Apellidos:</label>
        <input type="text" name="apellidos" required><br>

        <label for="especialidad">Especialidad:</label>
        <input type="text" name="especialidad" required><br>

        <label for="telefono">Teléfono:</label>
        <input type="text" name="telefono" required><br>

        <label for="horario">Horario:</label>
        <input type="text" name="horario" required><br>

        <input type="submit" value="Crear">
    </form>
</body>
</html>

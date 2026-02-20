<!DOCTYPE html>
<html>
<head>
    <title>Crear Usuario</title>
</head>
<body>
    <h1>Crear Usuario</h1>
    <form action="../../public/index.php?controller=user&action=create" method="POST">
        <label for="nombre_completo">Nombre Completo:</label>
        <input type="text" name="nombre_completo" required><br>

        <label for="correo_electronico">Correo Electrónico:</label>
        <input type="email" name="correo_electronico" required><br>

        <label for="usuario">Usuario:</label>
        <input type="text" name="usuario" required><br>

        <label for="contrasena">Contraseña:</label>
        <input type="password" name="contrasena" required><br>

        <input type="submit" value="Crear">
    </form>
</body>
</html>

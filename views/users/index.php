<?php
require_once '../../controllers/UserController.php';
$controller = new UserController();
$users = $controller->getUsers();

if ($users === null) {
    $users = []; // Asegúrate de que $users sea un array si json_decode devuelve null
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Lista de Usuarios</title>
</head>
<body>
    <h1>Lista de Usuarios</h1>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Nombre Completo</th>
            <th>Correo Electrónico</th>
            <th>Usuario</th>
        </tr>
        <?php foreach ($users as $user): ?>
        <tr>
            <td><?php echo htmlspecialchars($user['user_id']); ?></td>
            <td><?php echo htmlspecialchars($user['nombre_completo']); ?></td>
            <td><?php echo htmlspecialchars($user['correo_electronica']); ?></td>
            <td><?php echo htmlspecialchars($user['usuario']); ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>

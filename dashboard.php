<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel - MiApp</title>
</head>

<body>

<h1>Bienvenido</h1>

<p>
    Usuario:
    <?= htmlspecialchars($_SESSION['usuario_nombre']) ?>
</p>

<p>
    Correo:
    <?= htmlspecialchars($_SESSION['usuario_email']) ?>
</p>

<p>
    <a href="logout.php">Cerrar sesión</a>
</p>

</body>
</html>

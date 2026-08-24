<?php

session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/config/database.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    header('Location: usuarios.php');
    exit;
}

// Buscar usuario
$sql = "SELECT id, nombre, email
        FROM usuarios
        WHERE id = :id
        LIMIT 1";

$stmt = $conexion->prepare($sql);
$stmt->execute([
    'id' => $id
]);

$usuario = $stmt->fetch();

if (!$usuario) {
    header('Location: usuarios.php');
    exit;
}

// Evitar eliminar al usuario actualmente autenticado
if ((int)$usuario['id'] === (int)$_SESSION['usuario_id']) {
    die('No puedes eliminar tu propio usuario mientras tienes la sesión iniciada.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $sql = "DELETE FROM usuarios
            WHERE id = :id";

    $stmt = $conexion->prepare($sql);
    $stmt->execute([
        'id' => $id
    ]);

    header('Location: usuarios.php');
    exit;
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eliminar usuario - MiApp</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

<div class="contenedor">

    <h1>Eliminar usuario</h1>

    <p>
        ¿Estás seguro de que deseas eliminar al siguiente usuario?
    </p>

    <p>
        <strong>Nombre:</strong>
        <?= htmlspecialchars($usuario['nombre']) ?>
    </p>

    <p>
        <strong>Correo:</strong>
        <?= htmlspecialchars($usuario['email']) ?>
    </p>

    <form method="POST">
        <button type="submit">
            Sí, eliminar usuario
        </button>
    </form>

    <p>
        <a href="usuarios.php">Cancelar</a>
    </p>

</div>

</body>
</html>

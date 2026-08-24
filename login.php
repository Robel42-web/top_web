<?php
session_start();

require_once __DIR__ . '/config/database.php';

$error = '';

if (isset($_SESSION['usuario_id'])) {
    header('Location: dashboard.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = 'Todos los campos son obligatorios.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'El correo electrónico no es válido.';
    } else {

        $sql = "SELECT id, nombre, email, password
                FROM usuarios
                WHERE email = :email
                LIMIT 1";

        $stmt = $conexion->prepare($sql);
        $stmt->execute([
            'email' => $email
        ]);

        $usuario = $stmt->fetch();

        if ($usuario && password_verify($password, $usuario['password'])) {

            session_regenerate_id(true);

            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nombre'] = $usuario['nombre'];
            $_SESSION['usuario_email'] = $usuario['email'];

            header('Location: dashboard.php');
            exit;

        } else {
            $error = 'Correo electrónico o contraseña incorrectos.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión - MiApp</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

<div class="contenedor">

    <h1>Iniciar sesión</h1>

    <?php if ($error !== ''): ?>
        <div class="error">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="">

        <label for="email">Correo electrónico</label>
        <input
            type="email"
            id="email"
            name="email"
            required
        >

        <label for="password">Contraseña</label>
        <input
            type="password"
            id="password"
            name="password"
            required
        >

        <button type="submit">Iniciar sesión</button>

    </form>

</div>

</body>
</html>

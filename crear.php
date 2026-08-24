<?php

session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/config/database.php';

$errores = [];
$nombre = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validar nombre
    if ($nombre === '') {
        $errores[] = 'El nombre es obligatorio.';
    } elseif (strlen($nombre) < 3 || strlen($nombre) > 100) {
        $errores[] = 'El nombre debe tener entre 3 y 100 caracteres.';
    }

    // Validar correo
    if ($email === '') {
        $errores[] = 'El correo electrónico es obligatorio.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores[] = 'El correo electrónico no es válido.';
    } elseif (strlen($email) > 150) {
        $errores[] = 'El correo electrónico es demasiado largo.';
    }

    // Validar contraseña
    if ($password === '') {
        $errores[] = 'La contraseña es obligatoria.';
    } elseif (strlen($password) < 8) {
        $errores[] = 'La contraseña debe tener al menos 8 caracteres.';
    }

    if (empty($errores)) {

        // Comprobar que el correo no exista
        $sql = "SELECT id FROM usuarios WHERE email = :email LIMIT 1";

        $stmt = $conexion->prepare($sql);
        $stmt->execute([
            'email' => $email
        ]);

        if ($stmt->fetch()) {

            $errores[] = 'Ya existe un usuario con ese correo electrónico.';

        } else {

            // Nunca guardar la contraseña en texto plano
            $passwordHash = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            $sql = "INSERT INTO usuarios (nombre, email, password)
                    VALUES (:nombre, :email, :password)";

            $stmt = $conexion->prepare($sql);

            $stmt->execute([
                'nombre' => $nombre,
                'email' => $email,
                'password' => $passwordHash
            ]);

            header('Location: usuarios.php');
            exit;
        }
    }
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Registrar usuario - MiApp</title>

    <link rel="stylesheet" href="style.css">
</head>

<body>

<div class="contenedor">

    <h1>Registrar usuario</h1>

    <?php if (!empty($errores)): ?>

        <div class="error">

            <ul>

                <?php foreach ($errores as $error): ?>

                    <li>
                        <?= htmlspecialchars($error) ?>
                    </li>

                <?php endforeach; ?>

            </ul>

        </div>

    <?php endif; ?>

    <form method="POST" action="">

        <p>
            <label for="nombre">Nombre</label><br>

            <input
                type="text"
                id="nombre"
                name="nombre"
                maxlength="100"
                value="<?= htmlspecialchars($nombre) ?>"
                required
            >
        </p>

        <p>
            <label for="email">Correo electrónico</label><br>

            <input
                type="email"
                id="email"
                name="email"
                maxlength="150"
                value="<?= htmlspecialchars($email) ?>"
                required
            >
        </p>

        <p>
            <label for="password">Contraseña</label><br>

            <input
                type="password"
                id="password"
                name="password"
                minlength="8"
                required
            >
        </p>

        <button type="submit">
            Registrar usuario
        </button>

    </form>

    <p>
        <a href="usuarios.php">Volver a usuarios</a>
    </p>

</div>

</body>
</html>

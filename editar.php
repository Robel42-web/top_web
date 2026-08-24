<?php

session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/config/database.php';

$errores = [];

// Validar ID recibido
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

$nombre = $usuario['nombre'];
$email = $usuario['email'];

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

    // La contraseña es opcional al editar
    if ($password !== '' && strlen($password) < 8) {
        $errores[] = 'La nueva contraseña debe tener al menos 8 caracteres.';
    }

    // Comprobar correo duplicado
    if (empty($errores)) {

        $sql = "SELECT id
                FROM usuarios
                WHERE email = :email
                AND id != :id
                LIMIT 1";

        $stmt = $conexion->prepare($sql);

        $stmt->execute([
            'email' => $email,
            'id' => $id
        ]);

        if ($stmt->fetch()) {
            $errores[] = 'Ya existe otro usuario con ese correo electrónico.';
        }
    }

    // Actualizar usuario
    if (empty($errores)) {

        if ($password !== '') {

            $passwordHash = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            $sql = "UPDATE usuarios
                    SET nombre = :nombre,
                        email = :email,
                        password = :password
                    WHERE id = :id";

            $stmt = $conexion->prepare($sql);

            $stmt->execute([
                'nombre' => $nombre,
                'email' => $email,
                'password' => $passwordHash,
                'id' => $id
            ]);

        } else {

            $sql = "UPDATE usuarios
                    SET nombre = :nombre,
                        email = :email
                    WHERE id = :id";

            $stmt = $conexion->prepare($sql);

            $stmt->execute([
                'nombre' => $nombre,
                'email' => $email,
                'id' => $id
            ]);
        }

        header('Location: usuarios.php');
        exit;
    }
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Editar usuario - MiApp</title>

    <link rel="stylesheet" href="style.css">
</head>

<body>

<div class="contenedor">

    <h1>Editar usuario</h1>

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

    <form method="POST">

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
            <label for="password">Nueva contraseña</label><br>

            <input
                type="password"
                id="password"
                name="password"
                minlength="8"
            >

            <br>

            <small>
                Déjala vacía si no deseas cambiar la contraseña.
            </small>
        </p>

        <button type="submit">
            Guardar cambios
        </button>

    </form>

    <p>
        <a href="usuarios.php">Cancelar</a>
    </p>

</div>

</body>
</html>

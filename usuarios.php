<?php

session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/config/database.php';

$sql = "SELECT id, nombre, email, fecha_registro
        FROM usuarios
        ORDER BY id DESC";

$stmt = $conexion->prepare($sql);
$stmt->execute();

$usuarios = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Usuarios - MiApp</title>

    <link rel="stylesheet" href="style.css">
</head>

<body>

<div class="contenedor">

    <h1>Usuarios registrados</h1>

    <p>
        Sesión iniciada como:
        <strong>
            <?= htmlspecialchars($_SESSION['usuario_nombre']) ?>
        </strong>
    </p>

    <p>
        <a href="crear.php">Registrar nuevo usuario</a>
        |
        <a href="dashboard.php">Panel principal</a>
        |
        <a href="logout.php">Cerrar sesión</a>
    </p>

    <?php if (count($usuarios) > 0): ?>

        <table border="1" cellpadding="8">

            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Correo</th>
                    <th>Fecha de registro</th>
                    <th>Acciones</th>
                </tr>
            </thead>

            <tbody>

                <?php foreach ($usuarios as $usuario): ?>

                    <tr>

                        <td>
                            <?= (int) $usuario['id'] ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($usuario['nombre']) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($usuario['email']) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($usuario['fecha_registro']) ?>
                        </td>

                        <td>
                            <a href="editar.php?id=<?= (int) $usuario['id'] ?>">
                                Editar
                            </a>

                            |

                            <a href="eliminar.php?id=<?= (int) $usuario['id'] ?>">
                                Eliminar
                            </a>
                        </td>

                    </tr>

                <?php endforeach; ?>

            </tbody>

        </table>

    <?php else: ?>

        <p>No existen usuarios registrados.</p>

    <?php endif; ?>

</div>

</body>
</html>

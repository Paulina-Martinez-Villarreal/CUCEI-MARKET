<?php
session_start(); // Iniciar sesión

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario_id'])) {
    header("Location: inicio-de-sesión.php"); // Redirige al login si no ha iniciado sesión
    exit();
}

// Verificar si el usuario tiene el rol de "admin"
if ($_SESSION['rol'] !== 'admin') {
    header("Location: index.html"); // Si no es admin, lo manda a la página principal
    exit();
}

// Conectar a la base de datos
$host = "rds-cm-1.ckdg6oyb0qwj.us-west-2.rds.amazonaws.com";
$db = "test";
$user = "admin";
$pass = "Lu7yFCNGaCCGJrZ";
$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Error en la conexión: " . $conn->connect_error);
}

// Obtener los usuarios pendientes de aprobación
$sql = "SELECT id, nombre_completo, correo, carrera, documento_verificacion FROM usuarios WHERE estado = 'pendiente'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación de Usuarios</title>
</head>
<body>
    <h1>Usuarios Pendientes de Verificación</h1>
    <a href="cerrar_sesion.php">Cerrar Sesión</a>
    
    <?php if ($result->num_rows > 0): ?>
        <table>
            <tr>
                <th>Nombre</th>
                <th>Correo</th>
                <th>Carrera</th>
                <th>Documento</th>
                <th>Acciones</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['nombre_completo']; ?></td>
                    <td><?php echo $row['correo']; ?></td>
                    <td><?php echo $row['carrera']; ?></td>
                    <td><a href="<?php echo $row['documento_verificacion']; ?>" target="_blank">Ver Documento</a></td>
                    <td>
                        <form action="aprobar_usuario.php" method="POST">
                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                            <button type="submit" name="accion" value="aprobar">Aprobar</button>
                            <button type="submit" name="accion" value="rechazar">Rechazar</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No hay usuarios pendientes de verificación.</p>
    <?php endif; ?>
</body>
</html>

<?php
$conn->close();
?>

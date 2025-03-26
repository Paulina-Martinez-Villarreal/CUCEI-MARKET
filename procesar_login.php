<?php
session_start(); // Iniciar sesión



require 'conexion_test.php';

$error = ""; // Variable para mensajes de error

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recibir los datos del formulario
    $correo = $_POST['correo'];
    $clave = $_POST['clave'];

    // Consultar el usuario en la base de datos
    $sql = "SELECT id, nombre_completo, clave, rol, estado FROM usuarios WHERE correo = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();

        // Verificar si la cuenta está aprobada
        if ($row['estado'] !== 'aprobado') {
            $error = "Tu cuenta aún no ha sido aprobada.";
        } else {
            // Verificar la contraseña con SHA-256
            if (password_verify($clave, $row['clave']) || hash('sha256', $clave) === strtolower($row['clave']))
{
                // Iniciar sesión y guardar datos del usuario
                $_SESSION['usuario_id'] = $row['id'];
                $_SESSION['nombre_completo'] = $row['nombre_completo'];
                $_SESSION['rol'] = $row['rol'];
				


// Debug para verificar que la sesión se guarda correctamente
var_dump($_SESSION);
exit();


                // Redirigir según el rol
                if ($row['rol'] === 'admin') {
                    header("Location: verificar_usuarios.php");
                } else {
                    header("Location: ProductosDisponibles.php");
                }
                exit();
            } else {
                $error = "Correo o clave incorrectos.";
            }
        }
    } else {
        $error = "Correo o clave incorrectos.";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
</head>
<body>
    <h2>Iniciar Sesión</h2>
    
    <?php if (!empty($error)) { ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php } ?>

    <form action="" method="POST">
        <label for="correo">Correo:</label>
        <input type="email" id="correo" name="correo" required>
        
        <label for="clave">Contraseña:</label>
        <input type="password" id="clave" name="clave" required>

        <button type="submit">Iniciar Sesión</button>
    </form>
</body>
</html>

<?php
session_start(); // Iniciar sesión

// Conexión a la base de datos
$host = "rds-cm-1.ckdg6oyb0qwj.us-west-2.rds.amazonaws.com";
$db = "test";
$user = "admin";
$pass = "Lu7yFCNGaCCGJrZ";
$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Error en la conexión: " . $conn->connect_error);
}

$error = ""; // Variable para almacenar mensajes de error

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recibir los datos del formulario
    $correo = $_POST['correo'];
    $clave = $_POST['clave'];

    // Consultar el usuario en la base de datos
    $sql = "SELECT id, nombre_completo, clave, rol FROM usuarios WHERE correo = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();

        // Verificar la clave (utilizando password_verify si usas hash)
        if (password_verify($clave, $row['clave'])) {
            // Iniciar sesión y guardar datos del usuario
            $_SESSION['usuario_id'] = $row['id'];
            $_SESSION['nombre_completo'] = $row['nombre_completo']; // Nombre del usuario
            $_SESSION['rol'] = $row['rol']; // Guardamos el rol del usuario

            // Redirigir según el rol
            if ($row['rol'] === 'admin') {
                header("Location: verificar_usuarios.php"); // Si es admin, va a la verificación de usuarios
            } else {
                header("Location: ProductosDisponibles.php"); // Si es usuario normal, va a los productos
            }
            exit();
        } else {
            $error = "Clave incorrecta.";
        }
    } else {
        $error = "Correo o clave incorrectos.";
    }

    $stmt->close();
}

$conn->close();
?>

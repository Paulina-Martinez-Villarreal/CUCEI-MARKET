<?php 
session_start(); // Iniciar sesión

// Verificar si el usuario es admin
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'admin') {
    header("Location: index.html");
    exit();
}


require 'conexion_test.php';

// Verificar si se ha enviado el ID del usuario
if (isset($_POST['id']) && isset($_POST['accion'])) {
    $id_usuario = $_POST['id'];
    $accion = $_POST['accion'];

    if ($accion === 'rechazar') {
        // Definir la consulta
        $sql = "UPDATE usuarios SET estado = 'rechazado' WHERE id = ?";
        
        // Preparar y ejecutar la consulta
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id_usuario);

        if ($stmt->execute()) {
            $_SESSION['mensaje'] = "Usuario rechazado correctamente.";
        } else {
            $_SESSION['mensaje'] = "Error al actualizar usuario: " . $conn->error; // ← Muestra el error de SQL si falla
        }
        
        $stmt->close(); // Cerrar la consulta
    } else {
        $_SESSION['mensaje'] = "Acción no válida.";
    }
}

// Redirigir de vuelta a la lista de usuarios
header("Location: verificar_usuarios.php");
exit();

$conn->close();
?>

<?php
session_start(); // Iniciar sesión

// Verificar si el usuario es admin
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'admin') {
    header("Location: index.html");
    exit();
}

// Conectar a la base de datos

require 'conexion_test.php';

// Verificar si se ha enviado el ID del usuario
if (isset($_POST['id']) && isset($_POST['accion'])) {
    $id_usuario = $_POST['id'];
    $accion = $_POST['accion'];

    // Si la acción es "aprobar", cambiar el estado a "aprobado"
    if ($accion === 'aprobar') {
        $sql = "UPDATE usuarios SET estado = 'aprobado' WHERE id = ?";
    } elseif ($accion === 'rechazar') {
        $sql = "UPDATE usuarios SET estado = 'rechazado' WHERE id = ?";
    }

    // Ejecutar la consulta
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_usuario);
    if ($stmt->execute()) {
        $_SESSION['mensaje'] = "Usuario actualizado correctamente.";
    } else {
        $_SESSION['mensaje'] = "Error al actualizar usuario.";
    }

    // Redirigir de vuelta a la lista de usuarios
    header("Location: verificar_usuarios.php");
    exit();
}

$conn->close();
?>

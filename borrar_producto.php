<?php
include 'conexion_test.php';
include 'Verificar_Estado_Usuario.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: inicio-de-sesion.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['producto_id'])) {
    $productoId = $_POST['producto_id'];
    $usuarioId = $_SESSION['usuario_id'];

    // Verificar si el producto pertenece al usuario
    $sql = "SELECT id FROM productos WHERE id = ? AND id_vendedor = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $productoId, $usuarioId);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        // Eliminar el producto
        $sqlEliminar = "DELETE FROM productos WHERE id = ?";
        $stmtEliminar = $conn->prepare($sqlEliminar);
        $stmtEliminar->bind_param("i", $productoId);
        $stmtEliminar->execute();

        header("Location: productos_vendedor.php?id=" . $usuarioId);
        exit();
    } else {
        echo "No tienes permiso para eliminar este producto.";
    }
} else {
    echo "Solicitud no válida.";
}
?>

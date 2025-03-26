<?php

include 'conexion_test.php'; // Conexión a la BD
include 'Verificar_Estado_Usuario.php';

$remitente_id = $_SESSION['usuario_id'];
$destinatario_id = $_POST['destinatario_id'];
$mensaje = $_POST['mensaje'];

// Validar si producto_id está presente y no está vacío
$producto_id = isset($_POST['producto_id']) && !empty($_POST['producto_id']) ? $_POST['producto_id'] : NULL;

// Preparar la consulta
if ($producto_id === NULL) {
    $sql = "INSERT INTO mensajes (remitente_id, destinatario_id, mensaje) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iis", $remitente_id, $destinatario_id, $mensaje);
} else {
    $sql = "INSERT INTO mensajes (remitente_id, destinatario_id, producto_id, mensaje) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiis", $remitente_id, $destinatario_id, $producto_id, $mensaje);
}

// Ejecutar la consulta
if ($stmt->execute()) {
    echo "Mensaje enviado con éxito.";
} else {
    echo "Error al enviar mensaje.";
}

$stmt->close();
$conn->close();
?>

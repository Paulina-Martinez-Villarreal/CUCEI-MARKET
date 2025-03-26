<?php
include 'Verificar_Estado_Usuario.php';
include 'conexion_test.php';
require 's3_config.php'; // Configuración de S3

use Aws\S3\S3Client;
use Aws\Exception\AwsException;

$id_usuario = $_SESSION['usuario_id'];

if (isset($_FILES["imagen"]) && $_FILES["imagen"]["error"] == 0) {
    $nombreArchivo = basename($_FILES["imagen"]["name"]);
    $extension = strtolower(pathinfo($nombreArchivo, PATHINFO_EXTENSION));
    $formatosPermitidos = ["jpg", "jpeg", "png", "gif"];

    if (!in_array($extension, $formatosPermitidos)) {
        die("Error: Solo se permiten archivos JPG, JPEG, PNG y GIF.");
    }

    $nuevoNombre = "perfil_" . $id_usuario . "_" . time() . "." . $extension;

    // Configurar el cliente S3
    $s3 = new S3Client([
        'version' => 'latest',
        'region'  => AWS_REGION,
        'credentials' => [
            'key'    => AWS_ACCESS_KEY_ID,
            'secret' => AWS_SECRET_ACCESS_KEY,
        ]
    ]);

    try {
        $result = $s3->putObject([
            'Bucket' => S3_BUCKET,
            'Key'    => $nuevoNombre,
            'SourceFile' => $_FILES["imagen"]["tmp_name"],
            'ACL'    => 'private', // Mantener privado
            'ContentType' => $_FILES["imagen"]["type"]
        ]);

        // Guardar en la base de datos solo el nombre del archivo (no la URL)
        $sql = "UPDATE usuarios SET imagen_perfil = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $nuevoNombre, $id_usuario);

        if ($stmt->execute()) {
            echo "Imagen actualizada correctamente.";
        } else {
            echo "Error al actualizar la base de datos.";
        }

        $stmt->close();
    } catch (AwsException $e) {
        die("Error al subir a S3: " . $e->getMessage());
    }
} else {
    die("No se ha seleccionado ningún archivo.");
}

$conn->close();
header("Location: perfil.php");
exit();
?>

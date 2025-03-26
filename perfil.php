<?php
include 'Verificar_Estado_Usuario.php';
include 'conexion_test.php'; // Conexión a la BD
require 's3_config.php'; // Archivo con credenciales y configuración de S3

use Aws\S3\S3Client;
use Aws\Exception\AwsException;

// Determinar qué perfil mostrar
$id_usuario = isset($_GET['id']) ? intval($_GET['id']) : $_SESSION['usuario_id'];

// Obtener datos del usuario a mostrar
$sql = "SELECT nombre_completo, correo, imagen_perfil FROM usuarios WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();
$stmt->close();
$conn->close();

if (!$usuario) {
    die("Error: Usuario no encontrado.");
}

// Configurar el cliente S3
$s3 = new S3Client([
    'version' => 'latest',
    'region'  => AWS_REGION,
    'credentials' => [
        'key'    => AWS_ACCESS_KEY_ID,
        'secret' => AWS_SECRET_ACCESS_KEY,
    ]
]);

// Generar URL firmada para la imagen de perfil (si existe)
$imagenPerfil = 'imagenes/default.png'; // Imagen predeterminada
if (!empty($usuario['imagen_perfil'])) {
    try {
        $cmd = $s3->getCommand('GetObject', [
            'Bucket' => S3_BUCKET,
            'Key'    => $usuario['imagen_perfil']
        ]);
        $request = $s3->createPresignedRequest($cmd, '+10 minutes');
        $imagenPerfil = (string) $request->getUri();
    } catch (AwsException $e) {
        error_log("Error generando URL firmada: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de <?php echo htmlspecialchars($usuario['nombre_completo']); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #273553;
            color: white;
            margin: 0;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
        }
        .profile-container {
            background-color: #fff;
            color: #000;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        .profile-image {
            border-radius: 50%;
            width: 150px;
            height: 150px;
            object-fit: cover;
            margin-top: 20px;
            border: 5px solid white;
        }
        button {
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            width: 100%;
        }
        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

    <div class="profile-container">
        <img src="<?php echo $imagenPerfil; ?>" alt="Foto de perfil" class="profile-image">
        <h1>Perfil de <?php echo htmlspecialchars($usuario['nombre_completo']); ?></h1>
        <p><strong>Nombre:</strong> <?php echo htmlspecialchars($usuario['nombre_completo']); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($usuario['correo']); ?></p>

        <?php if ($id_usuario == $_SESSION['usuario_id']): ?>
            <h2>Actualizar Imagen de Perfil</h2>
            <form action="subir_imagen.php" method="post" enctype="multipart/form-data">
                <input type="file" name="imagen" accept="image/*" required>
                <button type="submit">Subir Imagen</button>
            </form>
        <?php endif; ?>
    </div>

</body>
</html>

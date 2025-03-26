<?php
include 'Verificar_Estado_Usuario.php';
include 'conexion_test.php'; // Conexión a la BD

if (!isset($_SESSION['id'])) {
    die("Error: No has iniciado sesión.");
}

$id_usuario = $_SESSION['id']; // ID del usuario logueado

// Obtener la imagen de perfil del usuario
$sql = "SELECT imagen_perfil FROM usuarios WHERE id = $id_usuario";
$resultado = $conn->query($sql);
$fila = $resultado->fetch_assoc();

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ver Imagen</title>
</head>
<body>
    <h2>Imagen de Perfil</h2>
    <?php if (!empty($fila['imagen_perfil'])): ?>
        <img src="<?= $fila['imagen_perfil']; ?>" width="150" height="150">
    <?php else: ?>
        <p>No hay imagen disponible.</p>
    <?php endif; ?>
</body>
</html>
<?php $conn->close(); ?>

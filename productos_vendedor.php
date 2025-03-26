<?php
include 'Verificar_Estado_Usuario.php';
include 'conexion_test.php'; // Conexión a la BD

if (!isset($_GET['id'])) {
    die("Error: No se ha recibido el ID del vendedor.");
}

$id_vendedor = $_GET['id'];

// Consulta para obtener la información del vendedor
$sql_vendedor = "SELECT nombre_completo, correo, imagen_perfil, imagen_portada FROM usuarios WHERE id = ?";
$stmt_vendedor = $conn->prepare($sql_vendedor);
$stmt_vendedor->bind_param("i", $id_vendedor);
$stmt_vendedor->execute();
$result_vendedor = $stmt_vendedor->get_result();
$vendedor = $result_vendedor->fetch_assoc();

// Si el vendedor no existe, mostrar error
if (!$vendedor) {
    die("Error: El usuario no existe.");
}

// Consulta para obtener los productos del vendedor
$sql_productos = "SELECT id, titulo, precio, imagen FROM productos WHERE id_vendedor = ?";
$stmt_productos = $conn->prepare($sql_productos);
$stmt_productos->bind_param("i", $id_vendedor);
$stmt_productos->execute();
$result_productos = $stmt_productos->get_result();

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos de <?php echo htmlspecialchars($vendedor['nombre_completo']); ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 80%;
            max-width: 800px;
            margin: 30px auto;
            background: white;
            padding: 20px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            text-align: center;
        }
        .perfil-header {
            position: relative;
            width: 100%;
            height: 250px;
            background-color: #ddd;
        }
        .perfil-header img.portada {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 10px 10px 0 0;
        }
        .perfil-container {
            position: relative;
            text-align: center;
            margin-top: -60px;
        }
        .perfil-container img.perfil {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid white;
        }
        .perfil-container h2 {
            margin: 10px 0;
            color: #004080;
        }
        .perfil-container p {
            font-size: 16px;
            color: #333;
        }
        .productos-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
            margin-top: 20px;
        }
        .card {
            width: 18rem;
            background: white;
            border-radius: 8px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            padding: 15px;
            text-align: center;
        }
        .card img {
            width: 100%;
            height: 200px;
            object-fit: contain;
            border-radius: 5px;
        }
        .card h3 {
            margin: 10px 0;
            color: #004080;
        }
        .card p {
            font-size: 16px;
            font-weight: bold;
            color: #008000;
        }
        .btn {
            display: inline-block;
            padding: 8px 15px;
            margin-top: 10px;
            background: #004080;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }
        .btn:hover {
            background: #002f5c;
        }
		
		.cover-image {
    width: 100%; /* La imagen ocupará todo el ancho del contenedor */
    max-height: 250px; /* Altura máxima para evitar que se vuelva gigante */
    object-fit: cover; /* Ajusta la imagen sin distorsionarla */
    display: block; /* Evita que la imagen afecte otros elementos */
}

/* para la flechita */
		
    .back-arrow {
        position: absolute;
        top: 20px;
        left: 20px;
        color: #004080;
        font-size: 18px;
        text-decoration: none;
        display: flex;
        align-items: center;
    }

    .back-arrow i {
        margin-right: 8px;
    }

    .back-arrow:hover {
        color: #007bff;
    }




/* para boton de mensaje */

.btn-mensaje {
    display: inline-block;
    padding: 8px 15px;
    background: #007bff;
    color: white;
    text-decoration: none;
    border-radius: 5px;
    font-weight: bold;
    transition: background 0.3s ease-in-out;
    margin-top: 10px;
}

.btn-mensaje:hover {
    background: #0056b3;
}


    </style>
</head>
<body>

<!-- Flecha para regresar -->
<a href="ProductosDisponibles.php" class="back-arrow">
    <i class="fas fa-arrow-left"> Atrás</i>
</a>

<div class="container">
    <!-- Portada del vendedor -->
    <div class="perfil-header">
<img src="<?= htmlspecialchars($vendedor['imagen_portada'] ?? 'Laboratorios_de_cómputo_Alfa_y_Beta_(noche),_CUCEI.jpg', ENT_QUOTES, 'UTF-8'); ?>" 
     alt="Portada" class="cover-image">

		
    </div>
	
	<img src="<?= htmlspecialchars($portada ?? '', ENT_QUOTES, 'UTF-8'); ?>" alt="Portada">

    <!-- Perfil del vendedor -->
    <div class="perfil-container">
        <?php
// Imagen de perfil por defecto si el usuario no ha subido una
$imagenPerfil = !empty($vendedor['imagen_perfil']) ? $vendedor['imagen_perfil'] : 'imagenes/default-profile.png';
?>
<img class="perfil" src="<?php echo htmlspecialchars($imagenPerfil); ?>" alt="Foto de <?php echo htmlspecialchars($vendedor['nombre_completo']); ?>">

        <h2><?php echo htmlspecialchars($vendedor['nombre_completo']); ?></h2>
        <p><strong>Correo:</strong> <?php echo htmlspecialchars($vendedor['correo']); ?></p>
		
		<?php
// Obtener el ID del usuario logueado
$usuario_logueado_id = $_SESSION['usuario_id'] ?? null;

// Mostrar el botón solo si NO estás viendo tu propio perfil
if ($usuario_logueado_id !== $id_vendedor): ?>
    <br>
    <a href="chat.php?usuario_id=<?php echo $id_vendedor; ?>&mensaje=Hola, ¿Sigue disponible?" class="btn-mensaje">
        Mensaje
</a>
<?php endif; ?>

		
		
    </div>

    <!-- Productos en venta -->
    <h2>Productos en venta</h2>
    <div class="productos-container">
        <?php if ($result_productos->num_rows > 0): ?>
            <?php while ($producto = $result_productos->fetch_assoc()): ?>
                <div class="card">
                    <img src="<?php echo htmlspecialchars($producto['imagen']); ?>" alt="Producto">
                    <h3><?php echo htmlspecialchars($producto['titulo']); ?></h3>
                    <p>$<?php echo number_format($producto['precio'], 2); ?></p>
                    <a href="producto.php?id=<?php echo $producto['id']; ?>" class="btn">Ver producto</a>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="text-muted">Este vendedor no tiene productos en venta.</p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>

<?php
$stmt_vendedor->close();
$stmt_productos->close();
$conn->close();
?>
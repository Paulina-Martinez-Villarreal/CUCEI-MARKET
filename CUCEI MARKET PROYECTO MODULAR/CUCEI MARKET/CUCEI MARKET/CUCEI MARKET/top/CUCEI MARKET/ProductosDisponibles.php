<?php
// Conexión a la base de datos
$host = "rds-cm-1.ckdg6oyb0qwj.us-west-2.rds.amazonaws.com";
$db = "test";
$user = "admin";
$pass = "Lu7yFCNGaCCGJrZ";
$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Error en la conexión: " . $conn->connect_error);
}

// Obtener los productos desde la base de datos
$sql = "SELECT titulo, descripcion, precio, imagen FROM productos";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Marketplace UDG</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" 
    rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" 
    crossorigin="anonymous">

    <!-- Estilos personalizados -->
    <style>
        /* Paleta de colores UDG */
        :root {
            --azul-udg: #0B2C4D;
            --dorado-udg: #FFD700;
            --gris-claro: #f8f9fa;
        }

        body {
            background-color: var(--gris-claro);
            font-family: Arial, sans-serif;
        }

        .navbar {
            background-color: var(--azul-udg);
        }

        .navbar-brand, .nav-link {
            color: white !important;
            font-weight: bold;
        }

        .container {
            margin-top: 50px;
        }

        .titulo {
            text-align: center;
            color: var(--azul-udg);
            margin-bottom: 20px;
            font-size: 32px;
            font-weight: bold;
        }

        .productos-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
        }

        .producto-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            width: 300px;
            text-align: center;
        }

        .producto-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 8px;
        }

        .producto-card h2 {
            font-size: 20px;
            margin: 10px 0;
            color: var(--azul-udg);
        }

        .producto-card p {
            font-size: 16px;
            color: #555;
        }

        .precio {
            font-size: 18px;
            font-weight: bold;
            color: var(--dorado-udg);
        }

        .btn-publicar {
            background-color: var(--azul-udg);
            color: white;
            font-weight: bold;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            display: inline-block;
            margin-top: 20px;
        }

        .btn-publicar:hover {
            background-color: #0A1F38;
        }
    </style>
</head>

<a href="cerrar_sesion.php" style="padding: 10px; background: red; color: white; text-decoration: none; border-radius: 5px;">
    Cerrar Sesión
</a>

<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="#">CUCEI MARKET</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="publicar.php">Publicar Producto</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Contenido principal -->
    <div class="container">
        <h1 class="titulo">Productos Disponibles</h1>

        <div class="productos-container">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="producto-card">
                        <img src="<?php echo $row['imagen']; ?>" alt="Imagen del producto">
                        <h2><?php echo $row['titulo']; ?></h2>
                        <p><?php echo $row['descripcion']; ?></p>
                        <p class="precio">Precio: $<?php echo $row['precio']; ?></p>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No hay productos publicados.</p>
            <?php endif; ?>
        </div>

        <!-- Botón para publicar un producto -->
        <div class="text-center">
            <a href="publicar.php" class="btn-publicar">Publicar un Producto</a>
			<br><br>
        </div>
    </div>

</body>
</html>

<?php
$conn->close();
?>

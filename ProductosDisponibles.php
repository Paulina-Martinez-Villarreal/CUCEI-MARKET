<?php
include 'Verificar_Estado_Usuario.php';

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario_id'])) {
    header("Location: inicio-de-sesion.php");
    exit();
}

include 'conexion_test.php'; // Conexión a la BD

// Manejo de errores en la conexión
if ($conn->connect_error) {
    die("Error en la conexión: " . $conn->connect_error);
}

// Obtener datos del usuario logueado
$id_usuario = $_SESSION['usuario_id'];
$sqlUsuario = "SELECT nombre_completo, imagen_perfil FROM usuarios WHERE id = ?";
$stmt = $conn->prepare($sqlUsuario);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();
$stmt->close();

// Definir imagen de perfil o usar una por defecto
$imagenPerfil = !empty($usuario['imagen_perfil']) ? htmlspecialchars($usuario['imagen_perfil']) : 'imagenes/default.png';

// Definir todas las categorías
$categoriasDefinidas = [
    "Accesorios", "Anime", "Arte", "Belleza y Cuidado Personal", "Computación", 
    "Deportes", "Electrónica", "Herramientas", "Hogar", "Instrumentos Musicales", 
    "Juguetes", "Libros", "Mascotas", "Muebles", "Ropa", "Salud", "Software", 
    "Vehículos", "Videojuegos", "Otros"
];

// Obtener categorías y la cantidad de productos en cada una
$sqlCategorias = "SELECT categoria, COUNT(*) as cantidad FROM productos GROUP BY categoria";
$resultCategorias = $conn->query($sqlCategorias);
$categorias = [];

// Inicializar todas las categorías con cantidad 0
foreach ($categoriasDefinidas as $categoria) {
    $categorias[$categoria] = 0;
}

// Actualizar las categorías que tienen productos
while ($row = $resultCategorias->fetch_assoc()) {
    $categorias[$row['categoria']] = $row['cantidad'];
}

// Obtener la categoría seleccionada (si existe)
$categoriaSeleccionada = isset($_GET['categoria']) ? $_GET['categoria'] : null;

// Obtener el término de búsqueda (si existe)
$busqueda = isset($_GET['busqueda']) ? $_GET['busqueda'] : null;

// Consulta SQL para obtener productos
if ($categoriaSeleccionada) {
    // Si hay una categoría seleccionada, filtrar por esa categoría
    $sqlProductos = "SELECT id, titulo, descripcion, precio, imagen, categoria FROM productos WHERE categoria = ?";
    $stmt = $conn->prepare($sqlProductos);
    $stmt->bind_param("s", $categoriaSeleccionada);
    $stmt->execute();
    $resultProductos = $stmt->get_result();
} elseif ($busqueda) {
    // Si hay un término de búsqueda, filtrar por título
    $sqlProductos = "SELECT id, titulo, descripcion, precio, imagen, categoria FROM productos WHERE titulo LIKE ?";
    $stmt = $conn->prepare($sqlProductos);
    $paramBusqueda = "%$busqueda%";
    $stmt->bind_param("s", $paramBusqueda);
    $stmt->execute();
    $resultProductos = $stmt->get_result();
} else {
    // Si no hay categoría seleccionada ni búsqueda, mostrar todos los productos
    $sqlProductos = "SELECT id, titulo, descripcion, precio, imagen, categoria FROM productos ORDER BY RAND()";
    $resultProductos = $conn->query($sqlProductos);
}

// Obtener los productos más likeados
$sqlMasLikeados = "
    SELECT p.id, p.titulo, p.imagen, COUNT(l.id) AS total_likes 
    FROM productos p
    LEFT JOIN likes l ON p.id = l.producto_id
    GROUP BY p.id
    ORDER BY total_likes DESC
    LIMIT 5"; // Mostrará los 5 más likeados

$resultMasLikeados = $conn->query($sqlMasLikeados);

$productosMasLikeados = [];
while ($row = $resultMasLikeados->fetch_assoc()) {
    $productosMasLikeados[] = $row;
}

// Consulta para obtener productos recomendados basados en los 'likes' del usuario
$sqlRecomendados = "
    SELECT p.id, p.titulo, p.imagen, p.precio, p.categoria 
    FROM productos p
    WHERE p.categoria IN (
        SELECT DISTINCT pr.categoria
        FROM productos pr
        JOIN likes l ON pr.id = l.producto_id
        WHERE l.usuario_id = ?
    ) 
    AND p.id NOT IN (
        SELECT producto_id FROM likes WHERE usuario_id = ?
    )
    ORDER BY RAND()
    LIMIT 4"; // Limitar a 4 productos

$stmt = $conn->prepare($sqlRecomendados);
$stmt->bind_param("ii", $id_usuario, $id_usuario);
$stmt->execute();
$resultRecomendados = $stmt->get_result();
$productosRecomendados = [];

while ($row = $resultRecomendados->fetch_assoc()) {
    $productosRecomendados[] = $row;
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CUCEI MARKET</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --azul-udg: #0B2C4D;
            --dorado-udg: #FFD700;
            --gris-claro: #f8f9fa;
        }

        body {
            background-color: var(--gris-claro);
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .navbar {
            background-color: var(--azul-udg);
            position: relative;
            top: 0;
            z-index: 1000;
            height: 90px; /* Altura del header */
            overflow: visible;
        }

        .navbar-brand, .nav-link {
            color: white !important;
            font-weight: bold;
        }

        .navbar-nav img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }

        .navbar-logo img {
            height: 60px; /* Misma altura que el header */
            width: auto; /* Mantiene la proporción */
        }

        .main-container {
            display: flex;
            gap: 20px;
            margin-top: 20px;
            padding: 0 20px;
            align-items: flex-start;
            flex: 1;
        }

        .productos-container {
            flex: 1;
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .card {
            width: 18rem;
            border-radius: 8px; /* Bordes redondeados */
            overflow: hidden; /* Asegura que el borde no se salga del contenedor */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Sombra opcional para mejor apariencia */
            transition: transform 0.2s ease, box-shadow 0.2s ease; /* Efecto de hover */
        }

        .card:hover {
            transform: translateY(-5px); /* Efecto de elevación al pasar el mouse */
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15); /* Sombra más pronunciada al pasar el mouse */
        }

        .titulo-superior {
            text-align: center;
            padding: 20px;
            background-color: var(--azul-udg);
            color: white;
        }

        .titulo-superior .LogoCucei {
            width: 150px;
            height: auto;
        }

        .barra-busqueda {
            padding: 10px;
            background-color: var(--azul-udg);
            border-radius: 5px;
            display: flex;
            align-items: center;
            margin-left: 0;
        }

        .barra-busqueda input {
            width: 500px;
            padding: 10px;
            border-radius: 4px;
            border: 1px solid #ccc;
            background-color: white;
            color: #333;
        }

        .barra-busqueda input::placeholder {
            color: #999;
        }

        .barra-busqueda button {
            padding: 10px 20px;
            border-radius: 4px;
            border: none;
            background-color: #007bff;
            color: white;
            cursor: pointer;
        }

        .barra-busqueda button:hover {
            background-color: #0056b3;
        }

        @media (max-width: 768px) {
            .main-container {
                flex-direction: column;
            }

            .titulo-superior h1 {
                font-size: 28px;
            }

            .barra-busqueda input {
                width: 100%;
            }
        }

        .mas-likeados-container {
            width: 250px;
            background: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .mas-likeados-container h3 {
            font-size: 18px;
            margin-bottom: 10px;
        }

        .carousel {
            position: relative;
            width: 100%;
            height: 200px;
            overflow: hidden;
        }

        .carousel-item {
            position: absolute;
            width: 100%;
            transition: opacity 1s ease-in-out;
            opacity: 0;
        }

        .carousel-item img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 8px;
        }

        .carousel-item.active {
            opacity: 1;
        }

        .like-info {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 2px;
        }

        .like-icon-small {
            width: 18px !important;
            height: auto !important;
            max-width: 18px;
        }

        .product-image-container {
            width: 100%;
            height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 10px;
            background-color: #fff;
            border-radius: 5px;
        }

        .product-image {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .mas-likeados-container img {
            width: 100px;
            height: 100px;
            object-fit: cover;
        }

        footer {
            background-color: var(--azul-udg);
            padding: 1px 0;
            text-align: center;
            margin-top: auto;
        }

        .dropdown-toggle {
            background: none;
            border: none;
            padding: 0;
        }

        .dropdown-toggle img {
            width: 30px;
            height: 30px;
        }

        .dropdown-menu {
            max-width: 250px;
            white-space: nowrap;
            overflow-x: auto;
        }

        .dropdown-item {
            color: var(--azul-udg);
            font-weight: bold;
            padding: 8px 16px;
            white-space: nowrap;
        }

        .dropdown-item:hover {
            background-color: var(--gris-claro);
            color: var(--dorado-udg);
        }

        .dropdown-item.active {
            color: #FFA500;
        }
        .producto-recomendado {
            text-align: center;
            margin-bottom: 15px;
        }

         .producto-recomendado img {
            width: 100%;
            height: 150px;
            object-fit: contain;
            padding: 10px;
            border-radius: 8px;
            transition: none !important; /* evita efecto de zoom si se aplica por otros estilos */
        }
        .carousel-wrapper {
            position: relative;
            width: 100%;
        }

        .carousel-control {
            position: absolute;
            top: 25%; /* 35% queda más centrado verticalmente */
            transform: translateY(-50%);
            background: none;
            border: none;
            padding: 0; /* Quita el padding para que no se vea espacio alrededor */
            cursor: pointer;
            z-index: 10;
        }
        /*mueven las felchas hacia los bordes... se agrego px para que se movieran correctamente*/
        .carousel-control.left {
            left: -20px;
        }
        .carousel-control.right {
            right: -20px;
        }

        .flecha-icono {
            width: 8px;
            height: 8px;
            object-fit: contain;
            transition: transform 0.2s ease;
            display: block;
            margin: auto;
        }

        .carousel-control:hover .flecha-icono {
            transform: scale(1.1);
        }
        /*cambia el tamaño de los icons de las felchas*/
        .carousel-control img {
            width: 30%; /* Ajusta este valor */
            height: 30%; /* Ajusta este valor */
            object-fit: contain; /* Asegura que la imagen se ajuste al tamaño del botón */
        }
        .main-container {
            margin-bottom: 40px; /* o el valor que tú prefieras */
        }
        .card-title a {
            text-decoration: none;
        }
        .barra-busqueda {
            position: absolute;
            left: 50%;
            transform: translateX(-80%);
            z-index: 5;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg">
    <div class="container-fluid d-flex align-items-center justify-content-between gap-3">
        <!-- Contenedor para el logo y el ícono de categorías -->
        <div class="d-flex align-items-center gap-3">
            <!-- Ícono de categorías con dropdown -->
            <div class="dropdown">
                <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="imagenes/icon-categoria.png" alt="Categorías" style="width: 30px; height: 30px;">
                </button>
                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    <?php foreach ($categorias as $categoria => $cantidad): ?>
                        <li>
                            <a class="dropdown-item" href="?categoria=<?php echo urlencode($categoria); ?>">
                                <?php echo htmlspecialchars($categoria); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>

        <!-- Logo de CUCEI -->
        <a href="?" class="navbar-logo">
                <img src="imagenes/CuceiLogo.png" alt="CUCEI MARKET">
        </a>

        <!-- Barra de búsqueda -->
        <div class="barra-busqueda">
            <form method="GET" action="" class="d-flex">
                <input type="text" name="busqueda" placeholder="Buscar productos..." value="<?php echo htmlspecialchars($busqueda ?? ''); ?>">
                <button type="submit">Buscar</button>
            </form>
        </div>

        <!-- Menú de navegación (Publicar Producto, Recomendaciones, Perfil) -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse flex-grow-1" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item">
                    <a class="nav-link" href="publicar.php">Publicar Producto</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="recomendaciones.php">Recomendaciones</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="perfilDropdown" role="button" data-bs-toggle="dropdown">
                        <img src="<?php echo $imagenPerfil; ?>" alt="Perfil">
                        <?php echo htmlspecialchars($usuario['nombre_completo']); ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="perfil.php">Mi Perfil</a></li>
                        <li><a class="dropdown-item" href="ver_mensajes.php">Mis mensajes</a></li>
                        <li><a class="dropdown-item" href="productos_vendedor.php?id=<?php echo $id_usuario; ?>">Mis productos</a></li>
                        <li><a class="dropdown-item text-danger" href="cerrar_sesion.php">Cerrar Sesión</a></li>
                    </ul>

                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Mensaje y enlace para volver a ver todos los productos -->
<?php if ($categoriaSeleccionada): ?>
    <div style="text-align: center; margin-bottom: 20px;">
        <p>Mostrando productos de la categoría: <strong><?php echo htmlspecialchars($categoriaSeleccionada); ?></strong></p>
        <a href="?" class="btn btn-primary">Ver todos los productos</a>
    </div>
<?php endif; ?>

<?php if ($busqueda && $resultProductos->num_rows === 0): ?>
    <div style="text-align: center; margin-bottom: 20px;">
        <p>No se encontraron productos con el nombre: <strong><?php echo htmlspecialchars($busqueda); ?></strong></p>
        <a href="?" class="btn btn-primary">Ver todos los productos</a>
    </div>
<?php endif; ?>

<div class="main-container">
    <!-- Contenedor de productos -->
    <div class="productos-container">
        <?php if ($resultProductos->num_rows > 0): ?>
            <?php while ($row = $resultProductos->fetch_assoc()): ?>
                <div class="card">
                    <a href="producto.php?id=<?php echo htmlspecialchars($row['id']); ?>">
                        <div class="product-image-container">
                            <img src="<?php echo !empty($row['imagen']) ? htmlspecialchars($row['imagen']) : 'imagenes/default-product.png'; ?>" 
                                class="product-image" 
                                alt="Producto">
                        </div>
                    </a>
                    <div class="card-body">
                        <h5 class="card-title">
                            <a href="producto.php?id=<?php echo htmlspecialchars($row['id']); ?>">
                                <?php echo htmlspecialchars($row['titulo']); ?>
                            </a>
                        </h5>
                        <p class="card-text">
                            <?php 
                                $descripcionCorta = strlen($row['descripcion']) > 100 ? substr($row['descripcion'], 0, 100) . '...' : $row['descripcion'];
                                echo htmlspecialchars($descripcionCorta); 
                            ?>
                        </p>
                        <p class="text-warning fw-bold">$<?php echo number_format($row['precio'], 2); ?></p>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <?php if (!$categoriaSeleccionada && !$busqueda): ?>
                <p>No hay productos disponibles.</p>
            <?php else: ?>
                <p>No hay productos disponibles en esta categoría.</p>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <!-- Contenedor combinado para más gustados y recomendados -->
    <div style="width: 250px;">
        <?php if (!empty($productosMasLikeados)): ?>
            <div class="mas-likeados-container">
                <h3>Productos más gustados</h3>
                <div class="carousel-wrapper" style="position: relative;">
                <button class="carousel-control left" onclick="prevItem()">
                    <img src="imagenes/FlechaL.png" alt="←" class="flecha-icono">
                </button>
                    <div class="carousel">
                        <?php foreach ($productosMasLikeados as $producto): ?>
                            <div class="carousel-item">
                                <a href="producto.php?id=<?php echo $producto['id']; ?>">
                                    <img src="<?php echo htmlspecialchars($producto['imagen']); ?>" alt="Producto">
                                </a>
                                <p><?php echo htmlspecialchars($producto['titulo']); ?></p>
                                <div class="like-info">
                                    <img src="imagenes/LikeIcon.png" alt="Like" class="like-icon-small">
                                    <span><?php echo $producto['total_likes']; ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <button class="carousel-control right" onclick="nextItem()">
                        <img src="imagenes/FlechaR.png" alt="→" class="flecha-icono">
                    </button>
                </div>
            </div>
        <?php endif; ?>

        <?php if (!empty($productosRecomendados)): ?>
            <div class="mas-likeados-container" style="margin-top: 20px;">
                <h3>Quizás te puede gustar esto</h3>
                <?php foreach ($productosRecomendados as $producto): ?>
                    <div class="producto-recomendado">
                        <a href="producto.php?id=<?php echo $producto['id']; ?>">
                            <img src="<?php echo htmlspecialchars($producto['imagen']); ?>" alt="Producto">
                        </a>
                        <p><?php echo htmlspecialchars($producto['titulo']); ?></p>
                        <p class="text-warning fw-bold">$<?php echo number_format($producto['precio'], 2); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<footer>
    <img src="imagenes/CuceiLogo.png" alt="CUCEI Logo" style="width: 30px; height: auto;"> <!-- Reducido de 100px a 80px -->
</footer>

<?php $conn->close(); ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Script para manejo de likes -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const toggleCategorias = document.getElementById("toggle-categorias");
        const listaCategorias = document.getElementById("lista-categorias");

        if (toggleCategorias && listaCategorias) {
            toggleCategorias.addEventListener("click", function () {
                listaCategorias.classList.toggle("visible");
            });
        }
    });

    function toggleLike(productoId) {
        const usuarioId = <?php echo json_encode($_SESSION['usuario_id'] ?? null); ?>; // Usuario autenticado

        if (!usuarioId) {
            alert("Debes iniciar sesión para dar like.");
            return;
        }

        fetch("likes.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: usuario_id=${usuarioId}&producto_id=${productoId}
        })
        .then(response => response.json())
        .then(data => {
            console.log(data.message);
            actualizarLikes(productoId);
        })
        .catch(error => console.error("Error:", error));
    }

    function actualizarLikes(productoId) {
        fetch(likes.php?producto_id=${productoId})
            .then(response => response.json())
            .then(data => {
                document.getElementById(likes-${productoId}).innerText = data.total_likes;
            })
            .catch(error => console.error("Error obteniendo likes:", error));
    }
</script>

<!-- Script para el carrusel de productos más likeados -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        let items = document.querySelectorAll(".carousel-item");
        let index = 0;

        if (items.length > 0) {
            items[index].classList.add("active");

            window.nextItem = function() {
                items[index].classList.remove("active");
                index = (index + 1) % items.length;
                items[index].classList.add("active");
            }

            window.prevItem = function() {
                items[index].classList.remove("active");
                index = (index - 1 + items.length) % items.length;
                items[index].classList.add("active");
            }

            setInterval(() => nextItem(), 3000);
        }
    });
</script>

</body>
</html>
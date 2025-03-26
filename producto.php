<?php
include 'Verificar_Estado_Usuario.php';
include 'conexion_test.php'; // Asegura la conexión a la base de datos

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Consulta que une los datos del producto con la información del vendedor
    $sql = "SELECT p.titulo, p.descripcion, p.precio, p.imagen, 
               p.condicion,  -- Aquí agregamos la condición
               p.id_vendedor, 
               u.nombre_completo AS vendedor 
        FROM productos p
        LEFT JOIN usuarios u ON p.id_vendedor = u.id
        WHERE p.id = ?";


    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $producto = $resultado->fetch_assoc();
    } else {
        echo "<h2>Producto no encontrado.</h2>";
        exit();
    }
} else {
    echo "<h2>ID de producto no recibido.</h2>";
    exit();
}

// Verificar si el usuario logueado es el dueño del producto
$esPropietario = isset($_SESSION['usuario_id']) && $_SESSION['usuario_id'] == $producto['id_vendedor'];
?>



<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($producto['titulo']); ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .contenedor {
            max-width: 600px;
            margin: auto;
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1);
        }
        img {
            max-width: 100%;
            border-radius: 10px;
        }
        h1 {
            font-size: 24px;
            color: #004080;
        }
        p {
            font-size: 18px;
            color: #555;
        }
        /* Flecha para regresar */S
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

        /* Estilos para el input y el botón */
        .mensaje-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
            margin-top: 20px;
        }
        #mensaje {
            width: 80%;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1);
        }
        .btn-enviar {
            background-color: #004080;
            color: white;
            border: none;
            padding: 12px 20px;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        .btn-enviar:hover {
            background-color: #002f5c;
        }
        .like-container {
            display: flex;
            justify-content: center;  /* Centra en el contenedor */
            margin-top: 15px;  /* Espaciado con el contenido de arriba */
        }

        .like-content {
            display: flex;
            align-items: center;
            gap: 10px; /* Espacio entre el icono y el texto */
            padding: 10px 20px;
            border-radius: 8px;
            box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1);
            margin-left: 50px; /* Ajusta el valor para mover más a la derecha */
        }

        .btn-like {
            background: none;
            border: none;
            cursor: pointer;
            padding: 5px;
            display: flex;
            align-items: center;
            transition: transform 0.2s, box-shadow 0.2s; /* Efecto suave */
        }

        .btn-like:hover .like-icon {
            transform: scale(1.1);
            filter: drop-shadow(5px 5px 8px rgba(0, 0, 0, 0.50)); /* Sombra más visible al pasar el mouse */
        }

        .like-icon {
            width: 30px; /* Ajusta el tamaño según necesites */
            height: auto;
            filter: drop-shadow(3px 3px 5px rgba(0, 0, 0, 0.25)); /* Sombra alrededor de la forma */
            transition: transform 0.2s;
        }
        .imagen-detalle-container {
            width: 100%;
            max-width: 300px;
            height: 300px;
            margin: 0 auto 20px auto;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .imagen-detalle-container img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            display: block;
        }
    </style>
</head>
<body>
    <!-- boton atras -->
    <a href="ProductosDisponibles.php" class="back-arrow">
        <i class="fas fa-arrow-left"></i> Atrás
    </a>

<div class="contenedor">
    <h1><?php echo htmlspecialchars($producto['titulo']); ?></h1>
    <div class="imagen-detalle-container">
        <img src="<?php echo htmlspecialchars($producto['imagen']); ?>" alt="Imagen del producto">
    </div>

    <p><strong>Descripción: </strong><?php echo nl2br(htmlspecialchars($producto['descripcion'])); ?></p>
    <p><strong>Precio:</strong> $<?php echo number_format($producto['precio'], 2); ?></p>
	<p><strong>Condición:</strong> <?php echo htmlspecialchars(ucfirst($producto['condicion'])); ?></p>
     
    <!-- Contador de Likes y Botón -->
    <?php if (isset($_SESSION['usuario_id'])): ?>
        <div class="like-container">
            <button class="btn-like" onclick="toggleLike(<?php echo $id; ?>)">
                <img id="like-icon-<?php echo $id; ?>" 
                    src="imagenes/LikeIconBYA.png" 
                    alt="Like" 
                    class="like-icon"
                    data-liked="false">
            </button>
            <p>Likes: <span id="likes-<?php echo $id; ?>">0</span></p>
        </div>

        <script>
        document.addEventListener("DOMContentLoaded", function() {
        const productoId = <?php echo json_encode($id); ?>;
        const likeIcon = document.getElementById(`like-icon-${productoId}`);

        // Cargar el número de likes al abrir la página y verificar si el usuario ya dio like
        fetch(`likes.php?producto_id=${productoId}&usuario_id=<?php echo json_encode($_SESSION['usuario_id'] ?? null); ?>`)
            .then(response => response.json())
            .then(data => {
                document.getElementById(`likes-${productoId}`).innerText = data.total_likes;
                
                if (data.usuario_like) { // Si el usuario ya ha dado like, cambiar la imagen
                    likeIcon.src = "imagenes/LikeIcon.png";
                    likeIcon.setAttribute("data-liked", "true");
                }
            })
            .catch(error => console.error("Error obteniendo likes:", error));
    });

    function toggleLike(productoId) {
        const usuarioId = <?php echo json_encode($_SESSION['usuario_id'] ?? null); ?>;

        if (!usuarioId) {
            alert("Debes iniciar sesión para dar like.");
            return;
        }

        fetch("likes.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `usuario_id=${usuarioId}&producto_id=${productoId}`
        })
        .then(response => response.json())
        .then(data => {
            console.log(data.message);
            actualizarLikes(productoId);
            cambiarImagenLike(productoId, data.usuario_like);
        })
        .catch(error => console.error("Error:", error));
    }

    function cambiarImagenLike(productoId, usuarioLike) {
        const likeIcon = document.getElementById(`like-icon-${productoId}`);

        if (usuarioLike) {
            likeIcon.src = "imagenes/LikeIcon.png"; // Imagen a color
            likeIcon.setAttribute("data-liked", "true");
        } else {
            likeIcon.src = "imagenes/LikeIconBYA.png"; // Imagen gris
            likeIcon.setAttribute("data-liked", "false");
        }
    }

    function actualizarLikes(productoId) {
        fetch(`likes.php?producto_id=${productoId}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById(`likes-${productoId}`).innerText = data.total_likes;
            })
            .catch(error => console.error("Error obteniendo likes:", error));
    }
        </script>
    <?php else: ?>
        <p><strong><a href="inicio-de-sesion.php">Inicia sesión</a> para dar like.</strong></p>
    <?php endif; ?>


    <?php if ($esPropietario): ?>
        <div style="margin-top: 10px;">
            <form action="borrar_producto.php" method="POST" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este producto?');" style="display: inline;">
                <input type="hidden" name="producto_id" value="<?php echo $id; ?>">
                <button type="submit" class="btn btn-danger" style="font-size: 1.2rem;" title="Eliminar producto">
                    <i class="fas fa-trash"></i>
                </button>
            </form>
        </div>

        <p><strong><a class="link-vendedor" href="productos_vendedor.php?id=<?php echo $producto['id_vendedor']; ?>">Ver todos mis productos</a></strong></p>
    <?php else: ?>


        <p><strong>Vendido por:</strong> 
            <a class="link-vendedor" href="productos_vendedor.php?id=<?php echo $producto['id_vendedor']; ?>">
                <?php echo htmlspecialchars($producto['vendedor']); ?>
            </a>
        </p>

        <!-- Mostrar solo si el usuario NO es el dueño del producto -->
        <div class="mensaje-container">
            <input type="text" id="mensaje" value="Hola, ¿Sigue estando disponible?">
            <button class="btn-enviar" onclick="abrirChat(<?php echo htmlspecialchars($id); ?>, <?php echo htmlspecialchars($producto['id_vendedor']); ?>); enviarMensaje();">
                Enviar Mensaje
            </button>
        </div>
    <?php endif; ?>
</div>

<!-- Importar el archivo JavaScript -->
<script src="chat.js"></script>
</body>
</html>

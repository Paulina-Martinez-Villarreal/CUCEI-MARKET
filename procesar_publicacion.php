<?php
include 'Verificar_Estado_Usuario.php';

// Verificar que el usuario ha iniciado sesión
if (!isset($_SESSION['usuario_id'])) {
    die("Error: Usuario no autenticado.");
}



require 'conexion_test.php';


$mensaje = "";
$tipo_mensaje = "danger"; // Por defecto, el mensaje será de error.

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === 0) {
        // Recibir datos del formulario
        $titulo = $conn->real_escape_string($_POST['titulo']);
        $descripcion = $conn->real_escape_string($_POST['descripcion']);
        $precio = floatval($_POST['precio']);
        $categoria = $conn->real_escape_string($_POST['categoria']);
        $subcategoria = $conn->real_escape_string($_POST['subcategoria']);
        $condicion = $conn->real_escape_string($_POST['condicion']); 
        $id_vendedor = $_SESSION['usuario_id']; // Obtener ID del usuario autenticado

        // Procesar la imagen
        $nombre_imagen = basename($_FILES['imagen']['name']);
        $ruta_temporal = $_FILES['imagen']['tmp_name'];
        $directorio = __DIR__ . '/imagenes/';

        // Verificar si la carpeta de imágenes existe, si no, crearla
        if (!is_dir($directorio)) {
            mkdir($directorio, 0777, true);
        }

        $ruta_destino = $directorio . $nombre_imagen;
        $ruta_relativa_para_bd = 'imagenes/' . $nombre_imagen;


        // Validar tipo de archivo y tamaño
        $tipo_imagen = strtolower(pathinfo($ruta_destino, PATHINFO_EXTENSION));
        $tamaño_imagen = $_FILES['imagen']['size'];

        if (in_array($tipo_imagen, ["jpg", "jpeg", "png"]) && $tamaño_imagen <= 5000000) {
            if (move_uploaded_file($ruta_temporal, $ruta_destino)) {
                // Insertar en la base de datos
                $sql = "INSERT INTO productos (titulo, descripcion, precio, categoria, subcategoria, condicion, imagen, id_vendedor) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssdssssi", $titulo, $descripcion, $precio, $categoria, $subcategoria, $condicion, $ruta_relativa_para_bd, $id_vendedor);

                if ($stmt->execute()) {
                    $mensaje = "¡Producto publicado exitosamente!";
                    $tipo_mensaje = "success";
                } else {
                    $mensaje = "Error al guardar el producto: " . $stmt->error;
                }

                $stmt->close();
            } else {
                $mensaje = "Error al mover la imagen a la carpeta de destino.";
            }
        } else {
            $mensaje = "Formato de imagen no permitido o tamaño excede los 5MB.";
        }
    } else {
        $mensaje = "No se recibió ninguna imagen o hubo un error al subirla.";
    }
} else {
    $mensaje = "Método de envío no válido.";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Publicación</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Íconos de Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    
    <style>
        :root {
            --azul-udg: #0B2C4D;
            --dorado-udg: #FFD700;
            --gris-claro: #f8f9fa;
        }

        body {
            background-color: var(--gris-claro);
            font-family: Arial, sans-serif;
        }

        .container {
            margin-top: 100px;
            text-align: center;
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 500px;
        }

        .icono-exito {
            font-size: 60px;
            color: var(--dorado-udg);
        }

        .icono-error {
            font-size: 60px;
            color: red;
        }

        h1 {
            color: var(--azul-udg);
            font-weight: bold;
        }

        p {
            font-size: 18px;
            color: #333;
            margin-top: 10px;
        }

        .btn-udg {
            background-color: var(--azul-udg);
            color: white;
            font-weight: bold;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            display: inline-block;
            margin-top: 20px;
        }

        .btn-udg:hover {
            background-color: #09253e;
        }
    </style>

</head>

<body>
    <div class="container">
        <?php if ($tipo_mensaje === "success") : ?>
            <i class="bi bi-check-circle-fill icono-exito"></i>
            <h1>¡Éxito!</h1>
        <?php else : ?>
            <i class="bi bi-x-circle-fill icono-error"></i>
            <h1>¡Error!</h1>
        <?php endif; ?>

        <p><?php echo $mensaje; ?></p>

        <?php if ($tipo_mensaje === "success") : ?>
            <p>Serás redirigido en <strong id="contador">3</strong> segundos...</p>
        <?php endif; ?>

        <a href="ProductosDisponibles.php" class="btn-udg">Ir a Productos</a>
    </div>

    <?php if ($tipo_mensaje === "success") : ?>
    <script>
        let segundos = 3;
        const contador = document.getElementById("contador");
        const intervalo = setInterval(() => {
            segundos--;
            contador.textContent = segundos;
            if (segundos <= 0) {
                clearInterval(intervalo);
            }
        }, 1000);

        setTimeout(function () {
            window.location.href = 'ProductosDisponibles.php';
        }, 3000);
    </script>
    <?php endif; ?>

</body>
</html>

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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificar que se haya enviado un archivo de imagen
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === 0) {
        // Recibir los datos del formulario
        $titulo = $_POST['titulo'];
        $descripcion = $_POST['descripcion'];
        $precio = $_POST['precio'];
        $categoria = $_POST['categoria'];
        $condicion = $_POST['condicion']; // Nuevo campo

        // Procesar la imagen
        $nombre_imagen = $_FILES['imagen']['name'];
        $ruta_temporal = $_FILES['imagen']['tmp_name'];
        $directorio = 'imagenes/' . basename($nombre_imagen);

        // Verificar que el archivo sea una imagen y no exceda los 5MB
        $tipo_imagen = strtolower(pathinfo($directorio, PATHINFO_EXTENSION));
        $tamaño_imagen = $_FILES['imagen']['size'];

        if (($tipo_imagen == "jpg" || $tipo_imagen == "png" || $tipo_imagen == "jpeg") && $tamaño_imagen <= 5000000) {
            if (move_uploaded_file($ruta_temporal, $directorio)) {
                // Guardar los datos del producto en la base de datos
                $sql = "INSERT INTO productos (titulo, descripcion, precio, categoria, condicion, imagen) 
                        VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssdsss", $titulo, $descripcion, $precio, $categoria, $condicion, $directorio);

                if ($stmt->execute()) {
                    echo "Producto publicado exitosamente.";
                } else {
                    echo "Error al guardar el producto: " . $conn->error;
                }

                $stmt->close(); // Cerrar la consulta preparada
            } else {
                echo "Error al subir la imagen.";
            }
        } else {
            echo "Solo se permiten imágenes JPG, JPEG, PNG de hasta 5MB.";
        }
    } else {
        echo "No se recibió ninguna imagen o hubo un error al subirla.";
    }
} else {
    echo "Método de envío no válido.";
}

$conn->close();
?>

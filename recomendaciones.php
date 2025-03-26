<?php
include 'Verificar_Estado_Usuario.php';
include 'conexion_test.php'; // Conexión a la BD

if (!isset($_SESSION['usuario_id'])) {
    die("Error: No has iniciado sesión.");
}

$usuario_id = $_SESSION['usuario_id'];

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recomendaciones</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #273553;
            margin: 0;
            padding: 20px;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center; /* Esto causa el desplazamiento */
            align-items: center;
            min-height: 100vh; /* Asegura que el contenido no se empuje hacia arriba */
        }
        h1 {
            color: white;
            font-size: 36px;
            margin-bottom: 20px;
        }
        form {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            width: 100%;
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #333;
        }
        select, button {
            width: 100%;
            padding: 10px;
            border-radius: 4px;
            border: 1px solid #ccc;
            font-size: 16px;
        }
        button {
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        .resultado {
            margin-top: 20px;
            padding: 20px;
            background-color: #e9ecef;
            border-radius: 8px;
            color: #333;
            width: 100%;
            max-width: 1300px; /* puedes ajustar más si gustas */
            box-sizing: border-box;
        }

        .titulo {
            font-size: 24px;
            font-weight: 600;
            color: white;
            text-transform: uppercase;
            margin-bottom: 10px;
        }

        .subtitulo {
            font-size: 18px;
            font-weight: 400;
            color: white;
            margin-bottom: 20px;
        }
		
        /* para la flechita */
        .back-arrow {
            position: absolute;
            top: 20px;
            left: 20px;
            color: white;
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
        .categoria-subcategoria {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }

        .grupo-select {
            flex: 1;
            min-width: 220px;
        }

        .grupo-select label {
            display: block;
            font-weight: bold;
            margin-bottom: 6px;
            color: #333;
            font-size: 14px;
        }

        /* Estilo blanco moderno */
        select {
            width: 100%;
            padding: 10px 12px;
            border-radius: 8px;
            border: 1px solid #ccc;
            background-color: white;
            font-size: 15px;
            color: #333;
            box-shadow: inset 0 1px 3px rgba(0,0,0,0.06);
        }
        .globo-dialogo {
            position: absolute;
            top: -40px;
            left: 180%;  /* Centrado hacia la derecha */
            transform: translateX(-50%);
            background: #ffffff;
            color: #333;
            padding: 15px 20px;
            border-radius: 15px;
            max-width: 360px;  /* MÁS ANCHO */
            width: max-content;  /* Para que se adapte al contenido sin romper */
            font-size: 14px;
            font-weight: 500;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            text-align: left;
            line-height: 1.4;
            z-index: 10;
        }

        .globo-dialogo::after {
            content: "";
            position: absolute;
            top: 75px;
            left: 5px; /* ahora sí será visible */
            width: 0;
            height: 0;
            border-style: solid;
            border-width: 8px 8px 8px 0;
            border-color: transparent #ffffff transparent transparent;
            transform: rotate(40deg); /* Giro para que apunte en diagonal */
            border-width: 15px 15px 15px 0;
            display:none; /desactive el tringulo/
        }

        .cuceito-container {
            position: relative;
            display: inline-block;
            margin-bottom: 10px;
        }
        .tarjeta-producto {
            width: 200px;
            background: #fff;
            padding: 10px;
            border-radius: 8px;
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }

        .tarjeta-producto:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 20px rgba(0,0,0,0.2);
        }
        .card-producto {
            width: 180px;
            border: 2px solid #0066ff;
            border-radius: 10px;
            padding: 10px;
            background-color: white;
            color: #000;
            text-align: center;
            transition: transform 0.2s;
        }

        .card-producto:hover {
            transform: scale(1.03);
        }

        .card-producto img {
            width: 100%;
            height: auto;
            margin-bottom: 10px;
        }

        .card-producto strong {
            font-size: 16px;
            display: block;
            margin: 5px 0;
            color: #333;
        }

        .card-precio {
            color: orange;
            font-weight: bold;
            margin-top: 5px;
        }
        .card {
            width: 15rem;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            background-color: white;
        }
        .card-body {
            padding: 10px;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }

        .product-image-container {
            width: 100%;
            height: 150px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 8px;
            background-color: #fff;
            border-radius: 5px;
        }

        .product-image {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .card-title a {
            text-decoration: none;
            color: #007bff;
            font-size: 16px;
            font-weight: bold;
        }

        .card-title a:hover {
            color: #0056b3;
        }

        .card-text {
            font-size: 13px;
            color: #333;
            display: -webkit-box;
            -webkit-line-clamp: 2; /* máximo 2 líneas */
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .text-warning.fw-bold {
            color: #ffc107;
            font-weight: bold;
        }
        .text-warning {
            color: #FFA500;
        }
    </style>
</head>

<body>

    <!-- boton atras -->
<a href="ProductosDisponibles.php" class="back-arrow">
    <i class="fas fa-arrow-left"></i> Atrás
</a>

<!-- Resto del contenido... -->
<div class="titulo" style="text-align: center; margin-top: 80px;">
    Recomendaciones
</div>

<br><br>

<div class="cuceito-container">
    <div class="globo-dialogo" id="globoTexto">
        <span id="mensajeCuceito"></span>
    </div>
    <img src="<?php echo 'imagenes/Cuceito.png'; ?>" alt="Cuceito Logo" class="header-image" style="width: 200px;">
</div>

<form method="POST" action="">
    <!-- Pregunta 1: Categoría -->
    <div class="categoria-subcategoria">
        <div class="grupo-select">
            <label for="categoria">¿Qué categoría te interesa?</label>
            <select id="categoria" name="categoria" required onchange="actualizarSubcategorias()">
                <option value="">Seleccione una categoría</option>
                <?php
                $categorias = ["Accesorios", "Alimentos y Bebidas", "Anime", "Arte", "Belleza", "Computacion", "Deportes", "Electronica", "Herramientas", "Hogar", "Instrumentos", "Juguetes", "Libros", "Mascotas", "Muebles", "Ropa", "Salud", "Software", "Vehiculos", "Videojuegos", "otros"];
                foreach ($categorias as $cat) {
                    $selected = (isset($_POST['categoria']) && $_POST['categoria'] == $cat) ? 'selected' : '';
                    echo "<option value=\"$cat\" $selected>$cat</option>";
                }
                ?>
            </select>
        </div>

        <!-- Pregunta 2: Subcategoría -->
        <div class="grupo-select">
            <label for="categoria">¿Qué subcategoría te interesa?</label>
            <select id="subcategoria" name="subcategoria" required>
                <option value="">Seleccione una subcategoría</option>
                <?php
                if (isset($_POST['categoria']) && isset($_POST['subcategoria'])) {
                    $categoriaSel = $_POST['categoria'];
                    $subcategoriaSel = $_POST['subcategoria'];

                    echo "<script>document.addEventListener('DOMContentLoaded', function() {
                        const categoria = '$categoriaSel';
                        const subcategoria = '$subcategoriaSel';
                        document.getElementById('categoria').value = categoria;
                        actualizarSubcategorias();
                        setTimeout(() => {
                            document.getElementById('subcategoria').value = subcategoria;
                        }, 100); // Espera breve para que las subcategorías se carguen
                    });</script>";
                }
                ?>
            </select>
        </div>
    </div>

    <!-- Botón de enviar -->
    <button type="submit">Obtener Recomendación</button>
</form>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $categoria = $_POST["categoria"];
    $subcategoria = $_POST["subcategoria"];

    // Consulta SQL para obtener un producto aleatorio de la categoría y subcategoría seleccionadas
    $query = "SELECT id, titulo, descripcion, precio, imagen FROM productos WHERE categoria = ? AND subcategoria = ? ORDER BY RAND()";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $categoria, $subcategoria);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        echo "<div class='resultado'>";
        echo "<h2 style='text-align: center;'>Resultado</h2>";
        echo "<div style='display: flex; flex-wrap: wrap; gap: 30px; justify-content: center; margin-top: 30px;'>";

        while ($producto = $resultado->fetch_assoc()):
            ?>
                <div class="card">
                    <a href="producto.php?id=<?php echo htmlspecialchars($producto['id']); ?>">
                        <div class="product-image-container">
                            <img src="<?php echo !empty($producto['imagen']) ? htmlspecialchars($producto['imagen']) : 'imagenes/default-product.png'; ?>" 
                                class="product-image" 
                                alt="Producto">
                        </div>
                    </a>
                    <div class="card-body">
                        <h5 class="card-title">
                            <a href="producto.php?id=<?php echo htmlspecialchars($producto['id']); ?>">
                                <?php echo htmlspecialchars($producto['titulo']); ?>
                            </a>
                        </h5>
                        <p class="card-text">
                            <?php 
                                $descripcionCorta = strlen($producto['descripcion']) > 100 
                                    ? substr($producto['descripcion'], 0, 100) . '...' 
                                    : $producto['descripcion'];
                                echo htmlspecialchars($descripcionCorta); 
                            ?>
                        </p>
                        <p class="text-warning fw-bold">$<?php echo number_format($producto['precio'], 2); ?></p>
                    </div>
                </div>

            <?php
            endwhile;
            
            // Obtener productos aleatorios solo después de que el usuario haya enviado el formulario
            $sqlRecomendados = "
            SELECT id, titulo, imagen, precio, categoria 
            FROM productos 
            WHERE categoria = ? AND subcategoria != ? 
            ORDER BY RAND() 
            LIMIT 4";

            $stmtRecom = $conn->prepare($sqlRecomendados);
            $stmtRecom->bind_param("ss", $categoria, $subcategoria);
            $stmtRecom->execute();
            $resultRecomendados = $stmtRecom->get_result();

            $productosRecomendados = [];
            while ($row = $resultRecomendados->fetch_assoc()) {
                $productosRecomendados[] = $row;
            }

            if (!empty($productosRecomendados)) {
            echo '<div class="resultado" style="margin-top: 40px;">
                    <h2 style="text-align: center;">Quizás te puede gustar esto</h2>
                    <div style="display: flex; flex-wrap: wrap; gap: 30px; justify-content: center; margin-top: 30px;">';

            foreach ($productosRecomendados as $producto) {
                echo '<div class="card">
                        <a href="producto.php?id=' . $producto['id'] . '">
                            <div class="product-image-container">
                                <img src="' . htmlspecialchars($producto['imagen']) . '" alt="Producto" class="product-image">
                            </div>
                        </a>
                        <div class="card-body">
                            <h5 class="card-title">
                                <a href="producto.php?id=' . $producto['id'] . '">' . htmlspecialchars($producto['titulo']) . '</a>
                            </h5>
                            <p class="card-text">' . (strlen($producto['titulo']) > 100 ? substr($producto['titulo'], 0, 100) . '...' : $producto['titulo']) . '</p>
                            <p class="text-warning fw-bold">$' . number_format($producto['precio'], 2) . '</p>
                        </div>
                    </div>';
            }

            echo '</div></div>';
            }


        echo "</div></div>";
    } else {
        echo "<div class='resultado'>No se encontraron productos en esta categoría y subcategoría.</div>";
    }

    $stmt->close();
}
?>


<script>
const subcategorias = {
    "Accesorios": ["Mochilas y bolsos", "Organizadores", "Fundas", "Reloj", "Joyería", "Bisutería"],
    "Alimentos y Bebidas": ["Snacks", "Bebidas frías", "Bebidas calientes", "Dulces y Postres", "Comida saludable", "Frutas y verduras", "Panaderia y Reposteria", "Comida Vegetariana"],
    "Anime": ["Ropa", "Figuras", "Posters", "Accesorios", "Mangas"],
    "Arte": ["Libros de arte y diseño", "Decoración artística", "Pinturas", "Esculturas", "Fotografía", "Suministros para artistas", "Arte digital"],
    "Belleza": ["Productos para la piel", "Maquillaje", "Fragancias", "Productos para el cuidado del cabello", "Artículos de higiene personal"],
    "Computacion": ["Laptops", "Software", "Hardware", "Auriculares"],
    "Deportes": ["Ropa deportiva", "Calzado deportivo", "Equipamiento para gimnasio", "Bicicletas", "Termos"],
    "Electronica": ["Tabletas", "Auriculares inalámbricos", "Cargadores", "Altavoces", "Adaptadores", "Cables"],
    "Herramientas": ["Herramientas para reparaciones básicas", "Organizadores de herramientas", "Equipos de medición para proyectos", "Herramientas manuales", "Herramientas eléctricas"],
    "Hogar": ["Organizadores de escritorio", "Lámparas de estudio", "Almohadas y cojines", "Electrodomésticos", "Decoración"],
    "Instrumentos": ["Instrumentos de Cuerda", "Instrumentos de Viento", "Instrumentos de Percusión", "Instrumentos Electrónicos", "Auriculares para producción musical", "Micrófonos para grabación", "Accesorios para instrumentos"],
    "Juguetes": ["Juguetes antiestrés", "Juegos de mesa", "Figuras coleccionables", "Juguetes para actividades al aire libre"],
    "Libros": ["Libros de texto universitarios", "Novelas y literatura clásica", "Guías de estudio", "Libros de autoayuda", "Revistas académicas"],
    "Mascotas": ["Accesorios para mascotas", "Juguetes interactivos", "Productos de higiene para mascotas", "Camas portátiles", "Alimentos saludables para mascotas"],
    "Muebles": ["Muebles de sala", "Muebles de dormitorio", "Muebles de oficina", "Muebles de exterior", "Almacenamiento"],
    "Ropa": ["Ropa casual", "Ropa deportiva", "Ropa interior", "Ropa formal", "Trajes de baño", "Shorts", "Abrigos", "Sudaderas", "Pantalones", "Faldas", "Vestidos", "Pijamas"],
    "Salud": ["Suplementos vitamínicos", "Productos para el cuidado de la vista", "Artículos de primeros auxilios", "Productos para el manejo del estrés", "Equipos de fitness", "Productos naturales"],
    "Software": ["Aplicaciones", "Programas de edición de video y audio", "Software de programación", "Licencias"],
    "Vehiculos": ["Bicicletas", "Scooters eléctricos", "Accesorios", "Cascos y protecciones", "Repuestos y herramientas para mantenimiento"],
    "Videojuegos": ["Consolas portátiles", "Juegos físicos", "Accesorios para gaming", "Merchandising de videojuegos"],
    "otros": ["Productos personalizados", "Artículos de temporada", "Productos tendencia", "Regalos y souvenirs"]
};

function actualizarSubcategorias() {
    const categoriaSeleccionada = document.getElementById("categoria").value;
    const subcategoriaSelect = document.getElementById("subcategoria");
    subcategoriaSelect.innerHTML = "<option value=''>Seleccione una subcategoría</option>";
    
    if (categoriaSeleccionada && subcategorias[categoriaSeleccionada]) {
        subcategorias[categoriaSeleccionada].forEach(subcat => {
            let opcion = document.createElement("option");
            opcion.value = subcat;
            opcion.textContent = subcat;
            subcategoriaSelect.appendChild(opcion);
        });
    }
}

// Inicializar las subcategorías al cargar la página
document.getElementById("categoria").addEventListener("change", actualizarSubcategorias);
</script>

<script>
    const fuePost = <?php echo ($_SERVER["REQUEST_METHOD"] === "POST") ? 'true' : 'false'; ?>;
</script>

<script>
const mensaje = "¡Bienvenido! Soy Cuceito y estoy aquí para ayudarte a encontrar lo que buscas. ¡Espero que disfrutes tu experiencia y encuentres justo lo que necesitas!";
const contenedor = document.getElementById("mensajeCuceito");
let i = 0;

function escribirTexto() {
    if (i < mensaje.length) {
        contenedor.innerHTML += mensaje.charAt(i);
        i++;
        setTimeout(escribirTexto, 40);
    }
}

window.addEventListener("DOMContentLoaded", function () {
    if (!fuePost) { // Solo se escribe si NO fue por POST
        contenedor.innerHTML = "";
        escribirTexto();
    } else {
        contenedor.innerHTML = mensaje; // Si fue post, se muestra sin animación
    }
});
</script>

</body>
</html>
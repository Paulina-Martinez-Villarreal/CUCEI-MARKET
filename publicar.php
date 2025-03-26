<?php

include 'Verificar_Estado_Usuario.php';

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Publicar Producto</title>

    <!-- Font Awesome para el icono de la flecha -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        /* Estilos generales */
        body {
            font-family: Arial, sans-serif;
            background-color: #273553;
            margin: 0;
            padding: 20px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            box-sizing: border-box;
            position: relative;
        }

        /* Flecha de regreso con texto "Atrás" */
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
            margin-right: 8px; /* Espacio entre el icono y el texto */
        }

        .back-arrow:hover {
            color: #007bff;
        }

        /* Estilo para el título */
        h2 {
            text-align: center;
            color: #fff;
            margin-bottom: 20px;
            font-size: 28px;
        }

        /* Contenedor del formulario */
        .form-container {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 800px;
        }

        /* Estilos para los campos del formulario */
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            color: #555;
            font-weight: bold;
            font-size: 16px;
        }

        input[type="text"],
        input[type="number"],
        input[type="file"],
        select,
        textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
            box-sizing: border-box;
        }

        textarea {
            resize: vertical;
        }

        /* Estilo para el botón */
        input[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
        }

        /* Estilo para los campos requeridos */
        input:required {
            border-color: #007bff;
        }

        input:required:focus {
            outline: none;
            border-color: #0056b3;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }

        /* Ajustes para pantallas pequeñas */
        @media (max-width: 768px) {
            .form-container {
                padding: 20px;
                max-width: 90%;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

            h2 {
                font-size: 24px;
            }

            label,
            input[type="text"],
            input[type="number"],
            input[type="file"],
            select,
            textarea {
                font-size: 14px;
            }
        }
    </style>
</head>

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
</script>

<script>
    let contador = 3; // Inicia en 3 segundos
    const contadorElemento = document.getElementById("contador"); // Obtiene el elemento del contador

    function actualizarContador() {
        if (contador > 0) {
            contadorElemento.innerText = contador; // Actualiza el número en pantalla
            contador--; // Disminuye el contador
            setTimeout(actualizarContador, 1000); // Llama a la función cada segundo
        } else {
            window.location.href = "ProductosDisponibles.php"; // Redirige cuando llega a 0
        }
    }

    setTimeout(actualizarContador, 1000); // Inicia el contador después de 1 segundo
</script>


<body>

    <!-- Ícono de flecha para volver con texto "Atrás" -->
    <a href="ProductosDisponibles.php" class="back-arrow">
        <i class="fas fa-arrow-left"></i> Atrás
    </a>

    <!-- Título fuera del formulario -->
    <h2>Publicar Producto</h2>

    <!-- Contenedor del formulario -->
    <div class="form-container">
        <form action="procesar_publicacion.php" method="POST" enctype="multipart/form-data">
            <!-- Sección de campos en dos columnas -->
            <div class="form-grid">
                <div>
                    <label for="titulo">Título:</label>
                    <input type="text" id="titulo" name="titulo" required>
                </div>
                <div>
                    <label for="precio">Precio:</label>
                    <input type="number" id="precio" name="precio" required>
                </div>
                <div>
                    <label for="categoria">Categoría:</label>
                    <select id="categoria" name="categoria" required onchange="actualizarSubcategorias()">
                        <option value="">Seleccione una categoría</option>
                        <option value="Accesorios">Accesorios</option>
                        <option value="Alimentos y Bebidas">Alimentos y Bebidas</option>
                        <option value="Anime">Anime</option>
                        <option value="Arte">Arte</option>
                        <option value="Belleza">Belleza y Cuidado Personal</option>
                        <option value="Computacion">Computación</option>
                        <option value="Deportes">Deportes</option>
                        <option value="Electronica">Electrónica</option>
                        <option value="Herramientas">Herramientas</option>
                        <option value="Hogar">Hogar</option>
                        <option value="Instrumentos">Instrumentos Musicales</option>
                        <option value="Juguetes">Juguetes</option>
                        <option value="Libros">Libros</option>
                        <option value="Mascotas">Mascotas</option>
                        <option value="Muebles">Muebles</option>
                        <option value="Ropa">Ropa</option>
                        <option value="Salud">Salud</option>
                        <option value="Software">Software</option>
                        <option value="Vehiculos">Vehículos</option>
                        <option value="Videojuegos">Videojuegos</option>
                        <option value="otros">Otros</option>
                    </select>
                </div>

                <div>
                    <label for="subcategoria">Subcategoría:</label>
                    <select id="subcategoria" name="subcategoria" required>
                        <option value="">Seleccione una subcategoría</option>
                    </select>
                </div>

                <div>
                    <label for="condicion">Condición:</label>
                    <select id="condicion" name="condicion" required>
                        <option value="nuevo">Nuevo</option>
                        <option value="usado">Usado</option>
                        <option value="reacondicionado">Reacondicionado</option>
                        <option value="para_repuestos">Para Repuestos</option>
                    </select>
                </div>
            </div>

            <!-- Descripción del producto -->
            <label for="descripcion">Descripción:</label>
            <textarea id="descripcion" name="descripcion" required></textarea>

            <!-- Imagen del producto -->
            <label for="imagen">Imagen del producto:</label>
            <input type="file" id="imagen" name="imagen" accept="image/*" required>

            <!-- Botón de enviar -->
            <input type="submit" value="Publicar Producto">
        </form>
    </div>

</body>
</html>

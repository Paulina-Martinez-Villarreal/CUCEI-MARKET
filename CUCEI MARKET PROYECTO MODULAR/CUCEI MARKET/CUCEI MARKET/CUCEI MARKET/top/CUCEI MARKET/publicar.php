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
<body>

    <!-- Ícono de flecha para volver con texto "Atrás" -->
    <a href="index.html" class="back-arrow">
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
                    <select id="categoria" name="categoria" required>
					<option value="comida">Comida</option>
                        <option value="electronica">Electrónica</option>
                        <option value="ropa">Ropa</option>
                        <option value="hogar">Hogar</option>
                        <option value="vehiculos">Vehículos</option>
                        <option value="deportes">Deportes</option>
                        <option value="juguetes">Juguetes</option>
                        <option value="herramientas">Herramientas</option>
                        <option value="libros">Libros</option>
                        <option value="computacion">Computación</option>
                        <option value="videojuegos">Videojuegos</option>
                        <option value="muebles">Muebles</option>
                        <option value="instrumentos">Instrumentos Musicales</option>
                        <option value="belleza">Belleza y Cuidado Personal</option>
                        <option value="otros">Otros</option>
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

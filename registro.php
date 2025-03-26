<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario</title>

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
        h1 {
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
        input[type="email"],
        input[type="password"],
        input[type="file"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
            box-sizing: border-box;
        }

        input[type="file"] {
            padding: 5px;
        }

        /* Estilo para el botón */
        button {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        /* Estilo para el mensaje de requerido */
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

            h1 {
                font-size: 24px;
            }

            label,
            input[type="text"],
            input[type="email"],
            input[type="password"],
            input[type="file"] {
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
    <h1>Crear una Cuenta</h1>

    <!-- Contenedor del formulario -->
    <div class="form-container">
        <form action="procesar_registro.php" method="POST" enctype="multipart/form-data">
            <!-- Sección de campos en dos columnas -->
            <div class="form-grid">
                <div>
                    <label for="nombre_completo">Nombre Completo:</label>
                    <input type="text" id="nombre_completo" name="nombre_completo" required>
                </div>
                <div>
                    <label for="correo">Correo Electrónico:</label>
                    <input type="email" id="correo" name="correo" required>
                </div>
                <div>
                    <label for="codigo">Código de Estudiante:</label>
                    <input type="text" id="codigo" name="codigo" required>
                </div>
                <div>
                    <label for="carrera">Carrera:</label>
                    <input type="text" id="carrera" name="carrera" required>
                </div>
                <div>
                    <label for="ciclo_ingreso">Ciclo de Ingreso:</label>
                    <input type="text" id="ciclo_ingreso" name="ciclo_ingreso" required>
                </div>
                <div>
                    <label for="ciclo_actual">Ciclo Actual:</label>
                    <input type="text" id="ciclo_actual" name="ciclo_actual" required>
                </div>
            </div>

            <!-- Campos que no están en dos columnas -->
            <label for="clave">Contraseña:</label>
            <input type="password" id="clave" name="clave" required>

            <label for="documento">Documento de Verificación (PDF):</label>
            <input type="file" id="documento" name="documento" accept=".pdf" required>

            <button type="submit">Registrarme</button>
        </form>
    </div>

</body>
</html>

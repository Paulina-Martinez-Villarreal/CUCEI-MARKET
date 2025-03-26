<?php
// Conexión a la base de datos
require 'conexion_test.php';

$mensaje = ""; // Variable para mostrar el mensaje

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recibir los datos del formulario
    $nombre_completo = $_POST['nombre_completo'];
    $correo = $_POST['correo'];
    $codigo = $_POST['codigo'];
    $carrera = $_POST['carrera'];
    $clave = password_hash($_POST['clave'], PASSWORD_BCRYPT);
    $ciclo_ingreso = $_POST['ciclo_ingreso'];
    $ciclo_actual = $_POST['ciclo_actual'];

    // Procesar el documento PDF
    if (isset($_FILES['documento']) && $_FILES['documento']['error'] == 0) {
        $nombre_documento = basename($_FILES['documento']['name']); 
        $ruta_temporal = $_FILES['documento']['tmp_name'];
        $directorio = "/var/www/html/private_uploads/" . $nombre_documento; // Ruta en el servidor

        // Verificar que el archivo sea un PDF y no exceda 5MB
        $tipo_documento = strtolower(pathinfo($directorio, PATHINFO_EXTENSION));
        $tamaño_documento = $_FILES['documento']['size'];

        if ($tipo_documento == "pdf" && $tamaño_documento <= 5000000) {
            if (move_uploaded_file($ruta_temporal, $directorio)) {
                // Guardamos en la base de datos solo la ruta relativa
                $ruta_bd = "private_uploads/" . $nombre_documento;

                // Verificar si el correo ya existe en la base de datos
                $sql_check = "SELECT * FROM usuarios WHERE correo = ?";
                $stmt_check = $conn->prepare($sql_check);
                $stmt_check->bind_param("s", $correo);
                $stmt_check->execute();
                $result = $stmt_check->get_result();

                if ($result->num_rows > 0) {
                    $mensaje = "El correo ya está registrado. Por favor, utiliza otro.";
                } else {
                    // Si no existe, procede a insertar el registro
                    $sql = "INSERT INTO usuarios (nombre_completo, correo, codigo, carrera, clave, ciclo_ingreso, ciclo_actual, documento_verificacion, estado) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pendiente')";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("ssssssss", $nombre_completo, $correo, $codigo, $carrera, $clave, $ciclo_ingreso, $ciclo_actual, $ruta_bd);

                    if ($stmt->execute()) {
                        $mensaje = "Registro exitoso. Tu cuenta será verificada.";
                        echo "<script>
                                setTimeout(function(){
                                    window.location.href = 'index.html';
                                }, 5000);
                              </script>";
                    } else {
                        $mensaje = "Error al guardar el registro: " . $conn->error;
                    }
                }
                $stmt_check->close(); // Cerrar la consulta de verificación
            } else {
                $mensaje = "Error al subir el documento.";
            }
        } else {
            $mensaje = "Solo se permiten archivos PDF de hasta 5MB.";
        }
    } else {
        $mensaje = "No se recibió ningún documento o hubo un error al subirlo.";
    }
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario</title>
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" 
          rel="stylesheet" 
          integrity="sha384-QWTKzYjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" 
          crossorigin="anonymous">
    <style>
        body {
            background-color: #0B2C4D;
            color: white;
            text-align: center;
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            flex-direction: column;
        }
        .container {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            color: black;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
            max-width: 500px;
        }
        .btn-redirect {
            margin-top: 15px;
            background-color: #FFD700;
            color: black;
            font-weight: bold;
            padding: 10px 15px;
            border-radius: 5px;
            text-decoration: none;
        }
        .btn-redirect:hover {
            background-color: #FFC107;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Registro de Usuario</h2>
        <p><?php echo $mensaje; ?></p>
        <p>Serás redirigido a la página principal en 5 segundos...</p>
        <a href="index.html" class="btn-redirect">Ir a la Página Principal</a>
    </div>
</body>
</html>

<?php
session_start(); // Iniciar sesión

// Conexión a la base de datos
$host = "rds-cm-1.ckdg6oyb0qwj.us-west-2.rds.amazonaws.com";
$db = "test";
$user = "admin";
$pass = "Lu7yFCNGaCCGJrZ";
$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Error en la conexión: " . $conn->connect_error);
}

$error = ""; // Variable para almacenar mensajes de error

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recibir los datos del formulario
    $correo = $_POST['correo'];
    $clave = $_POST['clave'];

    // Verificar si el correo existe en la base de datos
    $sql = "SELECT * FROM usuarios WHERE correo = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows == 1) {
        // Usuario encontrado
        $usuario = $resultado->fetch_assoc();

        // Verificar la contraseña hasheada
        if (password_verify($clave, $usuario['clave'])) {
            // Contraseña correcta, iniciar sesión
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['nombre_completo'] = $usuario['nombre_completo']; // Para mostrar el nombre si es necesario

            // Redirigir al usuario al dashboard o página principal
            header("Location: ProductosDisponibles.php");
            exit();
        } else {
            // Contraseña incorrecta
            $error = "Correo o clave incorrectos.";
        }
    } else {
        // Correo no encontrado
        $error = "Correo o clave incorrectos.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    
    <!-- Font Awesome para los iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: #273553;
            position: relative;
        }

        .back-arrow {
            position: absolute;
            top: 20px;
            left: 20px;
            color: white;
            font-size: 24px;
            text-decoration: none;
        }

        .back-arrow:hover {
            color: #007bff;
        }

        .logo-udg {
            width: 150px;
            margin-bottom: 20px;
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

        .login-container {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 400px;
            box-sizing: border-box;
        }

        .form-group {
            margin-bottom: 15px;
            text-align: left;
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
            box-sizing: border-box;
        }

        .btn-login {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            border: none;
            border-radius: 4px;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
        }

        .btn-login:hover {
            background-color: #0056b3;
        }

        .error-message {
            color: red;
            margin-bottom: 15px;
        }

        .register-link {
            margin-top: 15px;
            color: #555;
        }

        .register-link a {
            color: #007bff;
            text-decoration: none;
        }

        .register-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <!-- Ícono de flecha para volver -->
    <a href="index.html" class="back-arrow">
        <i class="fas fa-arrow-left"></i> <!-- Ícono de flecha izquierda de Font Awesome -->
    </a>

    <!-- Logo de la UDG -->
    <img src="Información_files/logo.png" alt="Logo UDG" class="logo-udg">

    <!-- Título y subtítulo -->
    <div class="titulo">CUCEI MARKET</div>
    <div class="subtitulo">¡Ingrese a su cuenta!</div>

    <!-- Contenedor del formulario -->
    <div class="login-container">
        <?php if (!empty($error)) { ?>
            <p class="error-message"><?php echo $error; ?></p>
        <?php } ?>

        <form action="" method="POST">
            <div class="form-group">
                <input type="email" id="correo" name="correo" required placeholder="Correo">
            </div>

            <div class="form-group">
                <input type="password" id="clave" name="clave" required placeholder="Contraseña">
            </div>

            <button type="submit" class="btn-login">Iniciar Sesión</button>
        </form>

        <p class="register-link">¿No tienes una cuenta? <a href="registro.php">Regístrate aquí</a>.</p>
    </div>
</body>
</html>

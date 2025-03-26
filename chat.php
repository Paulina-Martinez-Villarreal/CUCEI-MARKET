<?php

include 'Verificar_Estado_Usuario.php';
include 'conexion_test.php'; // Conexión a la BD

// Verifica si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    die("Error: No has iniciado sesión.");
}

$usuario_id = $_SESSION['usuario_id'];

// Obtener el ID del usuario con quien se está chateando
if (!isset($_GET['usuario_id'])) {
    die("Error: No se ha seleccionado un usuario.");
}

$chat_usuario_id = $_GET['usuario_id'];

// Consultar datos del usuario con quien se está chateando
$sql_usuario = "SELECT nombre_completo, imagen_perfil FROM usuarios WHERE id = ?";
$stmt_usuario = $conn->prepare($sql_usuario);
$stmt_usuario->bind_param("i", $chat_usuario_id);
$stmt_usuario->execute();
$result_usuario = $stmt_usuario->get_result();
$usuario = $result_usuario->fetch_assoc();

if (!$usuario) {
    die("Error: Usuario no encontrado.");
}

// Obtener mensajes del chat
$sql_mensajes = "SELECT * FROM mensajes 
                 WHERE (remitente_id = ? AND destinatario_id = ?) 
                 OR (remitente_id = ? AND destinatario_id = ?)
                 ORDER BY fecha_envio ASC";
$stmt_mensajes = $conn->prepare($sql_mensajes);
$stmt_mensajes->bind_param("iiii", $usuario_id, $chat_usuario_id, $chat_usuario_id, $usuario_id);
$stmt_mensajes->execute();
$result_mensajes = $stmt_mensajes->get_result();
?>

<script>
function enviarMensaje() {
    var mensaje = document.getElementById("mensaje").value;
    var destinatario_id = document.getElementById("destinatario_id").value;

    if (mensaje.trim() === "") {
        alert("No puedes enviar un mensaje vacío.");
        return;
    }

    var formData = new FormData();
    formData.append("mensaje", mensaje);
    formData.append("destinatario_id", destinatario_id);

    fetch("enviar_mensaje.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        console.log(data);
        if (data.includes("éxito")) {
            var chatMessages = document.querySelector(".chat-messages");
            var newMessage = document.createElement("div");
            newMessage.classList.add("mensaje", "remitente");
            newMessage.textContent = mensaje;
            chatMessages.appendChild(newMessage);
            document.getElementById("mensaje").value = ""; // Limpiar input
            chatMessages.scrollTop = chatMessages.scrollHeight; // Hacer scroll hacia abajo
        } else {
            alert("Hubo un error al enviar el mensaje.");
        }
    })
    .catch(error => console.error("Error:", error));
}
</script>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat con <?php echo htmlspecialchars($usuario['nombre_completo']); ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }
        .chat-container {
            width: 90%;
            max-width: 600px;
            margin: 20px auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .perfil-container {
            text-align: center;
            margin-bottom: 20px;
        }
        .perfil-container img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #004080;
        }
        .perfil-container h2 {
            margin: 10px 0;
            color: #004080;
            cursor: pointer;
            text-decoration: underline;
        }
        .perfil-container h2:hover {
            color: #002f5c;
        }
        .chat-messages {
            background: #e9e9e9;
            padding: 10px;
            border-radius: 5px;
            max-height: 300px;
            overflow-y: auto;
            margin-bottom: 10px;
        }
        .mensaje {
            padding: 8px;
            margin: 5px 0;
            border-radius: 5px;
        }
        .mensaje.remitente {
            background: #004080;
            color: white;
            text-align: right;
        }
        .mensaje.destinatario {
            background: #ccc;
            text-align: left;
        }
        .input-container {
            display: flex;
            gap: 5px;
        }
        .input-container input {
            flex: 1;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .input-container button {
            background: #004080;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
        }
        .input-container button:hover {
            background: #002f5c;
        }
		
		/* para la flechita */
		
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


    </style>
</head>
<body
<!-- Flecha para regresar -->
<a href="ver_mensajes.php" class="back-arrow">
    <i class="fas fa-arrow-left">Atrás</i> 
</a>


<div class="chat-container">
    <!-- Datos del usuario -->
    <div class="perfil-container">
        <img src="<?php echo ($usuario['imagen_perfil']); ?>" alt="Foto de <?php echo ($usuario['nombre_completo']); ?>">
        <h2 onclick="window.location.href='perfil.php?id=<?php echo htmlspecialchars($chat_usuario_id); ?>'">
            <?php echo htmlspecialchars($usuario['nombre_completo']); ?>
        </h2>
    </div>

    <!-- Mensajes del chat -->
    <div class="chat-messages">
        <?php while ($mensaje = $result_mensajes->fetch_assoc()): ?>
            <div class="mensaje <?php echo ($mensaje['remitente_id'] == $usuario_id) ? 'remitente' : 'destinatario'; ?>">
                <?php echo htmlspecialchars($mensaje['mensaje']); ?>
            </div>
        <?php endwhile; ?>
    </div>

   
        <!-- Enviar mensaje usando AJAX -->
<div class="input-container">
    <input type="hidden" id="destinatario_id" value="<?php echo htmlspecialchars($chat_usuario_id); ?>">
    <input type="text" id="mensaje" placeholder="Escribe tu mensaje..." required>
    <button onclick="enviarMensaje()">Enviar</button>
</div>

    </form>
</div>

</body>
</html>

<?php
$stmt_usuario->close();
$stmt_mensajes->close();
$conn->close();
?>

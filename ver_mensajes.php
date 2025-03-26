<?php
include 'Verificar_Estado_Usuario.php';
include 'conexion_test.php'; // Conexión a la BD

if (!isset($_SESSION['usuario_id'])) {
    die("Error: No has iniciado sesión.");
}

$usuario_id = $_SESSION['usuario_id'];

// Obtener los usuarios que han enviado mensajes al usuario logueado
$sql = "SELECT DISTINCT u.id AS remitente_id, u.nombre_completo 
        FROM mensajes m 
        JOIN usuarios u ON m.remitente_id = u.id 
        WHERE m.destinatario_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mensajes</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 80%;
            max-width: 600px;
            margin: 30px auto;
            background: white;
            padding: 20px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }
        h2 {
            text-align: center;
            color: #003366;
        }
        .chat-list {
            list-style: none;
            padding: 0;
        }
        .chat-list li {
            padding: 15px;
            border-bottom: 1px solid #ddd;
            cursor: pointer;
            transition: background 0.3s;
        }
        .chat-list li:hover {
            background: #e0e0e0;
        }
        .chat-list a {
            text-decoration: none;
            color: #003366;
            font-weight: bold;
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
<body>

<!-- Flecha para regresar -->
<a href="ProductosDisponibles.php" class="back-arrow">
    <i class="fas fa-arrow-left"></i> Atrás
</a>



<div class="container">
    <h2>Tus Chats</h2>
    <ul class="chat-list">
        <?php
        while ($row = $result->fetch_assoc()) {
            echo "<li><a href='chat.php?usuario_id=" . htmlspecialchars($row['remitente_id']) . "'>" . 
                 htmlspecialchars($row['nombre_completo']) . "</a></li>";
        }
        ?>
    </ul>
</div>

</body>
</html>

<?php
$stmt->close();
$conn->close();
?>

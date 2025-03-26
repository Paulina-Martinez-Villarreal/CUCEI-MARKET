<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'conexion_test.php';
include 'Verificar_Estado_Usuario.php';

header("Content-Type: application/json");

$method = $_SERVER["REQUEST_METHOD"];

// Verificar si el usuario ha dado like
if ($method == "GET" && isset($_GET['producto_id'])) {
    $producto_id = $_GET['producto_id'];
    $usuario_id = $_SESSION['usuario_id'] ?? null; // Verificar usuario logueado

    $query = "SELECT COUNT(*) AS total_likes FROM likes WHERE producto_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $producto_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();

    // Verificar si el usuario ha dado like
    $usuario_like = false;
    if ($usuario_id) {
        $queryLike = "SELECT 1 FROM likes WHERE usuario_id = ? AND producto_id = ?";
        $stmt = $conn->prepare($queryLike);
        $stmt->bind_param("ii", $usuario_id, $producto_id);
        $stmt->execute();
        $resultadoLike = $stmt->get_result();
        if ($resultadoLike->num_rows > 0) {
            $usuario_like = true;
        }
    }

    echo json_encode(["total_likes" => $data["total_likes"], "usuario_like" => $usuario_like]);
    exit();
}

// Manejo del like
if ($method == "POST") {
    $usuario_id = $_POST['usuario_id'] ?? null;
    $producto_id = $_POST['producto_id'] ?? null;

    if (!$usuario_id || !$producto_id) {
        echo json_encode(["error" => "Datos inválidos"]);
        exit();
    }

    // Verificar si ya dio like
    $query = "SELECT * FROM likes WHERE usuario_id = ? AND producto_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $usuario_id, $producto_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Eliminar like
        $query = "DELETE FROM likes WHERE usuario_id = ? AND producto_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $usuario_id, $producto_id);
        $stmt->execute();
        echo json_encode(["message" => "Like eliminado", "usuario_like" => false]);
    } else {
        // Agregar like
        $query = "INSERT INTO likes (usuario_id, producto_id) VALUES (?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $usuario_id, $producto_id);
        $stmt->execute();
        echo json_encode(["message" => "Like agregado", "usuario_like" => true]);
    }
    exit();
}
$conn->close();


if ($method == "GET") {
    $query = "
        SELECT p.id, p.titulo, COUNT(l.id) AS total_likes
        FROM productos p
        LEFT JOIN likes l ON p.id = l.producto_id
        GROUP BY p.id
        ORDER BY total_likes DESC
    ";
    $result = $conn->query($query);
    $productos = [];
    while ($row = $result->fetch_assoc()) {
        $productos[] = $row;
    }
    echo json_encode($productos);
    exit();
}

$conn->close();
?>

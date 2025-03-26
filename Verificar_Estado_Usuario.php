
<?php
session_start();


// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario_id'])) {
    header("Location: inicio-de-sesión.php");
    exit();
}

include 'conexion_test.php';

if ($conn->connect_error) {
    die("Error en la conexión: " . $conn->connect_error);
}

$id_usuario = $_SESSION['usuario_id'];
$sqlEstado = "SELECT estado FROM usuarios WHERE id = ?";
$stmtEstado = $conn->prepare($sqlEstado);
$stmtEstado->bind_param("i", $id_usuario);
$stmtEstado->execute();
$resultEstado = $stmtEstado->get_result();
$usuarioEstado = $resultEstado->fetch_assoc();
$stmtEstado->close();

if ($usuarioEstado['estado'] !== 'aprobado') {
    $_SESSION['mensaje'] = "Para iniciar sesión y acceder a CUCEI MARKET tu cuenta debe ser aprovada por un administrador.";
    header("Location: index.html");
    exit();
}




?>



<?php
//Debes esperar a que tu cuenta sea aprobada por un administrador para acceder a esta página.";
//include 'Verificar_Estado_Usuario.php';

?>
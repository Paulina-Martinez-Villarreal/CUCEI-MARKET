<?php
session_start();
require_once 'conexion.php'; // Asegúrate de que el archivo de conexión a la BD está incluido

// Verifica si el usuario ha iniciado sesión
$usuario = [];

if (isset($_SESSION['id_usuario'])) {
    $id_usuario = $_SESSION['id_usuario'];

    // Consulta para obtener los datos del usuario
    $sql = "SELECT nombre_completo, imagen_perfil FROM usuarios WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $usuario = $result->fetch_assoc();
    }
    $stmt->close();
}
?>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg bg-light">
    <div class="container">
        <a class="navbar-brand" href="index.php">CUCEI MARKET</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item">
                    <a class="nav-link" href="publicar.php">Publicar Producto</a>
                </li>

                <?php if (!empty($usuario)): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="perfilDropdown" role="button" data-bs-toggle="dropdown">
                            <img src="<?php echo !empty($usuario['imagen_perfil']) ? htmlspecialchars($usuario['imagen_perfil']) : 'img/default-profile.png'; ?>" 
                                alt="Perfil" width="30" height="30" class="rounded-circle">
                            <?php echo htmlspecialchars($usuario['nombre_completo'] ?? "Usuario"); ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="perfil.php">Mi Perfil</a></li>
                            <li><a class="dropdown-item text-danger" href="cerrar_sesion.php">Cerrar Sesión</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">Iniciar Sesión</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

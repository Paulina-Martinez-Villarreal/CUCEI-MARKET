<?php
session_start();
if (isset($_SESSION['id_usuario'])) {
    echo "Sesión activa. ID de usuario: " . $_SESSION['id_usuario'];
} else {
    echo "Error: Usuario no autenticado. Inicia sesión nuevamente.";
}
?>

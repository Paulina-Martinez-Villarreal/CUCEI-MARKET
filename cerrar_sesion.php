<?php
session_start();  // Inicia la sesión
session_destroy();  // Elimina la sesión actual
header("Location: inicio-de-sesión.php");  // Redirige al login
exit();  // Finaliza la ejecución del script
?>

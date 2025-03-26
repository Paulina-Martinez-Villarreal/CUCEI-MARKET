<?php
session_start(); // Iniciar sesión

// Verificar si el usuario es administrador
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    die("Acceso denegado. Debes ser administrador para ver este documento.");
}

// Obtener el nombre del archivo desde la URL
if (!isset($_GET['archivo']) || empty($_GET['archivo'])) {
    die("Archivo no especificado.");
}

// Sanitizar el nombre del archivo para evitar ataques
$archivo = basename($_GET['archivo']);

// Definir la ruta de los documentos en el servidor EC2
$ruta_documentos = "/var/www/html/private_uploads/" . $archivo;

// Verificar si el archivo existe y evitar acceso a archivos externos
if (!file_exists($ruta_documentos) || strpos(realpath($ruta_documentos), realpath("/var/www/html/private_uploads")) !== 0) {
    die("El archivo no existe o el acceso no está permitido.");
}

// Seguridad adicional para evitar que el navegador almacene en caché el documento
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

// Configurar las cabeceras para la visualización segura del PDF
header("Content-Type: application/pdf");
header("Content-Disposition: inline; filename=\"$archivo\"");

// Enviar el archivo al navegador
readfile($ruta_documentos);
exit();
?>

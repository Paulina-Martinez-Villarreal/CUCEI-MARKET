<?php
$directorio = 'uploads/';
if (is_writable($directorio)) {
    echo "La carpeta uploads tiene permisos de escritura.";
} else {
    echo "La carpeta uploads NO tiene permisos de escritura.";
}
?>
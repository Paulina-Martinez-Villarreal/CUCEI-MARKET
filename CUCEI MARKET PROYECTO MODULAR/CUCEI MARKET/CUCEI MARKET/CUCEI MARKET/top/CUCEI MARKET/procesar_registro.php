<?php
// Conexión a la base de datos
$host = "rds-cm-1.ckdg6oyb0qwj.us-west-2.rds.amazonaws.com";
$db = "test";
$user = "admin";
$pass = "Lu7yFCNGaCCGJrZ";
$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Error en la conexión: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recibir los datos del formulario
    $nombre_completo = $_POST['nombre_completo'];
    $correo = $_POST['correo'];
    $codigo = $_POST['codigo'];
    $carrera = $_POST['carrera'];
    $clave = password_hash($_POST['clave'], PASSWORD_BCRYPT);
    $ciclo_ingreso = $_POST['ciclo_ingreso'];
    $ciclo_actual = $_POST['ciclo_actual'];

    // Procesar el documento PDF
    if (isset($_FILES['documento']) && $_FILES['documento']['error'] == 0) {
        $nombre_documento = $_FILES['documento']['name'];
        $ruta_temporal = $_FILES['documento']['tmp_name'];
        $directorio = 'documentos/' . basename($nombre_documento);

        // Verificar que el archivo sea un PDF y no exceda 5MB
        $tipo_documento = strtolower(pathinfo($directorio, PATHINFO_EXTENSION));
        $tamaño_documento = $_FILES['documento']['size'];

        if ($tipo_documento == "pdf" && $tamaño_documento <= 5000000) {
            if (move_uploaded_file($ruta_temporal, $directorio)) {
                // Verificar si el correo ya existe en la base de datos
                $sql_check = "SELECT * FROM usuarios WHERE correo = ?";
                $stmt_check = $conn->prepare($sql_check);
                $stmt_check->bind_param("s", $correo);
                $stmt_check->execute();
                $result = $stmt_check->get_result();

                if ($result->num_rows > 0) {
                    echo "El correo ya está registrado. Por favor, utiliza otro correo.";
                } else {
                    // Si no existe, procede a insertar el registro
                    $sql = "INSERT INTO usuarios (nombre_completo, correo, codigo, carrera, clave, ciclo_ingreso, ciclo_actual, documento_verificacion) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("ssssssss", $nombre_completo, $correo, $codigo, $carrera, $clave, $ciclo_ingreso, $ciclo_actual, $directorio);

                    if ($stmt->execute()) {
                        echo "Registro exitoso. Tu cuenta será verificada.";
                    } else {
                        echo "Error al guardar el registro: " . $conn->error;
                    }
                }
                $stmt_check->close(); // Cerrar la consulta de verificación
            } else {
                echo "Error al subir el documento.";
            }
        } else {
            echo "Solo se permiten archivos PDF de hasta 5MB.";
        }
    } else {
        echo "No se recibió ningún documento o hubo un error al subirlo.";
    }
} else {
    echo "Método de envío no válido.";
}

$conn->close();
?>

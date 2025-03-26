<?php
session_start(); // Iniciar sesión

// Verificar si el usuario ha iniciado sesión y es administrador
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'admin') {
    header("Location: index.html"); // Redirigir si no es admin
    exit();
}

include 'conexion_test.php'; // Conexión a la BD

// Obtener los usuarios pendientes de aprobación
$sql = "SELECT id, nombre_completo, correo, carrera, documento_verificacion FROM usuarios WHERE estado = 'pendiente'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación de Usuarios</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --azul-udg: #0B2C4D;
            --dorado-udg: #FFD700;
            --gris-claro: #f8f9fa;
        }

        body {
            background-color: var(--gris-claro);
            font-family: Arial, sans-serif;
        }

        .navbar {
            background-color: var(--azul-udg);
        }

        .navbar-brand, .nav-link {
            color: white !important;
            font-weight: bold;
        }

        .container {
            margin-top: 50px;
        }

        .titulo {
            text-align: center;
            color: var(--azul-udg);
            margin-bottom: 20px;
            font-size: 32px;
            font-weight: bold;
        }

        table {
            width: 100%;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        th, td {
            padding: 15px;
            text-align: left;
        }

        th {
            background-color: var(--azul-udg);
            color: white;
        }

        .btn-aprobar {
            background-color: var(--azul-udg);
            color: white;
            font-weight: bold;
            border: none;
            padding: 8px 12px;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn-rechazar {
            background-color: red;
            color: white;
            font-weight: bold;
            border: none;
            padding: 8px 12px;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn-aprobar:hover {
            background-color: #09253e;
        }

        .btn-rechazar:hover {
            background-color: darkred;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="index.html">CUCEI MARKET</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ms-auto">
                    <?php if ($_SESSION['rol'] === 'admin'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="Pyodide.php" target="_blank">IA Predicción de Usuarios Nuevos</a>
                        </li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a class="nav-link" href="cerrar_sesion.php">Cerrar Sesión</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <h1 class="titulo">Usuarios Pendientes de Verificación</h1>
        
        <?php if ($result->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Correo</th>
                            <th>Carrera</th>
                            <th>Documento</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['nombre_completo'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($row['correo'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($row['carrera'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td>
                                    <a href="ver_documento.php?archivo=<?php echo urlencode($row['documento_verificacion']); ?>" target="_blank">
                                        Ver Documento
                                    </a>
                                </td>
                                <td>
                                    <form action="aprobar_usuario.php" method="POST">
                                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8'); ?>">
                                        <button type="submit" name="accion" value="aprobar" class="btn-aprobar">Aprobar</button>
                                        <button type="submit" name="accion" value="rechazar" class="btn-rechazar">Rechazar</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-center">No hay usuarios pendientes de verificación.</p>
        <?php endif; ?>
    </div>
</body>
</html>

<?php
$conn->close();
?>

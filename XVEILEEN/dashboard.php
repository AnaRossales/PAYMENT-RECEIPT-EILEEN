<?php
session_start();
include 'db.php'; // Conexión a la base de datos

if (!isset($_SESSION['telefono'])) {
    header("Location: login.php");
    exit();
}

$telefono = $_SESSION['telefono'];

// Consulta para obtener la información del usuario
$query = "SELECT nombre_completo, monto_pagado, monto_restante FROM USUARIO WHERE telefono = ?";
$stmt = $conn->prepare($query);

if (!$stmt) {
    die("Error en la preparación de la consulta: " . $conn->error);
}

$stmt->bind_param('s', $telefono);
$stmt->execute();
$stmt->bind_result($nombre_completo, $monto_pagado, $monto_restante);
$stmt->fetch();
$stmt->close();

// Consulta para obtener los comprobantes
$query = "
    SELECT COMPROBANTE.id_comprobante, COMPROBANTE.tipo_mime 
    FROM COMPROBANTE_USUARIO 
    JOIN COMPROBANTE ON COMPROBANTE_USUARIO.comprobante = COMPROBANTE.id_comprobante 
    WHERE COMPROBANTE_USUARIO.telefono = ?";
$stmt = $conn->prepare($query);

if (!$stmt) {
    die("Error en la preparación de la consulta: " . $conn->error);
}

$stmt->bind_param('s', $telefono);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <!-- Incluir Bootstrap CSS -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <!-- Incluir la hoja de estilos personalizada -->
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <div class="dashboard-container">
        <h1>Hola, <?php echo htmlspecialchars($nombre_completo); ?>!</h1>
        <p><strong>Monto Pagado:</strong> <?php echo htmlspecialchars($monto_pagado); ?></p>
        <p><strong>Monto Restante:</strong> <?php echo htmlspecialchars($monto_restante); ?></p>

        <h2>Comprobantes</h2>
        
        <!-- Tabla de comprobantes -->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID Comprobante</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo '<tr>';
                        echo '<td>' . htmlspecialchars($row['id_comprobante']) . '</td>';
                        echo '<td><a href="download.php?id_comprobante=' . htmlspecialchars($row['id_comprobante']) . '" class="btn btn-primary btn-sm">Descargar</a></td>';
                        echo '</tr>';
                    }
                } else {
                    // Si no hay comprobantes, muestra una fila vacía
                    echo '<tr>';
                    echo '<td colspan="2" class="text-center">No hay comprobantes disponibles.</td>';
                    echo '</tr>';
                }
                ?>
            </tbody>
        </table>

        <!-- Botones en la misma línea -->
        <div class="row">
            <div class="col-md-6">
                <a href="upload_comprobante.php" class="btn btn-primary w-100">Subir Comprobante</a>
            </div>
            <div class="col-md-6">
                <a href="logout.php" class="btn btn-secondary w-100">Cerrar Sesión</a>
            </div>
        </div>
    </div>
</div>

<!-- Incluir Bootstrap JS -->
<script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>

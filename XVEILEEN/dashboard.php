<?php
session_start();
include 'db.php'; // Conexión a la base de datos

if (!isset($_SESSION['telefono'])) {
    header("Location: login.php");
    exit();
}

$telefono = $_SESSION['telefono'];

// Consulta para obtener la información del usuario
$query = "SELECT monto_pagado, monto_restante FROM USUARIO WHERE telefono = ?";
$stmt = $conn->prepare($query);

if (!$stmt) {
    die("Error en la preparación de la consulta: " . $conn->error);
}

$stmt->bind_param('s', $telefono);
$stmt->execute();
$stmt->bind_result($monto_pagado, $monto_restante);
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

<h1>Dashboard</h1>
<p><strong>Monto Pagado:</strong> <?php echo htmlspecialchars($monto_pagado); ?></p>
<p><strong>Monto Restante:</strong> <?php echo htmlspecialchars($monto_restante); ?></p>

<h2>Comprobantes</h2>

<!-- Tabla de comprobantes -->
<table border="1">
    <tr>
        <th>ID Comprobante</th>
        <th>Acción</th>
    </tr>
    <?php
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($row['id_comprobante']) . '</td>';
            echo '<td><a href="download.php?id_comprobante=' . htmlspecialchars($row['id_comprobante']) . '">Descargar</a></td>';
            echo '</tr>';
        }
    } else {
        // Si no hay comprobantes, muestra una fila vacía
        echo '<tr>';
        echo '<td colspan="2">No hay comprobantes disponibles.</td>';
        echo '</tr>';
    }
    ?>
</table>

<!-- Botón para subir comprobantes -->
<a href="upload_comprobante.php">
    <button type="button">Subir Comprobante</button>
</a>

<a href="logout.php">Cerrar Sesión</a>

<?php
$stmt->close();
$conn->close();
?>

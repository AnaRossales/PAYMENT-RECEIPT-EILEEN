<?php
session_start();
include 'db.php'; // Conexión a la base de datos

if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $telefono = $_POST['telefono'];
} else {
    $telefono = '';
}

// Consulta para obtener los comprobantes asociados al teléfono proporcionado
$query = "
    SELECT COMPROBANTE.id_comprobante, COMPROBANTE.tipo_mime 
    FROM COMPROBANTE
    JOIN COMPROBANTE_USUARIO ON COMPROBANTE.id_comprobante = COMPROBANTE_USUARIO.comprobante
    WHERE COMPROBANTE_USUARIO.telefono = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('s', $telefono);
$stmt->execute();
$result = $stmt->get_result();
?>

<h1>Dashboard de Administrador</h1>

<form method="POST" action="">
    <label for="telefono">Teléfono:</label>
    <input type="text" name="telefono" id="telefono" value="<?php echo htmlspecialchars($telefono); ?>" required>
    <input type="submit" value="Buscar Comprobantes">
</form>

<?php if ($telefono): ?>
    <h2>Comprobantes para Teléfono: <?php echo htmlspecialchars($telefono); ?></h2>

    <!-- Tabla de comprobantes -->
    <form method="POST" action="convert_and_download.php">
        <table border="1">
            <tr>
                <th>ID Comprobante</th>
                <th>Tipo MIME</th>
                <th>Convertir</th>
            </tr>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<tr>';
                    echo '<td>' . htmlspecialchars($row['id_comprobante']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['tipo_mime']) . '</td>';
                    echo '<td><input type="checkbox" name="comprobantes[]" value="' . htmlspecialchars($row['id_comprobante']) . '"></td>';
                    echo '</tr>';
                }
            } else {
                echo '<tr><td colspan="3">No hay comprobantes disponibles para este teléfono.</td></tr>';
            }
            ?>
        </table>
        <br>
        <input type="submit" value="Convertir Seleccionados">
    </form>
<?php endif; ?>

<a href="admin_logout.php">Cerrar Sesión</a>

<?php
$stmt->close();
$conn->close();
?>

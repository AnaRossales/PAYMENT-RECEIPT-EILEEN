<?php
session_start();
include 'db.php';
include 'functions.php';

// Verifica si el usuario es administrador
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

// Obtener el nombre completo del administrador
$admin_nombre = "Administrador"; // Aquí puedes obtener el nombre del admin desde la base de datos, si es necesario.

// Obtener comprobantes no aprobados
$query = "SELECT c.id_comprobante, c.tipo_mime, c.archivo_comprobante, u.telefono, u.monto_pagado 
          FROM COMPROBANTE c 
          JOIN COMPROBANTE_USUARIO cu ON c.id_comprobante = cu.comprobante 
          JOIN USUARIO u ON u.telefono = cu.telefono 
          WHERE c.aprobado = 0";
$result = $conn->query($query);

// Si el administrador aprueba un comprobante
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['aprobar_comprobante'])) {
    $id_comprobante = $_POST['id_comprobante'];
    $monto_pagado = $_POST['monto_pagado'];
    $telefono = $_POST['telefono'];

    // Actualizar el comprobante como aprobado
    $update_comprobante = "UPDATE COMPROBANTE SET aprobado = 1 WHERE id_comprobante = ?";
    $stmt = $conn->prepare($update_comprobante);
    $stmt->bind_param('i', $id_comprobante);
    $stmt->execute();

    // Actualizar el monto del usuario
    $update_usuario = "UPDATE USUARIO SET monto_pagado = monto_pagado + ?, monto_restante = monto_restante - ? WHERE telefono = ?";
    $stmt = $conn->prepare($update_usuario);
    $stmt->bind_param('dds', $monto_pagado, $monto_pagado, $telefono);
    $stmt->execute();

    echo "Comprobante aprobado y montos actualizados.";
    header("Refresh: 0"); // Refresca la página automáticamente después de aprobar
}

// Función para descargar comprobante
if (isset($_GET['download'])) {
    $id_comprobante = $_GET['download'];
    
    // Obtener archivo comprobante de la base de datos
    $query = "SELECT archivo_comprobante, tipo_mime FROM COMPROBANTE WHERE id_comprobante = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $id_comprobante);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($archivo, $tipo_mime);
    $stmt->fetch();

    if ($archivo) {
        // Forzar la descarga del archivo
        header("Content-Type: " . $tipo_mime);
        header('Content-Disposition: attachment; filename="comprobante_' . $id_comprobante . '"');
        echo $archivo;
        exit();
    } else {
        echo "Archivo no encontrado.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard del Administrador</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <div class="dashboard-container">
        <h1>Hola, <?php echo $admin_nombre; ?></h1>

        <!-- Sección para aprobar comprobantes -->
        <h2>Comprobantes Pendientes de Aprobación</h2>
        <table border="1" class="table">
            <thead>
                <tr>
                    <th>ID Comprobante</th>
                    <th>Teléfono</th>
                    <th>Monto Pagado</th>
                    <th>Acción</th>
                    <th>Descargar</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $row['id_comprobante']; ?></td>
                        <td><?php echo $row['telefono']; ?></td>
                        <td><?php echo $row['monto_pagado']; ?></td>
                        <td>
                            <form method="POST">
                                <input type="hidden" name="id_comprobante" value="<?php echo $row['id_comprobante']; ?>">
                                <input type="hidden" name="monto_pagado" value="<?php echo $row['monto_pagado']; ?>">
                                <input type="hidden" name="telefono" value="<?php echo $row['telefono']; ?>">
                                <button type="submit" name="aprobar_comprobante" class="btn btn-primary">Aprobar</button>
                            </form>
                        </td>
                        <td>
                            <a href="?download=<?php echo $row['id_comprobante']; ?>" class="btn btn-secondary">Descargar</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <!-- Enlace para volver al inicio del dashboard u otras funcionalidades -->
        <div class="text-center" style="margin-top: 20px;">
            <a href="admin_dashboard.php" class="btn btn-secondary">Volver al Dashboard</a>
        </div>
    </div>

    <script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>

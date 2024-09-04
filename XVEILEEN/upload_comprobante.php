<?php
session_start();
include 'db.php'; // Conexión a la base de datos

if (!isset($_SESSION['telefono'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $telefono = $_SESSION['telefono'];
    
    // Verifica si se subió un archivo
    if (isset($_FILES['comprobante']) && $_FILES['comprobante']['error'] == 0) {
        $archivo = file_get_contents($_FILES['comprobante']['tmp_name']);
        $tipo_mime = $_FILES['comprobante']['type']; // Obtiene el tipo MIME del archivo
        $monto_pagado = $_POST['monto_pagado']; // Monto del comprobante subido

        // Inserta el archivo en la tabla COMPROBANTE
        $query = "INSERT INTO COMPROBANTE (archivo_comprobante, tipo_mime) VALUES (?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('ss', $archivo, $tipo_mime);
        $stmt->send_long_data(0, $archivo);
        $stmt->execute();
        
        // Obtener el ID del comprobante insertado
        $id_comprobante = $stmt->insert_id;

        // Inserta en la tabla COMPROBANTE_USUARIO
        $query = "INSERT INTO COMPROBANTE_USUARIO (comprobante, telefono) VALUES (?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('is', $id_comprobante, $telefono);
        $stmt->execute();

        // Actualizar los montos en la tabla USUARIO
        $query = "UPDATE USUARIO SET monto_pagado = monto_pagado + ?, monto_restante = monto_restante - ? WHERE telefono = ?";
        $stmt->prepare($query);
        $stmt->bind_param('dds', $monto_pagado, $monto_pagado, $telefono);
        $stmt->execute();

        echo "Comprobante subido exitosamente. El monto restante ha sido actualizado.";
    } else {
        echo "Error al subir el archivo.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subir Comprobante</title>
    <link href="assets/css/style.css" rel="stylesheet"> <!-- Vincula la hoja de estilo -->
</head>
<body>
<div class="dashboard-container">
    <h1>Subir Comprobante</h1>
    <form method="POST" enctype="multipart/form-data" action="">
        <div class="form-group">
            <label for="monto_pagado">Monto Pagado:</label>
            <input type="number" name="monto_pagado" id="monto_pagado" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="comprobante">Selecciona el comprobante:</label>
            <input type="file" name="comprobante" id="comprobante" class="form-control" required>
        </div>
        <div class="button-container">
            <button type="submit" class="btn btn-primary">Subir Comprobante</button>
        </div>
    </form>
</div>

<!-- Carga de Bootstrap o scripts adicionales -->
<script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>

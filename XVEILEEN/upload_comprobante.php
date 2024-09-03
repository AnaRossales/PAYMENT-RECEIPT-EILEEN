<?php
session_start();
include 'db.php'; // Conexión a la base de datos
include 'functions.php'; // Incluir funciones auxiliares para validación

if (!isset($_SESSION['telefono'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $telefono = $_SESSION['telefono'];
    
    // Verificar si se subió un archivo
    if (isset($_FILES['comprobante']) && $_FILES['comprobante']['error'] == 0) {
        // Definir tipos permitidos y tamaño máximo
        $allowedTypes = ['image/jpeg', 'image/png', 'application/pdf'];
        $maxSize = 2 * 1024 * 1024; // 2 MB

        // Validar archivo
        $errors = validateUploadedFile($_FILES['comprobante'], $allowedTypes, $maxSize);
        if (!empty($errors)) {
            foreach ($errors as $error) {
                echo "<p>$error</p>";
            }
        } else {
            // Obtener datos del archivo
            $archivo = file_get_contents($_FILES['comprobante']['tmp_name']);
            $tipo_mime = $_FILES['comprobante']['type']; // Obtiene el tipo MIME del archivo
            
            // Inserta el archivo en la tabla COMPROBANTE
            $query = "INSERT INTO COMPROBANTE (archivo_comprobante, tipo_mime) VALUES (?, ?)";
            $stmt = $conn->prepare($query);
            if (!$stmt) {
                die("Error en la preparación de la consulta: " . $conn->error);
            }
            $stmt->bind_param('ss', $archivo, $tipo_mime);
            $stmt->send_long_data(0, $archivo);
            $stmt->execute();
            
            // Obtener el ID del comprobante insertado
            $id_comprobante = $stmt->insert_id;

            // Inserta en la tabla COMPROBANTE_USUARIO
            $query = "INSERT INTO COMPROBANTE_USUARIO (comprobante, telefono) VALUES (?, ?)";
            $stmt = $conn->prepare($query);
            if (!$stmt) {
                die("Error en la preparación de la consulta: " . $conn->error);
            }
            $stmt->bind_param('is', $id_comprobante, $telefono);
            $stmt->execute();

            echo "Comprobante subido exitosamente.";
        }
    } else {
        echo "Error al subir el archivo.";
    }
}
?>

<h1>Subir Comprobante</h1>
<form method="POST" enctype="multipart/form-data" action="">
    <label for="comprobante">Selecciona el comprobante:</label>
    <input type="file" name="comprobante" id="comprobante" required><br><br>
    <input type="submit" value="Subir Comprobante">
</form>
<a href="dashboard.php">Volver al Dashboard</a>

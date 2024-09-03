<?php
session_start();
include 'db.php'; // Conexión a la base de datos

if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['comprobantes'])) {
    $comprobantes = $_POST['comprobantes'];

    foreach ($comprobantes as $id_comprobante) {
        // Consulta para obtener el archivo binario y el tipo MIME
        $query = "SELECT archivo_comprobante, tipo_mime FROM COMPROBANTE WHERE id_comprobante = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $id_comprobante);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($archivo, $tipo_mime);
        $stmt->fetch();

        if ($archivo && $tipo_mime) {
            // Configurar las cabeceras para la descarga del archivo
            header('Content-Type: ' . $tipo_mime);
            header('Content-Disposition: attachment; filename="comprobante_' . $id_comprobante . '.' . getExtensionFromMimeType($tipo_mime) . '"');
            echo $archivo;
            exit();
        } else {
            echo "Archivo no encontrado para ID: " . htmlspecialchars($id_comprobante);
        }

        $stmt->close();
    }
}

$conn->close();

// Función para obtener la extensión del archivo desde el tipo MIME
function getExtensionFromMimeType($mimeType) {
    $mimeTypes = array(
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'application/pdf' => 'pdf',
        // Agrega más tipos MIME y sus extensiones aquí
    );

    return isset($mimeTypes[$mimeType]) ? $mimeTypes[$mimeType] : 'bin';
}
?>

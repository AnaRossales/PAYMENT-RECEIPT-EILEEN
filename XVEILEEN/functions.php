<?php
function validateUploadedFile($file, $allowedTypes, $maxSize) {
    $errors = [];

    // Verificar si hubo un error en la carga del archivo
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errors[] = "Error en la carga del archivo.";
        return $errors;
    }

    // Verificar el tamaño del archivo
    if ($file['size'] > $maxSize) {
        $errors[] = "El archivo es demasiado grande. El tamaño máximo permitido es " . ($maxSize / 1048576) . " MB.";
        return $errors;
    }

    // Verificar el tipo MIME del archivo
    $fileType = mime_content_type($file['tmp_name']);
    if (!in_array($fileType, $allowedTypes)) {
        $errors[] = "Tipo de archivo no permitido. Los tipos permitidos son: " . implode(", ", $allowedTypes) . ".";
        return $errors;
    }

    return $errors;
}

function executeQuery($conn, $query, $params) {
    $stmt = $conn->prepare($query);

    if (!$stmt) {
        die("Error en la preparación de la consulta: " . $conn->error);
    }

    // Asignar parámetros a la consulta
    $types = str_repeat('s', count($params)); // Asumiendo que todos los parámetros son strings
    $stmt->bind_param($types, ...$params);

    if (!$stmt->execute()) {
        die("Error en la ejecución de la consulta: " . $stmt->error);
    }

    return $stmt;
}
?>

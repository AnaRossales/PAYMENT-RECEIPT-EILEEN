<?php
session_start();
include 'db.php'; // Conexión a la base de datos
include 'functions.php';

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

        while(){

        }

        // Inserta el archivo en la tabla COMPROBANTE
        $query = "INSERT INTO COMPROBANTE (archivo_comprobante, tipo_mime, aprobado) VALUES (?, ?, 0)"; // Inicialmente no aprobado
        $stmt = $conn->prepare($query);
        $stmt->bind_param('ss', $archivo, $tipo_mime);
        $stmt->send_long_data(0, $archivo);
        $stmt->execute();
        
        // Obtener el ID del comprobante insertado
        $id_comprobante = $stmt->insert_id;


        // Después de la lógica de subida del comprobante, agrega el código de envío de correo:
if ($archivo_subido_exitosamente) {
    // Configura y envía el correo al administrador
    require 'vendor/autoload.php'; // Cargar PHPMailer
    
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.example.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'your-email@example.com';
        $mail->Password = 'your-email-password';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('your-email@example.com', 'Your Name');
        $mail->addAddress('admin@example.com', 'Admin');
        $mail->isHTML(true);
        $mail->Subject = 'Nuevo comprobante subido';
        $mail->Body    = 'El usuario ha subido un nuevo comprobante.';

        $mail->send();
        echo 'Correo enviado exitosamente';
    } catch (Exception $e) {
        echo "No se pudo enviar el correo. Error: {$mail->ErrorInfo}";
    }
}
 //ESTA PARTE NO FUNCIONA, REVISAAAAAA
          // Enviar correo al administrador
          $admin_email = "fourier.anadev@gmail.com"; // Reemplaza con el correo del administrador
          $subject = "Nuevo comprobante subido por el usuario $telefono";
          $message = "El usuario con teléfono $telefono ha subido un comprobante de pago de $$monto_pagado.";
          $headers = "From: noreply@tudominio.com"; // Reemplaza con el correo que envía el aviso
  
          // Envía el correo
          if (mail($admin_email, $subject, $message, $headers)) {
              echo "Comprobante subido exitosamente. Pendiente de aprobación.";
          } else {
              echo "Comprobante subido, avise al encargado para su aprobacion";
          }
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
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
<div class="dashboard-container">
    <h1>Subir Comprobante</h1>
    <form method="POST" enctype="multipart/form-data" action="">
        <div class="form-group">
            <label for="monto_pagado">Monto Pagado:</label>
            <input type="number" name="monto_pagado" id="monto_pagado" class="form-control" min="0" required>
        </div>
        <div class="form-group">
            <label for="comprobante">Selecciona el comprobante:</label>
            <input type="file" name="comprobante" id="comprobante" class="form-control" required>
        </div>
        <div class="button-container">
            <button type="submit" class="btn btn-primary">Subir Comprobante</button>
            <br>
            <a href="dashboard.php" class="btn btn-secondary">Regresar al Dashboard</a>
        </div>
    </form>
</div>

<!-- Carga de Bootstrap o scripts adicionales -->
<script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
session_start();
include 'db.php'; // Archivo de conexión a la base de datos

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $telefono = $_POST['telefono'];
    $contrasena = $_POST['password']; // Cambié 'contrasena' por 'password' para coincidir con el nombre del campo en el formulario

    // Consulta para obtener la contraseña del usuario
    $sql = "SELECT contrasena FROM USUARIO WHERE telefono = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $telefono);
    $stmt->execute();
    $stmt->store_result();

    // Verificación de si el usuario existe
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($stored_password);
        $stmt->fetch();

        // Verificar que la contraseña sea correcta
        if ($contrasena === $stored_password) {
            $_SESSION['telefono'] = $telefono;
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Contraseña incorrecta";
        }
    } else {
        $error = "Usuario no encontrado";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    
    <!-- Incluir Bootstrap CSS -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Incluir la hoja de estilos personalizada -->
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>

<div class="login-container">
    <h1>Iniciar Sesión</h1>
    <form method="POST" action="login.php">
        <div class="form-group">
            <label for="telefono">Teléfono:</label>
            <input type="text" class="form-control" id="telefono" name="telefono" required>
        </div>
        <div class="form-group">
            <label for="password">Contraseña:</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <button type="submit" class="btn btn-primary">Ingresar</button>
    </form>
    <p class="text-center">
        <a href="recuperar_contrasena.php">¿Olvidaste tu contraseña?</a>
    </p>

    <?php
        if (isset($error)) {
            echo "<p class='error-message'>$error</p>";
        }
    ?>
</div>

<!-- Incluir Bootstrap JS -->
<script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>

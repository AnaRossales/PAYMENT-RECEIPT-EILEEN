<?php
session_start();
include 'db.php'; // Archivo de conexión a la base de datos

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $telefono = $_POST['telefono'];
    $contrasena = $_POST['contrasena'];

    // Consulta para obtener la contraseña del usuario
    $sql = "SELECT contrasena FROM USUARIO WHERE telefono = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $telefono);
    $stmt->execute();
    $stmt->store_result();

    // Verificacion de si el usuario existe
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($stored_password);
        $stmt->fetch();

        // Verificar que la contraseña sea correcta :)
        if ($contrasena === $stored_password) {
            session_start();
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
    <title>Iniciar Sesión</title>
</head>
<body>
    <h2>Iniciar Sesión</h2>
    <form method="post" action="login.php">
        <label>Teléfono:</label>
        <input type="text" name="telefono" maxlength="11" required><br>
        <label>Contraseña:</label>
        <input type="password" name="contrasena" required><br>
        <input type="submit" value="Iniciar Sesión">
    </form>
<?php
    if (isset($error)) {
        echo "<p style='color:red;'>$error</p>";
    }
 ?>
</body>
</html>
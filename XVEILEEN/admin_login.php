<?php
session_start();
include 'db.php'; // Conexión a la base de datos

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Consulta para obtener la información del administrador
    $query = "SELECT password FROM ADMIN WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($stored_password);
    $stmt->fetch();

    if ($password === $stored_password) {
        $_SESSION['admin'] = $username;
        header("Location: admin_dashboard.php");
        exit();
    } else {
        echo "Usuario o contraseña incorrectos.";
    }
}
?>

<h1>Iniciar sesión como Administrador</h1>
<form method="POST" action="">
    <label for="username">Usuario:</label>
    <input type="text" name="username" id="username" required><br><br>
    <label for="password">Contraseña:</label>
    <input type="password" name="password" id="password" required><br><br>
    <input type="submit" value="Iniciar sesión">
</form>

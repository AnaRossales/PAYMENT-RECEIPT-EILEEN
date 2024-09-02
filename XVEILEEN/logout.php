<?php
// archivo para cerrar sesion
session_start();
session_destroy();
header("Location: login.php");
exit();
?>

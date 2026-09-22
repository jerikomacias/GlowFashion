<?php
$host = "localhost";
$user = "root";      // Usuario por defecto de XAMPP
$pass = "";          // Contraseña por defecto (vacía)
$db   = "glow_fashion";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
?>
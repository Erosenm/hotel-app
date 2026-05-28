<?php
$host = "127.0.0.1";
$db   = "hotel_db";
$user = "root";
$pass = ""; // tu contraseña si tienes

try {
    $conn = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


} catch (PDOException $e) {
    // Depuración en consola del navegador con error
    echo "<script>console.error('Error de conexión: " . addslashes($e->getMessage()) . "');</script>";
    die("Error de conexión: " . $e->getMessage());
}
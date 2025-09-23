<?php
$host = 'localhost';
$db   = 'sistema_ventas';
$user = 'root';  // XAMPP por defecto
$pass = '';     // Sin contraseña por defecto
// Configuración de impresora térmica
$printer = 'HP-POS808'; // Nombre del puerto

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>
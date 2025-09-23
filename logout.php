<?php
session_start();
date_default_timezone_set('America/Lima'); // Zona horaria de Perú

$usuario_id = $_SESSION['usuario_id'];
$fecha = date('Y-m-d');
$hora = date('H:i:s');

// ✅ REGISTRAR SALIDA AL CERRAR SESIÓN
try {
    $pdo = new PDO("mysql:host=localhost;dbname=sistema_ventas;charset=utf8", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Actualizar la salida solo si no está ya registrada
    $stmt = $pdo->prepare("UPDATE asistencia 
                           SET salida = ? 
                           WHERE usuario_id = ? AND fecha = ? AND salida IS NULL");
    $stmt->execute([$hora, $usuario_id, $fecha]);

} catch (Exception $e) {
    error_log("Error al registrar salida: " . $e->getMessage());
}

// ✅ Cerrar sesión
$_SESSION = array();
session_destroy();

// Redirigir
header("Location: index.php?mensaje=Has cerrado sesión. Tu salida fue registrada.");
exit;
?>
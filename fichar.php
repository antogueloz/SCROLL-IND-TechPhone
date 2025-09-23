<?php
session_start();
include 'includes/conexion.php';

$usuario_id = $_SESSION['usuario_id'];
$fecha = date('Y-m-d');
$hora = date('H:i:s');
$accion = $_POST['accion'];

try {
    $stmt = $pdo->prepare("INSERT INTO asistencia (usuario_id, fecha, entrada) 
                           VALUES (?, ?, ?) 
                           ON DUPLICATE KEY UPDATE salida = ?");
    
    if ($accion == 'entrada') {
        $stmt->execute([$usuario_id, $fecha, $hora, null]);
    } else {
        $stmt->execute([$usuario_id, $fecha, $hora, $hora]); // actualiza salida
    }

} catch (Exception $e) {
    // No bloquea si hay error
}

header("Location: dashboard.php");
exit;
?>
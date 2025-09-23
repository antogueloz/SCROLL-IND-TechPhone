<?php
session_start();
include '../includes/conexion.php';

$id = $_GET['id'];

// Verificar si el producto fue usado en alguna venta
$stmt = $pdo->prepare("
    SELECT COUNT(*) as total, 
           SUM(cantidad) as unidades_vendidas 
    FROM detalle_venta 
    WHERE producto_id = ?
");
$stmt->execute([$id]);
$uso = $stmt->fetch();

if ($uso['total'] > 0) {
    // Producto ya fue usado → no se puede eliminar
    $mensaje = "No puedes eliminar este producto porque ya fue usado en {$uso['total']} venta(s) ({$uso['unidades_vendidas']} unidades vendidas).";
    $tipo = 'warning';
} else {
    // Producto no usado → se puede eliminar
    $stmt = $pdo->prepare("DELETE FROM productos WHERE id = ?");
    $stmt->execute([$id]);
    $mensaje = "Producto eliminado correctamente.";
    $tipo = 'success';
}

// Redirigir con mensaje
header("Location: index.php?mensaje=" . urlencode($mensaje) . "&tipo=" . $tipo);
exit;
?>
<?php
session_start();
include '../includes/conexion.php';

// Obtener filtros
$fechaDesde = $_GET['fechaDesde'] ?? null;
$mes = $_GET['mes'] ?? null;

// Calcular fecha base
$fechaBase = $fechaDesde ? $fechaDesde : date('Y-m-d');
$mesBase = $mes ? (int)$mes : (int)date('m');
$anioBase = (int)date('Y');

// Ventas del día (usar fechaDesde si existe)
$stmt = $pdo->prepare("SELECT SUM(total) as total FROM ventas WHERE DATE(fecha) = ?");
$stmt->execute([$fechaBase]);
$ventasDia = $stmt->fetchColumn() ?: 0;

// Ventas del mes (usar mes seleccionado o mes actual)
if ($mes) {
    $stmt = $pdo->prepare("SELECT SUM(total) as total FROM ventas WHERE MONTH(fecha) = ? AND YEAR(fecha) = ?");
    $stmt->execute([$mes, $anioBase]);
} else {
    $stmt = $pdo->prepare("SELECT SUM(total) as total FROM ventas WHERE MONTH(fecha) = ? AND YEAR(fecha) = ?");
    $stmt->execute([$mesBase, $anioBase]);
}
$ventasMes = $stmt->fetchColumn() ?: 0;

// Total acumulado
$stmt = $pdo->prepare("SELECT SUM(total) as total FROM ventas");
$stmt->execute();
$totalAcumulado = $stmt->fetchColumn() ?: 0;

// Ventas por día (últimos 7 días a partir de fechaDesde)
$fechas = [];
$ventas = [];
$inicio = $fechaDesde ? $fechaDesde : date('Y-m-d', strtotime('-6 days'));
$inicio = date('Y-m-d', strtotime($inicio . ' -6 days'));

for ($i = 0; $i < 7; $i++) {
    $fecha = date('Y-m-d', strtotime("+$i day", strtotime($inicio)));
    $stmt = $pdo->prepare("SELECT SUM(total) as total FROM ventas WHERE DATE(fecha) = ?");
    $stmt->execute([$fecha]);
    $total = $stmt->fetchColumn() ?: 0;
    $fechas[] = $fecha;
    $ventas[] = $total;
}

// ✅ Respuesta JSON
echo json_encode([
    'ventasDia' => round($ventasDia, 2),
    'ventasMes' => round($ventasMes, 2),
    'totalAcumulado' => round($totalAcumulado, 2),
    'dias' => $fechas,
    'ventas' => $ventas
]);
exit;
?>
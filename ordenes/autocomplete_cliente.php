<?php
session_start();
include '../includes/conexion.php';

$dni = $_GET['dni'] ?? '';

if (!$dni || !preg_match('/^\d{8}$/', $dni)) {
    echo json_encode(['nombre' => '']);
    exit;
}

$stmt = $pdo->prepare("SELECT nombre FROM clientes WHERE dni = ?");
$stmt->execute([$dni]);
$cliente = $stmt->fetch();

echo json_encode(['nombre' => $cliente['nombre'] ?? '']);
exit;
?>
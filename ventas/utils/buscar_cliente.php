<?php
session_start();

// ✅ Ruta corregida
include '../../includes/conexion.php';

$dni = $_GET['dni'] ?? '';

if (!$dni || !preg_match('/^\d{8}$/', $dni)) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'DNI inválido']);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT nombre, telefono FROM clientes WHERE dni = ?");
    $stmt->execute([$dni]);
    $cliente = $stmt->fetch();

    header('Content-Type: application/json');
    if ($cliente) {
        echo json_encode([
            'nombre' => $cliente['nombre'],
            'telefono' => $cliente['telefono'] ?? ''
        ]);
    } else {
        echo json_encode([
            'nombre' => null,
            'telefono' => ''
        ]);
    }
} catch (Exception $e) {
    error_log("Error en buscar_cliente.php: " . $e->getMessage());
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Error interno', 'nombre' => null, 'telefono' => '']);
}
exit;
?>
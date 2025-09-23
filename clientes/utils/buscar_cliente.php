<?php
// clientes/utils/buscar_cliente.php

$dni = $_GET['dni'] ?? '';

if (!$dni || !preg_match('/^\d{8}$/', $dni)) {
    echo json_encode(['error' => 'DNI inválido']);
    exit;
}

include '../../includes/conexion.php';

$stmt = $pdo->prepare("SELECT nombre, telefono, direccion FROM clientes WHERE dni = ?");
$stmt->execute([$dni]);
$cliente = $stmt->fetch();

if ($cliente) {
    echo json_encode([
        'nombre' => $cliente['nombre'],
        'telefono' => $cliente['telefono'] ?? '',
        'direccion' => $cliente['direccion'] ?? ''
    ]);
} else {
    echo json_encode([
        'nombre' => null,
        'telefono' => '',
        'direccion' => ''
    ]);
}

exit;
?>
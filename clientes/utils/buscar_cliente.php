<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

include '../../includes/conexion.php';

$dni = $_GET['dni'] ?? '';

if (!$dni || !preg_match('/^\d{8}$/', $dni)) {
    echo json_encode(['error' => 'DNI inválido']);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT nombre, telefono FROM clientes WHERE numero_documento = ?");
    $stmt->execute([$dni]);
    $cliente = $stmt->fetch();

    if ($cliente) {
        echo json_encode([
            'nombre' => $cliente['nombre'],
            'telefono' => $cliente['telefono']
        ]);
    } else {
        echo json_encode([
            'nombre' => null,
            'telefono' => null
        ]);
    }
} catch (Exception $e) {
    // Nunca mostrar errores sensibles
    error_log("Error en buscar_cliente.php: " . $e->getMessage());
    echo json_encode(['error' => 'Error interno']);
}
exit;
?>
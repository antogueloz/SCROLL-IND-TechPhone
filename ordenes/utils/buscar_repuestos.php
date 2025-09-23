<?php
// ordenes/utils/buscar_repuestos.php

session_start();
include '../../includes/conexion.php';

$query = $_GET['query'] ?? '';

if (!$query || strlen($query) < 2) {
    echo json_encode([]);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT id, nombre, precio FROM productos WHERE tipo = 'repuesto' AND stock > 0 AND nombre LIKE ? ORDER BY nombre LIMIT 10");
    $stmt->execute(["%{$query}%"]);
    $repuestos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    header('Content-Type: application/json');
    echo json_encode($repuestos);
} catch (Exception $e) {
    error_log("Error en buscar_repuestos.php: " . $e->getMessage());
    echo json_encode([]);
}
exit;
?>
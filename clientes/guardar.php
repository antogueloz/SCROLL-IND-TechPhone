<?php
session_start();
include '../includes/conexion.php';

try {
    $tipo_documento = $_POST['tipo_documento'] ?? null;
    $numero_documento = $_POST['numero_documento'] ?? null;
    $nombre = $_POST['nombre'] ?? null;
    $telefono = $_POST['telefono'] ?? null;
    $email = $_POST['email'] ?? null;
    $direccion = $_POST['direccion'] ?? null;
    $es_extranjero = isset($_POST['es_extranjero']) ? 1 : 0;

    // Validaciones
    if (!$tipo_documento || !$numero_documento) {
        throw new Exception("Tipo de documento y número son obligatorios");
    }

    // Validar número de documento según tipo
    switch ($tipo_documento) {
        case 'dni':
            if (!preg_match('/^\d{8}$/', $numero_documento)) {
                throw new Exception("DNI debe tener 8 dígitos numéricos");
            }
            break;
        case 'ruc':
            if (!preg_match('/^\d{11}$/', $numero_documento)) {
                throw new Exception("RUC debe tener 11 dígitos numéricos");
            }
            break;
        case 'ce':
            if (!preg_match('/^\d{12}$/', $numero_documento)) {
                throw new Exception("Carnet de Extranjería debe tener 12 dígitos");
            }
            break;
        default:
            throw new Exception("Tipo de documento no válido");
    }

    // Validar teléfono
    if (!$telefono) {
        throw new Exception("Teléfono es obligatorio");
    }
    if (!$es_extranjero && !preg_match('/^\d{9}$/', $telefono)) {
        throw new Exception("Teléfono debe tener 9 dígitos numéricos");
    }

    // Verificar si ya existe
    $stmt = $pdo->prepare("SELECT id FROM clientes WHERE tipo_documento = ? AND numero_documento = ?");
    $stmt->execute([$tipo_documento, $numero_documento]);
    $cliente = $stmt->fetch();

    if ($cliente) {
        throw new Exception("Cliente ya registrado con este documento");
    }

    // Insertar nuevo cliente
    $stmt = $pdo->prepare("INSERT INTO clientes (tipo_documento, numero_documento, nombre, telefono, email, direccion) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$tipo_documento, $numero_documento, $nombre, $telefono, $email, $direccion]);

    header("Location: index.php?mensaje=Cliente registrado correctamente");
    exit;

} catch (Exception $e) {
    $_SESSION['error'] = $e->getMessage();
    header("Location: agregar.php");
    exit;
}
?>
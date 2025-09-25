<?php
session_start();
include '../includes/conexion.php';

try {
    // Solo admin, cajera o vendedor pueden agregar clientes
    $roles_permitidos = ['admin', 'cajera', 'vendedor'];
    if (!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], $roles_permitidos)) {
        header("Location: ../login.php");
        exit;
    }

    $dni = $_POST['dni'] ?? null;
    $nombre = $_POST['nombre'] ?? null;
    $telefono = $_POST['telefono'] ?? null;
    $email = $_POST['email'] ?? null;
    $direccion = $_POST['direccion'] ?? null;

    // Validaciones
    if (!$dni || !preg_match('/^\d{8}$/', $dni)) {
        throw new Exception("DNI debe tener 8 dígitos numéricos");
    }
    if (!$nombre) {
        throw new Exception("Nombre del cliente es obligatorio");
    }

    // Verificar si ya existe
    $stmt = $pdo->prepare("SELECT id FROM clientes WHERE dni = ?");
    $stmt->execute([$dni]);
    $cliente = $stmt->fetch();

    if ($cliente) {
        throw new Exception("Cliente ya registrado con este DNI");
    }

    // Insertar nuevo cliente
    $stmt = $pdo->prepare("INSERT INTO clientes (dni, nombre, telefono, email, direccion) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$dni, $nombre, $telefono, $email, $direccion]);

    header("Location: index.php?mensaje=Cliente registrado correctamente");
    exit;

} catch (Exception $e) {
    error_log("Error en guardar.php: " . $e->getMessage());
    header("Location: agregar.php?mensaje=" . urlencode($e->getMessage()));
    exit;
}
?>
<?php
session_start();
include '../includes/conexion.php';

try {
    $pdo->beginTransaction();

    // Datos del cliente
    $cliente_dni = $_POST['cliente_dni'] ?? null;
    $cliente_nombre = $_POST['cliente_nombre'] ?? null;
    $telefono_cliente = $_POST['telefono_cliente'] ?? null;

    if (!$cliente_dni || !$cliente_nombre || !preg_match('/^\d{8}$/', $cliente_dni)) {
        throw new Exception("Datos del cliente inválidos");
    }

    $cliente_id = null;
    if ($cliente_dni) {
        $stmt = $pdo->prepare("SELECT id FROM clientes WHERE dni = ?");
        $stmt->execute([$cliente_dni]);
        $cliente = $stmt->fetch();

        if ($cliente) {
            $cliente_id = $cliente['id'];
        } else {
            $stmt = $pdo->prepare("INSERT INTO clientes (nombre, dni, telefono) VALUES (?, ?, ?)");
            $stmt->execute([$cliente_nombre, $cliente_dni, $telefono_cliente]);
            $cliente_id = $pdo->lastInsertId();
        }
    }

    // Datos de venta
    $total = $_POST['total'] ?? 0;

    // Recibir arrays
    $productos_ids = $_POST['producto_id'] ?? [];
    $cantidades = $_POST['cantidad'] ?? [];
    $precios = $_POST['precio_unitario'] ?? [];

    // Validar productos
    if (!is_array($productos_ids) || empty($productos_ids)) {
        throw new Exception("No se agregaron productos");
    }

    // Limpiar datos
    $productos_validos = [];
    foreach ($productos_ids as $index => $id) {
        $id = filter_var($id, FILTER_VALIDATE_INT);
        $cantidad = filter_var($cantidades[$index] ?? null, FILTER_VALIDATE_INT);
        $precio = filter_var($precios[$index] ?? null, FILTER_VALIDATE_FLOAT);

        if ($id && $cantidad && $cantidad > 0 && $precio && $precio > 0) {
            $productos_validos[] = [
                'id' => $id,
                'cantidad' => $cantidad,
                'precio' => $precio
            ];
        }
    }

    if (empty($productos_validos)) {
        throw new Exception("No se recibieron productos válidos con cantidad y precio");
    }

    // Insertar venta
    $stmt = $pdo->prepare("INSERT INTO ventas 
        (cliente_id, cliente_nombre, cliente_dni, usuario_id, total) 
        VALUES (?, ?, ?, ?, ?)");
    
    $stmt->execute([
        $cliente_id,
        $cliente_nombre,
        $cliente_dni,
        $_SESSION['usuario_id'],
        $total
    ]);

    // ✅ Obtener el ID generado
    $venta_id = $pdo->lastInsertId();

    if (!$venta_id) {
        throw new Exception("No se pudo obtener el ID de la venta");
    }

    // Insertar detalles y reducir stock
    foreach ($productos_validos as $prod) {
        $stmt = $pdo->prepare("INSERT INTO detalle_venta (venta_id, producto_id, cantidad, precio_unitario) VALUES (?, ?, ?, ?)");
        $stmt->execute([$venta_id, $prod['id'], $prod['cantidad'], $prod['precio']]);

        // Reducir stock
        $pdo->prepare("UPDATE productos SET stock = stock - ? WHERE id = ?")->execute([$prod['cantidad'], $prod['id']]);
    }

    $pdo->commit();

    header("Location: index.php?mensaje=Venta registrada correctamente");
    exit;

} catch (Exception $e) {
    $pdo->rollback();
    error_log("Error en guardar.php: " . $e->getMessage());
    header("Location: registrar.php?mensaje=" . urlencode($e->getMessage()));
    exit;
}
?>
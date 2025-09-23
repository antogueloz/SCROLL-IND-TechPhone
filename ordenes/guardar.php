<?php
session_start();
include '../includes/conexion.php';

try {
    $pdo->beginTransaction();

    // Datos del cliente
    $cliente_nombre = $_POST['cliente_nombre'] ?? null;
    $cliente_dni = $_POST['cliente_dni'] ?? null;
    $cliente_telefono = $_POST['cliente_telefono'] ?? null; // Valor del formulario

    if (!$cliente_nombre || !$cliente_dni || !preg_match('/^\d{8}$/', $cliente_dni)) {
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
            // Registrar cliente automáticamente si no existe
            $stmt = $pdo->prepare("INSERT INTO clientes (nombre, dni, telefono) VALUES (?, ?, ?)");
            $stmt->execute([$cliente_nombre, $cliente_dni, $cliente_telefono]);
            $cliente_id = $pdo->lastInsertId();
        }
    }

    // Datos principales
    $modelo_telefono = $_POST['modelo_telefono'] ?? null;
    $imei = $_POST['imei'] ?? null;
    $problema = $_POST['problema'] ?? null;
    $observaciones = $_POST['observaciones'] ?? null;
    $tecnico = $_POST['tecnico'] ?? null;
    $tipo_pago = $_POST['tipo_pago'] ?? 'efectivo';
    $total = $_POST['total'] ?? 0;
    $estado = $_POST['estado'] ?? 'recibido';
    $fecha_entrega = $_POST['fecha_entrega'] ?? null;

    if (!$modelo_telefono || !$problema || !$observaciones) {
        throw new Exception("Campos obligatorios faltantes");
    }

    // Insertar orden (¡importante! usa 'telefono_cliente', no 'cliente_telefono')
    $stmt = $pdo->prepare("INSERT INTO ordenes_servicio 
        (cliente_id, cliente_nombre, cliente_dni, telefono_cliente, modelo_telefono, imei, problema, observaciones, tecnico, tipo_pago, total, estado, fecha_entrega, usuario_id) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    $stmt->execute([
        $cliente_id,
        $cliente_nombre,
        $cliente_dni,
        $cliente_telefono, // ← Este valor se guarda en `telefono_cliente`
        $modelo_telefono,
        $imei,
        $problema,
        $observaciones,
        $tecnico,
        $tipo_pago,
        $total,
        $estado,
        $fecha_entrega,
        $_SESSION['usuario_id']
    ]);

    $orden_id = $pdo->lastInsertId();

    // Guardar múltiples repuestos (opcional)
    $repuestos_json = $_POST['repuestos'] ?? '[]';
    $repuestos = json_decode($repuestos_json, true);

    if (is_array($repuestos)) {
        foreach ($repuestos as $r) {
            $producto_id = $r['id'];
            $cantidad = $r['cantidad'];
            $precio = $r['precio'];

            // Insertar detalle
            $stmt = $pdo->prepare("INSERT INTO detalle_orden_repuestos (orden_id, producto_id, cantidad, precio_unitario) VALUES (?, ?, ?, ?)");
            $stmt->execute([$orden_id, $producto_id, $cantidad, $precio]);

            // Reducir stock
            $pdo->prepare("UPDATE productos SET stock = stock - ? WHERE id = ?")->execute([$cantidad, $producto_id]);
        }
    }

    $pdo->commit();

    header("Location: index.php?mensaje=Orden registrada correctamente");
    exit;

} catch (Exception $e) {
    $pdo->rollback();
    error_log("Error en guardar.php: " . $e->getMessage());
    header("Location: registrar.php?mensaje=" . urlencode($e->getMessage()));
    exit;
}
?>
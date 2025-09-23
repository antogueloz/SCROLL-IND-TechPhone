<?php
session_start();
include '../includes/conexion.php';

// Verificar que se envió el ID
if (!isset($_GET['id'])) {
    header("Location: index.php?mensaje=Orden no especificada");
    exit;
}

$id = $_GET['id'];

// Consultar la orden
$stmt = $pdo->prepare("SELECT o.*, c.nombre AS cliente_nombre 
                       FROM ordenes_servicio o
                       LEFT JOIN clientes c ON o.cliente_id = c.id
                       WHERE o.id = ?");
$stmt->execute([$id]);
$orden = $stmt->fetch();

if (!$orden) {
    header("Location: index.php?mensaje=Orden no encontrada");
    exit;
}

// Si el formulario se envía
if ($_POST) {
    $estado = $_POST['estado'];
    $tecnico = $_POST['tecnico'];
    $fecha_entrega = $_POST['fecha_entrega'] ?: null;

    try {
        $stmt = $pdo->prepare("UPDATE ordenes_servicio 
                               SET estado = ?, tecnico = ?, fecha_entrega = ?, actualizado_en = CURRENT_TIMESTAMP
                               WHERE id = ?");
        $stmt->execute([$estado, $tecnico, $fecha_entrega, $id]);

        header("Location: ver.php?id=$id&mensaje=Orden actualizada correctamente");
        exit;

    } catch (Exception $e) {
        $error = "Error al actualizar: " . $e->getMessage();
    }
}
?>

<?php include '../includes/header.php'; ?>

<!-- Breadcrumb -->
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="index.php">Órdenes</a></li>
        <li class="breadcrumb-item"><a href="ver.php?id=<?= $orden['id'] ?>">Orden #<?= $orden['id'] ?></a></li>
        <li class="breadcrumb-item active" aria-current="page">Editar Estado</li>
    </ol>
</nav>

<h2> 🔧 Editar Estado de la Orden </h2>

<a href="ver.php?id=<?= $orden['id'] ?>" class="btn btn-secondary mb-3">Volver a Detalles</a>

<?php if (isset($error)): ?>
    <div class="alert alert-danger"><?= $error ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <h5 class="card-title">Cliente y Dispositivo</h5>
        <p><strong>Cliente:</strong> <?= $orden['cliente_nombre'] ?></p>
        <p><strong>Modelo:</strong> <?= $orden['modelo_telefono'] ?></p>
        <p><strong>Problema:</strong> <?= substr($orden['problema'], 0, 50) ?>...</p>
    </div>
</div>

<form method="POST" class="mt-4">
    <div class="row">
        <div class="col-md-6 mb-3">
            <label>Estado del Servicio *</label>
            <select name="estado" class="form-control" required>
                <option value="recibido" <?= $orden['estado'] == 'recibido' ? 'selected' : '' ?>>Recibido</option>
                <option value="en_reparacion" <?= $orden['estado'] == 'en_reparacion' ? 'selected' : '' ?>>En Reparación</option>
                <option value="listo" <?= $orden['estado'] == 'listo' ? 'selected' : '' ?>>Listo para Entrega</option>
                <option value="entregado" <?= $orden['estado'] == 'entregado' ? 'selected' : '' ?>>Entregado</option>
            </select>
        </div>
        <div class="col-md-6 mb-3">
            <label>Técnico Asignado</label>
            <input type="text" name="tecnico" class="form-control" 
                   value="<?= htmlspecialchars($orden['tecnico']) ?>" 
                   placeholder="Nombre del técnico">
        </div>
    </div>

    <div class="mb-3">
        <label>Fecha de Entrega (opcional)</label>
        <input type="date" name="fecha_entrega" class="form-control"
               value="<?= $orden['fecha_entrega'] ?>">
    </div>

    <div class="mt-4">
        <a href="ver.php?id=<?= $orden['id'] ?>" class="btn btn-secondary">Cancelar</a>
        <button type="submit" class="btn btn-success">Actualizar Estado</button>
    </div>
</form>

<?php include '../includes/footer.php'; ?>
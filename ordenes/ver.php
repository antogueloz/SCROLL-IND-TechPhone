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
$stmt = $pdo->prepare("SELECT o.*, c.nombre AS cliente_nombre, p.nombre AS repuesto_nombre 
                       FROM ordenes_servicio o
                       LEFT JOIN clientes c ON o.cliente_id = c.id
                       LEFT JOIN productos p ON o.repuesto_usado = p.id
                       WHERE o.id = ?");
$stmt->execute([$id]);
$orden = $stmt->fetch();

if (!$orden) {
    header("Location: index.php?mensaje=Orden no encontrada");
    exit;
}
?>

<?php include '../includes/header.php'; ?>

<!-- Breadcrumb -->
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="index.php">Órdenes</a></li>
        <li class="breadcrumb-item active" aria-current="page">Orden #<?= $orden['id'] ?></li>
    </ol>
</nav>

<h2> 📄 Detalles de la Orden de Servicio </h2>

<a href="index.php" class="btn btn-secondary mb-3">Volver a Órdenes</a>

<div class="card">
    <div class="card-body">
        <h5 class="card-title">Información del Cliente</h5>
        <p><strong>Cliente:</strong> <?= $orden['cliente_nombre'] ?></p>
        <p><strong>DNI:</strong> <?= $orden['cliente_dni'] ?></p>
        <p><strong>Teléfono:</strong> <?= $orden['telefono_cliente'] ?></p>
    </div>
</div>

<div class="card mt-3">
    <div class="card-body">
        <h5 class="card-title">Dispositivo</h5>
        <p><strong>Modelo:</strong> <?= $orden['modelo_telefono'] ?></p>
        <p><strong>IMEI:</strong> <?= $orden['imei'] ?: 'No proporcionado' ?></p>
    </div>
</div>

<div class="card mt-3">
    <div class="card-body">
        <h5 class="card-title">Servicio Técnico</h5>
        <p><strong>Problema reportado:</strong><br><?= nl2br(htmlspecialchars($orden['problema'])) ?></p>
        <p><strong>Observaciones (clave, patrón):</strong><br><?= nl2br(htmlspecialchars($orden['observaciones'])) ?></p>
        <p><strong>Repuesto usado:</strong> <?= $orden['repuesto_nombre'] ?: 'Ninguno' ?></p>
        <p><strong>Costo del repuesto:</strong> S/ <?= number_format($orden['costo_repuesto'], 2) ?></p>
    </div>
</div>

<div class="card mt-3">
    <div class="card-body">
        <h5 class="card-title">Estado y Pago</h5>
        <p><strong>Total:</strong> S/ <?= number_format($orden['total'], 2) ?></p>
        <p><strong>Tipo de pago:</strong> <?= ucfirst($orden['tipo_pago']) ?></p>
        <p><strong>Estado:</strong>
            <span class="badge 
                <?= $orden['estado'] == 'recibido' ? 'bg-secondary' :
                   ($orden['estado'] == 'en_reparacion' ? 'bg-warning text-dark' :
                   ($orden['estado'] == 'listo' ? 'bg-info' : 'bg-success')) ?>">
                <?= ucfirst(str_replace('_', ' ', $orden['estado'])) ?>
            </span>
        </p>
        <p><strong>Técnico asignado:</strong> <?= $orden['tecnico'] ?: 'No asignado' ?></p>
        <p><strong>Fecha de entrega:</strong> <?= $orden['fecha_entrega'] ? date('d/m/Y', strtotime($orden['fecha_entrega'])) : 'No definida' ?></p>
        <p><strong>Registrado el:</strong> <?= date('d/m/Y H:i', strtotime($orden['creado_en'])) ?></p>
    </div>
</div>

<div class="mt-4">
    <a href="index.php" class="btn btn-secondary">Volver</a>
    <a href="editar.php?id=<?= $orden['id'] ?>" class="btn btn-warning">Editar Estado</a>
</div>

<?php include '../includes/footer.php'; ?>
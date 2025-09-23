<?php
session_start();
include '../includes/conexion.php';
include '../includes/funciones.php';

// Verificar que el usuario esté logueado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../index.php");
    exit;
}

// Obtener órdenes según el rol
if ($_SESSION['rol'] == 'tecnico') {
    // El técnico solo ve órdenes asignadas a su nombre o creadas por él
    $stmt = $pdo->prepare("SELECT 
                                o.*, 
                                COALESCE(c.nombre, o.cliente_nombre) AS cliente_nombre,
                                p.nombre AS repuesto_nombre 
                           FROM ordenes_servicio o
                           LEFT JOIN clientes c ON o.cliente_id = c.id
                           LEFT JOIN productos p ON o.repuesto_usado = p.id
                           WHERE o.tecnico = ? OR o.usuario_id = ?
                           ORDER BY o.creado_en DESC");
    $stmt->execute([$_SESSION['nombre'], $_SESSION['usuario_id']]);
} else {
    // Admin y cajera ven todas las órdenes
    $stmt = $pdo->query("SELECT 
                                o.*, 
                                COALESCE(c.nombre, o.cliente_nombre) AS cliente_nombre,
                                p.nombre AS repuesto_nombre 
                         FROM ordenes_servicio o
                         LEFT JOIN clientes c ON o.cliente_id = c.id
                         LEFT JOIN productos p ON o.repuesto_usado = p.id
                         ORDER BY o.creado_en DESC");
}

$ordenes = $stmt->fetchAll();
?>

<?php include '../includes/header.php'; ?>

<!-- Breadcrumb -->
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">Órdenes de Servicio</li>
    </ol>
</nav>

<h2> 🛠️ Órdenes de Servicio </h2>

<!-- Botón para nueva orden (solo admin y cajera) -->
<?php if ($_SESSION['rol'] != 'tecnico'): ?>
    <a href="registrar.php" class="btn btn-primary mb-3">+ Nueva Orden</a>
<?php endif; ?>

<!-- Mensaje de éxito -->
<?php if (isset($_GET['mensaje'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= $_GET['mensaje'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<a href="../dashboard.php" class="btn btn-secondary mb-3">
    ← Volver al Dashboard
</a>
<!-- Barra de búsqueda -->
<div class="mb-3">
    <input type="text" id="buscarOrden" class="form-control" 
           placeholder="Buscar por nombre, DNI, teléfono o modelo...">
</div>
<!-- Tabla de órdenes -->
<table class="table table-bordered table-striped">
    <thead class="table-dark">
        <tr>
            <th>ID</th>
            <th>Cliente</th>
            <th>Teléfono</th>
            <th>Modelo</th>
            <th>Problema</th>
            <th>Total</th>
            <th>Estado</th>
            <th>Fecha</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php if (count($ordenes) > 0): ?>
            <?php foreach ($ordenes as $o): ?>
            <tr class="orden-row" 
                data-buscar="<?= strtolower($o['cliente_nombre'] . ' ' . $o['cliente_dni'] . ' ' . $o['telefono_cliente'] . ' ' . $o['modelo_telefono']) ?>">
                <td><?= $o['id'] ?></td>
                <td><?= htmlspecialchars($o['cliente_nombre']) ?></td>
                <td><?= $o['telefono_cliente'] ?: 'No registrado' ?></td>
                <td><?= htmlspecialchars($o['modelo_telefono']) ?></td>
                <td><?= substr(htmlspecialchars($o['problema']), 0, 20) ?>...</td>
                <td>S/ <?= number_format($o['total'], 2) ?></td>
                <td>
                    <span class="badge 
                        <?= $o['estado'] == 'recibido' ? 'bg-secondary' :
                        ($o['estado'] == 'en_reparacion' ? 'bg-warning text-dark' :
                        ($o['estado'] == 'listo' ? 'bg-info' : 'bg-success')) ?>">
                        <?= ucfirst(str_replace('_', ' ', $o['estado'])) ?>
                    </span>
                </td>
                <td><?= date('d/m/Y', strtotime($o['creado_en'])) ?></td>
                <td>
                    <a href="ver.php?id=<?= $o['id'] ?>" class="btn btn-sm btn-info">Ver</a>
                    <?php if ($_SESSION['rol'] != 'tecnico'): ?>
                        <a href="editar.php?id=<?= $o['id'] ?>" class="btn btn-sm btn-warning">Editar</a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="9" class="text-center text-muted">No hay órdenes disponibles.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<?php include '../includes/footer.php'; ?>
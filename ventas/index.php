<?php
session_start();
include '../includes/conexion.php';
include '../includes/funciones.php';

// Verifica si está logueado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../index.php");
    exit;
}
// Solo cajera y admin pueden ver ventas
if (!in_array($_SESSION['rol'], ['cajera', 'admin'])) {
    header("Location: ../dashboard.php?error=Acceso denegado");
    exit;
}
?>

<?php include '../includes/header.php'; ?>

<!-- Breadcrumb -->
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">Ventas</li>
    </ol>
</nav>

<h2> 🛒 Ventas de Productos </h2>

<a href="../dashboard.php" class="btn btn-secondary mb-3">
    ← Volver al Dashboard
</a>

<a href="registrar.php" class="btn btn-primary mb-3">+ Nueva Venta</a>

<?php if (isset($_GET['mensaje'])): ?>
    <div class="alert alert-success"> <?= $_GET['mensaje'] ?> </div>
<?php endif; ?>

<table class="table table-bordered table-striped">
    <thead class="table-dark">
        <tr>
            <th>ID</th>
            <th>Cliente</th>
            <th>Total</th>
            <th>Fecha</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $stmt = $pdo->query("SELECT v.id, v.total, v.fecha, c.nombre AS cliente 
                             FROM ventas v 
                             LEFT JOIN clientes c ON v.cliente_id = c.id 
                             ORDER BY v.fecha DESC LIMIT 10");
        $ventas = $stmt->fetchAll();

        foreach ($ventas as $v): ?>
        <tr>
            <td><?= $v['id'] ?></td>
            <td><?= $v['cliente'] ?: 'Sin cliente' ?></td>
            <td>S/ <?= number_format($v['total'], 2) ?></td>
            <td><?= date('d/m/Y H:i', strtotime($v['fecha'])) ?></td>
            <td>
                <a href="ver.php?id=<?= $v['id'] ?>" class="btn btn-sm btn-info">Ver</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php include '../includes/footer.php'; ?>
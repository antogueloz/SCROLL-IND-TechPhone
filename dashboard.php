<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}
include 'includes/conexion.php';
include 'includes/funciones.php';
include 'includes/header.php';
?>

<!-- Mensaje de bienvenida -->
<div class="alert alert-light border-0 shadow-sm mb-4">
    <i class="bi bi-check-circle-fill text-success me-2"></i>
    <strong>Bienvenido, <?= $_SESSION['nombre'] ?>,</strong> 
    estamos listos para iniciar el día.
</div>

<div class="row">
    <!-- Productos (todos los roles) -->
    <?php if ($_SESSION['rol'] != 'tecnico'): ?>
    <div class="col-md-3">
        <a href="productos/" class="card text-center p-3 bg-success text-white">
            <h5>Productos</h5>
            <small>Todos tus Productos</small>
        </a>
    </div>
    <?php endif; ?>
    
    <!-- Ventas (cajera y admin) -->
    <?php if ($_SESSION['rol'] == 'cajera' || $_SESSION['rol'] == 'admin'): ?>
    <div class="col-md-3">
        <a href="ventas/" class="card text-center p-3 bg-info text-white">
            <h5>Ventas</h5>
            <small>Procesa Ventas</small>
        </a>
    </div>
    <?php endif; ?>
    
    <!-- Clientes (todos los roles) -->
    <div class="col-md-3">
        <a href="clientes/" class="card text-center p-3 bg-warning text-dark">
            <h5>Clientes</h5>
            <small>Gestiona Clientes</small>
        </a>
    </div>
    
    <!-- Reportes (solo admin) -->
    <?php if ($_SESSION['rol'] == 'admin'): ?>
    <div class="col-md-3">
        <a href="reportes/" class="card text-center p-3 bg-secondary text-white">
            <h5>Reportes</h5>
            <small>Revisa y Supervisa</small>
        </a>
    </div>
    <?php endif; ?>
</div>

<!-- Órdenes de Servicio (todos los roles) -->
<div class="row mt-4">
    <div class="col-md-3">
        <a href="ordenes/" class="card text-center p-3 bg-primary text-white">
            <h5>🛠️ Órdenes de Servicio</h5>
            <small>Gestiona reparaciones</small>
        </a>
    </div>
</div>

<!-- Usuarios (solo admin) -->
<?php if ($_SESSION['rol'] == 'admin'): ?>
<div class="row mt-4">
    <div class="col-md-3">
        <a href="usuarios/" class="card text-center p-3 bg-danger text-white">
            <h5>🔐 Usuarios</h5>
            <small>Gestiona accesos</small>
        </a>
    </div>
</div>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
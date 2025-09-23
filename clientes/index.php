<?php
session_start();
include '../includes/conexion.php';

$stmt = $pdo->query("SELECT * FROM clientes ORDER BY creado_en DESC");
$clientes = $stmt->fetchAll();
?>

<?php include '../includes/header.php'; ?>

<!-- Breadcrumb -->
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">Clientes</li>
    </ol>
</nav>

<h2> Gestión de Clientes </h2>
<a href="../dashboard.php" class="btn btn-secondary mb-3">
    ← Volver al Dashboard
</a>

<a href="agregar.php" class="btn btn-success mb-3">+ Agregar Cliente</a>

<?php if (isset($_GET['mensaje'])): ?>
    <div class="alert alert-success"> <?= $_GET['mensaje'] ?> </div>
<?php endif; ?>

<table class="table table-bordered table-striped">
    <thead class="table-dark">
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Teléfono</th>
            <th>Email</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($clientes as $c): ?>
        <tr>
            <td><?= $c['id'] ?></td>
            <td><?= htmlspecialchars($c['nombre']) ?></td>
            <td><?= $c['telefono'] ?></td>
            <td><?= $c['email'] ?></td>
            <td>
                <a href="editar.php?id=<?= $c['id'] ?>" class="btn btn-sm btn-warning">Editar</a>
                <a href="eliminar.php?id=<?= $c['id'] ?>" class="btn btn-sm btn-danger"
                   onclick="return confirm('¿Eliminar a <?= $c['nombre'] ?>?')">Eliminar</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php include '../includes/footer.php'; ?>
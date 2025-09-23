<?php
session_start();
include '../includes/conexion.php';


$stmt = $pdo->query("SELECT * FROM productos ORDER BY nombre");
$productos = $stmt->fetchAll();
?>

<?php include '../includes/header.php'; ?>

<h2> 🛠️ Gestión de Productos </h2>

<a href="../dashboard.php" class="btn btn-secondary mb-3">← Volver al Dashboard</a>
<a href="agregar.php" class="btn btn-success mb-3">+ Agregar Producto</a>

<!-- Mostrar mensaje -->
<?php if (isset($_GET['mensaje'])): ?>
    <div class="alert alert-<?php echo $_GET['tipo'] ?? 'info'; ?> alert-dismissible fade show" role="alert">
        <?= $_GET['mensaje'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<table class="table table-bordered table-striped">
    <thead class="table-dark">
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Precio</th>
            <th>Stock</th>
            <th>Tipo</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($productos as $p): ?>
        <tr>
            <td><?= $p['id'] ?></td>
            <td><?= htmlspecialchars($p['nombre']) ?></td>
            <td>S/ <?= number_format($p['precio'], 2) ?></td>
            <td><?= $p['stock'] ?: 0 ?></td> <!-- Mostrar stock -->
            <td>
                <span class="badge 
                    <?= $p['tipo'] == 'repuesto' ? 'bg-primary' : 'bg-success' ?>">
                    <?= ucfirst($p['tipo']) ?>
                </span>
            </td>
            <td>
                <a href="editar.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-warning">Editar</a>
                <a href="eliminar.php?id=<?= $p['id'] ?>" 
                class="btn btn-sm btn-danger"
                onclick="return confirm('¿Eliminar <?= addslashes($p['nombre']) ?>?')">
                Eliminar
                </a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php include '../includes/footer.php'; ?>
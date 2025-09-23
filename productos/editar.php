<?php
session_start();
include '../includes/conexion.php';

// Verificar rol (solo admin puede editar)
if ($_SESSION['rol'] != 'admin') {
    header("Location: index.php?mensaje=Acceso denegado");
    exit;
}

// Obtener ID del producto
$id = $_GET['id'] ?? null;

if (!$id) {
    header("Location: index.php?mensaje=Producto no especificado");
    exit;
}

// Obtener producto
$stmt = $pdo->prepare("SELECT * FROM productos WHERE id = ?");
$stmt->execute([$id]);
$producto = $stmt->fetch();

if (!$producto) {
    header("Location: index.php?mensaje=Producto no encontrado");
    exit;
}

// Procesar formulario
if ($_POST) {
    $nombre = $_POST['nombre'];
    $precio = $_POST['precio'];
    $stock = $_POST['stock'];
    $tipo = $_POST['tipo'];

    try {
        $stmt = $pdo->prepare("UPDATE productos SET nombre = ?, precio = ?, stock = ?, tipo = ? WHERE id = ?");
        $stmt->execute([$nombre, $precio, $stock, $tipo, $id]);

        header("Location: index.php?mensaje=Producto actualizado correctamente");
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
        <li class="breadcrumb-item"><a href="index.php">Productos</a></li>
        <li class="breadcrumb-item active" aria-current="page">Editar Producto</li>
    </ol>
</nav>

<h2> ✏️ Editar Producto </h2>

<a href="index.php" class="btn btn-secondary mb-3">Volver</a>

<?php if (isset($error)): ?>
    <div class="alert alert-danger"><?= $error ?></div>
<?php endif; ?>

<form method="POST">
    <div class="mb-3">
        <label>Nombre *</label>
        <input type="text" name="nombre" class="form-control" 
               value="<?= htmlspecialchars($producto['nombre']) ?>" required>
    </div>
    <div class="mb-3">
        <label>Precio (S/) *</label>
        <input type="number" step="0.01" name="precio" class="form-control" 
               value="<?= $producto['precio'] ?>" required>
    </div>
    <div class="mb-3">
        <label>Stock *</label>
        <input type="number" name="stock" class="form-control" 
               value="<?= $producto['stock'] ?>" required min="0">
    </div>
    <div class="mb-3">
        <label>Tipo de Producto *</label>
        <select name="tipo" class="form-control" required>
            <option value="venta" <?= $producto['tipo'] == 'venta' ? 'selected' : '' ?>>Venta al público</option>
            <option value="repuesto" <?= $producto['tipo'] == 'repuesto' ? 'selected' : '' ?>>Repuesto para reparación</option>
        </select>
    </div>
    <div class="mt-4">
        <a href="index.php" class="btn btn-secondary">Cancelar</a>
        <button type="submit" class="btn btn-primary">Actualizar Producto</button>
    </div>
</form>

<?php include '../includes/footer.php'; ?>
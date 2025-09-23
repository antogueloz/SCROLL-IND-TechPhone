<?php
session_start();
include '../includes/conexion.php';
include '../includes/funciones.php';

// Solo admin puede agregar productos
redirigirSiNoAutorizado(['admin']);

if ($_POST) {
    $nombre = $_POST['nombre'];
    $precio = $_POST['precio'];
    $stock = $_POST['stock'];
    $tipo = $_POST['tipo'];

    // Validar que el nombre no esté vacío
    if (empty($nombre)) {
        $error = "El nombre del producto es obligatorio.";
    } 
    // Validar precio
    elseif (!is_numeric($precio) || $precio < 0) {
        $error = "El precio debe ser un número válido.";
    }
    // Validar stock
    elseif (!is_numeric($stock) || $stock < 0) {
        $error = "El stock debe ser un número entero no negativo.";
    }
    else {
        try {
            // Verificar si ya existe un producto con el mismo nombre
            $stmt = $pdo->prepare("SELECT id FROM productos WHERE nombre = ?");
            $stmt->execute([$nombre]);
            if ($stmt->fetch()) {
                $error = "Ya existe un producto con ese nombre.";
            } else {
                // Insertar nuevo producto
                $stmt = $pdo->prepare("INSERT INTO productos (nombre, precio, stock, tipo) VALUES (?, ?, ?, ?)");
                $stmt->execute([$nombre, $precio, $stock, $tipo]);

                // Redirigir con mensaje de éxito
                header("Location: index.php?mensaje=Producto agregado correctamente");
                exit;
            }
        } catch (Exception $e) {
            $error = "Error al guardar: " . $e->getMessage();
        }
    }
}
?>

<?php include '../includes/header.php'; ?>

<!-- Breadcrumb -->
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="index.php">Productos</a></li>
        <li class="breadcrumb-item active" aria-current="page">Agregar Producto</li>
    </ol>
</nav>

<h2> ✅ Agregar Nuevo Producto </h2>

<a href="index.php" class="btn btn-secondary mb-3">← Volver a Productos</a>

<?php if (isset($error)): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= $error ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<form method="POST">
    <div class="mb-3">
        <label>Nombre del Producto *</label>
        <input type="text" name="nombre" class="form-control" 
               placeholder="Ej: Batería iPhone 14" required>
    </div>

    <div class="mb-3">
        <label>Precio (S/) *</label>
        <input type="number" step="0.01" name="precio" class="form-control" 
               placeholder="100.00" required min="0" step="0.01">
    </div>

    <div class="mb-3">
        <label>Stock *</label>
        <input type="number" name="stock" class="form-control" 
               placeholder="10" required min="0" value="0">
    </div>

    <div class="mb-3">
        <label>Tipo de Producto *</label>
        <select name="tipo" class="form-control" required>
            <option value="venta">Venta al público</option>
            <option value="repuesto">Repuesto para reparación</option>
        </select>
    </div>

    <div class="mt-4">
        <a href="index.php" class="btn btn-secondary">Cancelar</a>
        <button type="submit" class="btn btn-success">Guardar Producto</button>
    </div>
</form>

<?php include '../includes/footer.php'; ?>
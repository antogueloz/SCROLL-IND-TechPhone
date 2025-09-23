<?php
session_start();
include '../includes/conexion.php';

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM clientes WHERE id = ?");
$stmt->execute([$id]);
$cliente = $stmt->fetch();

if (!$cliente) {
    header("Location: index.php?mensaje=Cliente no encontrado");
    exit;
}

if ($_POST) {
    $nombre = $_POST['nombre'];
    $telefono = $_POST['telefono'];
    $email = $_POST['email'];

    $stmt = $pdo->prepare("UPDATE clientes SET nombre = ?, telefono = ?, email = ? WHERE id = ?");
    $stmt->execute([$nombre, $telefono, $email, $id]);

    header("Location: index.php?mensaje=Cliente actualizado");
    exit;
}
?>

<?php include '../includes/header.php'; ?>

<!-- Breadcrumb -->
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="index.php">Clientes</a></li>
        <li class="breadcrumb-item active" aria-current="page">Editar Cliente</li>
    </ol>
</nav>

<h2> Editar Cliente </h2>

<a href="index.php" class="btn btn-secondary mb-3">Volver a Clientes</a>

<form method="POST">
    <div class="mb-3">
        <label>Nombre</label>
        <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($cliente['nombre']) ?>" required>
    </div>
    <div class="mb-3">
        <label>Teléfono</label>
        <input type="text" name="telefono" class="form-control" value="<?= $cliente['telefono'] ?>">
    </div>
    <div class="mb-3">
        <label>Email</label>
        <input type="email" name="email" class="form-control" value="<?= $cliente['email'] ?>">
    </div>
    <button type="submit" class="btn btn-primary">Actualizar</button>
</form>

<?php include '../includes/footer.php'; ?>

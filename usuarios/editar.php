<?php
session_start();
include '../includes/conexion.php';
include '../includes/funciones.php';

redirigirSiNoAutorizado(['admin']);

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT id, nombre, email, rol FROM usuarios WHERE id = ?");
$stmt->execute([$id]);
$usuario = $stmt->fetch();

if (!$usuario) {
    header("Location: index.php?mensaje=Usuario no encontrado");
    exit;
}

if ($_POST) {
    $rol = $_POST['rol'];
    $password = $_POST['password'];

    // Actualizar rol
    $stmt = $pdo->prepare("UPDATE usuarios SET rol = ? WHERE id = ?");
    $stmt->execute([$rol, $id]);

    // Opcional: cambiar contraseña
    if (!empty($password)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE usuarios SET password = ? WHERE id = ?");
        $stmt->execute([$hash, $id]);
    }

    header("Location: index.php?mensaje=Usuario actualizado");
    exit;
}
?>

<?php include '../includes/header.php'; ?>

<!-- Breadcrumb -->
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="index.php">Usuarios</a></li>
        <li class="breadcrumb-item active" aria-current="page">Editar Usuario</li>
    </ol>
</nav>

<h2> ✏️ Editar Usuario: <?= $usuario['nombre'] ?> </h2>

<a href="index.php" class="btn btn-secondary mb-3">Volver</a>

<form method="POST">
    <div class="mb-3">
        <label>Nombre</label>
        <input type="text" class="form-control" value="<?= htmlspecialchars($usuario['nombre']) ?>" disabled>
    </div>
    <div class="mb-3">
        <label>Email</label>
        <input type="email" class="form-control" value="<?= $usuario['email'] ?>" disabled>
    </div>
    <div class="mb-3">
        <label>Rol *</label>
        <select name="rol" class="form-control" required>
            <option value="vendedor" <?= $usuario['rol'] == 'vendedor' ? 'selected' : '' ?>>Vendedor</option>
            <option value="cajera" <?= $usuario['rol'] == 'cajera' ? 'selected' : '' ?>>Cajera</option>
            <option value="tecnico" <?= $usuario['rol'] == 'tecnico' ? 'selected' : '' ?>>Técnico</option>
            <option value="admin" <?= $usuario['rol'] == 'admin' ? 'selected' : '' ?>>Admin</option>
        </select>
    </div>
    <div class="mb-3">
        <label>Nueva Contraseña (opcional)</label>
        <input type="password" name="password" class="form-control" placeholder="Dejar vacío si no cambia">
    </div>
    <button type="submit" class="btn btn-primary">Actualizar</button>
</form>

<?php include '../includes/footer.php'; ?>
<?php
session_start();
include '../includes/conexion.php';
include '../includes/funciones.php';

// Solo admin puede agregar
redirigirSiNoAutorizado(['admin']);

if ($_POST) {
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $rol = $_POST['rol'];
    $password = $_POST['password'];

    // Validar que el email no exista
    $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        $error = "Ya existe un usuario con ese email.";
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, email, password, rol) VALUES (?, ?, ?, ?)");
        $stmt->execute([$nombre, $email, $hash, $rol]);

        header("Location: index.php?mensaje=Usuario registrado correctamente");
        exit;
    }
}
?>

<?php include '../includes/header.php'; ?>

<!-- Breadcrumb -->
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="index.php">Usuarios</a></li>
        <li class="breadcrumb-item active" aria-current="page">Agregar Usuario</li>
    </ol>
</nav>

<h2> 👤 Agregar Nuevo Usuario </h2>

<a href="index.php" class="btn btn-secondary mb-3">Volver</a>

<?php if (isset($error)): ?>
    <div class="alert alert-danger"><?= $error ?></div>
<?php endif; ?>

<form method="POST">
    <div class="mb-3">
        <label>Nombre *</label>
        <input type="text" name="nombre" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Email *</label>
        <input type="email" name="email" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Contraseña *</label>
        <input type="password" name="password" class="form-control" required minlength="6">
    </div>
    <div class="mb-3">
        <label>Rol *</label>
        <select name="rol" class="form-control" required>
            <option value="vendedor">Vendedor</option>
            <option value="cajera">Cajera</option>
            <option value="tecnico">Técnico</option>
            <option value="admin">Admin</option>
        </select>
    </div>
    <button type="submit" class="btn btn-primary">Registrar Usuario</button>
</form>

<?php include '../includes/footer.php'; ?>
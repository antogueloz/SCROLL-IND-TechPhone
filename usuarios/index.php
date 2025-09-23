<?php
session_start();
include '../includes/conexion.php';
include '../includes/funciones.php';

// Solo admin puede gestionar usuarios
if ($_SESSION['rol'] != 'admin') {
    header("Location: ../dashboard.php?error=Acceso denegado");
    exit;
}

// Solo admin puede ver esta página
redirigirSiNoAutorizado(['admin']);
?>

<?php include '../includes/header.php'; ?>

<!-- Breadcrumb -->
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">Gestión de Usuarios</li>
    </ol>
</nav>

<h2> 👥 Gestión de Usuarios </h2>
<a href="../dashboard.php" class="btn btn-secondary mb-3">
    ← Volver al Dashboard
</a>

<a href="agregar.php" class="btn btn-success mb-3">+ Agregar Usuario</a>

<?php if (isset($_GET['mensaje'])): ?>
    <div class="alert alert-success"> <?= $_GET['mensaje'] ?> </div>
<?php endif; ?>

<table class="table table-bordered table-striped">
    <thead class="table-dark">
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Email</th>
            <th>Rol</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $stmt = $pdo->query("SELECT id, nombre, email, rol FROM usuarios ORDER BY rol, nombre");
        $usuarios = $stmt->fetchAll();

        foreach ($usuarios as $u): ?>
        <tr>
            <td><?= $u['id'] ?></td>
            <td><?= htmlspecialchars($u['nombre']) ?></td>
            <td><?= $u['email'] ?></td>
            <td>
                <span class="badge 
                    <?= $u['rol'] == 'admin' ? 'bg-danger' :
                       ($u['rol'] == 'tecnico' ? 'bg-primary' :
                       ($u['rol'] == 'cajera' ? 'bg-info' : 'bg-secondary')) ?>">
                    <?= ucfirst($u['rol']) ?>
                </span>
            </td>
            <td>
                <?php if ($u['id'] != $_SESSION['usuario_id']): // No permitir editarse a sí mismo ?>
                    <a href="editar.php?id=<?= $u['id'] ?>" class="btn btn-sm btn-warning">Editar</a>
                    <a href="eliminar.php?id=<?= $u['id'] ?>" class="btn btn-sm btn-danger"
                       onclick="return confirm('¿Eliminar a <?= $u['nombre'] ?>?')">Eliminar</a>
                <?php else: ?>
                    <span class="text-muted">Tú</span>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php include '../includes/footer.php'; ?>
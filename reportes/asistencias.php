<?php
session_start();
include '../includes/conexion.php';
include '../includes/funciones.php';

redirigirSiNoAutorizado(['admin']);

$fecha = $_GET['fecha'] ?? date('Y-m-d');

$stmt = $pdo->prepare("SELECT a.*, u.nombre, u.rol 
                       FROM asistencia a
                       JOIN usuarios u ON a.usuario_id = u.id
                       WHERE a.fecha = ?
                       ORDER BY u.rol, u.nombre");
$stmt->execute([$fecha]);
$registros = $stmt->fetchAll();
?>

<?php include '../includes/header.php'; ?>

<h2> 📅 Asistencia - <?= date('d/m/Y', strtotime($fecha)) ?> </h2>

<form method="GET" class="mb-3">
    <input type="date" name="fecha" value="<?= $fecha ?>" onchange="this.form.submit()">
</form>

<table class="table table-bordered">
    <thead class="table-dark">
        <tr>
            <th>Usuario</th>
            <th>Rol</th>
            <th>Entrada</th>
            <th>Salida</th>
            <th>Estado</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($registros as $r): ?>
        <tr>
            <td><?= $r['nombre'] ?></td>
            <td><?= ucfirst($r['rol']) ?></td>
            <td><?= $r['entrada'] ?></td>
            <td><?= $r['salida'] ?: '-' ?></td>
            <td>
                <span class="badge 
                    <?= $r['entrada'] ? 'bg-success' : 'bg-danger' ?>">
                    <?= $r['entrada'] ? 'Presente' : 'Falta' ?>
                </span>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php include '../includes/footer.php'; ?>
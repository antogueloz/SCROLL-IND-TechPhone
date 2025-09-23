<?php
session_start();
include '../includes/conexion.php';
include '../includes/funciones.php';

// Solo admin puede ver reportes
if ($_SESSION['rol'] != 'admin') {
    header("Location: ../dashboard.php?error=Acceso denegado");
    exit;
}

// Solo admin puede ver reportes
redirigirSiNoAutorizado(['admin']);
?>

<?php include '../includes/header.php'; ?>

<!-- Breadcrumb -->
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">Reportes</li>
    </ol>
</nav>

<h2> 📊 Análisis de Ventas </h2>

<a href="../dashboard.php" class="btn btn-secondary mb-3">
    ← Volver al Dashboard
</a>

<div class="row mb-4">
    <div class="col-md-6">
        <label>Filtro por fecha</label>
        <input type="date" id="fechaDesde" class="form-control" value="<?= date('Y-m-d') ?>">
    </div>
    <div class="col-md-6">
        <label>Mes</label>
        <select id="mes" class="form-control">
            <option value="">Todos los meses</option>
            <?php for ($i = 1; $i <= 12; $i++): ?>
                <option value="<?= $i ?>"><?= strftime('%B', mktime(0, 0, 0, $i, 1)) ?></option>
            <?php endfor; ?>
        </select>
    </div>
</div>

<div class="row">
    <!-- Ventas del día -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <h5>Ventas del Día</h5>
                <p class="display-4">S/ <span id="ventasDia">0.00</span></p>
                <small class="text-muted">Hoy: <?= date('d/m/Y') ?></small>
            </div>
        </div>
    </div>

    <!-- Ventas del mes -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <h5>Ventas del Mes</h5>
                <p class="display-4">S/ <span id="ventasMes">0.00</span></p>
                <small class="text-muted">Este mes: <?= date('F Y') ?></small>
            </div>
        </div>
    </div>

    <!-- Total acumulado -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <h5>Total Acumulado</h5>
                <p class="display-4">S/ <span id="totalAcumulado">0.00</span></p>
                <small class="text-muted">Desde inicio</small>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <!-- Gráfico de ventas por día -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5>Ventas por Día (Últimos 7 días)</h5>
            </div>
            <div class="card-body">
                <canvas id="ventasPorDia"></canvas>
            </div>
        </div>
    </div>

    <!-- Top 5 productos vendidos -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5>Top 5 Productos Vendidos</h5>
            </div>
            <div class="card-body">
                <ul class="list-group">
                    <?php
                    $stmt = $pdo->prepare("SELECT p.nombre, SUM(d.cantidad) as total 
                                           FROM detalle_venta d
                                           JOIN productos p ON d.producto_id = p.id
                                           GROUP BY p.id ORDER BY total DESC LIMIT 5");
                    $stmt->execute();
                    $productos = $stmt->fetchAll();

                    foreach ($productos as $p): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <?= htmlspecialchars($p['nombre']) ?>
                        <span class="badge bg-primary"><?= $p['total'] ?> unidades</span>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Datos para gráfico
function cargarDatos() {
    const fechaDesde = document.getElementById('fechaDesde').value;
    const mes = document.getElementById('mes').value;

    fetch('datos_reportes.php?fechaDesde=' + fechaDesde + '&mes=' + mes)
        .then(response => response.json())
        .then(data => {
            // Actualizar totales
            document.getElementById('ventasDia').textContent = data.ventasDia.toFixed(2);
            document.getElementById('ventasMes').textContent = data.ventasMes.toFixed(2);
            document.getElementById('totalAcumulado').textContent = data.totalAcumulado.toFixed(2);

            // Gráfico
            const ctx = document.getElementById('ventasPorDia').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.dias,
                    datasets: [{
                        label: 'Ventas diarias',
                        data: data.ventas,
                        borderColor: '#0d6efd',
                        tension: 0.3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        });
}

// Cargar al inicio
cargarDatos();

// Actualizar al cambiar fechas
document.getElementById('fechaDesde').addEventListener('change', cargarDatos);
document.getElementById('mes').addEventListener('change', cargarDatos);
</script>
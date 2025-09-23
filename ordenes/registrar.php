<?php
session_start();
include '../includes/conexion.php';
include '../includes/funciones.php';

// Verificar rol
if (!in_array($_SESSION['rol'], ['admin', 'cajera', 'tecnico'])) {
    header("Location: ../dashboard.php");
    exit;
}

// Obtener técnicos
$stmt = $pdo->prepare("SELECT nombre FROM usuarios WHERE rol = 'tecnico' ORDER BY nombre");
$stmt->execute();
$tecnicos = $stmt->fetchAll(PDO::FETCH_COLUMN);
?>

<?php include '../includes/header.php'; ?>

<!-- Breadcrumb -->
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="index.php">Órdenes</a></li>
        <li class="breadcrumb-item active" aria-current="page">Nueva Orden</li>
    </ol>
</nav>

<h2> 📱 Registrar Orden de Servicio </h2>

<a href="index.php" class="btn btn-secondary mb-3">Volver</a>

<form method="POST" action="guardar.php">
    <!-- Datos del cliente -->
    <h5>Datos del Cliente</h5>

    <div class="mb-3">
        <label>DNI del Cliente *</label>
        <div class="input-group">
            <input type="text" name="cliente_dni" class="form-control" maxlength="8" required placeholder="12345678" id="dniCliente" style="width: 120px;">
            <button type="button" class="btn btn-outline-primary" id="btnBuscar">
                🔍 Buscar Cliente
            </button>
        </div>
    </div>

    <div class="mb-3">
        <label>Nombre del Cliente *</label>
        <input type="text" name="cliente_nombre" class="form-control" required placeholder="Nombre del cliente" id="nombreCliente">
    </div>

    <div class="mb-3">
        <label>Teléfono del Cliente</label>
        <input type="text" name="cliente_telefono" class="form-control" placeholder="987654321">
    </div>

    <hr>

    <!-- Dispositivo -->
    <div class="mb-3">
        <label>Modelo del Teléfono *</label>
        <input type="text" name="modelo_telefono" class="form-control" required placeholder="iPhone 13, Samsung A24, etc.">
    </div>

    <div class="mb-3">
        <label>IMEI (opcional)</label>
        <input type="text" name="imei" class="form-control" placeholder="15 dígitos">
    </div>

    <div class="mb-3">
        <label>Problema Reportado *</label>
        <textarea name="problema" class="form-control" rows="3" required placeholder="Pantalla rota, no enciende, caída en agua..."></textarea>
    </div>

    <div class="mb-3">
        <label>Observaciones (clave, patrón, advertencias) *</label>
        <textarea name="observaciones" class="form-control" rows="3" required placeholder="Clave: 1234, Patrón dibujado, No tiene respaldo..."></textarea>
    </div>

    <hr>

    <!-- Repuesto usado -->
    <div class="row">
        <div class="col-md-6 mb-3">
            <label>Repuesto usado (opcional)</label>
            <select name="repuesto_usado" class="form-control" id="repuestoSelect">
                <option value="">Ninguno</option>
                <?php foreach ($repuestos as $r): ?>
                    <option value="<?= $r['id'] ?>" data-precio="<?= $r['precio'] ?>">
                        <?= $r['nombre'] ?> (S/ <?= number_format($r['precio'], 2) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-6 mb-3">
            <label>Costo del repuesto</label>
            <input type="number" step="0.01" name="costo_repuesto" id="costo_repuesto" class="form-control" value="0" readonly>
        </div>
    </div>

    <!-- Pago y estado -->
    <div class="row">
        <div class="col-md-4 mb-3">
            <label>Tipo de Pago</label>
            <select name="tipo_pago" class="form-control">
                <option value="efectivo">Efectivo</option>
                <option value="yape">Yape</option>
                <option value="plin">Plin</option>
                <option value="transferencia">Transferencia</option>
                <option value="tarjeta">Tarjeta</option>
            </select>
        </div>
        <div class="col-md-4 mb-3">
            <label>Total (S/) *</label>
            <input type="number" step="0.01" name="total" class="form-control" required placeholder="100.00" id="totalInput">
        </div>
        <div class="col-md-4 mb-3">
            <label>Estado *</label>
            <select name="estado" class="form-control" required>
                <option value="recibido">Recibido</option>
                <option value="en_reparacion">En Reparación</option>
                <option value="listo">Listo para Entrega</option>
            </select>
        </div>
    </div>

    <!-- Técnico y entrega -->
    <div class="row">
        <div class="col-md-6 mb-3">
            <label>Técnico Asignado</label>
            <input type="text" name="tecnico" id="tecnicoInput" class="form-control" placeholder="Escribe para buscar técnico...">
            <div id="sugerenciasTecnico" class="list-group" style="position: absolute; z-index: 1000; width: 100%; display: none;"></div>
        </div>
        <div class="col-md-6 mb-3">
            <label>Fecha de Entrega (opcional)</label>
            <input type="date" name="fecha_entrega" class="form-control" value="<?= date('Y-m-d') ?>">
        </div>
    </div>

    <div class="mt-4">
        <a href="index.php" class="btn btn-secondary">Cancelar</a>
        <button type="submit" class="btn btn-success">Guardar Orden</button>
    </div>
</form>

<?php include '../includes/footer.php'; ?>

<script>
// === Autocompletado de cliente por DNI ===
document.addEventListener('DOMContentLoaded', function () {
    const dniInput = document.getElementById('dniCliente');
    const nombreInput = document.getElementById('nombreCliente');
    const telefonoInput = document.querySelector('[name="cliente_telefono"]');
    const btnBuscar = document.getElementById('btnBuscar');

    if (!dniInput || !nombreInput || !btnBuscar) {
        console.warn("No se encontraron los elementos del cliente");
        return;
    }

    const baseUrl = window.location.origin + '/sistema_ventas';

    function buscarCliente() {
        const dni = dniInput.value.trim();

        if (dni.length !== 8 || !/^\d{8}$/.test(dni)) {
            alert('DNI debe tener 8 dígitos numéricos');
            return;
        }

        // ✅ Primero busca en tu base de datos local
        fetch(`${baseUrl}/ordenes/utils/buscar_cliente.php?dni=${dni}`)
            .then(response => response.json())
            .then(data => {
                if (data.nombre) {
                    // ✅ Encontrado en local → autocompleta
                    nombreInput.value = data.nombre;
                    if (data.telefono && !telefonoInput.value) {
                        telefonoInput.value = data.telefono;
                    }
                    alert('✅ Cliente encontrado en tu base de datos');
                } else {
                    // ❌ No encontrado en local → consulta API externa
                    alert('Buscando en Consultas Perú...');
                    fetch(`${baseUrl}/ordenes/utils/api-dni.php?dni=${dni}`)
                        .then(apiResponse => apiResponse.json())
                        .then(apiData => {
                            if (apiData.nombre) {
                                nombreInput.value = apiData.nombre;
                                alert('✅ Nombre encontrado en Consultas Perú');
                            } else {
                                alert('❌ ' + (apiData.error || 'No encontrado'));
                            }
                        })
                        .catch(error => {
                            console.error('Error al conectar con la API:', error);
                            alert('❌ Error al conectar con la API. Verifica tu token o conexión.');
                        });
                }
            })
            .catch(error => {
                console.error('Error al buscar en tu base de datos:', error);
                alert('❌ Error al buscar en tu base de datos. Verifica la conexión.');
            });
    }

    // ✅ Autocompletado al salir del campo (blur)
    dniInput.addEventListener('blur', function () {
        if (this.value.length === 8) {
            buscarCliente();
        }
    });

    // ✅ Botón manual
    btnBuscar.addEventListener('click', buscarCliente);
});
</script>
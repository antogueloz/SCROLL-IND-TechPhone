<?php
session_start();
include '../includes/conexion.php';
include '../includes/funciones.php';

// Solo admin, cajera o vendedor pueden hacer ventas
redirigirSiNoAutorizado(['admin', 'cajera', 'vendedor']);

// Obtener productos
$stmt = $pdo->query("SELECT * FROM productos WHERE stock > 0 ORDER BY nombre");
$productos = $stmt->fetchAll();
?>

<?php include '../includes/header.php'; ?>

<!-- Breadcrumb -->
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="index.php">Ventas</a></li>
        <li class="breadcrumb-item active" aria-current="page">Nueva Venta</li>
    </ol>
</nav>

<h2> 💵 Venta de Productos </h2>

<a href="index.php" class="btn btn-secondary mb-3">Volver</a>

<!-- Mensaje de error -->
<?php if (isset($_GET['mensaje'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= $_GET['mensaje'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<form method="POST" action="guardar.php">
    <!-- Datos del cliente -->
    <h5>Datos del Cliente</h5>

    <div class="row mb-3">
        <div class="col-md-6">
            <label>DNI del Cliente *</label>
            <input type="text" name="cliente_dni" class="form-control" maxlength="8" placeholder="12345678" required id="dniCliente">
        </div>
        <div class="col-md-6">
            <label>Nombre del Cliente *</label>
            <input type="text" name="cliente_nombre" class="form-control" placeholder="Nombre completo" required id="nombreCliente">
        </div>
    </div>

    <div class="mb-3">
        <label>Teléfono del Cliente</label>
        <input type="text" name="telefono_cliente" class="form-control" placeholder="987654321">
    </div>

    <div class="mb-3">
        <button type="button" class="btn btn-outline-primary" id="btnBuscar">
            🔍 Buscar Nombre por DNI
        </button>
    </div>

    <hr>

    <!-- Carrito de productos -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">Carrito de Productos</h5>
        <button type="button" class="btn btn-success" style="
            border-radius: 8px;
            background-color: #28a745;
            border: none;
            color: white;
            font-weight: 600;
            padding: 0 20px;
            height: 40px;
            transition: all 0.3s ease-in-out;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        " onclick="agregarFila()">
            <i class="fas fa-plus"></i>
            <span style="margin-left: 12px;">Agregar Producto</span>
        </button>
    </div>

    <table class="table table-bordered">
        <thead class="table-light">
            <tr>
                <th>Producto</th>
                <th>Precio (S/)</th>
                <th>Stock</th>
                <th>Cant</th>
                <th>Subtotal</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody id="carrito">
            <tr>
                <td>
                    <select class="form-control producto" onchange="calcularSubtotal(this)">
                        <option value="">Seleccionar</option>
                        <?php foreach ($productos as $p): ?>
                            <option value="<?= $p['id'] ?>" 
                                    data-precio="<?= $p['precio'] ?>" 
                                    data-stock="<?= $p['stock'] ?>">
                                <?= $p['nombre'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <input type="hidden" name="producto_id[]">
                    <input type="hidden" name="precio_unitario[]"> <!-- Campo oculto para guardar precio -->
                </td>
                <td class="precio">0.00</td>
                <td class="stock">0</td>
                <td><input type="number" name="cantidad[]" class="form-control cantidad" value="1" min="1" onchange="calcularSubtotal(this)"></td>
                <td class="subtotal">0.00</td>
                <td><button type="button" class="btn btn-danger btn-sm" onclick="eliminarFila(this)">Eliminar</button></td>
            </tr>
        </tbody>
    </table>

    <div class="mt-3">
        <h5>Total: S/ <span id="totalGeneral">0.00</span></h5>
        <input type="hidden" name="total" id="inputTotal">
    </div>

    <div class="mt-4">
        <a href="index.php" class="btn btn-secondary">Cancelar</a>
        <button type="submit" class="btn btn-success">Confirmar Venta</button>
    </div>
</form>

<?php include '../includes/footer.php'; ?>

<script>
// Calcular subtotal y total
function calcularSubtotal(fila) {
    const filaTr = fila.closest('tr');
    const select = filaTr.querySelector('.producto');
    const precioTd = filaTr.querySelector('.precio');
    const stockTd = filaTr.querySelector('.stock');
    const cantidadInput = filaTr.querySelector('.cantidad');
    const subtotalTd = filaTr.querySelector('.subtotal');

    if (!select.value) {
        precioTd.textContent = '0.00';
        stockTd.textContent = '0';
        subtotalTd.textContent = '0.00';
        return;
    }

    const precio = parseFloat(select.selectedOptions[0]?.getAttribute('data-precio')) || 0;
    const stock = parseInt(select.selectedOptions[0]?.getAttribute('data-stock')) || 0;
    let cantidad = parseInt(cantidadInput.value) || 1;

    if (cantidad > stock) {
        alert(`Stock insuficiente. Máximo: ${stock}`);
        cantidad = stock;
        cantidadInput.value = stock;
    }

    const subtotal = precio * cantidad;

    precioTd.textContent = precio.toFixed(2);
    stockTd.textContent = stock;
    subtotalTd.textContent = subtotal.toFixed(2);

    // Actualizar inputs ocultos
    filaTr.querySelector('input[name="producto_id[]"]').value = select.value;
    filaTr.querySelector('input[name="precio_unitario[]"]').value = precio;

    calcularTotal();
}

// Calcular total general
function calcularTotal() {
    let total = 0;
    document.querySelectorAll('#carrito .subtotal').forEach(td => {
        total += parseFloat(td.textContent) || 0;
    });
    document.getElementById('totalGeneral').textContent = total.toFixed(2);
    document.getElementById('inputTotal').value = total.toFixed(2);
}

// Agregar nueva fila al carrito
function agregarFila() {
    const tbody = document.getElementById('carrito');
    const fila = tbody.children[0].cloneNode(true);

    // Limpiar valores
    fila.querySelector('.producto').value = '';
    fila.querySelector('.precio').textContent = '0.00';
    fila.querySelector('.stock').textContent = '0';
    fila.querySelector('.cantidad').value = 1;
    fila.querySelector('.subtotal').textContent = '0.00';
    fila.querySelector('input[name="producto_id[]"]').value = '';
    fila.querySelector('input[name="precio_unitario[]"]').value = '';

    // Asignar eventos
    fila.querySelector('.producto').onchange = function() { calcularSubtotal(this); };
    fila.querySelector('.cantidad').onchange = function() { calcularSubtotal(this); };

    tbody.appendChild(fila);
}

// Eliminar fila
function eliminarFila(btn) {
    if (document.getElementById('carrito').children.length > 1) {
        btn.closest('tr').remove();
        calcularTotal();
    } else {
        alert('No puedes eliminar todas las filas.');
    }
}

// Buscar cliente por DNI
document.addEventListener('DOMContentLoaded', function () {
    const btnBuscar = document.getElementById('btnBuscar');
    const dniInput = document.getElementById('dniCliente');
    const nombreInput = document.getElementById('nombreCliente');
    const telefonoInput = document.querySelector('[name="telefono_cliente"]');

    if (!btnBuscar || !dniInput || !nombreInput || !telefonoInput) {
        return;
    }

    btnBuscar.addEventListener('click', function () {
        const dni = dniInput.value.trim();

        if (dni.length !== 8 || !/^\d{8}$/.test(dni)) {
            alert('DNI debe tener 8 dígitos numéricos');
            return;
        }

        fetch('utils/buscar_cliente.php?dni=' + dni)
            .then(response => response.json())
            .then(data => {
                if (data.nombre) {
                    nombreInput.value = data.nombre;
                    if (!telefonoInput.value && data.telefono) {
                        telefonoInput.value = data.telefono;
                    }
                    alert('✅ Cliente encontrado: ' + data.nombre);
                } else {
                    nombreInput.value = '';
                    alert('Cliente no registrado. Ingrese el nombre para crearlo automáticamente.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('No se pudo conectar al servidor. Intente nuevamente.');
            });
    });
});
</script>

<!-- Font Awesome para ícono "+" -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
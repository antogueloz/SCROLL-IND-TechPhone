<?php
session_start();
include '../includes/conexion.php';
include '../includes/funciones.php';

// Solo admin, cajera o vendedor pueden ver ventas
redirigirSiNoAutorizado(['admin', 'cajera', 'vendedor']);

$id = $_GET['id'] ?? null;

if (!$id) {
    header("Location: index.php?mensaje=Venta no encontrada");
    exit;
}

// Obtener venta
$stmt = $pdo->prepare("SELECT * FROM ventas WHERE id = ?");
$stmt->execute([$id]);
$venta = $stmt->fetch();

if (!$venta) {
    header("Location: index.php?mensaje=Venta no encontrada");
    exit;
}

// Obtener cliente
$cliente = null;
if ($venta['cliente_id']) {
    $stmt = $pdo->prepare("SELECT telefono FROM clientes WHERE id = ?");
    $stmt->execute([$venta['cliente_id']]);
    $cliente = $stmt->fetch();
}

// Obtener productos
$stmt = $pdo->prepare("
    SELECT dv.cantidad, dv.precio_unitario, p.nombre 
    FROM detalle_venta dv 
    JOIN productos p ON dv.producto_id = p.id 
    WHERE dv.venta_id = ?
");
$stmt->execute([$id]);
$productos = $stmt->fetchAll();

// Datos del negocio
$nombre_negocio = "RUDY STORE";
$ruc = "20606271850";
$direccion = "JR. ALONSO DE ALVARADO 804, MOYOBAMBA - SAN MARTÍN";
$telefono = "956813991";
$email = "mlopez126@gmail.com";
$serie = "BE01";
$numero = str_pad($venta['id'], 6, '0', STR_PAD_LEFT); // Ej: 0000309
$fecha_emision = date('Y-m-d');
$hora_emision = date('H:i:s');
$tipo_comprobante = "BOLETA DE VENTA ELECTRÓNICA";

// URL para el QR
$url_qr = "https://www.rudystore.com/boleta/{$serie}-{$numero}";
?>

<?php include '../includes/header.php'; ?>

<!-- Breadcrumb -->
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="index.php">Ventas</a></li>
        <li class="breadcrumb-item active" aria-current="page">Ver Venta #<?= $venta['id'] ?></li>
    </ol>
</nav>

<h2> 💵 Detalle de Venta #<?= $venta['id'] ?> </h2>

<a href="index.php" class="btn btn-secondary mb-3">Volver</a>

<div class="card">
    <div class="card-header bg-primary text-white">
        <strong>Datos del Cliente</strong>
    </div>
    <div class="card-body">
        <p><strong>Nombre:</strong> <?= htmlspecialchars($venta['cliente_nombre']) ?></p>
        <p><strong>DNI:</strong> <?= htmlspecialchars($venta['cliente_dni']) ?></p>
        <p><strong>Teléfono:</strong> 
            <?= $cliente && !empty($cliente['telefono']) ? htmlspecialchars($cliente['telefono']) : 'No registrado' ?>
        </p>
    </div>
</div>

<div class="card mt-4">
    <div class="card-header bg-success text-white">
        <strong>Productos Vendidos</strong>
    </div>
    <div class="card-body">
        <table class="table table-bordered">
            <thead class="table-light">
                <tr>
                    <th>Producto</th>
                    <th>Precio Unitario</th>
                    <th>Cantidad</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($productos as $p): ?>
                    <?php $subtotal = $p['cantidad'] * $p['precio_unitario']; ?>
                    <tr>
                        <td><?= htmlspecialchars($p['nombre']) ?></td>
                        <td>S/ <?= number_format($p['precio_unitario'], 2) ?></td>
                        <td><?= $p['cantidad'] ?></td>
                        <td>S/ <?= number_format($subtotal, 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr class="fw-bold">
                    <td colspan="3" class="text-end">Total:</td>
                    <td>S/ <?= number_format($venta['total'], 2) ?></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<div class="card mt-4">
    <div class="card-body">
        <p><strong>Vendedor:</strong> <?= obtenerNombreUsuario($pdo, $venta['usuario_id']) ?></p>
        <p><strong>Fecha:</strong> <?= date('d/m/Y H:i', strtotime($venta['fecha'])) ?></p>
    </div>
</div>

<!-- Botón Imprimir Boleta -->
<button type="button" class="btn btn-primary mt-4" onclick="imprimirTicket()">
    🖨️ Imprimir Boleta Electrónica
</button>

<?php include '../includes/footer.php'; ?>

<script>
// === Función para convertir número a letras (JavaScript) ===
function numeroALetras(numero) {
    const unidades = ["", "uno", "dos", "tres", "cuatro", "cinco", "seis", "siete", "ocho", "nueve"];
    const especiales = ["diez", "once", "doce", "trece", "catorce", "quince"];
    const decenas = ["", "", "veinte", "treinta", "cuarenta", "cincuenta", "sesenta", "setenta", "ochenta", "noventa"];
    
    let entero = Math.floor(numero);
    let decimal = Math.round((numero - entero) * 100);

    if (entero === 0) return "Cero con " + decimal.toString().padStart(2, '0') + "/100 Soles";

    let texto = "";
    if (entero >= 1000) {
        texto += Math.floor(entero / 1000) + " mil ";
        entero %= 1000;
    }
    if (entero >= 100) {
        if (entero >= 100 && entero < 200) texto += "ciento ";
        else if (entero >= 200 && entero < 300) texto += "doscientos ";
        else if (entero >= 300 && entero < 400) texto += "trescientos ";
        else if (entero >= 400 && entero < 500) texto += "cuatrocientos ";
        else if (entero >= 500 && entero < 600) texto += "quinientos ";
        else if (entero >= 600 && entero < 700) texto += "seiscientos ";
        else if (entero >= 700 && entero < 800) texto += "setecientos ";
        else if (entero >= 800 && entero < 900) texto += "ochocientos ";
        else if (entero >= 900 && entero < 1000) texto += "novecientos ";
        entero %= 100;
    }
    if (entero >= 20 || entero === 10) {
        if (entero < 16) texto += especiales[entero - 10] + " ";
        else if (entero === 20) texto += "veinte ";
        else if (entero > 20) texto += decenas[Math.floor(entero / 10)] + (entero % 10 ? " y " : "") + (unidades[entero % 10] || "");
    } else if (entero > 0) {
        texto += unidades[entero];
    }

    return texto.trim() + " con " + decimal.toString().padStart(2, '0') + "/100 Soles";
}

// === Función principal de impresión ===
function imprimirTicket() {
    // Datos inyectados por PHP
    const clienteNombre = "<?= addslashes(htmlspecialchars($venta['cliente_nombre'])) ?>";
    const clienteDni = "<?= htmlspecialchars($venta['cliente_dni']) ?>";
    const total = <?= $venta['total'] ?>;
    const serie = "<?= $serie ?>";
    const numero = "<?= $numero ?>";
    const fechaEmision = "<?= $fecha_emision ?>";
    const horaEmision = "<?= $hora_emision ?>";
    const urlQr = "<?= $url_qr ?>";

    const contenido = `
<!DOCTYPE html>
<html>
<head>
    <title>Boleta #${serie}-${numero}</title>
    <script src="<?= $base_url ?>/js/qrcode.min.js"><\/script>
    <style>
    /* Estilo para pantalla (pequeño) */
    @media screen {
        body { 
            font-family: 'Courier New', monospace; 
            font-size: 10px;
            line-height: 1.1;
            width: 300px;
        }
        .qr { display: none; } /* Ocultar QR en pantalla */
    }

    /* Estilo para impresión (grande y claro) */
    @media print {
        body { 
            font-family: 'Lucida Console', 'Monaco', monospace; 
            font-size: 16px !important;
            line-height: 1.4;
            width: 100%;
            -webkit-print-color-adjust: exact;
        }
        .header { text-align: center; }
        .logo { 
            width: 80px; 
            height: 80px; 
            border-radius: 50%; 
            background: black; 
            color: white; 
            font-weight: bold; 
            display: inline-block; 
            line-height: 80px; 
            font-size: 18px;
            margin: 0 auto;
        }
        .info-negocio { 
            font-size: 16px; 
            margin-top: 10px;
        }
        .titulo { 
            font-weight: bold; 
            text-align: center; 
            margin: 12px 0; 
            font-size: 20px;
        }
        .info-comprobante { 
            font-size: 16px; 
            margin-bottom: 12px; 
            line-height: 1.4;
        }
        .detalle th, .detalle td { 
            padding: 6px 0; 
            font-size: 16px;
        }
        .total { 
            font-weight: bold; 
            margin-top: 15px; 
            font-size: 18px;
        }
        .footer-texto { 
            font-size: 16px; 
            margin-top: 10px;
        }
        .footer { 
            margin-top: 20px; 
            font-size: 14px;
        }
        .qr { 
            width: 100px; 
            height: 100px; 
            margin: 15px auto; 
            border: 2px dashed #000;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }
        @page { margin: 5mm; }
    }
</style>
</head>
<body onload="generarQR(); window.print(); window.close();">
    <div class="header">
        <div class="logo">
            <img src="<?= $base_url ?>/images/logo.jpg" alt="Logo" style="width: 60px; height: 60px; border-radius: 50%; object-fit: cover;">
        </div>
        <div class="info-negocio">
            <strong><?= $nombre_negocio ?></strong><br>
            RUC: <?= $ruc ?><br>
            <?= $direccion ?><br>
            Teléfono: <?= $telefono ?><br>
            Email: <?= $email ?>
        </div>
    </div>

    <div class="titulo">
        BOLETA DE VENTA ELECTRÓNICA<br>
        ${serie}-${numero}
    </div>

    <div class="info-comprobante">
        F. Emisión: ${fechaEmision}<br>
        H. Emisión: ${horaEmision}<br>
        H. Vencimiento: ${new Date().toISOString().split('T')[0]}<br>
        Cliente: ${clienteNombre}<br>
        DNI: ${clienteDni}<br>
        Dirección: <?= addslashes($direccion) ?>
    </div>

    <table class="detalle">
        <thead>
            <tr>
                <th>CANT.</th>
                <th>UNIDAD</th>
                <th>DESCRIPCIÓN</th>
                <th>P.UNIT</th>
                <th>TOTAL</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($productos as $p): ?>
            <tr>
                <td><?= $p['cantidad'] ?></td>
                <td>NIU</td>
                <td><?= substr(htmlspecialchars($p['nombre']), 0, 20) ?></td>
                <td>S/ <?= number_format($p['precio_unitario'], 2) ?></td>
                <td>S/ <?= number_format($p['cantidad'] * $p['precio_unitario'], 2) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="total">
        <p><strong>OP. EXONERADAS: S/ <?= number_format($venta['total'], 2) ?></strong></p>
        <p><strong>IGV: S/ 0.00</strong></p>
        <p><strong>TOTAL A PAGAR: S/ <?= number_format($venta['total'], 2) ?></strong></p>
    </div>

    <div class="footer-texto">
        Son: ${numeroALetras(total)}
    </div>

    <div class="footer">
        <div class="qr" id="qrCode"></div>
        MUCHAS GRACIAS POR TU COMPRA
    </div>

    <script>
        function generarQR() {
            new QRCode(document.getElementById("qrCode"), {
                text: "${urlQr}",
                width: 80,
                height: 80,
                colorDark: "#000",
                colorLight: "#fff",
                correctLevel: QRCode.CorrectLevel.H
            });
        }
    <\/script>
</body>
</html>
`;

    const ventana = window.open('', '_blank');
    if (!ventana) {
        alert("❌ Bloqueador de ventanas activado. Permita ventanas emergentes.");
        return;
    }

    ventana.document.write(contenido);
    ventana.document.close();
}
</script>
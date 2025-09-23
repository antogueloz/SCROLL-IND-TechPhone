<?php
session_start();
include '../includes/conexion.php';
include '../includes/funciones.php';

// Solo admin puede agregar clientes
redirigirSiNoAutorizado(['admin']);

if ($_POST) {
    $nombre = $_POST['nombre'];
    $dni = $_POST['dni'];
    $telefono = $_POST['telefono'];
    $email = $_POST['email'] ?: null;
    $direccion = $_POST['direccion'];

    // Validar DNI
    if (!preg_match('/^\d{8}$/', $dni)) {
        $error = "DNI debe tener 8 dígitos.";
    } else {
        // Verificar si ya existe
        $stmt = $pdo->prepare("SELECT id FROM clientes WHERE dni = ?");
        $stmt->execute([$dni]);
        if ($stmt->fetch()) {
            $error = "Ya existe un cliente con ese DNI.";
        } else {
            $stmt = $pdo->prepare("INSERT INTO clientes (nombre, dni, telefono, email, direccion) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$nombre, $dni, $telefono, $email, $direccion]);

            header("Location: index.php?mensaje=Cliente agregado correctamente");
            exit;
        }
    }
}
?>

<?php include '../includes/header.php'; ?>

<!-- Breadcrumb -->
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="index.php">Clientes</a></li>
        <li class="breadcrumb-item active" aria-current="page">Agregar Cliente</li>
    </ol>
</nav>

<h2> 👤 Agregar Cliente </h2>

<a href="index.php" class="btn btn-secondary mb-3">Volver</a>

<?php if (isset($error)): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= $error ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<form method="POST">
    <div class="mb-3">
        <label>DNI *</label>
        <div class="input-group">
            <input type="text" name="dni" class="form-control" maxlength="8" required placeholder="12345678" id="dniCliente" style="width: 120px;">
            <button type="button" class="btn btn-outline-primary" id="btnBuscar">
                🔍 Buscar por DNI
            </button>
        </div>
    </div>

    <div class="mb-3">
        <label>Nombre *</label>
        <input type="text" name="nombre" class="form-control" required placeholder="Nombre completo" id="nombreCliente">
    </div>

    <div class="mb-3">
        <label>Teléfono</label>
        <input type="text" name="telefono" class="form-control" placeholder="987654321">
    </div>

    <div class="mb-3">
        <label>Email (opcional)</label>
        <input type="email" name="email" class="form-control" placeholder="usuario@correo.com">
    </div>

    <div class="mb-3">
        <label>Dirección (opcional)</label>
        <input type="text" name="direccion" class="form-control" placeholder="Av. Principal 123">
    </div>

    <div class="mt-4">
        <a href="index.php" class="btn btn-secondary">Cancelar</a>
        <button type="submit" class="btn btn-success">Guardar Cliente</button>
    </div>
</form>

<?php include '../includes/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const dniInput = document.getElementById('dniCliente');
    const nombreInput = document.getElementById('nombreCliente');
    const telefonoInput = document.querySelector('[name="telefono"]');
    const direccionInput = document.querySelector('[name="direccion"]');
    const btnBuscar = document.getElementById('btnBuscar');

    // ✅ Verificar que todos los elementos existan
    if (!dniInput || !nombreInput || !btnBuscar || !direccionInput) {
        console.warn("No se encontraron los elementos del formulario");
        return;
    }

    // ✅ Ruta base absoluta (funciona en cualquier puerto)
    const baseUrl = window.location.origin + '/sistema_ventas';

    function buscarCliente() {
        const dni = dniInput.value.trim();

        if (dni.length !== 8 || !/^\d{8}$/.test(dni)) {
            alert('DNI debe tener 8 dígitos numéricos');
            return;
        }

        // ✅ Rutas correctas
        const urlLocal = `${baseUrl}/clientes/utils/buscar_cliente.php?dni=${dni}`;
        const urlApi = `${baseUrl}/clientes/utils/api-dni.php?dni=${dni}`;

        fetch(urlLocal)
            .then(response => {
                if (!response.ok) throw new Error("HTTP " + response.status);
                return response.json();
            })
            .then(data => {
                console.log("Respuesta local:", data); // 🔍 Para depuración

                if (data.nombre) {
                    // ✅ Encontrado en local
                    nombreInput.value = data.nombre;

                    // 📞 Autocompletar teléfono si está vacío
                    if (data.telefono && !telefonoInput.value) {
                        telefonoInput.value = data.telefono;
                    }

                    // 🏠 Autocompletar dirección si está vacía
                    if (data.direccion && !direccionInput.value) {
                        direccionInput.value = data.direccion;
                    }

                    alert('✅ Cliente encontrado en tu base de datos');
                } else {
                    // ❌ No encontrado → consulta API externa
                    alert('Buscando en Consultas Perú...');
                    fetch(urlApi)
                        .then(apiResponse => {
                            if (!apiResponse.ok) throw new Error("HTTP " + apiResponse.status);
                            return apiResponse.json();
                        })
                        .then(apiData => {
                            console.log("Respuesta API:", apiData); // 🔍 Para depuración

                            if (apiData.nombre) {
                                // ✅ Nombre encontrado
                                nombreInput.value = apiData.nombre;

                                // 📞 Teléfono desde API (si viene)
                                if (apiData.telefono && !telefonoInput.value) {
                                    telefonoInput.value = apiData.telefono;
                                }

                                // 🏠 Dirección desde API
                                if (apiData.direccion && !direccionInput.value) {
                                    direccionInput.value = apiData.direccion;
                                }

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

    // ✅ Autocompletado al salir del campo
    dniInput.addEventListener('blur', buscarCliente);

    // ✅ Botón manual
    btnBuscar.addEventListener('click', buscarCliente);
});
</script>
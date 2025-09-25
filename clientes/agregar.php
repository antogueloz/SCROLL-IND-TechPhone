<?php
session_start();
include '../includes/conexion.php';
include '../includes/funciones.php';

// Solo admin, cajera o vendedor pueden agregar clientes
redirigirSiNoAutorizado(['admin', 'cajera', 'vendedor']);
?>

<?php include '../includes/header.php'; ?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-4">
                    <h2 class="mb-0"><i class="fas fa-user-plus"></i> Agregar Cliente</h2>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="guardar.php">
                        <div class="mb-3">
                            <label for="dni" class="form-label">DNI *</label>
                            <div class="input-group">
                                <input type="text" name="dni" id="dni" class="form-control" required placeholder="12345678" maxlength="8">
                                <button type="button" class="btn btn-outline-secondary" id="btnBuscar">
                                    🔍 Buscar por DNI
                                </button>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre *</label>
                            <input type="text" name="nombre" id="nombre" class="form-control" required placeholder="Nombre completo">
                        </div>

                        <div class="mb-3">
                            <label for="telefono" class="form-label">Teléfono</label>
                            <input type="text" name="telefono" id="telefono" class="form-control" placeholder="987654321">
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email (opcional)</label>
                            <input type="email" name="email" id="email" class="form-control" placeholder="usuario@correo.com">
                        </div>

                        <div class="mb-3">
                            <label for="direccion" class="form-label">Dirección (opcional)</label>
                            <input type="text" name="direccion" id="direccion" class="form-control" placeholder="Av. Principal 123">
                        </div>

                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-secondary" onclick="window.history.back()">
                                Cancelar
                            </button>
                            <button type="submit" class="btn btn-success">
                                Guardar Cliente
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Validación en tiempo real
document.getElementById('dni').addEventListener('input', function () {
    const dni = this.value.trim();
    if (dni.length > 0 && !/^\d{8}$/.test(dni)) {
        alert('DNI debe tener 8 dígitos numéricos');
        this.value = '';
    }
});

document.getElementById('btnBuscar').addEventListener('click', function () {
    const dni = document.getElementById('dni').value.trim();
    if (!dni || !/^\d{8}$/.test(dni)) {
        alert('DNI debe tener 8 dígitos numéricos');
        return;
    }

    // Aquí puedes hacer una petición a tu API local o SUNAT
    fetch(`utils/buscar_cliente.php?dni=${dni}`)
    .then(response => response.json())
    .then(data => {
        if (data.nombre) {
            document.getElementById('nombre').value = data.nombre;
            if (data.telefono) {
                document.getElementById('telefono').value = data.telefono;
            }
            alert('✅ Cliente encontrado en tu base de datos');
        } else {
            alert('❌ Cliente no registrado. Ingrese nombre manualmente.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('❌ Error al buscar en la base de datos');
    });

});
</script>

<?php include '../includes/footer.php'; ?>
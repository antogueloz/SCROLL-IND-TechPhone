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
                    <?php
                    if (isset($_SESSION['error'])) {
                        echo '<div class="alert alert-danger">' . htmlspecialchars($_SESSION['error']) . '</div>';
                        unset($_SESSION['error']);
                    }
                    ?>
                    <form method="POST" action="guardar.php" id="clienteForm">
                        <div class="mb-3">
                            <label for="tipo_documento" class="form-label">Tipo de Documento</label>
                            <select name="tipo_documento" id="tipo_documento" class="form-select" required>
                                <option value="dni">DNI</option>
                                <option value="ruc">RUC</option>
                                <option value="ce">Carnet de Extranjería</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="numero_documento" class="form-label">Número de Documento *</label>
                            <input type="text" name="numero_documento" id="numero_documento" class="form-control" required placeholder="Ingrese número de documento" inputmode="numeric" pattern="\d*" maxlength="12">
                            <div id="error_numero_documento" class="text-danger mt-1" style="font-size: 0.875rem;"></div>
                        </div>

                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre *</label>
                            <input type="text" name="nombre" id="nombre" class="form-control" required placeholder="Nombre completo">
                        </div>

                        <div class="mb-3">
                            <label for="telefono" class="form-label">Teléfono *</label>
                            <div class="input-group">
                                <input type="text" name="telefono" id="telefono" class="form-control" required placeholder="987654321" maxlength="9">
                                <div class="input-group-text">
                                    <input type="checkbox" id="es_extranjero" name="es_extranjero" value="1">
                                    <label for="es_extranjero" class="form-check-label ms-1">Extranjero</label>
                                </div>
                            </div>
                            <div id="error_telefono" class="text-danger mt-1" style="font-size: 0.875rem;"></div>
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
document.addEventListener('DOMContentLoaded', function() {
    // Establecer DNI como valor por defecto
    const tipoDocumento = document.getElementById('tipo_documento');
    tipoDocumento.value = 'dni';
    
    // Configurar inicialmente para DNI
    actualizarConfiguracionDocumento();
    
    // Evento change para el tipo de documento
    tipoDocumento.addEventListener('change', actualizarConfiguracionDocumento);
    
    // Validación del número de documento
    document.getElementById('numero_documento').addEventListener('input', function() {
        validarNumeroDocumento();
    });
    
    document.getElementById('numero_documento').addEventListener('blur', function() {
        validarNumeroDocumento();
    });
    
    // Validación del teléfono
    document.getElementById('telefono').addEventListener('input', function() {
        validarTelefono();
    });
    
    document.getElementById('telefono').addEventListener('blur', function() {
        validarTelefono();
    });
    
    document.getElementById('es_extranjero').addEventListener('change', function() {
        validarTelefono();
    });
    
    // Validación al enviar el formulario
    document.getElementById('clienteForm').addEventListener('submit', function(e) {
        const esValido = validarNumeroDocumento() && validarTelefono();
        
        if (!esValido) {
            e.preventDefault();
        }
    });
});

function actualizarConfiguracionDocumento() {
    const input = document.getElementById('numero_documento');
    const tipo = document.getElementById('tipo_documento').value;
    const errorDiv = document.getElementById('error_numero_documento');
    
    // Limpiar
    input.value = '';
    errorDiv.textContent = '';
    
    // Configurar según tipo
    if (tipo === 'dni') {
        input.placeholder = '12345678';
        input.maxLength = 8;
    } else if (tipo === 'ruc') {
        input.placeholder = '20123456789';
        input.maxLength = 11;
    } else if (tipo === 'ce') {
        input.placeholder = '123456789012';
        input.maxLength = 12;
    } else {
        input.placeholder = 'Ingrese número de documento';
        input.removeAttribute('maxlength');
    }
}

function validarNumeroDocumento() {
    const input = document.getElementById('numero_documento');
    const tipo = document.getElementById('tipo_documento').value;
    const errorDiv = document.getElementById('error_numero_documento');
    
    errorDiv.textContent = '';
    
    if (!input.value.trim()) {
        errorDiv.textContent = 'Este campo es obligatorio';
        return false;
    }
    
    let regex, mensaje;
    switch (tipo) {
        case 'dni':
            regex = /^\d{8}$/;
            mensaje = 'DNI debe tener 8 dígitos numéricos';
            break;
        case 'ruc':
            regex = /^\d{11}$/;
            mensaje = 'RUC debe tener 11 dígitos numéricos';
            break;
        case 'ce':
            regex = /^\d{12}$/;
            mensaje = 'Carnet de Extranjería debe tener 12 dígitos';
            break;
        default:
            errorDiv.textContent = 'Seleccione un tipo de documento';
            return false;
    }
    
    if (!regex.test(input.value)) {
        errorDiv.textContent = mensaje;
        return false;
    }
    
    return true;
}

function validarTelefono() {
    const input = document.getElementById('telefono');
    const esExtranjero = document.getElementById('es_extranjero').checked;
    const errorDiv = document.getElementById('error_telefono');
    
    errorDiv.textContent = '';
    
    if (!input.value.trim()) {
        errorDiv.textContent = 'Este campo es obligatorio';
        return false;
    }
    
    if (!esExtranjero && !/^\d{9}$/.test(input.value)) {
        errorDiv.textContent = 'Teléfono debe tener 9 dígitos numéricos';
        return false;
    }
    
    return true;
}
</script>

<?php include '../includes/footer.php'; ?>
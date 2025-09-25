<?php
// Define la URL base
$base_url = '/sistema_ventas';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Ventas</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome (correcto) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Estilos personalizados -->
    <link rel="stylesheet" href="<?= $base_url ?>/css/estilo.css">
    
    <!-- QR Code JS -->
    <script src="<?= $base_url ?>/js/qrcode.min.js"></script>
</head>
<body>
    <nav class="navbar navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="<?= $base_url ?>/dashboard.php">🛒 Sistema de Ventas</a>
            
            <?php if (isset($_SESSION['nombre'])): ?>
                <span class="text-white">
                    Usuario: <?= htmlspecialchars($_SESSION['nombre']) ?> 
                    (<?= ucfirst($_SESSION['rol']) ?>)
                </span>
            <?php endif; ?>

            <!-- Formulario de logout -->
            <form method="POST" action="<?= $base_url ?>/logout.php" style="display: none;" id="logout-form">
            </form>

            <button type="button" class="btn btn-outline-light btn-sm ms-2" onclick="confirmarLogout()">
                Salir
            </button>

            <script>
            function confirmarLogout() {
                if (confirm('¿Seguro que deseas cerrar sesión?')) {
                    document.getElementById('logout-form').submit();
                }
            }
            </script>
        </div>
    </nav>
    
    <div class="container mt-4"
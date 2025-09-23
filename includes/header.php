<?php
// Define la URL base
$base_url = '/sistema_ventas';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Sistema de Ventas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= $base_url ?>/css/estilo.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/fonts/fontawesome-icons/6.0.0/css/all.min.css">
    <script src="<?= $base_url ?>/js/qrcode.min.js"></script>
</head>
<body>
    <nav class="navbar navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="<?= $base_url ?>/dashboard.php">🛒 Sistema de Ventas</a>
            <span class="text-white">Usuario: <?php echo $_SESSION['nombre']; ?> (<?php echo ucfirst($_SESSION['rol']); ?>)</span>

        <!-- Formulario de logout (oculto, lo usa el botón) -->
        <form method="POST" action="logout.php" style="display: none;" id="logout-form">
        </form>

        <button type="button" class="btn btn-outline-light btn-sm ms-2" 
                onclick="confirmarLogout()">
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
    <div class="container mt-4">
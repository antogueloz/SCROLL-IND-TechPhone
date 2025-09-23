<?php
function formatPrecio($monto) {
    return 'S/ ' . number_format($monto, 2);
}
function esAdmin() {
    return isset($_SESSION['rol']) && $_SESSION['rol'] == 'admin';
}

function esTecnico() {
    return isset($_SESSION['rol']) && $_SESSION['rol'] == 'tecnico';
}

function esCajera() {
    return isset($_SESSION['rol']) && in_array($_SESSION['rol'], ['cajera', 'vendedor']);
}

function redirigirSiNoAutorizado($rolesPermitidos) {
    if (!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], $rolesPermitidos)) {
        header("Location: ../dashboard.php?error=Acceso denegado");
        exit;
    }
}
function obtenerNombreUsuario($pdo, $id) {
    if (!$id) return "Desconocido";
    
    try {
        $stmt = $pdo->prepare("SELECT nombre FROM usuarios WHERE id = ?");
        $stmt->execute([$id]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $usuario ? $usuario['nombre'] : "Desconocido";
    } catch (Exception $e) {
        return "Error";
    }
}
function numero_a_letras($numero) {
    $formatter = new NumberFormatter("es", NumberFormatter::SPELLOUT);
    $entero = floor($numero);
    $decimal = round(($numero - $entero) * 100);

    $texto_entero = ucfirst($formatter->format($entero));
    return "$texto_entero con {$decimal}/100 Soles";
}
?>
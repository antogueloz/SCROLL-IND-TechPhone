<?php
session_start();
include '../includes/conexion.php';
include '../includes/funciones.php';

redirigirSiNoAutorizado(['admin']);

$id = $_GET['id'];

// No permitir eliminarse a sí mismo
if ($id == $_SESSION['usuario_id']) {
    header("Location: index.php?mensaje=No puedes eliminarte a ti mismo");
    exit;
}

$stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = ?");
$stmt->execute([$id]);

header("Location: index.php?mensaje=Usuario eliminado");
exit;
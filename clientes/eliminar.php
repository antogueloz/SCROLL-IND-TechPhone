<?php
session_start();
include '../includes/conexion.php';

$id = $_GET['id'];

$stmt = $pdo->prepare("DELETE FROM clientes WHERE id = ?");
$stmt->execute([$id]);

header("Location: index.php?mensaje=Cliente eliminado");
exit;
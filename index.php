<?php
session_start();
include 'includes/conexion.php';

if ($_POST) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        // Iniciar sesión
        $_SESSION['usuario_id'] = $user['id'];
        $_SESSION['nombre'] = $user['nombre'];
        $_SESSION['rol'] = $user['rol'];

        // ✅ REGISTRO AUTOMÁTICO DE ENTRADA
        date_default_timezone_set('America/Lima'); // Zona horaria de Perú
        $usuario_id = $user['id'];
        $fecha = date('Y-m-d');
        $hora = date('H:i:s');

        try {
            $stmt = $pdo->prepare("INSERT INTO asistencia (usuario_id, fecha, entrada) 
                                VALUES (?, ?, ?) 
                                ON DUPLICATE KEY UPDATE entrada = IFNULL(entrada, ?)");
            $stmt->execute([$usuario_id, $fecha, $hora, $hora]);
        } catch (Exception $e) {
            // No detener el login si hay error
            error_log("Error al registrar entrada: " . $e->getMessage());
        }

        // Redirigir
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Email o contraseña incorrectos";
    }
}
?>

<?php if (isset($_GET['mensaje'])): ?>
    <div class="alert alert-info"><?= $_GET['mensaje'] ?></div>
<?php endif; ?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card p-4" style="width: 400px;">
            <h3 class="text-center">Iniciar Sesión</h3>
            <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
            <form method="POST">
                <div class="mb-3">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Contraseña</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Entrar</button>
            </form>
        </div>
    </div>
</body>
</html>
<?php
session_start();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = 'admin'; // Cambia por tus credenciales
    $password = 'admin123'; // Cambia esto
    
    if ($_POST['user'] === $usuario && $_POST['pass'] === $password) {
        $_SESSION['admin_logged'] = true;
        header('Location: panel.php');
        exit;
    } else {
        $error = 'Credenciales incorrectas';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body class="dark-theme">
    <div class="login-container">
        <h1>Acceso Administrador</h1>
        <?php if ($error): ?>
            <p class="error"><?= $error ?></p>
        <?php endif; ?>
        <form method="POST">
            <input type="text" name="user" placeholder="Usuario" required>
            <input type="password" name="pass" placeholder="Contraseña" required>
            <button type="submit">Entrar</button>
        </form>
    </div>
</body>
</html>
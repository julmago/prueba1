<?php
session_start();
if (!isset($_SESSION['admin_logged'])) {
    header('Location: index.php');
    exit;
}

$id = $_GET['id'];
$ventas = array_map('str_getcsv', file('../ventas.csv'));
$venta = null;

foreach ($ventas as $v) {
    if ($v[0] === $id) {
        $venta = $v;
        break;
    }
}

if (!$venta) {
    die('Venta no encontrada');
}

$detalles = json_decode($venta[6], true);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle de Pedido</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body class="dark-theme">
    <header>
        <h1>Detalle de Pedido #<?= substr($id, 0, 8) ?></h1>
        <a href="panel.php" class="back">Volver</a>
    </header>
    
    <div class="detail-container">
        <div class="client-info">
            <h2>Datos del Cliente</h2>
            <p><strong>Nombre:</strong> <?= $detalles['cliente']['nombre'] ?></p>
            <p><strong>Teléfono:</strong> <a href="https://wa.me/<?= $detalles['cliente']['telefono'] ?>"><?= $detalles['cliente']['telefono'] ?></a></p>
            <!-- Agrega más campos según necesites -->
        </div>
        
        <div class="products-info">
            <h2>Productos</h2>
            <ul>
                <?php foreach ($detalles['productos'] as $producto): ?>
                <li><?= $producto['nombre'] ?> - $<?= number_format($producto['precio'], 2) ?></li>
                <?php endforeach; ?>
            </ul>
            <p class="total"><strong>Total:</strong> $<?= number_format($venta[5], 2) ?></p>
        </div>
    </div>
</body>
</html>
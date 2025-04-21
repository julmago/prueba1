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
$raw_message = htmlspecialchars($detalles['raw_message']);
$fecha = $venta[1];
$total = number_format(floatval($venta[5]), 2);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle de Pedido</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
</head>
<body class="dark-theme">
    <header class="header">
        <h1>Detalle del Pedido #<?= substr($id, 0, 8) ?></h1>
        <a href="panel.php" class="back-btn">Volver al listado</a>
    </header>
    
    <div class="detail-container">
        <div class="detail-box client-details">
            <h2>Datos del Cliente</h2>
            <pre><?= $raw_message ?></pre>
        </div>
        
        <div class="detail-box order-details">
            <h2>Detalles del Pedido</h2>
            <p><strong>Fecha:</strong> <?= $fecha ?></p>
            <p><strong>ID:</strong> <?= $id ?></p>
            <p><strong>IP:</strong> <?= $detalles['ip'] ?></p>
            
            <div class="total-display">
                Total: $<?= $total ?>
            </div>
        </div>
    </div>
</body>
</html>
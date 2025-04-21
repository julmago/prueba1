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

if (!$venta) die('Venta no encontrada');

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
        <h1>Pedido #<?= substr($id, 0, 8) ?></h1>
        <a href="panel.php" class="back">Volver</a>
    </header>
    
    <div class="detail-container">
        <div class="client-info">
            <h2>Datos del Cliente</h2>
            <pre><?= htmlspecialchars($detalles['raw_message']) ?></pre>
        </div>
        
        <div class="meta-info">
            <h2>Información Adicional</h2>
            <p><strong>Fecha:</strong> <?= $venta[1] ?></p>
            <p><strong>IP:</strong> <?= $detalles['ip'] ?></p>
            <p class="total"><strong>Total:</strong> $<?= number_format($venta[5], 2) ?></p>
        </div>
    </div>
</body>
</html>
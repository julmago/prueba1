<?php
session_start();
if (!isset($_SESSION['admin_logged'])) {
    header('Location: index.php');
    exit;
}

$ventas = array_map('str_getcsv', file('../ventas.csv'));
usort($ventas, function($a, $b) {
    return strtotime($b[1]) - strtotime($a[1]); // Ordenar por fecha (más nuevas primero)
});
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Ventas</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body class="dark-theme">
    <header>
        <h1>Ventas Realizadas</h1>
        <a href="logout.php" class="logout">Salir</a>
    </header>
    
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Fecha</th>
                    <th>Cliente</th>
                    <th>WhatsApp</th>
                    <th>Productos</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($ventas as $venta): ?>
                <tr onclick="window.location='detalle_pedido.php?id=<?= $venta[0] ?>'">
                    <td><?= substr($venta[0], 0, 8) ?></td>
                    <td><?= $venta[1] ?></td>
                    <td><?= $venta[2] ?></td>
                    <td><a href="https://wa.me/<?= $venta[3] ?>" target="_blank">Contactar</a></td>
                    <td><?= $venta[4] ?></td>
                    <td>$<?= number_format($venta[5], 2) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
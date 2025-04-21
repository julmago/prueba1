<?php
session_start();
if (!isset($_SESSION['admin_logged'])) {
    header('Location: index.php');
    exit;
}

$ventas = array_map('str_getcsv', file('../ventas.csv'));
// Eliminar encabezados si existen
if (isset($ventas[0][0]) && $ventas[0][0] === 'ID') {
    array_shift($ventas);
}

// Ordenar por fecha (más recientes primero)
usort($ventas, function($a, $b) {
    return strtotime($b[1]) - strtotime($a[1]);
});
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Ventas</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
</head>
<body class="dark-theme">
    <header class="header">
        <h1>Ventas Realizadas</h1>
        <a href="logout.php" class="logout">Cerrar Sesión</a>
    </header>
    
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Cliente</th>
                    <th>Contacto</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($ventas as $venta): 
                    $detalles = json_decode($venta[6], true);
                    $nombre_completo = htmlspecialchars($venta[2]);
                    $whatsapp = htmlspecialchars($venta[3]);
                    $total = number_format(floatval($venta[5]), 2);
                ?>
                <tr onclick="window.location='detalle_pedido.php?id=<?= $venta[0] ?>'">
                    <td><?= substr($venta[0], 0, 8) ?></td>
                    <td><?= $nombre_completo ?></td>
                    <td onclick="event.stopPropagation()">
                        <a href="https://wa.me/<?= $whatsapp ?>" class="whatsapp-link" target="_blank">
                            <?= $whatsapp ?>
                        </a>
                    </td>
                    <td>$<?= $total ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
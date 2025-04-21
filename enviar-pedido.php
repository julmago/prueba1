<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Verificar si el pedido ya fue enviado
    if (isset($_SESSION['pedido_enviado']) && $_SESSION['pedido_enviado'] === true) {
        echo "<h2>Este pedido ya fue enviado anteriormente.</h2>";
        echo "<a href='https://tsmayorista.com.ar' style='display: inline-block; margin-top: 20px; padding: 10px 20px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 5px;'>Volver al sitio</a>";
        exit();
    }

    $para_principal = "julmago@gmail.com";
    $para_copia_oculta = "julmago@hotmail.com";
    $asunto = "Nuevo Pedido desde el sitio";
    $mensaje = isset($_POST["pedido"]) ? $_POST["pedido"] : "Sin datos.";

    // ==============================================
    // SECCIÓN NUEVA: Guardar en ventas.csv
    // ==============================================
    $fecha = date('Y-m-d H:i:s');
    $id = uniqid();
    
    // Extraer datos del cliente (ejemplo: "Nombre: Juan Pérez\nTeléfono: 123456789")
    $cliente = "No especificado";
    $whatsapp = "No especificado";
    $productos_resumen = "Productos no procesados";
    $total = "0";

    // Parsear el mensaje para extraer datos clave (ajusta según tu formato real)
    $lineas = explode("\n", $mensaje);
    foreach ($lineas as $linea) {
        if (strpos($linea, 'Nombre:') !== false) {
            $cliente = trim(str_replace('Nombre:', '', $linea));
        }
        if (strpos($linea, 'Teléfono:') !== false) {
            $whatsapp = preg_replace('/[^0-9]/', '', trim(str_replace('Teléfono:', '', $linea)));
        }
        if (strpos($linea, 'Total:') !== false) {
            $total = trim(str_replace('Total:', '', $linea));
            $total = preg_replace('/[^0-9.]/', '', $total);
        }
    }

    // Guardar datos completos en JSON
    $detalles = json_encode([
        'raw_message' => $mensaje,
        'fecha' => $fecha,
        'ip' => $_SERVER['REMOTE_ADDR']
    ]);

    // Crear línea CSV
    $venta = [
        $id,
        $fecha,
        $cliente,
        $whatsapp,
        $productos_resumen,
        $total,
        $detalles
    ];

    // Guardar en archivo (si no existe, se crea con headers)
    $archivo_csv = 'ventas.csv';
    $file = fopen($archivo_csv, 'a');
    
    if (filesize($archivo_csv) == 0) {
        fputcsv($file, ['ID', 'Fecha', 'Cliente', 'WhatsApp', 'Productos', 'Total', 'Detalles']);
    }
    
    fputcsv($file, $venta);
    fclose($file);
    // ==============================================
    // FIN DE SECCIÓN NUEVA
    // ==============================================

    // Enviar emails (código original)
    $cabeceras = "From: pedidos@tuseduccion.com.ar\r\n" .
                 "Reply-To: pedidos@tuseduccion.com.ar\r\n" .
                 "Content-Type: text/plain; charset=UTF-8\r\n";

    $enviado_principal = mail($para_principal, $asunto, $mensaje, $cabeceras);
    $enviado_copia = mail($para_copia_oculta, $asunto, $mensaje, $cabeceras);

    if ($enviado_principal || $enviado_copia) {
        $_SESSION['pedido_enviado'] = true;
        echo "<h2>¡Gracias! Tu pedido fue enviado correctamente.</h2>";
        echo "<a href='https://tsmayorista.com.ar' style='display: inline-block; margin-top: 20px; padding: 10px 20px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 5px;'>Volver al sitio</a>";
    } else {
        echo "<h2>Ocurrió un error al enviar el pedido. Por favor intenta nuevamente.</h2>";
        echo "<a href='https://tsmayorista.com.ar' style='display: inline-block; margin-top: 20px; padding: 10px 20px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 5px;'>Volver al sitio</a>";
    }
} else {
    echo "Acceso no válido.";
}
?>
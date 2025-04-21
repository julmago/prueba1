<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Verificar si el pedido ya fue enviado (solo si se recarga esta página)
    if (isset($_SESSION['pedido_enviado']) && $_SESSION['pedido_enviado'] === true) {
        echo "<h2>Este pedido ya fue procesado. Por favor no actualices esta página.</h2>";
        echo "<a href='https://tsmayorista.com.ar' style='display: inline-block; margin-top: 20px; padding: 10px 20px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 5px;'>Volver al sitio</a>";
        exit();
    }

    $para_principal = "julmago@gmail.com";
    $para_copia_oculta = "julmago@hotmail.com";
    $asunto = "Nuevo Pedido desde el sitio";
    $mensaje = isset($_POST["pedido"]) ? $_POST["pedido"] : "Sin datos.";

    // ==============================================
    // PROCESAMIENTO PARA CSV
    // ==============================================
    $fecha = date('Y-m-d H:i:s');
    $id = uniqid();
    
    // Inicializar datos
    $cliente = "No especificado";
    $whatsapp = "No especificado";
    $total = "0";
    
    // Parsear datos del mensaje (formato esperado: "Nombre: Juan Pérez\nTeléfono: 01112345678\n...")
    $lineas = explode("\n", $mensaje);
    foreach ($lineas as $linea) {
        if (strpos($linea, 'Nombre:') !== false) {
            $cliente = trim(str_replace('Nombre:', '', $linea));
        }
        if (strpos($linea, 'Teléfono:') !== false) {
            $whatsapp = trim(str_replace('Teléfono:', '', $linea));
            // Limpiar número (eliminar todo excepto dígitos)
            $whatsapp = preg_replace('/[^0-9]/', '', $whatsapp);
        }
        if (strpos($linea, 'Total:') !== false) {
            $total = trim(str_replace('Total:', '', $linea));
            // Limpiar formato de precio
            $total = preg_replace('/[^0-9.]/', '', $total);
        }
    }

    // Guardar detalles completos en JSON
    $detalles = json_encode([
        'raw_message' => $mensaje,
        'timestamp' => $fecha,
        'ip' => $_SERVER['REMOTE_ADDR']
    ], JSON_UNESCAPED_UNICODE);

    // Crear array con los datos de la venta
    $venta = [
        $id,
        $fecha,
        $cliente,
        $whatsapp,
        "", // Campo de productos vacío (ya no se usa)
        $total,
        $detalles
    ];
    
    // Guardar en archivo CSV
    $archivo_csv = 'ventas.csv';
    $file = fopen($archivo_csv, 'a');
    
    // Si el archivo está vacío, agregar encabezados
    if (filesize($archivo_csv) == 0) {
        fputcsv($file, ['ID', 'Fecha', 'Cliente', 'WhatsApp', 'Productos', 'Total', 'Detalles']);
    }
    
    fputcsv($file, $venta);
    fclose($file);
    // ==============================================
    // FIN DE PROCESAMIENTO CSV
    // ==============================================

    // Enviar emails
    $cabeceras = "From: pedidos@tuseduccion.com.ar\r\n" .
                 "Reply-To: pedidos@tuseduccion.com.ar\r\n" .
                 "Content-Type: text/plain; charset=UTF-8\r\n";

    $enviado_principal = mail($para_principal, $asunto, $mensaje, $cabeceras);
    $enviado_copia = mail($para_copia_oculta, $asunto, $mensaje, $cabeceras);

    if ($enviado_principal || $enviado_copia) {
        $_SESSION['pedido_enviado'] = true; // Marcar como enviado SOLO para esta sesión
        echo "<div style='text-align:center; padding:20px;'>";
        echo "<h2>¡Pedido enviado con éxito!</h2>";
        echo "<p>Hemos recibido tu pedido correctamente.</p>";
        echo "<a href='https://tsmayorista.com.ar' style='display: inline-block; margin-top: 20px; padding: 10px 20px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 5px;'>Volver al sitio</a>";
        echo "</div>";
    } else {
        echo "<h2>Error al enviar el pedido</h2>";
        echo "<p>Por favor intenta nuevamente o contáctanos directamente.</p>";
        echo "<a href='https://tsmayorista.com.ar' style='display: inline-block; margin-top: 20px; padding: 10px 20px; background-color: #f44336; color: white; text-decoration: none; border-radius: 5px;'>Volver al sitio</a>";
    }
} else {
    // Si alguien intenta acceder directamente al archivo
    header("Location: https://tsmayorista.com.ar");
    exit();
}
?>
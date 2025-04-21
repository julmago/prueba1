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
    $para_copia_oculta = "julmago@hotmail.com";  // Correo oculto
    $asunto = "Nuevo Pedido desde el sitio";
    $mensaje = isset($_POST["pedido"]) ? $_POST["pedido"] : "Sin datos.";

    // ==============================================
    // NUEVO: Procesamiento para guardar en CSV
    // ==============================================
    $fecha = date('Y-m-d H:i:s');
    $id = uniqid();
    
    // Parsear datos del pedido (asumiendo que viene en formato texto)
    $lineas = explode("\n", $mensaje);
    $cliente = "No especificado";
    $whatsapp = "No especificado";
    $productos_resumen = "";
    $total = "0";
    
    // Extraer información importante del mensaje
    foreach ($lineas as $linea) {
        if (strpos($linea, 'Nombre:') !== false) {
            $cliente = trim(str_replace('Nombre:', '', $linea));
        }
        if (strpos($linea, 'Teléfono:') !== false) {
            $whatsapp = trim(str_replace('Teléfono:', '', $linea));
            // Limpiar número para WhatsApp
            $whatsapp = preg_replace('/[^0-9]/', '', $whatsapp);
        }
        if (strpos($linea, 'Total:') !== false) {
            $total = trim(str_replace('Total:', '', $linea));
            // Limpiar formato de precio
            $total = preg_replace('/[^0-9.]/', '', $total);
        }
        // Detectar líneas de productos (ajusta según tu formato)
        if (strpos($linea, 'x ') !== false && strpos($linea, '$') !== false) {
            $productos_resumen .= trim($linea) . ", ";
        }
    }
    
    $productos_resumen = rtrim($productos_resumen, ", ");
    
    // Guardar detalles completos en formato JSON
    $detalles = json_encode([
        'raw_message' => $mensaje,
        'timestamp' => $fecha,
        'ip' => $_SERVER['REMOTE_ADDR']
    ]);
    
    // Crear array con los datos de la venta
    $venta = [
        $id,
        $fecha,
        $cliente,
        $whatsapp,
        $productos_resumen,
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
    // FIN de la nueva sección para CSV
    // ==============================================

    // Cabeceras para el correo principal (julmago@gmail.com)
    $cabeceras_principal = "From: pedidos@tuseduccion.com.ar\r\n";
    $cabeceras_principal .= "Reply-To: pedidos@tuseduccion.com.ar\r\n";
    $cabeceras_principal .= "Content-Type: text/plain; charset=UTF-8\r\n";

    // Cabeceras para la copia oculta (julmago@hotmail.com)
    $cabeceras_copia = "From: pedidos@tuseduccion.com.ar\r\n";
    $cabeceras_copia .= "Reply-To: pedidos@tuseduccion.com.ar\r\n";
    $cabeceras_copia .= "Content-Type: text/plain; charset=UTF-8\r\n";

    // Enviar al correo principal (sin referencia al otro correo)
    $enviado_principal = mail($para_principal, $asunto, $mensaje, $cabeceras_principal);

    // Enviar copia oculta (independiente, no aparece en el primer correo)
    $enviado_copia = mail($para_copia_oculta, $asunto, $mensaje, $cabeceras_copia);

    if ($enviado_principal || $enviado_copia) {
        $_SESSION['pedido_enviado'] = true; // Evitar reenvío
        echo "<h2>¡Gracias! Tu pedido fue enviado correctamente.</h2>";
        echo "<a href='https://tsmayorista.com.ar' style='display: inline-block; margin-top: 20px; padding: 10px 20px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 5px;'>Volver al sitio</a>";
    } else {
        echo "<h2>Ocurrió un error al enviar el pedido. Probá de nuevo más tarde.</h2>";
        echo "<a href='https://tsmayorista.com.ar' style='display: inline-block; margin-top: 20px; padding: 10px 20px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 5px;'>Volver al sitio</a>";
    }
} else {
    echo "Acceso no válido.";
    echo "<a href='https://tsmayorista.com.ar' style='display: inline-block; margin-top: 20px; padding: 10px 20px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 5px;'>Volver al sitio</a>";
}
?>
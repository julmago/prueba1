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
<?php
// Recoge los datos del formulario
$nombre_y_apellidos = htmlspecialchars($_POST['nombre_y_apellidos']);
$pais = htmlspecialchars($_POST['pais']);
$email = htmlspecialchars($_POST['email']);
$telefono = htmlspecialchars($_POST['telefono']);
$quequieresestudiar = htmlspecialchars($_POST['quequieresestudiar']);

// Configuración del correo
$to = 'kareilydominguez48@gmail.com';
$subject = 'Nuevo registro de formulario';
$message = "Nombre y Apellidos: $nombre_y_apellidos\n";
$message .= "País: $pais\n";
$message .= "Email: $email\n";
$message .= "Teléfono: $telefono\n";
$message .= "Curso de interés: $quequieresestudiar\n";

// Encabezados del correo
$headers = 'From: ' . $email . "\r\n" .
           'Reply-To: ' . $email . "\r\n" .
           'X-Mailer: PHP/' . phpversion();

// Envía el correo
if (mail($to, $subject, $message, $headers)) {
    echo '<div id="mensajeExito" class="mensaje-exito">¡Los datos se han enviado con éxito!</div>';
} else {
    echo '<div id="mensajeExito" class="mensaje-exito">Hubo un problema al enviar el formulario. Por favor, inténtalo de nuevo.</div>';
}
?>

<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Incluir los archivos de PHPMailer
require 'PHPMailer-6.9.1/src/PHPMailer.php';
require 'PHPMailer-6.9.1/src/SMTP.php';
require 'PHPMailer-6.9.1/src/Exception.php';

// Cambiar el valor de las variables según tus credenciales y configuración
$host = 'localhost'; // O el nombre de tu servidor de base de datos
$dbname = 'usuarios';
$username = 'root';
$password = '';

try {
    // Conectar a la base de datos
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Obtener el código del formulario
    $codigo = $_POST['codigo'];

    // Preparar la consulta para buscar el correo electrónico
    $stmt = $pdo->prepare("SELECT correo FROM usuarios WHERE codigo = :codigo");
    $stmt->bindParam(':codigo', $codigo);
    $stmt->execute();

    // Obtener el correo electrónico
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $correo = $user['correo'];

        // Generar un token único para el cambio de contraseña
        $token = bin2hex(random_bytes(16));

        // Guardar el token en la base de datos para verificarlo más tarde
        $stmt = $pdo->prepare("INSERT INTO tokens (codigo, token) VALUES (:codigo, :token)");
        $stmt->bindParam(':codigo', $codigo);
        $stmt->bindParam(':token', $token);
        $stmt->execute();

        // Configuración del correo
        $mail = new PHPMailer(true);

        try {
            // Configurar el servidor SMTP
            $mail->Host = 'smtp.office365.com';
            $mail->SMTPAuth = true;
            $mail->Username = '1507631'; // Tu dirección de correo de Outlook
            $mail->Password = 'tu_contraseña'; // Tu contraseña de Outlook
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;
            
            // Destinatario y contenido
            $mail->setFrom('no-reply@tu-sitio-web.com', 'No Reply');
            $mail->addAddress($correo);
            $mail->Subject = 'Cambio de Contraseña';
            $mail->Body    = 'Haz clic en el siguiente enlace para cambiar tu contraseña: ';
            $mail->Body   .= 'https://tu-sitio-web.com/cambiar_contrasena_formulario.php?token=' . $token;

            // Enviar el correo
            $mail->send();
            echo 'Se ha enviado un enlace a tu correo electrónico para cambiar tu contraseña.';
        } catch (Exception $e) {
            echo "El correo no se pudo enviar. Error: {$mail->ErrorInfo}";
        }
    } else {
        echo 'ID no encontrado.';
    }
} catch (PDOException $e) {
    echo 'Error: ' . $e->getMessage();
}
?>




<!-- HTML fuera del PHP -->
<a href="aula_virtual.php" class="button">Ir a Aula Virtual</a>

<!-- CSS para el botón -->
<style>
    .button {
        text-decoration: none;
        color: #fff;
        background-color: #007bff; /* Blue */
        padding: 10px 20px;
        border-radius: 5px;
        display: inline-block;
        font-size: 16px;
        transition: background-color 0.3s ease;
    }

    .button:hover {
        background-color: #0056b3; /* Darker Blue */
    }
</style>
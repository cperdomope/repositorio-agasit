<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recoger datos del formulario
    $nombre = htmlspecialchars($_POST['Nombre']);
    $correo = htmlspecialchars($_POST['Correo']);
    $telefono = htmlspecialchars($_POST['Teléfono']);
    $asunto = htmlspecialchars($_POST['Asunto']);
    $mensaje = htmlspecialchars($_POST['Mensaje']);

    // Crear instancia de PHPMailer
    $mail = new PHPMailer(true);

    try {
        // Configuración del servidor SMTP
        $mail->isSMTP();
        $mail->Host = 'mail.agasit.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'prueba@agasit.com';
        $mail->Password = 'Charly2024**..';
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;
        // Configurar la codificación del correo a UTF-8
        $mail->CharSet = 'UTF-8';

        // Configuración de los destinatarios
        $mail->setFrom('prueba@agasit.com', 'Cliente Agasit SAS');
        $mail->addAddress('agasitsas@gmail.com'); // Destinatario principal
       // $mail->addCC(''); // Destinatario en copia

        // Configuración del correo
        $mail->isHTML(true);
        $mail->Subject = "Nuevo mensaje de cliente: $asunto";
        $mail->Body    = "
            <h2>Nuevo mensaje de contacto</h2>
            <p><strong>Nombre:</strong> $nombre</p>
            <p><strong>Correo:</strong> $correo</p>
            <p><strong>Teléfono:</strong> $telefono</p>
            <p><strong>Asunto:</strong> $asunto</p>
            <p><strong>Mensaje:</strong> $mensaje</p>
        ";

        $mail->send();
        echo '<h2>El mensaje ha sido enviado con éxito, gracias por contactarnos, Pronto nos comunicaremos contigo!!!.</h2>';
        echo '<p><a href="https://agasit.com">Volver a la página de inicio</a></p>';
    } catch (Exception $e) {
        echo "Error al enviar el mensaje: {$mail->ErrorInfo}";
    }
} else {
    echo "Método de solicitud no permitido.";
}
?>

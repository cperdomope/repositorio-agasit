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

        // Configuración de los destinatarios
        $mail->setFrom('prueba@agasit.com', 'Agasit SAS');
        $mail->addAddress('perdomocarlos081@gmail.com'); // Destinatario principal
        $mail->addCC('sharonpt2007@gmail.com'); // Destinatario en copia

        // Configuración del correo
        $mail->isHTML(true);
        $mail->Subject = "Nuevo mensaje de contacto: $asunto";
        $mail->Body    = "
            <h2>Nuevo mensaje de contacto</h2>
            <p><strong>Nombre:</strong> $nombre</p>
            <p><strong>Correo:</strong> $correo</p>
            <p><strong>Teléfono:</strong> $telefono</p>
            <p><strong>Asunto:</strong> $asunto</p>
            <p><strong>Mensaje:</strong> $mensaje</p>
        ";

        $mail->send();
        echo 'El mensaje ha sido enviado correctamente';
    } catch (Exception $e) {
        echo "Error al enviar el mensaje: {$mail->ErrorInfo}";
    }
} else {
    echo "Método de solicitud no permitido.";
}
?>

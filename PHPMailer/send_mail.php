<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Cargar las clases de PHPMailer
require 'path/to/PHPMailer/src/Exception.php';
require 'path/to/PHPMailer/src/PHPMailer.php';
require 'path/to/PHPMailer/src/SMTP.php';

// Recibir datos del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $phone = htmlspecialchars($_POST['phone']);
    $subject = htmlspecialchars($_POST['subject']);
    $message = htmlspecialchars($_POST['message']);

    // Configuración de PHPMailer
    $mail = new PHPMailer(true);

    try {
        // Configurar servidor SMTP
        $mail->isSMTP();
        $mail->Host = 'mail.agasit.com';  // Servidor SMTP
        $mail->SMTPAuth = true;
        $mail->Username = 'prueba@agasit.com'; // Tu correo electrónico
        $mail->Password = 'Charly2024**..'; // Tu contraseña de correo
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // Encriptación SSL/TLS
        $mail->Port = 465; // Puerto SMTP

        // Configurar correo
        $mail->setFrom($email, $name);
        $mail->addAddress('prueba@agasit.com'); // Reemplaza con tu dirección de correo

        // Contenido del correo
        $mail->isHTML(true); // Establecer el formato del correo a HTML
        $mail->Subject = "Nuevo mensaje de contacto: $subject";
        $mail->Body    = "Has recibido un nuevo mensaje de contacto.<br><br>".
                         "<b>Nombre:</b> $name<br>".
                         "<b>Email:</b> $email<br>".
                         "<b>Teléfono:</b> $phone<br>".
                         "<b>Asunto:</b> $subject<br><br>".
                         "<b>Mensaje:</b><br>$message";

        // Enviar correo
        $mail->send();
        echo json_encode(['status' => 'success', 'message' => 'Correo enviado con éxito']);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'No se pudo enviar el correo. Error: ' . $mail->ErrorInfo]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Método no permitido']);
}
?>




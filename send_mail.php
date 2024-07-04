<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Requiere los archivos de PHPMailer
require 'path/to/PHPMailer/src/Exception.php';
require 'path/to/PHPMailer/src/PHPMailer.php';
require 'path/to/PHPMailer/src/SMTP.php';

// Verifica si la solicitud es POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $phone = filter_var($_POST['phone'], FILTER_SANITIZE_STRING);
    $subject = filter_var($_POST['subject'], FILTER_SANITIZE_STRING);
    $message = filter_var($_POST['message'], FILTER_SANITIZE_STRING);

    // Configura el correo electrónico destinatario y el asunto
    $to = 'perdomocarlos081@gmail.com';
    $email_subject = "Nuevo mensaje de contacto: $subject";

    // Instancia PHPMailer
    $mail = new PHPMailer(true);

    try {
        // Configuración del servidor SMTP
        $mail->isSMTP();
        $mail->Host = 'mail.agasit.com'; // Cambia a tu servidor SMTP
        $mail->SMTPAuth = true;
        $mail->Username = 'prueba@agasit.com'; // Tu usuario SMTP
        $mail->Password = 'Charly2024**..'; // Tu contraseña SMTP
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Usa TLS
        $mail->Port = 465; // Puerto ssl

        // Configuración del correo
        $mail->setFrom($email, $name);
        $mail->addAddress($to);
        $mail->Subject = $email_subject;
        $mail->Body = "Nombre: $name\n".
                      "Email: $email\n".
                      "Teléfono: $phone\n".
                      "Asunto: $subject\n\n".
                      "Mensaje:\n$message";
        $mail->AltBody = "Nombre: $name\n".
                         "Email: $email\n".
                         "Teléfono: $phone\n".
                         "Asunto: $subject\n\n".
                         "Mensaje:\n$message";

        // Envía el correo
        $mail->send();
        echo json_encode(['status' => 'success', 'message' => 'Correo enviado con éxito']);
    } catch (Exception $e) {
        // Error al enviar el correo
        error_log("Error al enviar el correo: " . $mail->ErrorInfo);
        echo json_encode(['status' => 'error', 'message' => 'No se pudo enviar el correo']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Método no permitido']);
}
?>



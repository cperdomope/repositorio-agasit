<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener y sanitizar los datos del formulario
    $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $phone = filter_var($_POST['phone'], FILTER_SANITIZE_STRING);
    $subject = filter_var($_POST['subject'], FILTER_SANITIZE_STRING);
    $message = filter_var($_POST['message'], FILTER_SANITIZE_STRING);

    // Dirección de correo a la que se enviará el mensaje
    $to = 'perdomocarlos081@gmail.com'; // Reemplaza con tu dirección de correo

    // Asunto del correo
    $email_subject = "Nuevo mensaje de contacto: $subject";

    // Cuerpo del correo
    $email_body = "Has recibido un nuevo mensaje de contacto.\n\n".
                  "Nombre: $name\n".
                  "Email: $email\n".
                  "Teléfono: $phone\n".
                  "Asunto: $subject\n\n".
                  "Mensaje:\n$message";

    // Cabeceras del correo
    $headers = "From: $email\r\n";
    $headers .= "Reply-To: $email\r\n";

    // Enviar el correo
    if (mail($to, $email_subject, $email_body, $headers)) {
        echo json_encode(['status' => 'success', 'message' => 'Correo enviado con éxito']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No se pudo enviar el correo']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Método no permitido']);
}
?>

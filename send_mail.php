<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $phone = htmlspecialchars($_POST['phone']);
    $subject = htmlspecialchars($_POST['subject']);
    $message = htmlspecialchars($_POST['message']);

    // Dirección de correo a la que se enviará el mensaje
    $to = 'tucorreo@ejemplo.com'; // Reemplaza con tu dirección de correo

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

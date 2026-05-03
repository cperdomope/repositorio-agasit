<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Cabeceras de seguridad HTTP (se envían antes de cualquier salida)
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('Referrer-Policy: strict-origin-when-cross-origin');

// Solo aceptar POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Método no permitido.');
}

// Cargar configuración desde fuera del document root
$configPath = dirname(__DIR__) . '/app_config.php';
if (!file_exists($configPath)) {
    http_response_code(500);
    error_log('[AGA] app_config.php no encontrado en: ' . $configPath);
    exit('<p class="text-danger">Error de configuración del servidor. Contacte al administrador.</p>');
}
$config = require $configPath;

// Cargar PHPMailer
require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

// --- Validación y sanitización de entradas ---
$nombre   = filter_input(INPUT_POST, 'Nombre',    FILTER_DEFAULT);
$correo   = filter_input(INPUT_POST, 'Correo',    FILTER_VALIDATE_EMAIL);
$telefono = filter_input(INPUT_POST, 'Teléfono',  FILTER_DEFAULT);
$asunto   = filter_input(INPUT_POST, 'Asunto',    FILTER_DEFAULT);
$mensaje  = filter_input(INPUT_POST, 'Mensaje',   FILTER_DEFAULT);

$nombre   = $nombre   ? mb_substr(strip_tags($nombre),   0, 100)  : '';
$telefono = $telefono ? mb_substr(strip_tags($telefono), 0, 20)   : '';
$asunto   = $asunto   ? mb_substr(strip_tags($asunto),   0, 150)  : '';
$mensaje  = $mensaje  ? mb_substr(strip_tags($mensaje),  0, 2000) : '';

if (!$correo || $nombre === '' || $mensaje === '') {
    http_response_code(400);
    exit('<div class="alert alert-warning" role="alert">Por favor complete todos los campos requeridos con información válida.</div>');
}

// --- Rate limiting simple (máx. 3 envíos por hora por sesión) ---
session_start();
$ahora = time();
$_SESSION['contacto_intentos'] = array_values(array_filter(
    $_SESSION['contacto_intentos'] ?? [],
    static fn($t) => ($ahora - $t) < 3600
));

if (count($_SESSION['contacto_intentos']) >= 3) {
    http_response_code(429);
    exit('<div class="alert alert-warning" role="alert">Demasiados intentos. Por favor espere una hora antes de enviar otro mensaje.</div>');
}

// --- Envío del correo ---
$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host       = $config['smtp_host'];
    $mail->SMTPAuth   = true;
    $mail->Username   = $config['smtp_user'];
    $mail->Password   = $config['smtp_pass'];
    $mail->SMTPSecure = $config['smtp_secure'];
    $mail->Port       = $config['smtp_port'];
    $mail->CharSet    = 'UTF-8';

    $mail->setFrom($config['mail_from'], $config['mail_from_name']);
    $mail->addAddress($config['mail_to']);
    $mail->addReplyTo($correo, $nombre);

    $mail->isHTML(true);
    $mail->Subject = 'Nuevo contacto web: ' . htmlspecialchars($asunto, ENT_QUOTES, 'UTF-8');
    $mail->Body = '
        <h2 style="color:#0c1a96;">Nuevo mensaje de contacto</h2>
        <p><strong>Nombre:</strong> '   . htmlspecialchars($nombre,   ENT_QUOTES, 'UTF-8') . '</p>
        <p><strong>Correo:</strong> '   . htmlspecialchars($correo,   ENT_QUOTES, 'UTF-8') . '</p>
        <p><strong>Teléfono:</strong> ' . htmlspecialchars($telefono, ENT_QUOTES, 'UTF-8') . '</p>
        <p><strong>Asunto:</strong> '   . htmlspecialchars($asunto,   ENT_QUOTES, 'UTF-8') . '</p>
        <p><strong>Mensaje:</strong><br>' . nl2br(htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8')) . '</p>
    ';
    $mail->AltBody = "Nombre: $nombre\nCorreo: $correo\nTeléfono: $telefono\nAsunto: $asunto\nMensaje:\n$mensaje";

    $mail->send();

    $_SESSION['contacto_intentos'][] = $ahora;

    echo '<div class="alert alert-success" role="alert">
        <strong>¡Mensaje enviado con éxito!</strong> Gracias por contactarnos. Pronto nos comunicaremos contigo.
        <br><a href="/" class="alert-link">Volver al inicio</a>
    </div>';

} catch (Exception $e) {
    error_log('[AGA] Error PHPMailer: ' . $mail->ErrorInfo);
    http_response_code(500);
    echo '<div class="alert alert-danger" role="alert">
        Ocurrió un error al enviar el mensaje. Por favor escríbanos directamente a
        <strong>agasitsas@gmail.com</strong>.
    </div>';
}

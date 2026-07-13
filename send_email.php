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

// --- Verificación de origen (mitiga CSRF y envíos automatizados directos al endpoint) ---
$origenHeader = $_SERVER['HTTP_ORIGIN'] ?? $_SERVER['HTTP_REFERER'] ?? '';
$origenHost   = $origenHeader !== '' ? parse_url($origenHeader, PHP_URL_HOST) : null;
if ($origenHost === null || strcasecmp($origenHost, $_SERVER['HTTP_HOST']) !== 0) {
    http_response_code(403);
    error_log('[AGA] Origen no permitido: ' . $origenHeader);
    exit('Origen no permitido.');
}

// Cargar configuración desde fuera del document root
$configPath = dirname(__DIR__) . '/app_config.php';
if (!file_exists($configPath)) {
    http_response_code(500);
    error_log('[AGA] app_config.php no encontrado en: ' . $configPath);
    exit('Error de configuración del servidor. Contacte al administrador.');
}
$config = require $configPath;

$claveConfigRequerida = ['smtp_host', 'smtp_user', 'smtp_pass', 'smtp_port', 'smtp_secure', 'mail_from', 'mail_from_name', 'mail_to'];
foreach ($claveConfigRequerida as $clave) {
    if (!isset($config[$clave]) || $config[$clave] === '') {
        http_response_code(500);
        error_log('[AGA] app_config.php incompleto: falta la clave "' . $clave . '"');
        exit('Error de configuración del servidor. Contacte al administrador.');
    }
}

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
    header('Location: /contacto.html?error=1');
    exit;
}

// --- Rate limiting por IP en disco (máx. 3 envíos por hora) ---
// No depende de cookies/sesión: a diferencia de un límite basado en $_SESSION,
// no se evade simplemente descartando la cookie entre peticiones.
$ahora = time();
$ip = $_SERVER['REMOTE_ADDR'] ?? 'desconocido';
$rateLimitFile = sys_get_temp_dir() . '/aga_contacto_' . md5($ip) . '.json';

$intentos = [];
if (is_readable($rateLimitFile)) {
    $intentos = json_decode(file_get_contents($rateLimitFile), true) ?: [];
}
$intentos = array_values(array_filter($intentos, static fn($t) => ($ahora - $t) < 3600));

if (count($intentos) >= 3) {
    http_response_code(429);
    header('Location: /contacto.html?limite=1');
    exit;
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

    $intentos[] = $ahora;
    file_put_contents($rateLimitFile, json_encode($intentos), LOCK_EX);

    header('Location: /contacto.html?enviado=1');
    exit;

} catch (Exception $e) {
    error_log('[AGA] Error PHPMailer: ' . $mail->ErrorInfo);
    header('Location: /contacto.html?error=1');
    exit;
}

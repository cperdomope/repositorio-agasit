<?php
/**
 * INSTRUCCIONES DE DESPLIEGUE (leer antes de subir al servidor):
 *
 * 1. Copiar este archivo al servidor cPanel como:
 *       /home/agasitco/app_config.php
 *    (UN nivel ARRIBA de public_html, nunca dentro de él)
 *
 * 2. Rellenar los valores reales de las credenciales SMTP.
 *
 * 3. Verificar que la ruta desde send_email.php sea correcta:
 *       dirname(__DIR__) apunta a /home/agasitco/
 *
 * 4. Nunca agregar app_config.php al repositorio Git.
 *    Ya está en .gitignore como precaución adicional.
 */

return [
    'smtp_host'      => 'mail.agasit.com',
    'smtp_user'      => 'usuario@agasit.com',       // Reemplazar con usuario real
    'smtp_pass'      => '',                          // Reemplazar con contraseña real
    'smtp_port'      => 465,
    'smtp_secure'    => 'ssl',
    'mail_from'      => 'usuario@agasit.com',        // Reemplazar
    'mail_from_name' => 'AGA Soluciones IT',
    'mail_to'        => 'agasitsas@gmail.com',
];

<?php
/**
 * Mailer.php — Clase central de envío de emails con PHPMailer + Gmail SMTP
 *
 * CONFIGURACIÓN:
 *  1. Instala PHPMailer: composer require phpmailer/phpmailer
 *  2. En Gmail → Seguridad → Verificación en 2 pasos → Contraseñas de aplicación
 *     Crea una contraseña para "Correo / Windows" y ponla en MAIL_PASS abajo.
 *  3. Ajusta las constantes MAIL_* con tus datos.
 */
 
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
 
// Autoload de Composer
require_once __DIR__ . '/../../vendor/autoload.php';
 
// ─── CONFIGURACIÓN ────────────────────────────────────────────────────────────
define('MAIL_HOST',     'smtp.gmail.com');
define('MAIL_PORT',     587);
define('MAIL_USER',     'hotelrealplaza7@gmail.com');      // ← TU EMAIL GMAIL
define('MAIL_PASS',     'hwek seoz bpwo hslm');    // ← CONTRASEÑA DE APP (16 chars)
define('MAIL_FROM',     'hotelrealplaza7@gmail.com');      // ← mismo que MAIL_USER
define('MAIL_FROMNAME', 'Hotel Real Plaza');
// ──────────────────────────────────────────────────────────────────────────────
 
class Mailer
{
    /**
     * Método base — construye y envía un email HTML.
     * Todos los métodos específicos llaman a este.
     *
     * @param string $toEmail  Destinatario
     * @param string $toName   Nombre del destinatario
     * @param string $subject  Asunto
     * @param string $body     Cuerpo HTML
     * @return bool
     */
    public static function send(string $toEmail, string $toName, string $subject, string $body): bool
    {
        $mail = new PHPMailer(true);
 
        try {
            // Servidor SMTP
            $mail->isSMTP();
            $mail->Host       = MAIL_HOST;
            $mail->SMTPAuth   = true;
            $mail->Username   = MAIL_USER;
            $mail->Password   = MAIL_PASS;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = MAIL_PORT;
            $mail->CharSet    = 'UTF-8';
 
            // Remitente y destinatario
            $mail->setFrom(MAIL_FROM, MAIL_FROMNAME);
            $mail->addAddress($toEmail, $toName);
 
            // Contenido
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $body;
            $mail->AltBody = strip_tags(str_replace(['<br>', '<br/>'], "\n", $body));
 
            $mail->send();
            return true;
 
        } catch (Exception $e) {
            error_log('Mailer Error: ' . $mail->ErrorInfo);
            return false;
        }
    }
 
    // ─────────────────────────────────────────────────────────────────────────
    // TEMPLATES DE EMAIL
    // ─────────────────────────────────────────────────────────────────────────
 
    /**
     * Email de recuperación de contraseña
     */
    public static function enviarRecuperacion(string $email, string $nombre, string $link): bool
    {
        $subject = '🔑 Recuperar contraseña — Hotel Real Plaza';
 
        $body = self::template('Recuperar Contraseña', "
            <p>Hola <strong>{$nombre}</strong>,</p>
            <p>Recibimos una solicitud para restablecer la contraseña de tu cuenta.</p>
            <p>Haz clic en el siguiente botón para crear una nueva contraseña:</p>
            <div style='text-align:center; margin: 30px 0;'>
                <a href='{$link}' style='
                    background-color: #c8a96e;
                    color: #fff;
                    padding: 14px 32px;
                    border-radius: 6px;
                    text-decoration: none;
                    font-size: 16px;
                    font-weight: bold;
                    display: inline-block;
                '>Restablecer Contraseña</a>
            </div>
            <p style='color:#888; font-size:13px;'>
                Este enlace expira en <strong>30 minutos</strong>.<br>
                Si no solicitaste esto, ignora este mensaje.
            </p>
            <hr style='border:none; border-top:1px solid #eee; margin:20px 0;'>
            <p style='font-size:12px; color:#aaa;'>
                O copia y pega este enlace en tu navegador:<br>
                <a href='{$link}' style='color:#c8a96e;'>{$link}</a>
            </p>
        ");
 
        return self::send($email, $nombre, $subject, $body);
    }
 
    /**
     * Email de bienvenida al registrarse
     */
    public static function enviarBienvenida(string $email, string $nombre): bool
    {
        $subject = '🏨 ¡Bienvenido a Hotel Real Plaza!';
 
        $body = self::template('Bienvenida', "
            <p>Hola <strong>{$nombre}</strong>, ¡bienvenido a Hotel Real Plaza!</p>
            <p>Tu cuenta ha sido creada exitosamente. Ya puedes iniciar sesión y disfrutar de todos nuestros servicios.</p>
            <div style='background:#f9f4ec; border-left:4px solid #c8a96e; padding:16px; border-radius:4px; margin:20px 0;'>
                <p style='margin:0; color:#555;'><strong>¿Qué puedes hacer ahora?</strong></p>
                <ul style='color:#555; margin:10px 0 0; padding-left:20px;'>
                    <li>Ver y reservar habitaciones disponibles</li>
                    <li>Gestionar tus reservas</li>
                    <li>Solicitar servicios adicionales</li>
                </ul>
            </div>
            <div style='text-align:center; margin:30px 0;'>
                <a href='http://localhost/hotel-app/public/login' style='
                    background-color:#c8a96e;
                    color:#fff;
                    padding:14px 32px;
                    border-radius:6px;
                    text-decoration:none;
                    font-size:16px;
                    font-weight:bold;
                    display:inline-block;
                '>Iniciar Sesión</a>
            </div>
            <p style='color:#888; font-size:13px;'>
                Si no creaste esta cuenta, ignora este mensaje.
            </p>
        ");
 
        return self::send($email, $nombre, $subject, $body);
    }
 
    // ─────────────────────────────────────────────────────────────────────────
    // LAYOUT BASE (todos los emails usan este diseño)
    // ─────────────────────────────────────────────────────────────────────────
    private static function template(string $titulo, string $contenido): string
    {
        return "
        <!DOCTYPE html>
        <html lang='es'>
        <head><meta charset='UTF-8'></head>
        <body style='margin:0; padding:0; background:#f5f5f5; font-family: Arial, sans-serif;'>
            <table width='100%' cellpadding='0' cellspacing='0' style='background:#f5f5f5; padding: 40px 0;'>
                <tr><td align='center'>
                    <table width='600' cellpadding='0' cellspacing='0' style='background:#fff; border-radius:8px; overflow:hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.08);'>
 
                        <!-- HEADER -->
                        <tr>
                            <td style='background:#1a1a2e; padding: 30px; text-align:center;'>
                                <h1 style='color:#c8a96e; margin:0; font-size:22px; letter-spacing:2px;'>
                                    🏨 HOTEL REAL PLAZA
                                </h1>
                                <p style='color:#aaa; margin:6px 0 0; font-size:13px;'>{$titulo}</p>
                            </td>
                        </tr>
 
                        <!-- CUERPO -->
                        <tr>
                            <td style='padding: 36px 40px; color:#333; font-size:15px; line-height:1.7;'>
                                {$contenido}
                            </td>
                        </tr>
 
                        <!-- FOOTER -->
                        <tr>
                            <td style='background:#f9f9f9; padding:20px; text-align:center; color:#aaa; font-size:12px; border-top:1px solid #eee;'>
                                © " . date('Y') . " Hotel Real Plaza — Todos los derechos reservados<br>
                                Este es un mensaje automático, por favor no respondas a este correo.
                            </td>
                        </tr>
 
                    </table>
                </td></tr>
            </table>
        </body>
        </html>";
    }
}
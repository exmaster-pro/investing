<?php
declare(strict_types=1);
require_once __DIR__ . '/bootstrap.php';

function send_otp_email(string $recipient, string $name, string $code): bool {
    $autoload = __DIR__ . '/../vendor/autoload.php';
    if (!is_file($autoload)) {
        error_log('PHPMailer missing. Run composer require phpmailer/phpmailer in project folder.');
        return false;
    }
    require_once $autoload;
    $username = defined('MAIL_USERNAME_LOCAL') ? MAIL_USERNAME_LOCAL : MAIL_USERNAME;
    $password = defined('MAIL_PASSWORD_LOCAL') ? MAIL_PASSWORD_LOCAL : MAIL_PASSWORD;
    if ($username === 'your-sender@gmail.com' || $password === 'replace-with-google-app-password') {
        error_log('Configure Gmail SMTP credentials before sending email.');
        return false;
    }
    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = MAIL_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = $username;
        $mail->Password = $password;
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = MAIL_PORT;
        $mail->CharSet = 'UTF-8';
        $mail->setFrom(MAIL_FROM, MAIL_FROM_NAME);
        $mail->addAddress($recipient, $name);
        $mail->isHTML(true);
        $mail->Subject = 'Your ' . APP_NAME . ' verification code';
        $mail->Body = '<div style="font-family:Arial,sans-serif;max-width:560px;margin:auto;padding:24px">'
            . '<h2>Email verification</h2><p>Hello ' . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . ',</p>'
            . '<p>Your verification code is:</p><p style="font-size:30px;letter-spacing:8px;font-weight:bold">'
            . $code . '</p><p>This code expires in ' . OTP_TTL_MINUTES . ' minutes. Do not share it.</p>'
            . '<p>If you did not request this, you can ignore this email.</p></div>';
        $mail->AltBody = "Hello $name. Your verification code is $code. It expires in " . OTP_TTL_MINUTES . " minutes.";
        $mail->send();
        return true;
    } catch (Throwable $exception) {
        error_log('OTP email error: ' . $exception->getMessage());
        return false;
    }
}

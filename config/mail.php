<?php

require_once __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;

$mailEnv = [];
$mailEnvPath = __DIR__ . '/../.env';
if (is_file($mailEnvPath)) {
    $mailEnv = parse_ini_file($mailEnvPath, false, INI_SCANNER_RAW) ?: [];
}

function mailConfig(string $key, string $default = ''): string
{
    global $mailEnv;
    return (string) ($mailEnv[$key] ?? getenv($key) ?: $default);
}

function sendVerificationEmail (string $email, string $name, string $code): void
{
    $mail = new PHPMailer(true);
    $mail ->isSMTP();
    $mail ->Host = mailConfig('MAIL_HOST', 'sandbox.smtp.mailtrap.io');
    $mail ->SMTPAuth = true;
    $mail ->Username = mailConfig('smtp@mailtrap.io');
    $mail ->Password = mailConfig('1a69710d817d6b544d8b0d21f770a171');
    $mail ->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail ->Port = (int) mailConfig('MAIL_PORT', '587');
    $mail ->setFrom(mailConfig('MAIL_FROM_ADDRESS', $mail->Username), mailConfig('MAIL_FROM_NAME', 'Auth Task'));
    $mail ->addAddress($email, $name);
    $mail ->isHTML(true);
    $mail ->Subject = 'Email confirmation code';
    $mail ->Body = '<p> Your confirmation code is: </p><h2>' . htmlspecialchars($code, ENT_QUOTES, 'UTF-8') . '</h2><p> It expires in 15 minutes. </p>';
    $mail ->AltBody = "Your confirmation code is: {$code}. It expires in 15 minutes.";
    $mail->send();
}
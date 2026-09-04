<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Mailtrap\MailtrapClient;
use Mailtrap\Mime\MailtrapEmail;
use Symfony\Component\Mime\Address;

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

function sendVerificationLinkEmail(string $email, string $name, string $token): void
{
    $apiKey = mailConfig('MAILTRAP_API_KEY');

    if ($apiKey === '' || str_starts_with($apiKey, 'YOUR_')) {
        throw new RuntimeException('Mailtrap API key is missing in .env.');
    }

    $verifyUrl = rtrim(mailConfig('APP_URL', 'http://localhost:8000'), '/')
        . '/Components/Registration/Verify.php?token=' . urlencode($token);

    $mailtrap = MailtrapClient::initSendingEmails(apiKey: $apiKey);
    $message = (new MailtrapEmail())
        ->from(new Address(mailConfig('MAIL_FROM_ADDRESS', 'hello@demomailtrap.co'), mailConfig('MAIL_FROM_NAME', 'Auth Task')))
        ->to(new Address($email, $name))
        ->subject('Verify your email address')
        ->html('<p>Click the link below to verify your email address:</p><p><a href="' . htmlspecialchars($verifyUrl, ENT_QUOTES, 'UTF-8') . '">Verify email</a></p><p>This link expires in 60 minutes.</p>')
        ->text("Verify your email address: {$verifyUrl}\nThis link expires in 60 minutes.")
        ->category('Email Verification');

    $mailtrap->send($message);
}

function sendPasswordResetEmail(string $email, string $name, string $token): void
{
    $apiKey = mailConfig('MAILTRAP_API_KEY');

    if ($apiKey === '' || str_starts_with($apiKey, 'YOUR_')) {
        throw new RuntimeException('Mailtrap API key is missing in .env.');
    }

    $resetUrl = rtrim(mailConfig('APP_URL', 'http://localhost:8000'), '/')
        . '/Components/PasswordReset/Reset.php?token=' . urlencode($token);

    $mailtrap = MailtrapClient::initSendingEmails(apiKey: $apiKey);
    $message = (new MailtrapEmail())
        ->from(new Address(mailConfig('MAIL_FROM_ADDRESS', 'hello@demomailtrap.co'), mailConfig('MAIL_FROM_NAME', 'Auth Task')))
        ->to(new Address($email, $name))
        ->subject('Password reset request')
        ->html('<p>Click the link below to reset your password:</p><p><a href="' . htmlspecialchars($resetUrl, ENT_QUOTES, 'UTF-8') . '">Reset password</a></p><p>This link expires in 60 minutes.</p>')
        ->text("Reset your password: {$resetUrl}\nThis link expires in 60 minutes.")
        ->category('Password Reset');

    $mailtrap->send($message);
}

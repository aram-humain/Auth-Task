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

function sendVerificationEmail(string $email, string $name, string $code): void
{
    $apiKey = mailConfig('MAILTRAP_API_KEY');

    if ($apiKey === '' || str_starts_with($apiKey, 'YOUR_')) {
        throw new RuntimeException('Mailtrap API key is missing in .env.');
    }

    $mailtrap = MailtrapClient::initSendingEmails(apiKey: $apiKey);
    $message = (new MailtrapEmail())
        ->from(new Address(mailConfig('MAIL_FROM_ADDRESS', 'hello@demomailtrap.co'), mailConfig('MAIL_FROM_NAME', 'Auth Task')))
        ->to(new Address($email, $name))
        ->subject('Email confirmation code')
        ->html('<p>Your confirmation code is:</p><h2>' . htmlspecialchars($code, ENT_QUOTES, 'UTF-8') . '</h2><p>It expires in 15 minutes.</p>')
        ->text("Your confirmation code is: {$code}. It expires in 15 minutes.")
        ->category('Email Verification');

    $mailtrap->send($message);
}

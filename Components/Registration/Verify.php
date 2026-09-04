<?php

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/VerifyDB.php';
require_once __DIR__ . '/../../config/mail.php';

$token = trim($_GET['token'] ?? '');
$email = trim($_GET['email'] ?? $_POST['email'] ?? '');
$error = '';
$success = '';

if (isset($_GET['unverified'])) {
    $error = 'Please verify your email before opening the Dashboard.';
}

if ($token !== '') {
    if (verifyEmailToken($pdo, $token)) {
        header('Location: ../Login/Login.php?verified=1');
        exit;
    }

    $error = 'The verification link is invalid or expired.';
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? 'verify';

    if ($action === 'resend') {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'A valid email address is required.';
        } else {
            try {
                $newToken = bin2hex(random_bytes(32));
                $pdo->beginTransaction();
                $name = replaceVerificationToken($pdo, $email, $newToken);

                if ($name === null) {
                    throw new RuntimeException('Verification request was not found.');
                }

                sendVerificationLinkEmail($email, $name, $newToken);
                $pdo->commit();
                $success = 'A new verification link has been sent.';
            } catch (Throwable $exception) {
                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }
                error_log($exception->getMessage());
                $error = 'The new confirmation code could not be sent.';
            }
        }
    } else {
        $error = 'Open the verification link from your email.';
    }
}

require_once __DIR__ . '/Verify.html';

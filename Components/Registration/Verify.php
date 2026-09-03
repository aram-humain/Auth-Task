<?php

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/VerifyDB.php';
require_once __DIR__ . '/../../config/mail.php';

$email = trim($_GET['email'] ?? $_POST['email'] ?? '');
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? 'verify';

    if ($action === 'resend') {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'A valid email address is required.';
        } else {
            try {
                $code = (string) random_int(100000, 999999);
                $pdo->beginTransaction();
                $name = replaceVerificationCode($pdo, $email, $code);

                if ($name === null) {
                    throw new RuntimeException('Verification request was not found.');
                }

                sendVerificationEmail($email, $name, $code);
                $pdo->commit();
                $success = 'A new confirmation code has been sent.';
            } catch (Throwable $exception) {
                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }
                error_log($exception->getMessage());
                $error = 'The new confirmation code could not be sent.';
            }
        }
    } else {
        $code = trim($_POST['code'] ?? '');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match('/^\d{6}$/', $code)) {
            $error = 'Enter the six-digit code from your email.';
        } elseif (verifyEmailCode($pdo, $email, $code)) {
            header('Location: ../Login/Login.php?verified=1');
            exit;
        } else {
            $error = 'The code is invalid or expired.';
        }
    }
}

require_once __DIR__ . '/Verify.html';

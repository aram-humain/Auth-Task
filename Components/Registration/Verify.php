<?php

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/VerifyDB.php';

$email = trim($_GET['email'] ?? $_POST['email'] ?? '');
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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

require_once __DIR__ . '/Verify.html';

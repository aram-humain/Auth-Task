<?php

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/RegisterVal.php';
require_once __DIR__ . '/RegisterDB.php';
require_once __DIR__ . '/../../config/mail.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['user_id'])) {
    header('Location: ../Dashboard/Dashboard.php');
    exit;
}

$errors = [];

$name = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $errors = validateRegistrationInput($name, $email, $password);

    if (empty($errors)) {
        if (emailAlreadyExists($pdo, $email)) {
            $errors[] = 'An account with this email already exists.';
        }
    }

    if (empty($errors)) {
        try {
            $token = bin2hex(random_bytes(32));
            $pdo->beginTransaction();
            createUser($pdo, $name, $email, $password, $token);
            sendVerificationLinkEmail($email, $name, $token);
            $pdo->commit();

            header('Location: Verify.php?email=' . urlencode($email));
            exit;
        } catch (Throwable $e) {

            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            error_log($e->getMessage());

            if ($e->getCode() === '23000') {
                $errors[] = 'An account with this email already exists.';
            } elseif (str_contains($e->getMessage(), 'Sending from domain')) {
                $errors[] = 'Email could not be sent. Verify MAIL_FROM_ADDRESS in Mailtrap.';
            } elseif (str_contains($e->getMessage(), 'Could not authenticate')) {
                $errors[] = 'Email could not be sent. Check Mailtrap Sandbox username and password.';
            } elseif (str_contains($e->getMessage(), 'Mailtrap API key is missing')) {
                $errors[] = 'Email could not be sent. Add MAILTRAP_API_KEY to .env.';
            } elseif (str_contains($e->getMessage(), 'Demo domains can only be used')) {
                $errors[] = 'Mailtrap demo emails can only be sent to the account owner email.';
            } else {
                $errors[] = 'Something went wrong. Please try again.';
            }
        }
    }
}

require_once __DIR__ . '/Registration.html.php';
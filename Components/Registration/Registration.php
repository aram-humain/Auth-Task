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

$firstName = '';
$lastName = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $firstName = trim($_POST['first_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $lastName = trim($_POST['last_name'] ?? '');

    $errors = validateRegistrationInput($firstName, $lastName, $email, $password);

    if (empty($errors)) {
        if (emailAlreadyExists($pdo, $email)) {
            $errors[] = 'An account with this email already exists.';
        }
    }

    if (empty($errors)) {
        try {
            $token = bin2hex(random_bytes(32));

            $publicSlug = generatePublicSlug(
                $firstName,
                $lastName
            );
            $pdo->beginTransaction();
            createUser($pdo,$firstName,$lastName,$email,$password,$token,$publicSlug);
            sendVerificationLinkEmail($email, $firstName, $token);
            $pdo->commit();

            header('Location: Verify.php?email=' . urlencode($email));
            exit;
        } catch (Throwable $e) {

            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            error_log($e->getMessage());

            // if ($e->getCode() === '23000') {
            //     $errors[] = 'An account with this email already exists.';
            // } elseif (str_contains($e->getMessage(), 'Sending from domain')) {
            //     $errors[] = 'Email could not be sent. Verify MAIL_FROM_ADDRESS in Mailtrap.';
            // } elseif (str_contains($e->getMessage(), 'Could not authenticate')) {
            //     $errors[] = 'Email could not be sent. Check Mailtrap Sandbox username and password.';
            // } elseif (str_contains($e->getMessage(), 'Mailtrap API key is missing')) {
            //     $errors[] = 'Email could not be sent. Add MAILTRAP_API_KEY to .env.';
            // } elseif (str_contains($e->getMessage(), 'Demo domains can only be used')) {
            //     $errors[] = 'Mailtrap demo emails can only be sent to the account owner email.';
            // } else {
            //     $errors[] = 'Something went wrong. Please try again.';
            // }
        }
    }
}

require_once __DIR__ . '/Registration.html.php';
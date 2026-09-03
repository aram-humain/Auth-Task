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
            $code = (string) random_int(100000, 999999);
            createUser($pdo, $name, $email, $password, password_hash($code, PASSWORD_DEFAULT));
            sendVerificationEmail($email, $name, $code);

            header('Location: Verify.php?email=' . urlencode($email));
            exit;
        } catch (Throwable $e) {

            if ($e->getCode() === '23000') {
                $errors[] = 'An account with this email already exists.';
            } else {
                $errors[] = 'Something went wrong. Please try again.';
            }
        }
    }
}

require_once __DIR__ . '/Registration.html';
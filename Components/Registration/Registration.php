<?php

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/RegisterVal.php';
require_once __DIR__ . '/RegisterDB.php';

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
            createUser($pdo, $name, $email, $password);

            header('Location: ../Login/Login.php?registered=1');
            exit;
        } catch (PDOException $e) {

            if ($e->getCode() === '23000') {
                $errors[] = 'An account with this email already exists.';
            } else {
                $errors[] = 'Something went wrong. Please try again.';
            }
        }
    }
}

require_once __DIR__ . '/Registration.html';
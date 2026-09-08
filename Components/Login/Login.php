<?php

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/LoginVal.php';
require_once __DIR__ . '/LoginDB.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['user_id'])) {
    header('Location: ../Dashboard/Dashboard.php');
    exit;
}

$errors = [];

$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $errors = validateLoginInput($email, $password);

    if (empty($errors)) {
        $user = findUserByCredentials($pdo, $email, $password);

        if ($user) {

            session_regenerate_id(true);

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];

            header('Location: ../Dashboard/Dashboard.php');
            exit;
        } else {
            $errors[] = 'Invalid email or password.';
        }
    }
}

require_once __DIR__ . '/Login.html.php';
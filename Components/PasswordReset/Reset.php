<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/ResetVal.php';
require_once __DIR__ . '/ResetDB.php';

$token = trim($_GET['token'] ?? $_POST['token'] ?? '');
$errors = [];
$success = '';

if ($token === '') {
    $errors[] = 'The password reset link is invalid.';
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    $passwordConfirmation = $_POST['password_confirmation'] ?? '';
    $errors = validateResetPassword($password, $passwordConfirmation);

    if (empty($errors)) {
        $reset = findResetRequest($pdo, $token);

        if (!$reset) {
            $errors[] = 'The password reset link is invalid or expired.';
        } else {
            try {
                updatePasswordAndConsumeToken($pdo, $reset['id'], $reset['user_id'], $password);
                header('Location: ../Login/Login.php?reset=1');
                exit;
            } catch (Throwable $exception) {
                error_log($exception->getMessage());
                $errors[] = 'The password could not be reset. Please try again.';
            }
        }
    }
}

require_once __DIR__ . '/Reset.html.php';

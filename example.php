<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/config/db.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Components/Profile/ProfileDB.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: ../Login/Login.php');
    exit;
}

$userId = (int) $_SESSION['user_id'];

$profile = getProfileByUserId($pdo, $userId);

if ($profile === null) {
    // На этом этапе profile должен существовать,
    // потому что мы создаём его при регистрации.
    // Но оставляем проверку на случай ошибки/старых пользователей.
    die('Profile not found.');
}

require_once __DIR__ . '/Profile.html.php';

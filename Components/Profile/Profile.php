<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/db.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Components/Profile/ProfileDB.php';

if(session_status() === PHP_SESSION_NONE) {
    session_start();
}

if(!isset($_SESSION['user_id'])) {
    header('Location: ./Components/Login/login.php');
    exit;
}

$userId = (int) $_SESSION['user_id'];

$profile = getProfileByUserId($pdo, $userId);

if($profile === null) {
    exit('Profile not found.');
}

require_once __DIR__ . '/Profile.html.php';
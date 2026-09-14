<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/db.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/auth.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Components/Profile/ProfileDB.php';

requireLogin();

$userId = currentUserId();

$slug = trim($_GET['slug'] ?? '');

if($slug !== '') {
    $profile = getProfileBySlug($pdo, $slug);
} else {
    $profile = getProfileByUserId($pdo, $userId);
}

if ($profile === null) {
    http_response_code(404);
    exit('Profile not found.');
}

$isOwner = (int) $profile['user_id'] === $userId;


require_once __DIR__ . '/Profile.html.php';
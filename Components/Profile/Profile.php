<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/db.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/auth.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Components/Profile/ProfileDB.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Components/Posts/PostsDB.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/authorization.php';

use Ramsey\Uuid\Uuid;


$viewerUserId = currentUserId();

$slug = trim($_GET['slug'] ?? '');

if ($slug !== '') {
    $profile = getProfileBySlug(
        $pdo,
        $slug
    );
} else {
    if ($viewerUserId === null) {
        header('Location: /Components/Login/Login.php');
        exit;
    } 

    $profile = getProfileByUserId($pdo, $viewerUserId);

    if($profile === null) {
        http_response_code(404);
        exit('Profile not found.');
    }

    header('Location: /Components/Profile/Profile.php?slug=' . urlencode($profile['public_slug']));

    exit;
}

if ($profile === null) {
    http_response_code(404);
    exit('Profile not found.');
}

$isOwner = $viewerUserId !== null &&
    (int) $profile['user_id'] === $viewerUserId;

$isAdmin = $viewerUserId !== null &&
    hasRole($pdo, 'Admin');

$canSeePrivatePosts =
    $isOwner || $isAdmin;


$profilePosts = getProfilePostsByUserId(
    $pdo,
    (int) $profile['user_id'],
    $canSeePrivatePosts
);



foreach ($profilePosts as &$profilePost) {

    $profilePost['uuid'] =
        Uuid::fromBytes(
            $profilePost['public_id']
        )->toString();
}

unset($profilePost);


require_once __DIR__ . '/Profile.html.php';
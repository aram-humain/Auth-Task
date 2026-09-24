<?php
// require_once $_SERVER['DOCUMENT_ROOT'] . '/config/db.php';
// require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/auth.php';
// require_once $_SERVER['DOCUMENT_ROOT'] . '/Components/Profile/ProfileDB.php';
// require_once $_SERVER['DOCUMENT_ROOT'] . '/Components/Posts/PostsDB.php';
// require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';

// use Ramsey\Uuid\Uuid;

// requireLogin();

// $userId = currentUserId();

// $slug = trim($_GET['slug'] ?? '');

// if($slug !== '') {
//     $profile = getProfileBySlug($pdo, $slug);
// } else {
//     $profile = getProfileByUserId($pdo, $userId);
// }

// if ($profile === null) {
//     http_response_code(404);
//     exit('Profile not found.');
// }

// $isOwner = (int) $profile['user_id'] === $userId;

// $profilePosts = getPublishedPostByUserId($pdo, (int) $profile['user_id']);

// foreach($profilePosts as $profilePost) {
//     $profilePost['uuid'] = Uuid::fromBytes($profilePost['public_id'])->toString();
// }

// unset($profilePost);

// require_once __DIR__ . '/Profile.html.php';

require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/db.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/auth.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Components/Profile/ProfileDB.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Components/Posts/PostsDB.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/authorization.php';

use Ramsey\Uuid\Uuid;

requireLogin();

$userId = currentUserId();

$slug = trim($_GET['slug'] ?? '');

if ($slug !== '') {
    $profile = getProfileBySlug(
        $pdo,
        $slug
    );
} else {
    $profile = getProfileByUserId(
        $pdo,
        $userId
    );
}

if ($profile === null) {
    http_response_code(404);
    exit('Profile not found.');
}

$isOwner =
    (int) $profile['user_id'] === $userId;

$isAdmin = hasRole($pdo, 'Admin');

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
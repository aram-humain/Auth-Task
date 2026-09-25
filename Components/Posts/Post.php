<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/db.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/csrf.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/flash.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/auth.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/authorization.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Components/Posts/PostsDB.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Components/Comments/CommentsDB.php';

use Ramsey\Uuid\Uuid;

$uuid = trim($_GET['id'] ?? '');


if($uuid === '' || !Uuid::isValid($uuid)) {
    http_response_code(404);
    exit('Post not found');
}

$publicIdBytes = Uuid::fromString($uuid)->getBytes();

$post = getPostByPublicId($pdo, $publicIdBytes);

if($post === null) {
    http_response_code(404);

    exit('Post not found.');
}

$viewerUserId = currentUserId();

$isOwner = $viewerUserId !== null && (int) $post['user_id'] === $viewerUserId;

$isAdmin = $viewerUserId !== null && hasRole($pdo, 'Admin');


if ($post['deleted_at'] !== null) {
    if($viewerUserId === null || !can($pdo, 'view_deleted_posts')) {
        http_response_code(404);

        exit('Post not found.');
    }
}


// draft / archived post permission check (only owner or admin)
if($post['deleted_at'] === null && $post['status'] !== 'published' && !$isOwner && !$isAdmin) {
    http_response_code(404);
    exit('Post not found');
}

$images = getPostImages(
    $pdo,
    (int) $post['id']
);

$tags = getPostTags(
    $pdo,
    (int) $post['id']
);


$authorName = trim(
    ($post['first_name'] ?? '')
    . ' '
    . ($post['last_name'] ?? '')
);

if ($authorName === '') {
    $authorName = 'User';
}

$comments = getPostComments($pdo, (int) $post['id']);

$csrfToken = csrfToken();

$canComment = $viewerUserId !== null && $post['deleted_at'] === null && $post['status'] === 'published';

$flashSuccess = getFlash('success');

$flashError = getFlash('error');

$canModerateComments = $viewerUserId !== null && can($pdo, 'moderate_comments');

require_once __DIR__ . '/Post.html.php';
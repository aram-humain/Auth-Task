<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/db.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/auth.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/csrf.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/flash.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Components/Posts/PostsDB.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Components/Reports/ReportDB.php';

use Ramsey\Uuid\Uuid;

requireLogin();

if($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method not allowed.');
}

if(!verifyCsrfToken($_POST['csrf_token'] ?? null)) {
    http_response_code(403);
    exit('Invalid CSRF token');
}

$userId = currentUserId();

$postUuid = trim($_POST['post_id'] ?? '');

$reason = trim($_POST['reason'] ?? '');

$description = trim($_POST['description'] ?? '');


if($postUuid === '' || !Uuid::isValid($postUuid)) {
    http_response_code(404);
    exit('Post not found');
}

$postPublicId = Uuid::fromString($postUuid)->getBytes();

$post = getPostByPublicId($pdo, $postPublicId);

if($post === null || $post['deleted_at'] !== null || $post['status'] !== 'published') {
    http_response_code(404);
    exit('Post not found');
}

$postId = (int) $post['id'];

if((int) $post['user_id'] === $userId) {
    setFlash('error', 'You cannot report your own post');

    header('Location: /Components/Posts/Post.php?id=' . urlencode($postUuid));
    exit;
}

if($reason === '') {
    setFlash('error', 'Reason is required');

    header('Location: /Components/Posts/Post.php?id=' . urlencode($postUuid));
    exit;
}

if(mb_strlen($reason) > 50) {
    setFlash('error', 'Report reason is too long');

    header('Location: /Components/Posts/Post.php?id=' . urlencode($postUuid));
    exit;
}

if($description !== '' && mb_strlen($description) > 2000) {
    setFlash('error', 'Report description is too long');

    header('Location: /Components/Posts/Post.php?id=' . urlencode($postUuid));
    exit;
}


$description = $description === '' ? null : $description;

if (hasUserReportedPost($pdo, $postId, $userId)) {
    setFlash('error', 'You have already reported this post.');

    header('Location: /Components/Posts/Post.php?id=' . urlencode($postUuid));
    exit;
}

try {
    createPostReport($pdo, $postId, $userId, $reason);

    setFlash('success', 'Post reported successfully.');

    header('Location: /Components/Posts/Post.php?id=' . urlencode($postUuid));
    exit;
    
} catch (PDOException $exception) {
    if($exception->getCode() === '23000') {
        setFlash('error', 'You have already reported this post.');
        
        header('Location: /Components/Posts/Post.php?id=' . urlencode($postUuid));
        exit;
    }

    http_response_code(500);
    exit('Unable to report post');
}
<?php

use GuzzleHttp\Psr7\Header;
use Ramsey\Uuid\Uuid;

require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/db.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/auth.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/csrf.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/flash.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/rate_limit.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Components/Posts/PostsDB.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Components/Comments/CommentsDB.php';

requireLogin();

if($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method not allowed.');
}

if(!verifyCsrfToken($_POST['csrf_token'] ?? null)) {
    http_response_code(403);
    exit('Invalid CSRF token.');
}

$userId = currentUserId();

$uuid = trim($_POST['post_id'] ?? '');

$content = trim($_POST['content'] ?? '');

if($uuid === '' || !Uuid::isValid($uuid)) {
    http_response_code(404);
    exit('Post not found.');
}

$publicId = Uuid::fromString($uuid)->getBytes();

$post = getPostByPublicId($pdo, $publicId);

if($post === null || $post['deleted_at'] !== null || $post['status'] !== 'published') {
    http_response_code(404);
    exit('Post not found.');
}

if(!isUserVerified($pdo, $userId)) {
    setFlash('error', 'You must verify your email before commenting.');

    header('Location: /Components/Posts/Posts.php?id=' . urlencode($uuid));
    exit;
}

if($content === '') {
    setFlash('error', 'Comment cannot be empty');

    header('Location: /Components/Posts/Posts.php?id=' . urlencode($uuid));
    exit;
}

if(mb_strlen($content) > 2000) {
    setFlash('error', 'Comment is too long');

    header('Location: /Components/Posts/Posts.php?id=' . urlencode($uuid));
    exit;
}

$commentAttempt = countRecentAttempts($pdo, 'comment_create', 60, $userId);

if($commentAttempt >= 5) {
    setFlash('error', 'Too many comments. Please wait before comment again');

    header('Location: /Components/Posts/Posts.php?id=' . urlencode($uuid));
    exit;
}

try {
    $pdo->beginTransaction();

    createComment($pdo, (int) $post['id'], $userId, $content);

    recordRateLimitAttempt($pdo, 'comment_create', $userId, null, $_SERVER['REMOTE_ADDR'] ?? null);

    $pdo->commit();

    setFlash('success', 'Comment added successfully');

    header('Location: /Components/Posts/Posts.php?id=' . urlencode($uuid) . '#comments');
    exit;
    
} catch(Throwable $exception) {
    if($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    http_response_code(500);
    exit('Unable to add comment.');
}
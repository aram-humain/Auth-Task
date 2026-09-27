<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/db.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/auth.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/csrf.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/flash.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Components/Posts/PostsDB.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Components/SavedPosts/SavedPostDB.php';

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

$userId = (int) currentUserId();

$postUuid = trim($_POST['post_id'] ?? '');

if($postUuid === '' || !Uuid::isValid($postUuid)) {
    http_response_code(404);

    exit('Post not found.');
}

$postPublicId = Uuid::fromString($postUuid)->getBytes();

$post = getPostByPublicId($pdo, $postPublicId);

if($post === null  || $post['deleted_at'] !== null || $post['status'] !== 'published') {
    http_response_code(404);
    exit('Post not found');
}

$postId = (int) $post['id'];

try{
    $pdo->beginTransaction();

    $alreadySaved = hasUserSavedPost($pdo, $postId, $userId);

    if($alreadySaved) {
        removeSavedPost($pdo, $postId, $userId);

        setFlash('success', 'Post removed from saved psots.');
    } else {
        addSavedPost($pdo, $postId, $userId);

        setFlash('success', 'Post saved successfully');
    }

    $pdo->commit();

    header('Location: /Components/Posts/Post.php?id=' . urlencode($postUuid));

    exit;
} catch (Throwable $exception) { 
    if($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    http_response_code(500);

    exit('Unable to update saved post.');
}
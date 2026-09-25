<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/db.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/auth.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/authorization.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/csrf.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/flash.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/activity_log.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Components/Comments/CommentsDB.php';

use Ramsey\Uuid\Uuid;

requireLogin();

if($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method not allowed');
}

if(!verifyCsrfToken($_POST['csrf_token'] ?? null)) {
    http_response_code(403);
    exit('Invalid CSRF token');
}

$userId = currentUserId();

$commentId = filter_input(INPUT_POST, 'comment_id', FILTER_VALIDATE_INT);

if (!$commentId) {
    http_response_code(404);
    require_once $_SERVER['DOCUMENT_ROOT'] . '/Components/Error/403.php';
    exit('Comment not found');
}

$comment = getCommentById($pdo, $commentId);

if($comment === null || $comment['deleted_at'] !== null){
    http_response_code(404);
    exit('Comment not found');
}

$isCommentOwner = (int) $comment['user_id'] === $userId;

$isPostOwner = (int) $comment['post_user_id'] === $userId;

$canModerate = can($pdo, 'moderate_comments');

if(!$isCommentOwner && !$isPostOwner && !$canModerate) {
    http_response_code(403);
    require_once $_SERVER['DOCUMENT_ROOT'] . '/Components/Error/403.php';
    exit;
}

$postUuid = Uuid::fromBytes($comment['post_public_id'])->toString();

try {
    $pdo->beginTransaction();

    $deleted = softDeleteComment($pdo, $commentId);

    if(!$deleted) {
        throw new RuntimeException('Comment could not be deleted.');
    }

    logActivity($pdo, $userId, 'comment_deleted', 'comment', $commentId, $_SERVER['REMOTE_ADDR'] ?? null);

    $pdo->commit();

    setFlash('success', 'Comment deleted successfully.');

    header('Location: /Components/Posts/Post.php?id=' . urlencode($postUuid) . '#comments');

    exit;
} catch (Throwable $exception) {
    if($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    http_response_code(500);

    exit('Unable to delete comment.');
}
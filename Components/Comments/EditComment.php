<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/db.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/auth.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/csrf.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/flash.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Components/Comments/CommentsDB.php';

use Ramsey\Uuid\Uuid;

requireLogin();

$userId = currentUserId();

$commentId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if(!$commentId) {
    http_response_code(404);

    exit('Comment not found.');
}

$comment = getCommentById($pdo, $commentId);

if($comment === null || $comment['deleted_at'] !== null || $comment['post_deleted_at'] !== null) {
    http_response_code(404);
    exit('Comment not found.');
}

if((int) $comment['user_id'] !== $userId) {
    http_response_code(403);

    require_once $_SERVER['DOCUMENT_ROOT'] . '/Components/Error/403.php';
    exit;
}

$postUuid = Uuid::fromBytes($comment['post_public_id'])->toString();

$content = $comment['content'];

$error = null;

$csrfToken = csrfToken();

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(!verifyCsrfToken($_POST['csrf_token'] ?? null)) {
        http_response_code(403);
        exit('Invalid CSRF token');
    }

    $content = trim($_POST['content'] ?? '');

    if($content === '') {
        $error = 'Comment cannot be empty';
    } else if(mb_strlen($content) > 2000) {
        $error = 'Comment is too long';
    }

    if($error === null) {
        updateOwnComment($pdo, $commentId, $userId, $content);

        setFlash('success', 'Comment updated successfully.');

        header('Location: /Components/Posts/Post.php?id=' . urlencode($postUuid) . '#comment-' . $commentId);
    }
}

require_once __DIR__ . '/EditComment.html.php';
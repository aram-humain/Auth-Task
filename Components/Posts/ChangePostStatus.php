<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/db.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/auth.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/csrf.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/flash.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/activity_log.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Components/Posts/PostsDB.php';

use Ramsey\Uuid\Uuid;

requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method not allowed');
}

if(!verifyCsrfToken($_POST['csrf_token'] ?? null)) {
    http_response_code(403);
    exit('Invalid CSRF token');
}

$userId = (int) currentUserId();

$postUuid = trim($_POST['post_id'] ?? '');

$newStatus = trim($_POST['status'] ?? '');

if($postUuid === '' || !Uuid::isValid($postUuid)) {
    http_response_code(404);
    exit('Post not found.');
}

if(!in_array($newStatus, ['published', 'archived'], true))  {
    http_response_code(400);
    exit('Invalid post status');
}

$publicId = Uuid::fromString($postUuid)->getBytes();

$post = getOwnedPostByPublicId($pdo, $publicId, $userId);

if($post === null) {
    http_response_code(403);

    require $_SERVER['DOCUMENT_ROOT'] . '/Components/Error/403.php';

    exit;
}

$allowedTransition = match($post['status']) {
    'published' => $newStatus === 'archived',

    'archived' => $newStatus === 'published',

    'draft' => $newStatus === 'published',

    default => false
};

if(!$allowedTransition) {
    http_response_code(400);

    exit('Invalid status transition');
}

try {
    $pdo->beginTransaction();

    $changed = changeOwnedPostStatus(
        $pdo, (int) $post['id'], $userId, $newStatus
    );

    if(!$changed) {
         throw new RuntimeException('Post status was not chagned');
    }

    logActivity($pdo, $userId, 'post_updated', 'post', (int) $post['id'], $_SERVER['REMOTE_ADDR'] ?? null);

    $pdo->commit();

    if($newStatus === 'archived') {
        setFlash('success', 'Post archived successfully');
    } else {
        setFlash('success', 'Post published successfully');
    }

    header('Location: /Components/Posts/Post.php?id=' . urlencode($postUuid));
    exit;
} catch (Throwable $exception) {
    if($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    http_response_code(500);
    exit('Unable to change post status');
}
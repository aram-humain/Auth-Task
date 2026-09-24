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

if($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method not allowed.');
}

if(!verifyCsrfToken($_POST['csrf_token'] ?? null)) {
    http_response_code(403);
    require_once $_SERVER['DOCUMENT_ROOT'] . '/Components/Error/403.php';
    exit;
}

$userId = currentUserId();
$uuid = trim($_POST['id'] ?? '');

if ($uuid === '' || !Uuid::isValid($uuid)) {
    http_response_code(404);
    exit('Post not found.');
}

$publicId = Uuid::fromString($uuid)->getBytes();

$post = getOwnedPostByPublicId($pdo, $publicId, $userId);

if ($post === null) {
    http_response_code(403);
    require_once $_SERVER['DOCUMENT_ROOT'] . '/Components/Error/403.php';
    exit;
}

try {
    $pdo->beginTransaction();

    $deletd = softDeleteOwnedPost($pdo, (int) $post['id'], $userId);

    if(!$deletd) {
        throw new RuntimeException('Post could not be deleted');
    }

    logActivity($pdo, $userId, 'post_deleted', 'post', (int) $post['id'], $_SERVER['REMOTE_ADDR'] ?? null);

    $pdo->commit();

    setFlash('success', 'Post deleted successfully');

    header('Location: /Components/Profile/Profile.php');

    exit;
} catch(Throwable $exception) {
    if($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    http_response_code(500);

    exit('Unable to delete post.');
}
<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/config/db.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/auth.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/authorization.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/csrf.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/flash.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/activity_log.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Components/Posts/PostsDB.php';


requireRole($pdo, 'Admin');

if($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method not allowed.');
}

if(!verifyCsrfToken($_POST['csrf_token'] ?? null)) {
    http_response_code(403);
    require_once $_SERVER['DOCUMENT_ROOT'] . '/Components/Error/403.php';
    exit;
}

$postId = filter_input(INPUT_POST, 'post_id', FILTER_VALIDATE_INT);

try {
    $pdo->beginTransaction();

    $restored = restoreDeletedPost($pdo, $postId);

    if(!$restored) {
        throw new RuntimeException('Post could not be restored.');
    }

    logActivity($pdo, currentUserId(), 'post_restored', 'post', $postId, $_SERVER['REMOTE_ADDR'] ?? null);

    $pdo->commit();

    setFlash('success', 'Post restored successfully.');

    header('Location: /Components/Admin/DeletedPosts.php');

    exit;
} catch(Throwable $exception) {
    if($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    http_response_code(500);
    exit('Unable to restore post.');
}
<?php

use Symfony\Component\Mime\Test\Constraint\EmailHeaderSame;

require_once $_SERVER['DOCUMENT_ROOT'] . '/config/db.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/auth.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/authorization.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/csrf.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/flash.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/activity_log.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Components/Posts/PostsDB.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Components/Reports/ReportDB.php';

requirePermission($pdo, 'moderate_posts');

if($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);

    exit('Method not allowed');
}

if(!verifyCsrfToken($_POST['csrf_token'] ?? null)) {
    http_response_code(403);
    exit('Invalid CSRF token');
}

$reportId = filter_input(INPUT_POST, 'report_id', FILTER_VALIDATE_INT);

if(!$reportId) {
    http_response_code(404);
    exit('Report not found');
}

$report = getPostReportById($pdo, $reportId);

if($report === null || $report['status'] !== 'pending') {
    http_response_code(404);
    exit('Report not found');
}

$postId = (int) $report['post_id'];

$moderatorUserId = currentUserId();

try {
    $pdo->beginTransaction();

    if($report['post_deleted_at'] === null) {
        $deleted = softDeletePostById($pdo, $postId);

        if(!$deleted) {
            throw new RuntimeException('Post could not be hidden.');
        }
    } 

    resolveAllPostReports($pdo, $postId, $moderatorUserId);

    logActivity($pdo, $moderatorUserId, 'moderator_post_hidden', 'post', $postId, $_SERVER['REMOTE_ADDR'] ?? null);

   $pdo->commit();
   
   setFlash('success', 'Post hidden successfully.');

   header('Location: /Components/Moderator/Moderatpor.php');
   exit;
} catch(Throwable $exception) {
    if($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    http_response_code(500);
    exit('Unable to hide post');
}
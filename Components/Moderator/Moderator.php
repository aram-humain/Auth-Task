<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/db.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/auth.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/authorization.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/csrf.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/flash.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Components/Reports/ReportDB.php';

use Ramsey\Uuid\Uuid;

requirePermission($pdo, 'access_moderator_page');

$canModeratePosts = can($pdo, 'moderate_posts');

$canModerateComments = can($pdo, 'moderate_comments');


if(!$canModerateComments && !$canModeratePosts) {
    http_response_code(403);

    require_once $_SERVER['DOCUMENT_ROOT'] . '/Components/Error/403.php';

    exit;
}

$postReports = $canModeratePosts ? getPendingPostReports($pdo) : [];

$commentReports = $canModerateComments ? getPendingCommentReports($pdo) : [];

foreach($postReports as &$postReport) {
    $postReport['post_uuid'] = Uuid::fromBytes($postReport['post_public_id'])->toString();
}

unset($postReport);

foreach ($commentReports as &$report) {

    $report['post_uuid'] =
        Uuid::fromBytes(
            $report['post_public_id']
        )->toString();


    $report['comment_uuid'] =
        Uuid::fromBytes(
            $report['comment_public_id']
        )->toString();
}

unset($report);

$csrfToken = csrfToken();

$flashSuccess = getFlash('success');
$flashError = getFlash('error');


require_once __DIR__ . '/Moderator.html.php';
<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/config/db.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/auth.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/authorization.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/csrf.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/flash.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/activity_log.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Components/Comments/CommentsDB.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Components/Reports/ReportDB.php';


requirePermission(
    $pdo,
    'moderate_comments'
);


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    http_response_code(405);

    exit('Method not allowed.');
}


if (!verifyCsrfToken(
    $_POST['csrf_token'] ?? null
)) {

    http_response_code(403);

    exit('Invalid CSRF token.');
}


$reportId = filter_input(
    INPUT_POST,
    'report_id',
    FILTER_VALIDATE_INT
);


if (!$reportId) {

    http_response_code(404);

    exit('Report not found.');
}


$report =
    getCommentReportById(
        $pdo,
        $reportId
    );


if (
    $report === null
    || $report['status'] !== 'pending'
) {

    http_response_code(404);

    exit('Report not found.');
}


$commentId =
    (int) $report['comment_id'];


$moderatorUserId =
    currentUserId();


try {

    $pdo->beginTransaction();


    if (
        $report['comment_deleted_at']
        === null
    ) {

        $deleted =
            softDeleteComment(
                $pdo,
                $commentId
            );


        if (!$deleted) {

            throw new RuntimeException(
                'Comment could not be hidden.'
            );
        }
    }


    resolveAllCommentReports(
        $pdo,
        $commentId,
        $moderatorUserId
    );


    logActivity(
        $pdo,
        $moderatorUserId,
        'moderator_comment_hidden',
        'comment',
        $commentId,
        $_SERVER['REMOTE_ADDR'] ?? null
    );


    $pdo->commit();


    setFlash(
        'success',
        'Comment hidden successfully.'
    );


    header(
        'Location: /Components/Moderator/Moderator.php'
    );

    exit;


} catch (Throwable $exception) {

    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }


    http_response_code(500);

    exit(
        'Unable to hide comment.'
    );
}
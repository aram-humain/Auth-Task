<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/config/db.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/auth.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/authorization.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/csrf.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/flash.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/activity_log.php';
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


$report = getCommentReportById(
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


$moderatorUserId = currentUserId();


try {

    $pdo->beginTransaction();


    $dismissed = dismissCommentReport(
        $pdo,
        $reportId,
        $moderatorUserId
    );


    if (!$dismissed) {

        throw new RuntimeException(
            'Report could not be dismissed.'
        );
    }


    logActivity(
        $pdo,
        $moderatorUserId,
        'comment_report_dismissed',
        'comment_report',
        $reportId,
        $_SERVER['REMOTE_ADDR'] ?? null
    );


    $pdo->commit();


    setFlash(
        'success',
        'Comment report dismissed.'
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
        'Unable to dismiss comment report.'
    );
}
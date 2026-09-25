<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/config/db.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/authorization.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/activity_log.php';


requirePermission(
    $pdo,
    'access_admin_page'
);

$filterUserId = filter_input(
    INPUT_GET,
    'user_id',
    FILTER_VALIDATE_INT
);


if (
    $filterUserId === false
    || $filterUserId === null
    || $filterUserId < 1
) {
    $filterUserId = null;
}

$page = filter_input(
    INPUT_GET,
    'page',
    FILTER_VALIDATE_INT
);


if (!$page || $page < 1) {
    $page = 1;
}


$perPage = 10;


$totalLogs = getActivityLogsCount(
    $pdo,
    $filterUserId
);


$totalPages = max(
    1,
    (int) ceil(
        $totalLogs / $perPage
    )
);


if ($page > $totalPages) {
    $page = $totalPages;
}


$offset =
    ($page - 1)
    * $perPage;

$activityLogs = getActivityLogs(
    $pdo,
    $filterUserId,
    $perPage,
    $offset
);


$activityUsers =
    getActivityLogUsers($pdo);


require_once __DIR__
    . '/Audit.html.php';
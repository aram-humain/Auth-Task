<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/config/db.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/authorization.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Components/Admin/AdminDB.php';

requirePermission($pdo, 'access_admin_page');

$auditLogs = getAuditLogs($pdo);

require_once __DIR__ . '/Audit.html.php';
<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/config/db.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/authorization.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/csrf.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/flash.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Components/Categories/CategoryDB.php';

requireRole($pdo, 'Admin');

$categories = getAdminCategories($pdo);

$csrfToken = csrfToken();

$flashSuccess = getFlash('success');
$flashError = getFlash('error');

require_once __DIR__ . '/AdminCategories.html.php';
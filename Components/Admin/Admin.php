<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/config/db.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/authorization.php';
requirePermission($pdo, 'access_admin_page');

require_once __DIR__ . '/Admin.html.php';
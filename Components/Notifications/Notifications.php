<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/config/db.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/auth.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/csrf.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Components/Notifications/NotificationDB.php';

requireLogin();

$userId = (int) currentUserId();

$notifications = getNotifications($pdo, $userId, 50);

$unreadCount = getUnreadNotificationCount($pdo, $userId);

$csrfToken = csrfToken();

require_once __DIR__ . '/Notifications.html.php';
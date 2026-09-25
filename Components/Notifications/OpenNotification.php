<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/db.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/auth.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Components/Notifications/NotificationDB.php';

use Ramsey\Uuid\Uuid;

requireLogin();

$userId = (int) currentUserId();


$notificationId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if($notificationId === false || $notificationId === null || $notificationId < 0) {
    http_response_code(404);
    exit('Notification not found');
}

$notification = getNotificationForUser($pdo, $notificationId, $userId);

if($notification === null) {
    http_response_code(404);
    exit('Notification not found');
}

markNotificationRead($pdo, (int) $notificationId, $userId);

if($notification['post_public_id'] === null) {
    header('Location: /Components/Notification/Notifications.php');
    exit;
}

$postUuid = Uuid::fromBytes($notification['post_public_id'])->toString();

$url = '/Components/Posts/Post.php?id=' . urlencode($postUuid);

if($notification['comment_public_id'] !== null) {
    $commentUuid = Uuid::fromBytes($notification['comment_public_id'])->toString();

    $url = '#comment-' . urlencode($commentUuid);
}

    header('Location: ' . $url);

    exit;

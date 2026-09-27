<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/db.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/auth.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Components/SavedPosts/SavedPostDB.php';

use Ramsey\Uuid\Uuid;

requireLogin();

$userId = (int) currentUserId();

$savedPosts = getSavedPosts($pdo, $userId);

foreach($savedPosts as &$savedPost) {
    $savedPost['uuid'] = Uuid::fromBytes($savedPost['public_id'])->toString();
}

unset($savedPost);

require_once __DIR__ . '/SavedPosts.html.php';
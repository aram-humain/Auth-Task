<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/db.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/auth.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/authorization.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/csrf.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Components/Posts/PostsDB.php';

use Ramsey\Uuid\Uuid;

requirePermission($pdo, 'view_deleted_posts');

$deletedPosts = getDeletedPosts($pdo);

foreach($deletedPosts as &$post) {
    $post['uuid'] = Uuid::fromBytes($post['public_id'])->toString();
}

unset($post);

$csrfToken = csrfToken();

require_once __DIR__ . '/DeletedPosts.html.php';
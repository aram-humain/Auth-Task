<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/db.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/csrf.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/flash.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/auth.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/authorization.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Components/Posts/PostsDB.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Components/Comments/CommentsDB.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Components/Likes/LikeDB.php';

use Ramsey\Uuid\Uuid;

$uuid = trim($_GET['id'] ?? '');


if($uuid === '' || !Uuid::isValid($uuid)) {
    http_response_code(404);
    exit('Post not found');
}

$publicIdBytes = Uuid::fromString($uuid)->getBytes();

$post = getPostByPublicId($pdo, $publicIdBytes);

if($post === null) {
    http_response_code(404);

    exit('Post not found.');
}

$viewerUserId = currentUserId();

$isOwner = $viewerUserId !== null && (int) $post['user_id'] === $viewerUserId;

$isAdmin = $viewerUserId !== null && hasRole($pdo, 'Admin');


if ($post['deleted_at'] !== null) {
    if($viewerUserId === null || !can($pdo, 'view_deleted_posts')) {
        http_response_code(404);

        exit('Post not found.');
    }
}


// draft / archived post permission check (only owner or admin)
if($post['deleted_at'] === null && $post['status'] !== 'published' && !$isOwner && !$isAdmin) {
    http_response_code(404);
    exit('Post not found');
}

$images = getPostImages(
    $pdo,
    (int) $post['id']
);

$tags = getPostTags(
    $pdo,
    (int) $post['id']
);


$authorName = trim(
    ($post['first_name'] ?? '')
    . ' '
    . ($post['last_name'] ?? '')
);

if ($authorName === '') {
    $authorName = 'User';
}

$comments = getPostComments($pdo, (int) $post['id']);

foreach ($comments as &$comment) {

    $comment['uuid'] =
        Uuid::fromBytes(
            $comment['public_id']
        )->toString();
}

unset($comment);

$topLevelComments = [];
$repliesByParent = [];

foreach($comments as $comment) {
    if($comment['parent_id'] === null) {
        $topLevelComments[] = $comment;
    } else {
        $parentId = (int) $comment['parent_id'];

        $repliesByParent[$parentId][] = $comment;
    }
}

$csrfToken = csrfToken();

$canComment = $viewerUserId !== null && $post['deleted_at'] === null && $post['status'] === 'published';

$flashSuccess = getFlash('success');

$flashError = getFlash('error');

$canModerateComments = $viewerUserId !== null && can($pdo, 'moderate_comments');

$likeCount = getPostLikeCount(
    $pdo,
    (int) $post['id']
);


$hasLiked =
    $viewerUserId !== null
    && hasUserLikedPost(
        $pdo,
        (int) $post['id'],
        $viewerUserId
    );


$canLike =
    $viewerUserId !== null
    && $post['deleted_at'] === null
    && $post['status'] === 'published';


$canReportPost =
    $viewerUserId !== null
    && (int) $post['user_id'] !== $viewerUserId
    && $post['status'] === 'published'
    && $post['deleted_at'] === null;

require_once __DIR__ . '/Post.html.php';
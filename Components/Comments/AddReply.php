<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/db.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/auth.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/csrf.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/flash.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/rate_limit.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Components/Posts/PostsDB.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Components/Comments/CommentsDB.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Components/Notifications/NotificationDB.php';

use Ramsey\Uuid\Uuid;


requireLogin();


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


$userId = currentUserId();


$postUuid = trim(
    $_POST['post_id'] ?? ''
);



$parentUuid = trim(
    $_POST['parent_uuid'] ?? ''
);


$content = trim(
    $_POST['content'] ?? ''
);



if (
    $postUuid === ''
    || !Uuid::isValid($postUuid)
) {
    http_response_code(400);
    exit('Invalid post.');
}



if (
    $parentUuid === ''
    || !Uuid::isValid($parentUuid)
) {
    http_response_code(400);
    exit('Invalid parent comment.');
}



$postPublicId = Uuid::fromString(
    $postUuid
)->getBytes();


$post = getPostByPublicId(
    $pdo,
    $postPublicId
);


if (
    $post === null
    || $post['deleted_at'] !== null
    || $post['status'] !== 'published'
) {
    http_response_code(404);
    exit('Post not found.');
}



$parentPublicId = Uuid::fromString(
    $parentUuid
)->getBytes();


$parentComment = getActiveCommentByPublicId(
    $pdo,
    $parentPublicId
);


if ($parentComment === null) {
    http_response_code(404);
    exit('Parent comment not found.');
}



if (
    (int) $parentComment['post_id']
    !== (int) $post['id']
) {
    http_response_code(400);
    exit('Invalid parent comment.');
}



$normalizedParentId =
    $parentComment['parent_id'] !== null
    ? (int) $parentComment['parent_id']
    : (int) $parentComment['id'];


if (!isUserVerified(
    $pdo,
    $userId
)) {

    setFlash(
        'error',
        'You must verify your email before replying.'
    );

    header(
        'Location: /Components/Posts/Post.php?id='
            . urlencode($postUuid)
            . '#comments'
    );

    exit;
}


if ($content === '') {

    setFlash(
        'error',
        'Reply cannot be empty.'
    );

    header(
        'Location: /Components/Posts/Post.php?id='
            . urlencode($postUuid)
            . '#comments'
    );

    exit;
}


if (mb_strlen($content) > 2000) {

    setFlash(
        'error',
        'Reply is too long.'
    );

    header(
        'Location: /Components/Posts/Post.php?id='
            . urlencode($postUuid)
            . '#comments'
    );

    exit;
}



$commentAttempts = countRecentAttempts(
    $pdo,
    'comment_create',
    60,
    $userId
);


if ($commentAttempts >= 5) {

    setFlash(
        'error',
        'Too many comments. Please wait one minute and try again.'
    );

    header(
        'Location: /Components/Posts/Post.php?id='
            . urlencode($postUuid)
            . '#comments'
    );

    exit;
}


try {

    $pdo->beginTransaction();



    $reply = createComment(
        $pdo,
        (int) $post['id'],
        $userId,
        $content,
        $normalizedParentId
    );

    createNotification(
        $pdo,
        (int) $parentComment['user_id'],
        $userId,
        'comment_reply',
        (int) $post['id'],
        (int) $reply['id']
    );


    recordRateLimitAttempt(
        $pdo,
        'comment_create',
        $userId,
        null,
        $_SERVER['REMOTE_ADDR'] ?? null
    );


    $pdo->commit();


    setFlash(
        'success',
        'Reply added successfully.'
    );


    header(
        'Location: /Components/Posts/Post.php?id='
            . urlencode($postUuid)
            . '#comment-'
            . urlencode($reply['uuid'])
    );

    exit;
} catch (Throwable $exception) {

    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }


    http_response_code(500);

    exit('Unable to add reply.');
}

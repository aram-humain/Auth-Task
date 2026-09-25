<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/db.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/auth.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/csrf.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/flash.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Components/Comments/CommentsDB.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Components/Reports/ReportDB.php';

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


$commentUuid = trim(
    $_POST['comment_uuid'] ?? ''
);


$reason = trim(
    $_POST['reason'] ?? ''
);


$description = trim(
    $_POST['description'] ?? ''
);


if (
    $commentUuid === ''
    || !Uuid::isValid($commentUuid)
) {

    http_response_code(404);

    exit('Comment not found.');
}


$commentPublicId =
    Uuid::fromString(
        $commentUuid
    )->getBytes();


$comment = getCommentByPublicId(
    $pdo,
    $commentPublicId
);


if (
    $comment === null
    || $comment['deleted_at'] !== null
    || $comment['post_deleted_at'] !== null
    || $comment['post_status'] !== 'published'
) {

    http_response_code(404);

    exit('Comment not found.');
}


$commentId = (int) $comment['id'];


if (
    (int) $comment['user_id']
    === $userId
) {

    setFlash(
        'error',
        'You cannot report your own comment.'
    );


    $postUuid =
        Uuid::fromBytes(
            $comment['post_public_id']
        )->toString();


    header(
        'Location: /Components/Posts/Post.php?id='
        . urlencode($postUuid)
        . '#comment-'
        . urlencode($commentUuid)
    );

    exit;
}


if ($reason === '') {

    setFlash(
        'error',
        'Report reason is required.'
    );


    $postUuid =
        Uuid::fromBytes(
            $comment['post_public_id']
        )->toString();


    header(
        'Location: /Components/Posts/Post.php?id='
        . urlencode($postUuid)
        . '#comment-'
        . urlencode($commentUuid)
    );

    exit;
}


if (mb_strlen($reason) > 50) {

    setFlash(
        'error',
        'Report reason is too long.'
    );


    $postUuid =
        Uuid::fromBytes(
            $comment['post_public_id']
        )->toString();


    header(
        'Location: /Components/Posts/Post.php?id='
        . urlencode($postUuid)
        . '#comment-'
        . urlencode($commentUuid)
    );

    exit;
}


if (
    $description !== ''
    && mb_strlen($description) > 2000
) {

    setFlash(
        'error',
        'Report description is too long.'
    );


    $postUuid =
        Uuid::fromBytes(
            $comment['post_public_id']
        )->toString();


    header(
        'Location: /Components/Posts/Post.php?id='
        . urlencode($postUuid)
        . '#comment-'
        . urlencode($commentUuid)
    );

    exit;
}


$description =
    $description === ''
        ? null
        : $description;


if (
    hasUserReportedComment(
        $pdo,
        $commentId,
        $userId
    )
) {

    setFlash(
        'error',
        'You have already reported this comment.'
    );


    $postUuid =
        Uuid::fromBytes(
            $comment['post_public_id']
        )->toString();


    header(
        'Location: /Components/Posts/Post.php?id='
        . urlencode($postUuid)
        . '#comment-'
        . urlencode($commentUuid)
    );

    exit;
}


try {

    createCommentReport(
        $pdo,
        $commentId,
        $userId,
        $reason,
        $description
    );


    setFlash(
        'success',
        'Comment reported successfully.'
    );


    $postUuid =
        Uuid::fromBytes(
            $comment['post_public_id']
        )->toString();


    header(
        'Location: /Components/Posts/Post.php?id='
        . urlencode($postUuid)
        . '#comment-'
        . urlencode($commentUuid)
    );

    exit;


} catch (PDOException $exception) {

    if ($exception->getCode() === '23000') {

        setFlash(
            'error',
            'You have already reported this comment.'
        );


        $postUuid =
            Uuid::fromBytes(
                $comment['post_public_id']
            )->toString();


        header(
            'Location: /Components/Posts/Post.php?id='
            . urlencode($postUuid)
            . '#comment-'
            . urlencode($commentUuid)
        );

        exit;
    }


    http_response_code(500);

    exit('Unable to report comment.');
}
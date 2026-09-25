<?php

/** @var array<string, mixed> $post */
/** @var array<int, array<string, mixed>> $images */
/** @var array<int, array<string, mixed>> $tags */
/** @var string $authorName */
/** @var string $uuid */
/** @var bool $isOwner */
/** @var bool $isAdmin */
/** @var string $csrfToken */
/** @var int|null $viewerUserId */
/** @var bool $canComment */
/** @var array $comments */
/** @var array $topLevelComments */
/** @var array $repliesByParent */
/** @var string|null $flashError */
/** @var string|null $flashSuccess */
/** @var bool $canModerateComments */
/** @var int $likeCount */
/** @var bool $hasLiked */
/** @var bool $canLike */
/** @var bool $canReportPost */

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>
        <?= htmlspecialchars(
            $post['title'],
            ENT_QUOTES,
            'UTF-8'
        ) ?>
    </title>

    <link
        rel="stylesheet"
        href="/assets/css/theme.css">

    <link
        rel="stylesheet"
        href="/Components/Posts/Post.css">

    <script
        src="/assets/js/theme.js"
        defer>
    </script>

</head>

<body>


<button
    type="button"
    id="theme-toggle"
    class="theme-toggle">

    Theme

</button>


<main class="post-page">


    <article class="post-card">


        <?php if ($post['deleted_at'] !== null): ?>

            <div class="post-warning">

                This post has been deleted.

            </div>

        <?php endif; ?>


        <!-- =========================
             POST HEADER
             ========================= -->

        <header class="post-header">


            <div class="post-author">


                <a
                    href="/Components/Profile/Profile.php?slug=<?= urlencode(
                        $post['public_slug']
                    ) ?>"
                    class="post-author-name">

                    <?= htmlspecialchars(
                        $authorName,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>

                </a>


                <div class="post-meta">


                    <span>

                        <?= htmlspecialchars(
                            $post['category_name'],
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>

                    </span>


                    <span>·</span>


                    <time
                        datetime="<?= htmlspecialchars(
                            $post['created_at'],
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>">

                        <?= htmlspecialchars(
                            date(
                                'd M Y, H:i',
                                strtotime(
                                    $post['created_at']
                                )
                            ),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>

                    </time>


                </div>


            </div>


            <?php if ($isOwner || $isAdmin): ?>

                <div class="post-status">

                    <?= htmlspecialchars(
                        ucfirst(
                            $post['status']
                        ),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>

                </div>

            <?php endif; ?>


        </header>


        <!-- =========================
             POST CONTENT
             ========================= -->

        <section class="post-content">


            <h1>

                <?= htmlspecialchars(
                    $post['title'],
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>

            </h1>


            <?php if (
                trim(
                    $post['content'] ?? ''
                ) !== ''
            ): ?>

                <div class="post-text">

                    <?= nl2br(
                        htmlspecialchars(
                            $post['content'],
                            ENT_QUOTES,
                            'UTF-8'
                        )
                    ) ?>

                </div>

            <?php endif; ?>


        </section>


        <!-- =========================
             IMAGES
             ========================= -->

        <?php if (!empty($images)): ?>

            <section class="post-images">


                <?php foreach ($images as $image): ?>

                    <div class="post-image">


                        <img
                            src="<?= htmlspecialchars(
                                $image['image_url'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            alt="Post image"
                            loading="lazy">


                    </div>

                <?php endforeach; ?>


            </section>

        <?php endif; ?>


        <!-- =========================
             TAGS
             ========================= -->

        <?php if (!empty($tags)): ?>

            <section class="post-tags">


                <?php foreach ($tags as $tag): ?>

                    <span class="post-tag">

                        #<?= htmlspecialchars(
                            $tag['name'],
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>

                    </span>

                <?php endforeach; ?>


            </section>

        <?php endif; ?>


        <!-- =========================
             LIKES
             ========================= -->

        <section class="post-reactions">


            <div class="post-like-area">


                <?php if ($canLike): ?>


                    <form
                        method="POST"
                        action="/Components/Likes/ToggleLike.php"
                        class="like-form">


                        <input
                            type="hidden"
                            name="csrf_token"
                            value="<?= htmlspecialchars(
                                $csrfToken,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>">


                        <input
                            type="hidden"
                            name="post_id"
                            value="<?= htmlspecialchars(
                                $uuid,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>">


                        <button
                            type="submit"
                            class="like-button <?= $hasLiked
                                ? 'like-button-active'
                                : '' ?>">

                            <?= $hasLiked
                                ? 'Unlike'
                                : 'Like' ?>


                            <span class="like-count">

                                <?= (int) $likeCount ?>

                            </span>


                        </button>


                    </form>


                <?php else: ?>


                    <div class="like-readonly">

                        Likes:

                        <span>

                            <?= (int) $likeCount ?>

                        </span>

                    </div>


                <?php endif; ?>


            </div>


        </section>


        <!-- =========================
             REPORT POST
             ========================= -->

        <?php if ($canReportPost): ?>


            <section class="post-report-section">


                <details class="post-report-details">


                    <summary class="post-report-button">

                        Report Post

                    </summary>


                    <form
                        method="POST"
                        action="/Components/Reports/ReportPost.php"
                        class="post-report-form">


                        <input
                            type="hidden"
                            name="csrf_token"
                            value="<?= htmlspecialchars(
                                $csrfToken,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>">


                        <input
                            type="hidden"
                            name="post_id"
                            value="<?= htmlspecialchars(
                                $uuid,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>">


                        <label>

                            Reason

                            <input
                                type="text"
                                name="reason"
                                maxlength="50"
                                required
                                placeholder="Why are you reporting this post?">

                        </label>


                        <label>

                            Description

                            <textarea
                                name="description"
                                maxlength="2000"
                                rows="4"
                                placeholder="Additional details (optional)"></textarea>

                        </label>


                        <button
                            type="submit"
                            class="post-report-submit">

                            Submit Report

                        </button>


                    </form>


                </details>


            </section>


        <?php endif; ?>


        <!-- =========================
             COMMENTS
             ========================= -->

        <section
            class="comments-section"
            id="comments">


            <!-- FLASH SUCCESS -->

            <?php if ($flashSuccess !== null): ?>

                <div
                    class="comment-message
                           comment-message-success">

                    <?= htmlspecialchars(
                        $flashSuccess,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>

                </div>

            <?php endif; ?>


            <!-- FLASH ERROR -->

            <?php if ($flashError !== null): ?>

                <div
                    class="comment-message
                           comment-message-error">

                    <?= htmlspecialchars(
                        $flashError,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>

                </div>

            <?php endif; ?>


            <!-- COMMENT HEADER -->

            <div class="comments-header">


                <h2>

                    Comments

                </h2>


                <span class="comments-count">

                    <?= count($comments) ?>

                </span>


            </div>


            <!-- =========================
                 ADD COMMENT
                 ========================= -->

            <?php if ($canComment): ?>


                <form
                    method="POST"
                    action="/Components/Comments/AddComment.php"
                    class="comment-form">


                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?= htmlspecialchars(
                            $csrfToken,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>">


                    <input
                        type="hidden"
                        name="post_id"
                        value="<?= htmlspecialchars(
                            $uuid,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>">


                    <textarea
                        name="content"
                        rows="4"
                        maxlength="2000"
                        required
                        placeholder="Write a comment..."></textarea>


                    <div class="comment-form-actions">


                        <button
                            type="submit"
                            class="comment-submit-button">

                            Add Comment

                        </button>


                    </div>


                </form>


            <?php elseif ($viewerUserId === null): ?>


                <div class="comment-login-message">


                    <a
                        href="/Components/Login/Login.php">

                        Log in

                    </a>

                    to leave a comment.


                </div>


            <?php endif; ?>


            <!-- =========================
                 COMMENT LIST
                 ========================= -->

            <?php if (empty($comments)): ?>


                <div class="comments-empty">

                    No comments yet.

                </div>


            <?php else: ?>


                <div class="comments-list">


                    <?php foreach (
                        $topLevelComments
                        as $comment
                    ): ?>


                        <?php

                        $commentAuthor = trim(
                            ($comment['first_name'] ?? '')
                            . ' '
                            . ($comment['last_name'] ?? '')
                        );


                        if ($commentAuthor === '') {
                            $commentAuthor = 'User';
                        }


                        $isCommentOwner =
                            $viewerUserId !== null
                            && (int) $comment['user_id']
                                === $viewerUserId;


                        $canDeleteComment =
                            $isCommentOwner
                            || $isOwner
                            || $canModerateComments;


                        /*
                         * Logged-in users may report
                         * comments belonging to other users.
                         */
                        $canReportComment =
                            $viewerUserId !== null
                            && !$isCommentOwner;


                        $commentReplies =
                            $repliesByParent[
                                (int) $comment['id']
                            ]
                            ?? [];

                        ?>


                        <!-- =========================
                             TOP LEVEL COMMENT
                             ========================= -->

                        <article
                            class="comment-card"
                            id="comment-<?= htmlspecialchars(
                                $comment['uuid'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>">


                            <div class="comment-card-header">


                                <a
                                    href="/Components/Profile/Profile.php?slug=<?= urlencode(
                                        $comment['public_slug']
                                    ) ?>"
                                    class="comment-author">

                                    <?= htmlspecialchars(
                                        $commentAuthor,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>

                                </a>


                                <time class="comment-date">

                                    <?= htmlspecialchars(
                                        date(
                                            'd M Y, H:i',
                                            strtotime(
                                                $comment['created_at']
                                            )
                                        ),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>

                                </time>


                            </div>


                            <div class="comment-content">

                                <?= nl2br(
                                    htmlspecialchars(
                                        $comment['content'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    )
                                ) ?>

                            </div>


                            <!-- =========================
                                 COMMENT ACTIONS
                                 ========================= -->

                            <?php if (
                                $viewerUserId !== null
                            ): ?>


                                <div class="comment-actions">


                                    <!-- EDIT -->

                                    <?php if (
                                        $isCommentOwner
                                    ): ?>


                                        <a
                                            href="/Components/Comments/EditComment.php?id=<?= urlencode(
                                                $comment['uuid']
                                            ) ?>"
                                            class="comment-edit-button">

                                            Edit

                                        </a>


                                    <?php endif; ?>


                                    <!-- REPLY -->

                                    <?php if ($canComment): ?>


                                        <details class="reply-details">


                                            <summary
                                                class="reply-button">

                                                Reply

                                            </summary>


                                            <form
                                                method="POST"
                                                action="/Components/Comments/AddReply.php"
                                                class="reply-form">


                                                <input
                                                    type="hidden"
                                                    name="csrf_token"
                                                    value="<?= htmlspecialchars(
                                                        $csrfToken,
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>">


                                                <input
                                                    type="hidden"
                                                    name="post_id"
                                                    value="<?= htmlspecialchars(
                                                        $uuid,
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>">


                                                <input
                                                    type="hidden"
                                                    name="parent_uuid"
                                                    value="<?= htmlspecialchars(
                                                        $comment['uuid'],
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>">


                                                <textarea
                                                    name="content"
                                                    maxlength="2000"
                                                    rows="3"
                                                    required
                                                    placeholder="Write a reply..."></textarea>


                                                <button
                                                    type="submit"
                                                    class="reply-submit-button">

                                                    Send Reply

                                                </button>


                                            </form>


                                        </details>


                                    <?php endif; ?>


                                    <!-- REPORT COMMENT -->

                                    <?php if (
                                        $canReportComment
                                    ): ?>


                                        <details class="comment-report-details">


                                            <summary
                                                class="comment-report-button">

                                                Report

                                            </summary>


                                            <form
                                                method="POST"
                                                action="/Components/Reports/ReportComment.php"
                                                class="comment-report-form">


                                                <input
                                                    type="hidden"
                                                    name="csrf_token"
                                                    value="<?= htmlspecialchars(
                                                        $csrfToken,
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>">


                                                <input
                                                    type="hidden"
                                                    name="comment_uuid"
                                                    value="<?= htmlspecialchars(
                                                        $comment['uuid'],
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>">


                                                <input
                                                    type="text"
                                                    name="reason"
                                                    maxlength="50"
                                                    required
                                                    placeholder="Report reason">


                                                <textarea
                                                    name="description"
                                                    maxlength="2000"
                                                    rows="3"
                                                    placeholder="Additional details (optional)"></textarea>


                                                <button
                                                    type="submit"
                                                    class="comment-report-submit">

                                                    Submit Report

                                                </button>


                                            </form>


                                        </details>


                                    <?php endif; ?>


                                    <!-- DELETE COMMENT -->

                                    <?php if (
                                        $canDeleteComment
                                    ): ?>


                                        <form
                                            method="POST"
                                            action="/Components/Comments/DeleteComment.php"
                                            class="comment-delete-form"
                                            onsubmit="return confirm('Delete this comment?');">


                                            <input
                                                type="hidden"
                                                name="csrf_token"
                                                value="<?= htmlspecialchars(
                                                    $csrfToken,
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>">


                                            <input
                                                type="hidden"
                                                name="comment_uuid"
                                                value="<?= htmlspecialchars(
                                                    $comment['uuid'],
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>">


                                            <button
                                                type="submit"
                                                class="comment-delete-button">

                                                Delete

                                            </button>


                                        </form>


                                    <?php endif; ?>


                                </div>


                            <?php endif; ?>


                            <!-- =========================
                                 REPLIES
                                 ========================= -->

                            <?php if (
                                !empty($commentReplies)
                            ): ?>


                                <div class="comment-replies">


                                    <?php foreach (
                                        $commentReplies
                                        as $reply
                                    ): ?>


                                        <?php

                                        $replyAuthor = trim(
                                            ($reply['first_name'] ?? '')
                                            . ' '
                                            . ($reply['last_name'] ?? '')
                                        );


                                        if ($replyAuthor === '') {
                                            $replyAuthor = 'User';
                                        }


                                        $isReplyOwner =
                                            $viewerUserId !== null
                                            && (int) $reply['user_id']
                                                === $viewerUserId;


                                        $canDeleteReply =
                                            $isReplyOwner
                                            || $isOwner
                                            || $canModerateComments;


                                        $canReportReply =
                                            $viewerUserId !== null
                                            && !$isReplyOwner;

                                        ?>


                                        <!-- =========================
                                             REPLY CARD
                                             ========================= -->

                                        <article
                                            class="reply-card"
                                            id="comment-<?= htmlspecialchars(
                                                $reply['uuid'],
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>">


                                            <div
                                                class="comment-card-header">


                                                <a
                                                    href="/Components/Profile/Profile.php?slug=<?= urlencode(
                                                        $reply['public_slug']
                                                    ) ?>"
                                                    class="comment-author">

                                                    <?= htmlspecialchars(
                                                        $replyAuthor,
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>

                                                </a>


                                                <time
                                                    class="comment-date">

                                                    <?= htmlspecialchars(
                                                        date(
                                                            'd M Y, H:i',
                                                            strtotime(
                                                                $reply['created_at']
                                                            )
                                                        ),
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>

                                                </time>


                                            </div>


                                            <div class="comment-content">

                                                <?= nl2br(
                                                    htmlspecialchars(
                                                        $reply['content'],
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    )
                                                ) ?>

                                            </div>


                                            <!-- =========================
                                                 REPLY ACTIONS
                                                 ========================= -->

                                            <?php if (
                                                $viewerUserId !== null
                                            ): ?>


                                                <div
                                                    class="comment-actions">


                                                    <!-- EDIT REPLY -->

                                                    <?php if (
                                                        $isReplyOwner
                                                    ): ?>


                                                        <a
                                                            href="/Components/Comments/EditComment.php?id=<?= urlencode(
                                                                $reply['uuid']
                                                            ) ?>"
                                                            class="comment-edit-button">

                                                            Edit

                                                        </a>


                                                    <?php endif; ?>


                                                    <!-- REPLY TO REPLY -->

                                                    <?php if (
                                                        $canComment
                                                    ): ?>


                                                        <details
                                                            class="reply-details">


                                                            <summary
                                                                class="reply-button">

                                                                Reply

                                                            </summary>


                                                            <form
                                                                method="POST"
                                                                action="/Components/Comments/AddReply.php"
                                                                class="reply-form">


                                                                <input
                                                                    type="hidden"
                                                                    name="csrf_token"
                                                                    value="<?= htmlspecialchars(
                                                                        $csrfToken,
                                                                        ENT_QUOTES,
                                                                        'UTF-8'
                                                                    ) ?>">


                                                                <input
                                                                    type="hidden"
                                                                    name="post_id"
                                                                    value="<?= htmlspecialchars(
                                                                        $uuid,
                                                                        ENT_QUOTES,
                                                                        'UTF-8'
                                                                    ) ?>">


                                                                <input
                                                                    type="hidden"
                                                                    name="parent_uuid"
                                                                    value="<?= htmlspecialchars(
                                                                        $reply['uuid'],
                                                                        ENT_QUOTES,
                                                                        'UTF-8'
                                                                    ) ?>">


                                                                <textarea
                                                                    name="content"
                                                                    maxlength="2000"
                                                                    rows="3"
                                                                    required
                                                                    placeholder="Write a reply..."></textarea>


                                                                <button
                                                                    type="submit"
                                                                    class="reply-submit-button">

                                                                    Send Reply

                                                                </button>


                                                            </form>


                                                        </details>


                                                    <?php endif; ?>


                                                    <!-- REPORT REPLY -->

                                                    <?php if (
                                                        $canReportReply
                                                    ): ?>


                                                        <details
                                                            class="comment-report-details">


                                                            <summary
                                                                class="comment-report-button">

                                                                Report

                                                            </summary>


                                                            <form
                                                                method="POST"
                                                                action="/Components/Reports/ReportComment.php"
                                                                class="comment-report-form">


                                                                <input
                                                                    type="hidden"
                                                                    name="csrf_token"
                                                                    value="<?= htmlspecialchars(
                                                                        $csrfToken,
                                                                        ENT_QUOTES,
                                                                        'UTF-8'
                                                                    ) ?>">


                                                                <input
                                                                    type="hidden"
                                                                    name="comment_uuid"
                                                                    value="<?= htmlspecialchars(
                                                                        $reply['uuid'],
                                                                        ENT_QUOTES,
                                                                        'UTF-8'
                                                                    ) ?>">


                                                                <input
                                                                    type="text"
                                                                    name="reason"
                                                                    maxlength="50"
                                                                    required
                                                                    placeholder="Report reason">


                                                                <textarea
                                                                    name="description"
                                                                    maxlength="2000"
                                                                    rows="3"
                                                                    placeholder="Additional details (optional)"></textarea>


                                                                <button
                                                                    type="submit"
                                                                    class="comment-report-submit">

                                                                    Submit Report

                                                                </button>


                                                            </form>


                                                        </details>


                                                    <?php endif; ?>


                                                    <!-- DELETE REPLY -->

                                                    <?php if (
                                                        $canDeleteReply
                                                    ): ?>


                                                        <form
                                                            method="POST"
                                                            action="/Components/Comments/DeleteComment.php"
                                                            class="comment-delete-form"
                                                            onsubmit="return confirm('Delete this reply?');">


                                                            <input
                                                                type="hidden"
                                                                name="csrf_token"
                                                                value="<?= htmlspecialchars(
                                                                    $csrfToken,
                                                                    ENT_QUOTES,
                                                                    'UTF-8'
                                                                ) ?>">


                                                            <input
                                                                type="hidden"
                                                                name="comment_uuid"
                                                                value="<?= htmlspecialchars(
                                                                    $reply['uuid'],
                                                                    ENT_QUOTES,
                                                                    'UTF-8'
                                                                ) ?>">


                                                            <button
                                                                type="submit"
                                                                class="comment-delete-button">

                                                                Delete

                                                            </button>


                                                        </form>


                                                    <?php endif; ?>


                                                </div>


                                            <?php endif; ?>


                                        </article>


                                    <?php endforeach; ?>


                                </div>


                            <?php endif; ?>


                        </article>


                    <?php endforeach; ?>


                </div>


            <?php endif; ?>


        </section>


        <!-- =========================
             POST FOOTER
             ========================= -->

        <footer class="post-footer">


            <a
                href="/Components/Posts/Posts.php"
                class="post-back-button">

                Back to Posts

            </a>


            <?php if (
                $isOwner
                && $post['deleted_at'] === null
            ): ?>


                <div class="post-owner-actions">


                    <span class="post-owner-label">

                        Your post

                    </span>


                    <a
                        href="/Components/Posts/EditPost.php?id=<?= urlencode(
                            $uuid
                        ) ?>"
                        class="post-edit-button">

                        Edit Post

                    </a>


                    <form
                        method="POST"
                        action="/Components/Posts/DeletePost.php"
                        class="post-delete-form"
                        onsubmit="return confirm('Are you sure you want to delete this post?');">


                        <input
                            type="hidden"
                            name="csrf_token"
                            value="<?= htmlspecialchars(
                                $csrfToken,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>">


                        <input
                            type="hidden"
                            name="id"
                            value="<?= htmlspecialchars(
                                $uuid,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>">


                        <button
                            type="submit"
                            class="post-delete-button">

                            Delete Post

                        </button>


                    </form>


                </div>


            <?php endif; ?>


        </footer>


    </article>


</main>


</body>

</html>
<?php

/** @var array<string, mixed> $post */
/** @var array<int, array<string, mixed>> $images */
/** @var array<int, array<string, mixed>> $tags */
/** @var string $authorName */
/** @var string $uuid */
/** @var bool $isOwner */
/** @var bool $isAdmin */
/** @var string $csrfToken */
/** @var int $viewerUserId */
/** @var bool $canComment */
/** @var array $comments */
/** @var string $flashError */
/** @var string $flashSuccess */
/** @var null|bool $canModerateComments */

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


            <section
                class="comments-section"
                id="comments">

                <?php if ($flashSuccess !== null): ?>

                    <div class="comment-message comment-message-success">

                        <?= htmlspecialchars(
                            $flashSuccess,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>

                    </div>

                <?php endif; ?>


                <?php if ($flashError !== null): ?>

                    <div class="comment-message comment-message-error">

                        <?= htmlspecialchars(
                            $flashError,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>

                    </div>

                <?php endif; ?>
                <div class="comments-header">

                    <h2>
                        Comments
                    </h2>

                    <span class="comments-count">

                        <?= count($comments) ?>

                    </span>

                </div>


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

                        <a href="/Components/Login/Login.php">
                            Log in
                        </a>

                        to leave a comment.

                    </div>

                <?php endif; ?>


                <?php if (empty($comments)): ?>

                    <div class="comments-empty">

                        No comments yet.

                    </div>

                <?php else: ?>

                    <div class="comments-list">

                        <?php foreach ($comments as $comment): ?>

                            <?php

                            $commentAuthor = trim(
                                ($comment['first_name'] ?? '')
                                    . ' '
                                    . ($comment['last_name'] ?? '')
                            );

                            if ($commentAuthor === '') {
                                $commentAuthor = 'User';
                            }

                            $isCommentOwner = $viewerUserId !== null && (int) $comment['user_id'] === $viewerUserId;


                            $canDeleteComment = $isCommentOwner || $isOwner || $canModerateComments;
                            ?>

                            <article
                                class="comment-card"
                                id="comment-<?= (int) $comment['id'] ?>">

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

                                <?php if (
                                    $isCommentOwner
                                    || $canDeleteComment
                                ): ?>

                                    <div class="comment-actions">


                                        <?php if ($isCommentOwner): ?>

                                            <a
                                                href="/Components/Comments/EditComment.php?id=<?= (int) $comment['id'] ?>"
                                                class="comment-edit-button">

                                                Edit

                                            </a>

                                        <?php endif; ?>


                                        <?php if ($canDeleteComment): ?>

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
                                                    name="comment_id"
                                                    value="<?= (int) $comment['id'] ?>">


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

            </section>

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

                        <div class="post-owner-actions">

                            <span class="post-owner-label">
                                Your post
                            </span>

                            <a
                                href="/Components/Posts/EditPost.php?id=<?= urlencode($uuid) ?>"
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
                    </div>

                <?php endif; ?>

            </footer>


        </article>

    </main>


</body>

</html>
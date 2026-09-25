<?php

/** @var array $postReports */
/** @var array $commentReports */
/** @var bool $canModeratePosts */
/** @var bool $canModerateComments */
/** @var string $csrfToken */
/** @var string|null $flashSuccess */
/** @var string|null $flashError */

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>
        Moderation
    </title>


    <link
        rel="stylesheet"
        href="/assets/css/theme.css">


    <link
        rel="stylesheet"
        href="/Components/Moderator/Moderator.css">


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


    <main class="moderator-page">


        <header class="moderator-header">


            <div>

                <h1>
                    Moderation
                </h1>

                <p>
                    Review reported posts and comments.
                </p>

            </div>


            <a
                href="/Components/Dashboard/Dashboard.php"
                class="back-button">

                Back to Dashboard

            </a>


        </header>


        <?php if ($flashSuccess !== null): ?>

            <div class="message message-success">

                <?= htmlspecialchars(
                    $flashSuccess,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>

            </div>

        <?php endif; ?>


        <?php if ($flashError !== null): ?>

            <div class="message message-error">

                <?= htmlspecialchars(
                    $flashError,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>

            </div>

        <?php endif; ?>


        <!-- =========================
         REPORTED POSTS
         ========================= -->

        <?php if ($canModeratePosts): ?>


            <section class="moderation-section">


                <div class="section-header">

                    <h2>
                        Reported Posts
                    </h2>

                    <span class="count-badge">

                        <?= count($postReports) ?>

                    </span>

                </div>


                <?php if (empty($postReports)): ?>


                    <div class="empty-state">

                        No pending post reports.

                    </div>


                <?php else: ?>


                    <div class="reports-list">


                        <?php foreach (
                            $postReports
                            as $report
                        ): ?>


                            <?php

                            $reporterName = trim(
                                ($report['reporter_first_name'] ?? '')
                                    . ' '
                                    . ($report['reporter_last_name'] ?? '')
                            );


                            if ($reporterName === '') {
                                $reporterName = 'User';
                            }


                            $authorName = trim(
                                ($report['author_first_name'] ?? '')
                                    . ' '
                                    . ($report['author_last_name'] ?? '')
                            );


                            if ($authorName === '') {
                                $authorName = 'User';
                            }

                            ?>


                            <article class="report-card">


                                <div class="report-top">


                                    <div>

                                        <span class="report-type">
                                            Post
                                        </span>


                                        <h3>

                                            <?= htmlspecialchars(
                                                $report['title'],
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>

                                        </h3>

                                    </div>


                                    <time>

                                        <?= htmlspecialchars(
                                            date(
                                                'd M Y, H:i',
                                                strtotime(
                                                    $report['created_at']
                                                )
                                            ),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>

                                    </time>


                                </div>


                                <div class="report-info">


                                    <div>

                                        <strong>
                                            Author:
                                        </strong>


                                        <a
                                            href="/Components/Profile/Profile.php?slug=<?= urlencode(
                                                                                            $report['author_slug']
                                                                                        ) ?>">

                                            <?= htmlspecialchars(
                                                $authorName,
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>

                                        </a>

                                    </div>


                                    <div>

                                        <strong>
                                            Reporter:
                                        </strong>


                                        <a
                                            href="/Components/Profile/Profile.php?slug=<?= urlencode(
                                                                                            $report['reporter_slug']
                                                                                        ) ?>">

                                            <?= htmlspecialchars(
                                                $reporterName,
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>

                                        </a>

                                    </div>


                                    <div>

                                        <strong>
                                            Reason:
                                        </strong>

                                        <?= htmlspecialchars(
                                            $report['reason'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>

                                    </div>


                                    <?php if (
                                        trim(
                                            $report['description']
                                                ?? ''
                                        ) !== ''
                                    ): ?>

                                        <div class="report-description">

                                            <strong>
                                                Description:
                                            </strong>

                                            <?= nl2br(
                                                htmlspecialchars(
                                                    $report['description'],
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                )
                                            ) ?>

                                        </div>

                                    <?php endif; ?>


                                </div>


                                <div class="report-actions">


                                    <?php if (
                                        $report['post_deleted_at']
                                        === null
                                    ): ?>


                                        <a
                                            href="/Components/Posts/Post.php?id=<?= urlencode(
                                                                                    $report['post_uuid']
                                                                                ) ?>"
                                            class="view-button">

                                            View Post

                                        </a>


                                    <?php else: ?>

                                        <span class="deleted-label">

                                            Post already deleted

                                        </span>

                                    <?php endif; ?>


                                    <form
                                        method="POST"
                                        action="/Components/Moderator/HidePost.php"
                                        onsubmit="return confirm('Hide this post?');">


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
                                            name="report_id"
                                            value="<?= (int) $report['report_id'] ?>">


                                        <button
                                            type="submit"
                                            class="hide-button">

                                            Hide Post

                                        </button>


                                    </form>

                                    <form
                                        method="POST"
                                        action="/Components/Moderator/DismissPostReport.php"
                                        onsubmit="return confirm('Dismiss this report without hiding the post?');">


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
                                            name="report_id"
                                            value="<?= (int) $report['report_id'] ?>">


                                        <button
                                            type="submit"
                                            class="dismiss-button">

                                            Dismiss Report

                                        </button>


                                    </form>


                                </div>


                            </article>


                        <?php endforeach; ?>


                    </div>


                <?php endif; ?>


            </section>


        <?php endif; ?>


        <!-- =========================
         REPORTED COMMENTS
         ========================= -->

        <?php if ($canModerateComments): ?>


            <section class="moderation-section">


                <div class="section-header">


                    <h2>
                        Reported Comments
                    </h2>


                    <span class="count-badge">

                        <?= count($commentReports) ?>

                    </span>


                </div>


                <?php if (
                    empty($commentReports)
                ): ?>


                    <div class="empty-state">

                        No pending comment reports.

                    </div>


                <?php else: ?>


                    <div class="reports-list">


                        <?php foreach (
                            $commentReports
                            as $report
                        ): ?>


                            <?php

                            $reporterName = trim(
                                ($report['reporter_first_name'] ?? '')
                                    . ' '
                                    . ($report['reporter_last_name'] ?? '')
                            );


                            if ($reporterName === '') {
                                $reporterName = 'User';
                            }


                            $authorName = trim(
                                ($report['author_first_name'] ?? '')
                                    . ' '
                                    . ($report['author_last_name'] ?? '')
                            );


                            if ($authorName === '') {
                                $authorName = 'User';
                            }

                            ?>


                            <article class="report-card">


                                <div class="report-top">


                                    <div>

                                        <span class="report-type">
                                            Comment
                                        </span>


                                        <div class="reported-comment">

                                            <?= nl2br(
                                                htmlspecialchars(
                                                    $report['content'],
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                )
                                            ) ?>

                                        </div>

                                    </div>


                                    <time>

                                        <?= htmlspecialchars(
                                            date(
                                                'd M Y, H:i',
                                                strtotime(
                                                    $report['created_at']
                                                )
                                            ),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>

                                    </time>


                                </div>


                                <div class="report-info">


                                    <div>

                                        <strong>
                                            Author:
                                        </strong>


                                        <a
                                            href="/Components/Profile/Profile.php?slug=<?= urlencode(
                                                                                            $report['author_slug']
                                                                                        ) ?>">

                                            <?= htmlspecialchars(
                                                $authorName,
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>

                                        </a>

                                    </div>


                                    <div>

                                        <strong>
                                            Reporter:
                                        </strong>


                                        <a
                                            href="/Components/Profile/Profile.php?slug=<?= urlencode(
                                                                                            $report['reporter_slug']
                                                                                        ) ?>">

                                            <?= htmlspecialchars(
                                                $reporterName,
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>

                                        </a>

                                    </div>


                                    <div>

                                        <strong>
                                            Reason:
                                        </strong>

                                        <?= htmlspecialchars(
                                            $report['reason'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>

                                    </div>


                                    <?php if (
                                        trim(
                                            $report['description']
                                                ?? ''
                                        ) !== ''
                                    ): ?>

                                        <div class="report-description">

                                            <strong>
                                                Description:
                                            </strong>

                                            <?= nl2br(
                                                htmlspecialchars(
                                                    $report['description'],
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                )
                                            ) ?>

                                        </div>

                                    <?php endif; ?>


                                </div>


                                <div class="report-actions">


                                    <?php if (
                                        $report['comment_deleted_at']
                                        === null
                                    ): ?>


                                        <a
                                            href="/Components/Posts/Post.php?id=<?= urlencode(
                                                                                    $report['post_uuid']
                                                                                ) ?>#comment-<?= urlencode(
                                                            $report['comment_uuid']
                                                        ) ?>"
                                            class="view-button">

                                            View Comment

                                        </a>


                                    <?php else: ?>

                                        <span class="deleted-label">

                                            Comment already deleted

                                        </span>

                                    <?php endif; ?>


                                    <form
                                        method="POST"
                                        action="/Components/Moderator/HideComment.php"
                                        onsubmit="return confirm('Hide this comment?');">


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
                                            name="report_id"
                                            value="<?= (int) $report['report_id'] ?>">


                                        <button
                                            type="submit"
                                            class="hide-button">

                                            Hide Comment

                                        </button>


                                    </form>

                                    <form
                                        method="POST"
                                        action="/Components/Moderator/DismissCommentReport.php"
                                        onsubmit="return confirm('Dismiss this report without hiding the comment?');">


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
                                            name="report_id"
                                            value="<?= (int) $report['report_id'] ?>">


                                        <button
                                            type="submit"
                                            class="dismiss-button">

                                            Dismiss Report

                                        </button>


                                    </form>


                                </div>


                            </article>


                        <?php endforeach; ?>


                    </div>


                <?php endif; ?>


            </section>


        <?php endif; ?>


    </main>


</body>

</html>
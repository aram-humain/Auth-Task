<?php

/** @var array $notifications */
/** @var int $unreadCount */
/** @var string $csrfToken */

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Notifications</title>


    <link
        rel="stylesheet"
        href="/assets/css/theme.css">

    <link
        rel="stylesheet"
        href="/Components/Notifications/Notifications.css">


    <script
        src="/assets/js/theme.js">
    </script>

</head>

<body>


<button
    type="button"
    id="theme-toggle"
    class="theme-toggle">
    Theme
</button>


<main class="notifications-container">


    <section class="notifications-card">


        <header class="notifications-header">

            <div>

                <h1>
                    Notifications
                </h1>


                <p>

                    <?php if ($unreadCount > 0): ?>

                        You have

                        <strong>
                            <?= htmlspecialchars(
                                (string) $unreadCount,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </strong>

                        unread notification<?= $unreadCount === 1
                            ? ''
                            : 's' ?>.

                    <?php else: ?>

                        You have no unread notifications.

                    <?php endif; ?>

                </p>

            </div>


            <?php if ($unreadCount > 0): ?>

                <form
                    action="/Components/Notifications/MarkAllRead.php"
                    method="POST">

                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?= htmlspecialchars(
                            $csrfToken,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>">

                    <button
                        type="submit"
                        class="mark-all-button">

                        Mark all as read

                    </button>

                </form>

            <?php endif; ?>

        </header>


        <?php if (empty($notifications)): ?>


            <div class="notifications-empty">

                <div class="notifications-empty-icon">
                    !
                </div>

                <h2>
                    No notifications yet
                </h2>

                <p>
                    Likes, comments and replies will appear here.
                </p>

            </div>


        <?php else: ?>


            <div class="notifications-list">


                <?php foreach ($notifications as $notification): ?>

                    <?php

                    $type =
                        $notification['type'];

                    $actorName =
                        trim(
                            $notification['actor_name']
                            ?? ''
                        );

                    if ($actorName === '') {
                        $actorName = 'Unknown user';
                    }


                    $postTitle =
                        trim(
                            $notification['post_title']
                            ?? ''
                        );


                    $message = match ($type) {

                        'post_like' =>
                            'liked your post',

                        'post_comment' =>
                            'commented on your post',

                        'comment_reply' =>
                            'replied to your comment',

                        default =>
                            'sent you a notification'
                    };


                    $isUnread =
                        (int) $notification['is_read']
                        === 0;

                    ?>


                    <a
                        href="/Components/Notifications/OpenNotification.php?id=<?= urlencode(
                            (string) $notification['id']
                        ) ?>"
                        class="notification-item <?= $isUnread
                            ? 'notification-unread'
                            : '' ?>">


                        <div class="notification-main">


                            <div class="notification-icon">

                                <?php if ($type === 'post_like'): ?>

                                    Like

                                <?php elseif ($type === 'post_comment'): ?>

                                    Comment

                                <?php elseif ($type === 'comment_reply'): ?>

                                    Reply

                                <?php else: ?>

                                    Notification

                                <?php endif; ?>

                            </div>


                            <div class="notification-content">


                                <div class="notification-message">

                                    <strong>

                                        <?= htmlspecialchars(
                                            $actorName,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>

                                    </strong>

                                    <?= htmlspecialchars(
                                        $message,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>.


                                    <?php if ($postTitle !== ''): ?>

                                        <span class="notification-post-title">

                                            <?= htmlspecialchars(
                                                $postTitle,
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>

                                        </span>

                                    <?php endif; ?>

                                </div>


                                <time class="notification-date">

                                    <?= htmlspecialchars(
                                        date(
                                            'd M Y, H:i',
                                            strtotime(
                                                $notification['created_at']
                                            )
                                        ),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>

                                </time>

                            </div>

                        </div>


                        <?php if ($isUnread): ?>

                            <span
                                class="notification-unread-dot"
                                title="Unread">
                            </span>

                        <?php endif; ?>


                    </a>


                <?php endforeach; ?>


            </div>


        <?php endif; ?>


        <footer class="notifications-footer">

            <a
                href="/Components/Dashboard/Dashboard.php"
                class="back-button">

                Back to Dashboard

            </a>

        </footer>


    </section>


</main>


</body>

</html>
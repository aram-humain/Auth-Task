<?php

/** @var array $savedPosts */

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Saved Posts</title>


    <link
        rel="stylesheet"
        href="/assets/css/theme.css">

    <link
        rel="stylesheet"
        href="/Components/SavedPosts/SavedPosts.css">


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


<main class="saved-container">


    <header class="saved-header">

        <div>

            <h1>
                Saved Posts
            </h1>

            <p>
                Posts you saved for later.
            </p>

        </div>


        <a
            href="/posts.php"
            class="saved-feed-button">

            Browse Posts

        </a>

    </header>


    <?php if (empty($savedPosts)): ?>


        <section class="saved-empty">

            <h2>
                No saved posts
            </h2>

            <p>
                Posts you save will appear here.
            </p>

        </section>


    <?php else: ?>


        <section class="saved-list">


            <?php foreach ($savedPosts as $post): ?>


                <?php

                $authorName = trim(
                    ($post['first_name'] ?? '')
                    . ' '
                    . ($post['last_name'] ?? '')
                );


                if ($authorName === '') {
                    $authorName = 'User';
                }


                $preview =
                    trim(
                        $post['content']
                        ?? ''
                    );


                if (
                    mb_strlen($preview)
                    > 300
                ) {

                    $preview =
                        mb_substr(
                            $preview,
                            0,
                            300
                        )
                        . '...';
                }

                ?>


                <article class="saved-post-card">


                    <div class="saved-post-meta">


                        <a
                            href="/Components/Profile/Profile.php?slug=<?= urlencode(
                                $post['public_slug']
                            ) ?>"
                            class="saved-author">

                            <?= htmlspecialchars(
                                $authorName,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>

                        </a>


                        <span>
                            ·
                        </span>


                        <span>

                            <?= htmlspecialchars(
                                $post['category_name'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>

                        </span>


                        <span>
                            ·
                        </span>


                        <time>

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


                    <h2>

                        <a
                            href="/Components/Posts/Post.php?id=<?= urlencode(
                                $post['uuid']
                            ) ?>">

                            <?= htmlspecialchars(
                                $post['title'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>

                        </a>

                    </h2>


                    <?php if ($preview !== ''): ?>

                        <p class="saved-preview">

                            <?= nl2br(
                                htmlspecialchars(
                                    $preview,
                                    ENT_QUOTES,
                                    'UTF-8'
                                )
                            ) ?>

                        </p>

                    <?php endif; ?>


                    <div class="saved-post-footer">


                        <div class="saved-stats">

                            <span>
                                Likes:
                                <?= (int) $post['likes_count'] ?>
                            </span>

                            <span>
                                Comments:
                                <?= (int) $post['comments_count'] ?>
                            </span>

                        </div>


                        <div class="saved-date">

                            Saved:

                            <?= htmlspecialchars(
                                date(
                                    'd M Y, H:i',
                                    strtotime(
                                        $post['saved_at']
                                    )
                                ),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>

                        </div>


                        <a
                            href="/Components/Posts/Post.php?id=<?= urlencode(
                                $post['uuid']
                            ) ?>"
                            class="open-post-button">

                            Open Post

                        </a>


                    </div>


                </article>


            <?php endforeach; ?>


        </section>


    <?php endif; ?>


    <footer class="saved-footer">

        <a
            href="/Components/Dashboard/Dashboard.php"
            class="back-dashboard">

            Back to Dashboard

        </a>

    </footer>


</main>


</body>

</html>
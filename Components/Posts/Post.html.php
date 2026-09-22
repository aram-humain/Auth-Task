<?php

/** @var array<string, mixed> $post */
/** @var array<int, array<string, mixed>> $images */
/** @var array<int, array<string, mixed>> $tags */
/** @var string $authorName */
/** @var string $postUuid */
/** @var bool $isOwner */
/** @var bool $isAdmin */

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


        <!-- DELETED WARNING -->

        <?php if ($post['deleted_at'] !== null): ?>

            <div class="post-warning">

                This post has been deleted.

            </div>

        <?php endif; ?>



        <!-- HEADER -->

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


            <?php if (
                $isOwner
                || $isAdmin
            ): ?>

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



        <!-- CONTENT -->

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



        <!-- IMAGES -->

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



        <!-- TAGS -->

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



        <!-- FOOTER -->

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

                </div>

            <?php endif; ?>


        </footer>


    </article>

</main>


</body>

</html>
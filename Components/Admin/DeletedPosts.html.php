<?php

/** @var array<int, array<string, mixed>> $deletedPosts */
/** @var string $csrfToken */

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Deleted Posts</title>

    <link
        rel="stylesheet"
        href="/assets/css/theme.css">

    <link
        rel="stylesheet"
        href="/Components/Admin/DeletedPosts.css">

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


<main class="deleted-posts-page">


    <header class="deleted-posts-header">

        <div>

            <h1>
                Deleted Posts
            </h1>

            <p>
                View and restore soft-deleted posts.
            </p>

        </div>


        <a
            href="/Components/Admin/Admin.php"
            class="back-admin-button">

            Back to Admin

        </a>

    </header>


    <?php if (empty($deletedPosts)): ?>

        <section class="empty-deleted-posts">

            <h2>
                No deleted posts
            </h2>

            <p>
                Deleted posts will appear here.
            </p>

        </section>

    <?php else: ?>


        <section class="deleted-posts-card">

            <div class="deleted-posts-table-wrapper">

                <table class="deleted-posts-table">

                    <thead>

                    <tr>

                        <th>
                            Author
                        </th>

                        <th>
                            Title
                        </th>

                        <th>
                            Category
                        </th>

                        <th>
                            Status
                        </th>

                        <th>
                            Deleted At
                        </th>

                        <th>
                            Actions
                        </th>

                    </tr>

                    </thead>


                    <tbody>

                    <?php foreach ($deletedPosts as $post): ?>

                        <?php

                        $authorName = trim(
                            ($post['first_name'] ?? '')
                            . ' '
                            . ($post['last_name'] ?? '')
                        );

                        if ($authorName === '') {
                            $authorName = 'User';
                        }

                        ?>

                        <tr>


                            <!-- AUTHOR -->

                            <td>

                                <a
                                    href="/Components/Profile/Profile.php?slug=<?= urlencode(
                                        $post['public_slug']
                                    ) ?>"
                                    class="deleted-post-author">

                                    <?= htmlspecialchars(
                                        $authorName,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>

                                </a>

                            </td>


                            <!-- TITLE -->

                            <td>

                                <div class="deleted-post-title">

                                    <?= htmlspecialchars(
                                        $post['title'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>

                                </div>

                            </td>


                            <!-- CATEGORY -->

                            <td>

                                <span class="deleted-post-category">

                                    <?= htmlspecialchars(
                                        $post['category_name'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>

                                </span>

                            </td>


                            <!-- STATUS -->

                            <td>

                                <span
                                    class="deleted-post-status status-<?= htmlspecialchars(
                                        $post['status'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>">

                                    <?= htmlspecialchars(
                                        ucfirst(
                                            $post['status']
                                        ),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>

                                </span>

                            </td>


                            <!-- DELETED DATE -->

                            <td>

                                <time
                                    class="deleted-post-date"
                                    datetime="<?= htmlspecialchars(
                                        $post['deleted_at'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>">

                                    <?= htmlspecialchars(
                                        date(
                                            'd M Y, H:i',
                                            strtotime(
                                                $post['deleted_at']
                                            )
                                        ),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>

                                </time>

                            </td>


                            <!-- ACTIONS -->

                            <td>

                                <div class="deleted-post-actions">


                                    <a
                                        href="/Components/Posts/Post.php?id=<?= urlencode(
                                            $post['uuid']
                                        ) ?>"
                                        class="view-button">

                                        View

                                    </a>


                                    <form
                                        method="POST"
                                        action="/Components/Admin/RestorePost.php"
                                        class="restore-form"
                                        onsubmit="return confirm('Restore this post?');">


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
                                            value="<?= (int) $post['id'] ?>">


                                        <button
                                            type="submit"
                                            class="restore-button">

                                            Restore

                                        </button>


                                    </form>

                                </div>

                            </td>


                        </tr>

                    <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        </section>


    <?php endif; ?>


</main>


</body>

</html>
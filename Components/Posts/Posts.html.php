<?php

/** @var array<int, array<string, mixed>> $posts */
/** @var array<int, array<string, mixed>> $categories */
/** @var array<int, array<string, mixed>> $tags */
/** @var int|null $currentUserId */
/** @var int|null $categoryId */
/** @var int|null $tagId */
/** @var int $page */
/** @var int $totalPages */
/** @var string $search */
/** @var string $sort */

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Posts</title>

    <link
        rel="stylesheet"
        href="/assets/css/theme.css">

    <link
        rel="stylesheet"
        href="/Components/Posts/Posts.css">

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


    <main class="feed-container">


        <!-- HEADER -->

        <header class="feed-header">

            <div>

                <h1>Posts</h1>

                <p>
                    Latest posts from the community.
                </p>

            </div>


            <?php if ($currentUserId !== null): ?>

                <a
                    href="/Components/Posts/CreatePost.php"
                    class="create-post-button">

                    Create Post

                </a>

            <?php endif; ?>

        </header>



        <!-- FILTERS -->

        <form
            method="GET"
            action="/Components/Posts/Posts.php"
            class="feed-search">


            <!-- SEARCH -->

            <div class="feed-search-field">

                <label for="feed-search">
                    Search
                </label>

                <input
                    type="search"
                    id="feed-search"
                    name="q"
                    placeholder="Search posts..."
                    value="<?= htmlspecialchars(
                                $search,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>">

            </div>


            <!-- CATEGORY -->

            <div class="feed-search-field">

                <label for="category_id">
                    Category
                </label>

                <select
                    id="category_id"
                    name="category_id">

                    <option value="">
                        All categories
                    </option>

                    <?php foreach ($categories as $category): ?>

                        <option
                            value="<?= (int) $category['id'] ?>"
                            <?= $categoryId === (int) $category['id']
                                ? 'selected'
                                : '' ?>>

                            <?= htmlspecialchars(
                                $category['name'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>

                        </option>

                    <?php endforeach; ?>

                </select>

            </div>


            <!-- TAG -->

            <div class="feed-search-field">

                <label for="tag_id">
                    Tag
                </label>

                <select
                    id="tag_id"
                    name="tag_id">

                    <option value="">
                        All tags
                    </option>

                    <?php foreach ($tags as $tag): ?>

                        <option
                            value="<?= (int) $tag['id'] ?>"
                            <?= $tagId === (int) $tag['id']
                                ? 'selected'
                                : '' ?>>

                            #<?= htmlspecialchars(
                                    $tag['name'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>

                        </option>

                    <?php endforeach; ?>

                </select>

            </div>


            <!-- SORT -->

            <div class="feed-search-field">

                <label for="sort">
                    Sort by
                </label>

                <select
                    id="sort"
                    name="sort">

                    <option
                        value="newest"
                        <?= $sort === 'newest'
                            ? 'selected'
                            : '' ?>>

                        Newest

                    </option>

                    <option
                        value="liked"
                        <?= $sort === 'liked'
                            ? 'selected'
                            : '' ?>>

                        Most liked

                    </option>

                    <option
                        value="commented"
                        <?= $sort === 'commented'
                            ? 'selected'
                            : '' ?>>

                        Most commented

                    </option>

                </select>

            </div>


            <!-- ACTIONS -->

            <div class="feed-search-actions">

                <button
                    type="submit"
                    class="feed-search-button">

                    Search

                </button>


                <?php if (
                    $search !== ''
                    || $categoryId !== null
                    || $tagId !== null
                    || $sort !== 'newest'
                ): ?>

                    <a
                        href="/Components/Posts/Posts.php"
                        class="feed-clear-button">

                        Clear

                    </a>

                <?php endif; ?>

            </div>


        </form>



        <!-- EMPTY -->

        <?php if (empty($posts)): ?>

            <section class="empty-feed">

                <h2>No posts found</h2>

                <p>
                    No published posts match your filters.
                </p>

            </section>

        <?php endif; ?>



        <!-- POSTS -->

        <section class="feed-list">


            <?php foreach ($posts as $post): ?>

                <article class="feed-post">


                    <!-- AUTHOR -->

                    <header class="feed-post-header">

                        <div class="author-info">


                            <?php if (
                                !empty($post['profile_picture'])
                            ): ?>

                                <img
                                    class="author-avatar"
                                    src="<?= htmlspecialchars(
                                                $post['profile_picture'],
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>"
                                    alt="Profile picture">

                            <?php else: ?>

                                <div class="author-avatar-placeholder">

                                    <?= htmlspecialchars(
                                        strtoupper(
                                            mb_substr(
                                                $post['author_name'],
                                                0,
                                                1
                                            )
                                        ),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>

                                </div>

                            <?php endif; ?>


                            <div>

                                <a
                                    href="/Components/Profile/Profile.php?slug=<?= urlencode(
                                                                                    $post['public_slug']
                                                                                ) ?>"
                                    class="author-name">

                                    <?= htmlspecialchars(
                                        $post['author_name'],
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

                        </div>

                    </header>



                    <!-- TITLE + CONTENT -->

                    <a
                        href="/Components/Posts/Post.php?id=<?= urlencode(
                                                                $post['uuid']
                                                            ) ?>"
                        class="post-main-link">

                        <h2>

                            <?= htmlspecialchars(
                                $post['title'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>

                        </h2>


                        <?php if (
                            trim($post['content']) !== ''
                        ): ?>

                            <?php

                            $content = $post['content'];

                            if (mb_strlen($content) > 300) {
                                $content =
                                    mb_substr(
                                        $content,
                                        0,
                                        300
                                    )
                                    . '...';
                            }

                            ?>

                            <p class="post-preview">

                                <?= nl2br(
                                    htmlspecialchars(
                                        $content,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    )
                                ) ?>

                            </p>

                        <?php endif; ?>

                    </a>



                    <!-- IMAGES -->

                    <?php if (!empty($post['images'])): ?>

                        <a
                            href="/Components/Posts/Post.php?id=<?= urlencode(
                                                                    $post['uuid']
                                                                ) ?>"
                            class="feed-images <?= count($post['images']) > 1
                                                    ? 'feed-images-multiple'
                                                    : '' ?>">

                            <?php foreach (
                                array_slice(
                                    $post['images'],
                                    0,
                                    4
                                ) as $image
                            ): ?>

                                <div class="feed-image">

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

                        </a>

                    <?php endif; ?>



                    <!-- TAGS -->

                    <?php if (!empty($post['tags'])): ?>

                        <div class="feed-tags">

                            <?php foreach ($post['tags'] as $tag): ?>

                                <span class="feed-tag">

                                    #<?= htmlspecialchars(
                                            $tag['name'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>

                                </span>

                            <?php endforeach; ?>

                        </div>

                    <?php endif; ?>



                    <!-- FOOTER -->

                    <footer class="feed-post-footer">

                        <div class="post-stat">

                            <span>Likes</span>

                            <strong>
                                <?= (int) $post['likes_count'] ?>
                            </strong>

                        </div>


                        <div class="post-stat">

                            <span>Comments</span>

                            <strong>
                                <?= (int) $post['comments_count'] ?>
                            </strong>

                        </div>


                        <a
                            href="/Components/Posts/Post.php?id=<?= urlencode(
                                                                    $post['uuid']
                                                                ) ?>"
                            class="open-post-button">

                            Open post

                        </a>

                    </footer>


                </article>

            <?php endforeach; ?>


        </section>



        <!-- PAGINATION -->

        <?php if ($totalPages > 1): ?>

            <?php

            $paginationParams = [];

            if ($search !== '') {
                $paginationParams['q'] = $search;
            }

            if ($categoryId !== null) {
                $paginationParams['category_id'] =
                    $categoryId;
            }

            if ($tagId !== null) {
                $paginationParams['tag_id'] =
                    $tagId;
            }

            if ($sort !== 'newest') {
                $paginationParams['sort'] =
                    $sort;
            }

            ?>


            <nav class="pagination">


                <?php if ($page > 1): ?>

                    <?php

                    $previousParams =
                        $paginationParams;

                    $previousParams['page'] =
                        $page - 1;

                    ?>

                    <a
                        href="?<?= htmlspecialchars(
                                    http_build_query(
                                        $previousParams
                                    ),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                        class="pagination-button">

                        Previous

                    </a>

                <?php endif; ?>


                <span class="pagination-info">

                    Page <?= (int) $page ?>
                    of <?= (int) $totalPages ?>

                </span>


                <?php if ($page < $totalPages): ?>

                    <?php

                    $nextParams =
                        $paginationParams;

                    $nextParams['page'] =
                        $page + 1;

                    ?>

                    <a
                        href="?<?= htmlspecialchars(
                                    http_build_query(
                                        $nextParams
                                    ),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                        class="pagination-button">

                        Next

                    </a>

                <?php endif; ?>


            </nav>

        <?php endif; ?>


    </main>


</body>

</html>
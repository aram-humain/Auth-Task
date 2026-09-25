<?php

/** @var array $posts */
/** @var array $categories */
/** @var array $tags */
/** @var array $authors */
/** @var string $search */
/** @var int|null $categoryId */
/** @var int|null $tagId */
/** @var string|null $authorSlug */
/** @var string $sort */
/** @var int $page */
/** @var int $totalPages */
/** @var array $imagesByPost */
/** @var int|null $currentUserId */


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
        href="/Components/Posts/Posts.css">

    <link
        rel="stylesheet"
        href="/assets/css/theme.css">

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


    <main class="posts-container">


        <!-- HEADER -->

        <header class="posts-header">

            <div>

                <h1>Posts</h1>

                <p>
                    Latest posts from the community.
                </p>

            </div>


            <?php if ($currentUserId !== null): ?>

                <a
                    href="/Components/Posts/CreatePosts.php"
                    class="create-post-button">

                    Create Post

                </a>

            <?php endif; ?>

        </header>


        <!-- FILTERS -->

        <section class="posts-filters">

            <form
                method="GET"
                action="/Components/Posts/Posts.php"
                class="posts-filter-form">


                <!-- SEARCH -->

                <div class="filter-group">

                    <label for="search">
                        Search
                    </label>

                    <input
                        type="search"
                        id="search"
                        name="q"
                        value="<?= htmlspecialchars(
                                    $search,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                        placeholder="Search posts...">

                </div>


                <!-- CATEGORY -->

                <div class="filter-group">

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
                                <?= $categoryId !== null
                                    && (int) $categoryId === (int) $category['id']
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

                <div class="filter-group">

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
                                <?= $tagId !== null
                                    && (int) $tagId === (int) $tag['id']
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


                <!-- AUTHOR -->

                <div class="filter-group">

                    <label for="author">
                        Author
                    </label>

                    <select
                        id="author"
                        name="author">

                        <option value="">
                            All authors
                        </option>

                        <?php foreach ($authors as $author): ?>

                            <?php

                            $authorName = trim(
                                ($author['first_name'] ?? '')
                                    . ' '
                                    . ($author['last_name'] ?? '')
                            );

                            if ($authorName === '') {
                                $authorName = 'User';
                            }

                            ?>

                            <option
                                value="<?= htmlspecialchars(
                                            $author['public_slug'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>"
                                <?= $authorSlug !== null
                                    && $authorSlug === $author['public_slug']
                                    ? 'selected'
                                    : '' ?>>

                                <?= htmlspecialchars(
                                    $authorName,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>

                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>


                <!-- SORT -->

                <div class="filter-group">

                    <label for="sort">
                        Sort
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

                <div class="filter-actions">

                    <button
                        type="submit"
                        class="filter-button">

                        Search

                    </button>


                    <a
                        href="/Components/Posts/Posts.php"
                        class="filter-clear-button">

                        Clear

                    </a>

                </div>

            </form>

        </section>


        <!-- POSTS -->

        <?php if (empty($posts)): ?>

            <section class="empty-posts">

                <h2>
                    No posts found
                </h2>

                <p>
                    No published posts match your current filters.
                </p>

            </section>

        <?php else: ?>

            <section class="posts-list">


                <?php foreach ($posts as $post): ?>

                    <?php

                    $postUuid = '';

                    if (
                        isset($post['public_id'])
                        && $post['public_id'] !== null
                    ) {

                        try {

                            $postUuid =
                                \Ramsey\Uuid\Uuid::fromBytes(
                                    $post['public_id']
                                )->toString();
                        } catch (\Throwable $e) {

                            $postUuid = '';
                        }
                    }


                    $authorName = trim(
                        ($post['first_name'] ?? '')
                            . ' '
                            . ($post['last_name'] ?? '')
                    );

                    if ($authorName === '') {
                        $authorName = 'User';
                    }

                    ?>


                    <article class="post-card">


                        <!-- AUTHOR -->

                        <div class="post-card-header">

                            <div class="post-author">

                                <?php if (!empty($post['profile_picture'])): ?>

                                    <img
                                        src="<?= htmlspecialchars(
                                                    $post['profile_picture'],
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>"
                                        alt=""
                                        class="post-avatar">

                                <?php endif; ?>


                                <div>

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


                                    <div class="post-date">

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

                                    </div>

                                </div>

                            </div>


                            <!-- CATEGORY -->

                            <span class="post-category">

                                <?= htmlspecialchars(
                                    $post['category_name'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>

                            </span>

                        </div>


                        <!-- TITLE -->

                        <h2 class="post-title">

                            <?php if ($postUuid !== ''): ?>

                                <a
                                    href="/Components/Posts/Post.php?id=<?= urlencode(
                                                                            $postUuid
                                                                        ) ?>">

                                    <?= htmlspecialchars(
                                        $post['title'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>

                                </a>

                            <?php else: ?>

                                <?= htmlspecialchars(
                                    $post['title'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>

                            <?php endif; ?>

                        </h2>


                        <!-- CONTENT -->

                        <?php if (
                            trim(
                                $post['content'] ?? ''
                            ) !== ''
                        ): ?>

                            <div class="post-excerpt">

                                <?= nl2br(
                                    htmlspecialchars(
                                        mb_substr(
                                            $post['content'],
                                            0,
                                            300
                                        ),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    )
                                ) ?>


                                <?php if (
                                    mb_strlen(
                                        $post['content']
                                    ) > 300
                                ): ?>

                                    ...

                                <?php endif; ?>

                            </div>

                        <?php endif; ?>


                        <!-- IMAGES -->

                        <?php

                        $postImages =
                            $imagesByPost[(int) $post['id']]
                            ?? [];

                        $previewImages =
                            array_slice(
                                $postImages,
                                0,
                                2
                            );

                        $totalImages =
                            count($postImages);

                        ?>

                        <?php if (!empty($previewImages)): ?>

                            <div
                                class="post-images-preview <?= $totalImages === 1
                                                                ? 'post-images-preview-single'
                                                                : 'post-images-preview-multiple' ?>">

                                <?php foreach (
                                    $previewImages as $index => $image
                                ): ?>

                                    <a
                                        href="/Components/Posts/Post.php?id=<?= urlencode(
                                                                                $postUuid
                                                                            ) ?>"
                                        class="post-preview-image-wrapper">

                                        <img
                                            src="<?= htmlspecialchars(
                                                        $image['image_url'],
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>"
                                            alt="Post image"
                                            loading="lazy"
                                            class="post-preview-image">


                                        <?php if (
                                            $index === 1
                                            && $totalImages > 2
                                        ): ?>

                                            <span class="post-more-images">

                                                +<?= $totalImages - 2 ?>

                                            </span>

                                        <?php endif; ?>

                                    </a>

                                <?php endforeach; ?>

                            </div>

                        <?php endif; ?>


                        <!-- TAGS -->

                        <?php

                        $postTags =
                            $tagsByPost[(int) $post['id']] ?? [];

                        ?>

                        <?php if (!empty($postTags)): ?>

                            <div class="post-tags">

                                <?php foreach ($postTags as $tag): ?>

                                    <span class="post-tag">

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

                        <footer class="post-card-footer">


                            <div class="post-stats">

                                <span>

                                    ♥

                                    <?= (int) (
                                        $post['likes_count'] ?? 0
                                    ) ?>

                                </span>


                                <span>

                                    💬

                                    <?= (int) (
                                        $post['comments_count'] ?? 0
                                    ) ?>

                                </span>

                            </div>


                            <a
                                href="/Components/Posts/Post.php?id=<?= urlencode(
                                                                        $postUuid
                                                                    ) ?>"
                                class="read-post-button">

                                Read post

                            </a>

                        </footer>


                    </article>

                <?php endforeach; ?>

            </section>


            <!-- PAGINATION -->

            <?php if ($totalPages > 1): ?>

                <nav
                    class="pagination"
                    aria-label="Posts pagination">


                    <?php

                    $buildPageUrl = function (
                        int $targetPage
                    ) use (
                        $search,
                        $categoryId,
                        $tagId,
                        $authorSlug,
                        $sort
                    ): string {

                        $params = [
                            'page' => $targetPage
                        ];


                        if ($search !== '') {

                            $params['q'] = $search;
                        }


                        if ($categoryId !== null) {

                            $params['category_id'] =
                                $categoryId;
                        }


                        if ($tagId !== null) {

                            $params['tag_id'] =
                                $tagId;
                        }


                        if ($authorSlug !== null) {

                            $params['author'] =
                                $authorSlug;
                        }


                        if ($sort !== 'newest') {

                            $params['sort'] =
                                $sort;
                        }


                        return '/Components/Posts/Posts.php?'
                            . http_build_query($params);
                    };

                    ?>


                    <!-- PREVIOUS -->

                    <?php if ($page > 1): ?>

                        <a
                            href="<?= htmlspecialchars(
                                        $buildPageUrl($page - 1),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>"
                            class="pagination-button">

                            ← Previous

                        </a>

                    <?php endif; ?>


                    <!-- PAGE NUMBERS -->

                    <div class="pagination-pages">

                        <?php

                        $startPage = max(
                            1,
                            $page - 2
                        );

                        $endPage = min(
                            $totalPages,
                            $page + 2
                        );

                        ?>


                        <?php if ($startPage > 1): ?>

                            <a
                                href="<?= htmlspecialchars(
                                            $buildPageUrl(1),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>"
                                class="pagination-page">

                                1

                            </a>


                            <?php if ($startPage > 2): ?>

                                <span class="pagination-dots">
                                    ...
                                </span>

                            <?php endif; ?>

                        <?php endif; ?>


                        <?php for (
                            $pageNumber = $startPage;
                            $pageNumber <= $endPage;
                            $pageNumber++
                        ): ?>

                            <?php if ($pageNumber === $page): ?>

                                <span
                                    class="pagination-page active">

                                    <?= $pageNumber ?>

                                </span>

                            <?php else: ?>

                                <a
                                    href="<?= htmlspecialchars(
                                                $buildPageUrl(
                                                    $pageNumber
                                                ),
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>"
                                    class="pagination-page">

                                    <?= $pageNumber ?>

                                </a>

                            <?php endif; ?>

                        <?php endfor; ?>


                        <?php if ($endPage < $totalPages): ?>

                            <?php if (
                                $endPage < $totalPages - 1
                            ): ?>

                                <span class="pagination-dots">
                                    ...
                                </span>

                            <?php endif; ?>


                            <a
                                href="<?= htmlspecialchars(
                                            $buildPageUrl(
                                                $totalPages
                                            ),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>"
                                class="pagination-page">

                                <?= $totalPages ?>

                            </a>

                        <?php endif; ?>

                    </div>


                    <!-- NEXT -->

                    <?php if ($page < $totalPages): ?>

                        <a
                            href="<?= htmlspecialchars(
                                        $buildPageUrl($page + 1),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>"
                            class="pagination-button">

                            Next →

                        </a>

                    <?php endif; ?>


                </nav>

            <?php endif; ?>

        <?php endif; ?>


    </main>

</body>

</html>
<?php

/** @var array<string, mixed> $post */
/** @var array<int, array<string, mixed>> $categories */
/** @var string $title */
/** @var string $content */
/** @var string $tagsInput */
/** @var int $categoryId */
/** @var string $status */
/** @var string $csrfToken */
/** @var string $uuid */
/** @var string|null $error */

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Edit Post</title>

    <link
        rel="stylesheet"
        href="/assets/css/theme.css">

    <link
        rel="stylesheet"
        href="/Components/Posts/EditPost.css">

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


<main class="edit-post-page">

    <section class="edit-post-card">


        <header class="edit-post-header">

            <div>

                <h1>
                    Edit Post
                </h1>

                <p>
                    Update your post information and publication status.
                </p>

            </div>

            <a
                href="/Components/Posts/Post.php?id=<?= urlencode(
                    $uuid
                ) ?>"
                class="view-post-button">

                View Post

            </a>

        </header>


        <?php if ($error !== null): ?>

            <div class="edit-post-error">

                <?= htmlspecialchars(
                    $error,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>

            </div>

        <?php endif; ?>


        <form
            method="POST"
            action="/Components/Posts/EditPost.php?id=<?= urlencode(
                $uuid
            ) ?>"
            class="edit-post-form">


            <input
                type="hidden"
                name="csrf_token"
                value="<?= htmlspecialchars(
                    $csrfToken,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>">


            <!-- TITLE -->

            <div class="form-group">

                <label for="title">

                    Title

                </label>

                <input
                    type="text"
                    id="title"
                    name="title"
                    maxlength="255"
                    required
                    value="<?= htmlspecialchars(
                        $title,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>">

            </div>


            <!-- CATEGORY -->

            <div class="form-group">

                <label for="category_id">

                    Category

                </label>

                <select
                    id="category_id"
                    name="category_id"
                    required>

                    <?php foreach ($categories as $category): ?>

                        <option
                            value="<?= (int) $category['id'] ?>"
                            <?= $categoryId ===
                                (int) $category['id']
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


            <!-- TAGS -->

            <div class="form-group">

                <label for="tags">

                    Tags

                </label>

                <input
                    type="text"
                    id="tags"
                    name="tags"
                    maxlength="500"
                    placeholder="php, mysql, security"
                    value="<?= htmlspecialchars(
                        $tagsInput,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>">

                <small class="form-help">

                    Separate tags with commas.

                </small>

            </div>


            <!-- CONTENT -->

            <div class="form-group">

                <label for="content">

                    Content

                </label>

                <textarea
                    id="content"
                    name="content"
                    rows="12"
                    placeholder="Write your post..."><?= htmlspecialchars(
                        $content,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?></textarea>

            </div>


            <!-- STATUS -->

            <div class="form-group">

                <label for="status">

                    Status

                </label>

                <select
                    id="status"
                    name="status"
                    required>

                    <option
                        value="draft"
                        <?= $status === 'draft'
                            ? 'selected'
                            : '' ?>>

                        Draft

                    </option>

                    <option
                        value="published"
                        <?= $status === 'published'
                            ? 'selected'
                            : '' ?>>

                        Published

                    </option>

                    <option
                        value="archived"
                        <?= $status === 'archived'
                            ? 'selected'
                            : '' ?>>

                        Archived

                    </option>

                </select>


                <div class="status-help">

                    <div>

                        <strong>Draft</strong>

                        <span>
                            Visible only to you and Admin.
                        </span>

                    </div>

                    <div>

                        <strong>Published</strong>

                        <span>
                            Visible in the feed and on your profile.
                        </span>

                    </div>

                    <div>

                        <strong>Archived</strong>

                        <span>
                            Hidden from the feed but still visible to you.
                        </span>

                    </div>

                </div>

            </div>


            <!-- ACTIONS -->

            <div class="edit-post-actions">

                <a
                    href="/Components/Posts/Post.php?id=<?= urlencode(
                        $uuid
                    ) ?>"
                    class="cancel-button">

                    Cancel

                </a>

                <button
                    type="submit"
                    class="save-button">

                    Save Changes

                </button>

            </div>


        </form>

    </section>

</main>


</body>

</html>
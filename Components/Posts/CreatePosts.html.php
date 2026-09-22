<?php

/** @var array $categories */
/** @var string $csrfToken */
/** @var string|null $error */
/** @var string $title */
/** @var string $content */
/** @var string $tagsInput */
/** @var int|null $categoryId */
/** @var string $status */

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Create Post</title>

    <link
        rel="stylesheet"
        href="/Components/Posts/CreatePosts.css">

    <link
        rel="stylesheet"
        href="/assets/css/theme.css">

    <script src="/assets/js/theme.js"></script>

</head>

<body>

    <button
        type="button"
        id="theme-toggle"
        class="theme-toggle">
        Theme
    </button>


    <main class="create-post-container">

        <section class="create-post-card">

            <div class="create-post-header">

                <div>

                    <h1>Create Post</h1>

                </div>

            </div>


            <?php if ($error !== null): ?>

                <div
                    class="error-message"
                    role="alert">

                    <?= htmlspecialchars(
                        $error,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>

                </div>

            <?php endif; ?>


            <form
                method="POST"
                enctype="multipart/form-data"
                class="create-post-form">


                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= htmlspecialchars(
                        $csrfToken,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>">


                <div class="form-section">

                    <h2>Post Information</h2>


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
                            placeholder="Enter post title"
                            value="<?= htmlspecialchars(
                                $title,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>">

                    </div>


                    <div class="form-group">

                        <label for="content">
                            Content
                        </label>

                        <textarea
                            id="content"
                            name="content"
                            rows="10"
                            placeholder="Write your post..."><?= htmlspecialchars(
                                $content,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?></textarea>

                        <small>
                            A post must contain text,
                            at least one image, or both.
                        </small>

                    </div>

                </div>


                <div class="form-section">

                    <h2>Classification</h2>


                    <div class="form-grid">

                        <div class="form-group">

                            <label for="category_id">
                                Category
                            </label>

                            <select
                                id="category_id"
                                name="category_id"
                                required>

                                <option value="">
                                    Select category
                                </option>


                                <?php foreach ($categories as $category): ?>

                                    <option
                                        value="<?= (int) $category['id'] ?>"
                                        <?= (int) $categoryId === (int) $category['id']
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

                            </select>

                        </div>

                    </div>


                    <div class="form-group">

                        <label for="tags">
                            Tags
                        </label>

                        <input
                            type="text"
                            id="tags"
                            name="tags"
                            maxlength="500"
                            placeholder="anime, movie, art"
                            value="<?= htmlspecialchars(
                                $tagsInput,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>">

                        <small>
                            Separate tags with commas.
                            Existing tags will be reused automatically.
                        </small>

                    </div>

                </div>


                <div class="form-section">

                    <h2>Images</h2>

                    <div class="form-group">

                        <label for="images">
                            Post Images
                        </label>

                        <input
                            type="file"
                            id="images"
                            name="images[]"
                            accept="image/jpeg,image/png,image/webp"
                            multiple>

                        <small>
                            You can select multiple JPG, PNG or WEBP images.
                        </small>

                    </div>

                </div>


                <div class="create-post-actions">

                    <a
                        href="/Components/Dashboard/Dashboard.php"
                        class="post-button post-button-secondary">
                        Cancel
                    </a>

                    <button
                        type="submit"
                        class="post-button post-button-primary">
                        Create Post
                    </button>

                </div>

            </form>

        </section>

    </main>

    <script src="/Components/Posts/CreatePosts.js"></script>

</body>

</html>
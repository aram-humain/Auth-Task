<?php

/** @var array $categories */
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

    <title>Manage Categories</title>

    <link
        rel="stylesheet"
        href="/assets/css/theme.css">

    <link
        rel="stylesheet"
        href="/Components/Categories/AdminCategories.css">

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


<main class="categories-page">


    <div class="categories-header">

        <div>

            <h1>
                Manage Categories
            </h1>

            <p>
                Create, rename and delete post categories.
            </p>

        </div>


        <a
            href="/Components/Admin/Admin.php"
            class="back-button">

            Back to Admin

        </a>

    </div>


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


    <!-- CREATE CATEGORY -->

    <section class="category-create-card">

        <h2>
            Create Category
        </h2>


        <form
            method="POST"
            action="/Components/Categories/CreateCategory.php"
            class="category-create-form">


            <input
                type="hidden"
                name="csrf_token"
                value="<?= htmlspecialchars(
                    $csrfToken,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>">


            <input
                type="text"
                name="name"
                maxlength="100"
                required
                placeholder="Category name">


            <button
                type="submit">

                Create

            </button>


        </form>

    </section>


    <!-- CATEGORY LIST -->

    <section class="categories-card">

        <h2>
            Categories
        </h2>


        <?php if (empty($categories)): ?>

            <div class="empty-state">

                No categories found.

            </div>

        <?php else: ?>

            <div class="categories-table-wrapper">

                <table class="categories-table">

                    <thead>

                    <tr>

                        <th>ID</th>
                        <th>Name</th>
                        <th>Posts</th>
                        <th>Actions</th>

                    </tr>

                    </thead>


                    <tbody>


                    <?php foreach ($categories as $category): ?>

                        <tr>


                            <td>

                                <?= (int) $category['id'] ?>

                            </td>


                            <td>

                                <?= htmlspecialchars(
                                    $category['name'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>

                            </td>


                            <td>

                                <?= (int) $category['posts_count'] ?>

                            </td>


                            <td>

                                <div class="category-actions">


                                    <!-- RENAME -->

                                    <form
                                        method="POST"
                                        action="/Components/Categories/RenameCategory.php"
                                        class="rename-form">


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
                                            name="category_id"
                                            value="<?= (int) $category['id'] ?>">


                                        <input
                                            type="text"
                                            name="name"
                                            maxlength="100"
                                            required
                                            value="<?= htmlspecialchars(
                                                $category['name'],
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>">


                                        <button
                                            type="submit"
                                            class="rename-button">

                                            Rename

                                        </button>


                                    </form>


                                    <!-- DELETE -->

                                    <form
                                        method="POST"
                                        action="/Components/Categories/DeleteCategory.php"
                                        class="delete-form"
                                        onsubmit="return confirm('Delete this category?');">


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
                                            name="category_id"
                                            value="<?= (int) $category['id'] ?>">


                                        <button
                                            type="submit"
                                            class="delete-button"
                                            <?= (int) $category['posts_count'] > 0
                                                ? 'disabled'
                                                : '' ?>>

                                            Delete

                                        </button>


                                    </form>


                                </div>

                            </td>


                        </tr>

                    <?php endforeach; ?>


                    </tbody>

                </table>

            </div>

        <?php endif; ?>

    </section>


</main>


</body>

</html>
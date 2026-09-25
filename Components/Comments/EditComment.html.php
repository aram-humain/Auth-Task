<?php

/** @var string $content */
/** @var string|null $error */
/** @var string $csrfToken */
/** @var string $postUuid */

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Edit Comment</title>

    <link
        rel="stylesheet"
        href="/assets/css/theme.css">

    <link
        rel="stylesheet"
        href="/Components/Comments/EditComment.css">

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


<main class="edit-comment-page">

    <section class="edit-comment-card">


        <header class="edit-comment-header">

            <h1>
                Edit Comment
            </h1>

            <p>
                Update your comment.
            </p>

        </header>


        <?php if ($error !== null): ?>

            <div class="edit-comment-error">

                <?= htmlspecialchars(
                    $error,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>

            </div>

        <?php endif; ?>


        <form
            method="POST"
            class="edit-comment-form">


            <input
                type="hidden"
                name="csrf_token"
                value="<?= htmlspecialchars(
                    $csrfToken,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>">


            <label for="content">

                Comment

            </label>


            <textarea
                id="content"
                name="content"
                rows="8"
                maxlength="2000"
                required><?= htmlspecialchars(
                    $content,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?></textarea>


            <div class="edit-comment-actions">

                <a
                    href="/Components/Posts/Post.php?id=<?= urlencode(
                        $postUuid
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
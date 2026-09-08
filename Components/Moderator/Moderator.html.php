<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Moderator Page</title>

    <link
        rel="stylesheet"
        href="/Components/Moderator/Moderator.css"
    >

    <link
        rel="stylesheet"
        href="/assets/css/theme.css"
    >

    <script src="/assets/js/theme.js"></script>

</head>

<body>

<button
    type="button"
    id="theme-toggle"
    class="theme-toggle"
>
    Theme
</button>

<main class="moderator-container">

    <section class="moderator-card">

        <h1>
            Moderator Page
        </h1>

        <p class="moderator-description">
            You have permission to access moderator page.
        </p>

        <div class="moderator-actions">

            <?php if (can($pdo, 'view_users')): ?>

                <a
                    href="/Components/Admin/Users/Users.php"
                    class="moderator-button"
                >
                    View Users
                </a>

            <?php endif; ?>

            <a
                href="/Components/Dashboard/Dashboard.php"
                class="moderator-button moderator-button-secondary"
            >
                Back to Dashboard
            </a>

        </div>

    </section>

</main>

</body>

</html>
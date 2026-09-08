<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Admin Page</title>

    <link
        rel="stylesheet"
        href="/Components/Admin/Admin.css">

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

    <main class="admin-container">

        <section class="admin-card">

            <h1>
                Admin Page
            </h1>

            <p class="admin-description">
                You have permission to access admin page.
            </p>

            <div class="admin-actions">

                <a
                    href="/Components/Admin/Users/Users.php"
                    class="admin-button">
                    Go to Users
                </a>

                <a
                    href="/Components/Dashboard/Dashboard.php"
                    class="admin-button admin-button-secondary">
                    Back to Dashboard
                </a>

                <a
                    href="/Components/Admin/Audit/Audit.php"
                    class="admin-button">
                    Audit Log
                </a>

            </div>

        </section>

    </main>

</body>

</html>
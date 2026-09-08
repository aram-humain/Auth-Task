<?php

/** @var array $user */

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Dashboard</title>

    <link
        rel="stylesheet"
        href="./Dashboard.css"
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

<main class="dashboard-container">

    <section class="dashboard-card">

        <div class="dashboard-header">

            <h1>
                Dashboard
            </h1>

            <p>
                Welcome,
                <strong>
                    <?= htmlspecialchars(
                        $user['name'],
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </strong>
            </p>

        </div>

        <section class="info-section">

            <h2>
                Your Information
            </h2>

            <div class="info-box">

                <div class="info-row">

                    <span class="info-label">
                        ID
                    </span>

                    <span class="info-value">
                        <?= htmlspecialchars(
                            (string) $user['id'],
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </span>

                </div>

                <div class="info-row">

                    <span class="info-label">
                        Name
                    </span>

                    <span class="info-value">
                        <?= htmlspecialchars(
                            $user['name'],
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </span>

                </div>

                <div class="info-row">

                    <span class="info-label">
                        Email
                    </span>

                    <span class="info-value">
                        <?= htmlspecialchars(
                            $user['email'],
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </span>

                </div>

                <div class="info-row">

                    <span class="info-label">
                        Registered
                    </span>

                    <span class="info-value">
                        <?= htmlspecialchars(
                            $user['created_at'],
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </span>

                </div>

            </div>

        </section>

        <div class="dashboard-actions">

            <?php if (can($pdo, 'access_moderator_page')): ?>

                <a
                    href="/Components/Moderator/Moderator.php"
                    class="dashboard-action"
                >
                    Moderator
                </a>

            <?php endif; ?>

            <?php if (can($pdo, 'access_admin_page')): ?>

                <a
                    href="/Components/Admin/admin.php"
                    class="dashboard-action"
                >
                    Admin
                </a>

            <?php endif; ?>

            <?php if (can($pdo, 'view_users')): ?>

                <a
                    href="/Components/Admin/Users/Users.php"
                    class="dashboard-action"
                >
                    Users
                </a>

            <?php endif; ?>

        </div>

        <div class="logout-container">

            <a
                href="../logout.php"
                class="logout-button"
            >
                Logout
            </a>

        </div>

    </section>

</main>

</body>

</html>
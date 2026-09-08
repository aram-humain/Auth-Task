<?php

/** @var string $token */
/** @var array $errors */

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Reset Password</title>

    <link
        rel="stylesheet"
        href="./Reset.css"
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

<main class="reset-container">

    <section
        class="reset-card"
        aria-labelledby="reset-title"
    >

        <div class="reset-header">

            <h1 id="reset-title">
                Reset Password
            </h1>

            <p>
                Choose a new password for your account.
            </p>

        </div>

        <?php if (!empty($errors)): ?>

            <div
                class="message message-error"
                role="alert"
            >

                <?php foreach ($errors as $error): ?>

                    <p>
                        <?= htmlspecialchars(
                            $error,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </p>

                <?php endforeach; ?>

            </div>

        <?php endif; ?>

        <form method="POST" action="">

            <input
                type="hidden"
                name="token"
                value="<?= htmlspecialchars(
                    $token,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>"
            >

            <div class="form-group">

                <label for="password">
                    New Password
                </label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Minimum 8 characters"
                    minlength="8"
                    autocomplete="new-password"
                    required
                >

            </div>

            <div class="form-group">

                <label for="password_confirmation">
                    Confirm Password
                </label>

                <input
                    type="password"
                    id="password_confirmation"
                    name="password_confirmation"
                    placeholder="Repeat your password"
                    minlength="8"
                    autocomplete="new-password"
                    required
                >

            </div>

            <button
                type="submit"
                class="reset-button"
            >
                Reset Password
            </button>

        </form>

        <p class="back-link">

            <a href="../Login/Login.php">
                Back to Login
            </a>

        </p>

    </section>

</main>

</body>

</html>
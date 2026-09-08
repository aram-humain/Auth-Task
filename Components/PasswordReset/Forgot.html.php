<?php

/** @var string|null $error */
/** @var string|null $success */
/** @var string $email */

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Forgot Password</title>

    <link
        rel="stylesheet"
        href="./Forgot.css"
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

<main class="forgot-container">

    <section
        class="forgot-card"
        aria-labelledby="forgot-title"
    >

        <div class="forgot-header">

            <div
                class="lock-mark"
                aria-hidden="true"
            >
                ?
            </div>

            <h1 id="forgot-title">
                Forgot Password?
            </h1>

            <p>
                Enter your email and we will send you
                a password reset link.
            </p>

        </div>

        <?php if (!empty($error)): ?>

            <div
                class="message message-error"
                role="alert"
            >
                <?= htmlspecialchars(
                    $error,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </div>

        <?php endif; ?>

        <?php if (!empty($success)): ?>

            <div
                class="message message-success"
                role="status"
            >
                <?= htmlspecialchars(
                    $success,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </div>

        <?php endif; ?>

        <form method="POST" action="">

            <div class="form-group">

                <label for="email">
                    Email Address
                </label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    placeholder="you@example.com"
                    value="<?= htmlspecialchars(
                        $email ?? '',
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>"
                    autocomplete="email"
                    required
                >

            </div>

            <button
                type="submit"
                class="forgot-button"
            >
                Send Reset Link
            </button>

        </form>

        <p class="back-link">

            Remember your password?

            <a href="/Components/Login/Login.php">
                Back to Login
            </a>

        </p>

    </section>

</main>

</body>

</html>
<?php

/** @var array $errors */
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

    <title>Login</title>

    <link
        rel="stylesheet"
        href="./Login.css"
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

<main class="login-container">

    <section class="login-card">

        <div class="login-header">

            <h1>
                Welcome Back
            </h1>

            <p>
                Login to your account
            </p>

        </div>

        <?php if (!empty($errors)): ?>

            <div
                class="error-box"
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

            <div class="form-group">

                <label for="email">
                    Email
                </label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    placeholder="Enter your email"
                    value="<?= htmlspecialchars(
                        $email,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>"
                    autocomplete="email"
                    required
                >

            </div>

            <div class="form-group">

                <label for="password">
                    Password
                </label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Enter your password"
                    autocomplete="current-password"
                    required
                >

            </div>

            <button
                type="submit"
                class="login-button"
            >
                Login
            </button>

        </form>

        <p class="register-link">

            Don't have an account?

            <a href="../Registration/Registration.php">
                Register
            </a>

        </p>

        <p class="forgot-link">

            <a href="../PasswordReset/Forgot.php">
                Forgot Password?
            </a>

        </p>

    </section>

</main>

</body>

</html>
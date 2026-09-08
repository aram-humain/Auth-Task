<?php

/** @var string $email */
/** @var string|null $error */
/** @var string|null $success */

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Verify Email</title>

    <link
        rel="stylesheet"
        href="./Registration.css"
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

<div class="register-container">

    <div class="register-card">

        <div class="register-header">

            <h1>
                Verify Email
            </h1>

            <p>
                Use the link sent to your email
                to verify your account.
            </p>

        </div>

        <?php if (!empty($error)): ?>

            <div class="error-box">

                <p>
                    <?= htmlspecialchars(
                        $error,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </p>

            </div>

        <?php endif; ?>

        <?php if (!empty($success)): ?>

            <div class="success-box">

                <p>
                    <?= htmlspecialchars(
                        $success,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </p>

            </div>

        <?php endif; ?>

        <form method="POST" action="">

            <input
                type="hidden"
                name="email"
                value="<?= htmlspecialchars(
                    $email,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>"
            >

            <input
                type="hidden"
                name="action"
                value="resend"
            >

            <button
                type="submit"
                class="register-button"
            >
                Resend Verification Email
            </button>

        </form>

    </div>

</div>

</body>

</html>
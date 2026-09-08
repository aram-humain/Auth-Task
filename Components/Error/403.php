<?php

http_response_code(403);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>403 Forbidden</title>

    <link
        rel="stylesheet"
        href="/Components/Error/403.css"
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

<main class="error-container">

    <section class="error-card">

        <div class="error-code">
            403
        </div>

        <h1>
            Access Denied
        </h1>

        <p>
            You do not have permission to access this page.
        </p>

        <a
            href="/Components/Dashboard/Dashboard.php"
            class="error-button"
        >
            Back to Dashboard
        </a>

    </section>

</main>

</body>

</html>
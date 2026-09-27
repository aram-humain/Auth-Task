<?php

/** @var array $user */

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Dashboard</title>


    <link
        rel="stylesheet"
        href="./Dashboard.css">

    <link
        rel="stylesheet"
        href="/assets/css/theme.css">


    <script
        src="/assets/js/theme.js"
        defer>
    </script>

</head>


<body>


    <?php

    require $_SERVER['DOCUMENT_ROOT']
        . '/includes/app_header.php';

    ?>


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
                            trim(
                                ($user['first_name'] ?? '')
                                    . ' '
                                    . ($user['last_name'] ?? '')
                            ) !== ''
                                ? trim(
                                    ($user['first_name'] ?? '')
                                        . ' '
                                        . ($user['last_name'] ?? '')
                                )
                                : $user['email'],
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
                            Full Name
                        </span>


                        <span class="info-value">

                            <?= htmlspecialchars(
                                trim(
                                    ($user['first_name'] ?? '')
                                        . ' '
                                        . ($user['last_name'] ?? '')
                                ),
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


        </section>


    </main>


</body>

</html>
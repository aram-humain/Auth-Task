<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/config/db.php';
require_once __DIR__ . '/LoginVal.php';
require_once __DIR__ . '/LoginDB.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/rate_limit.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/activity_log.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/csrf.php';



if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


if (isset($_SESSION['user_id'])) {

    header(
        'Location: ../Dashboard/Dashboard.php'
    );

    exit;
}


$errors = [];

$email = '';

$csrfToken =
    csrfToken();


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (
        !verifyCsrfToken(
            $_POST['csrf_token']
                ?? null
        )
    ) {

        http_response_code(403);

        exit('Invalid CSRF token.');
    }

    $email = strtolower(
        trim(
            $_POST['email'] ?? ''
        )
    );

    $password =
        $_POST['password']
        ?? '';

    $ipAddress =
        $_SERVER['REMOTE_ADDR']
        ?? null;


    $errors =
        validateLoginInput(
            $email,
            $password
        );


    if (empty($errors)) {

        $windowSeconds =
            15 * 60;


        $maxFailedAttempts =
            5;

        $failedByEmail =
            countRecentAttempts(
                $pdo,
                'failed_login',
                $windowSeconds,
                null,
                $email,
                null
            );


        $failedByIp = 0;


        if (
            $ipAddress !== null
            && $ipAddress !== ''
        ) {

            $failedByIp =
                countRecentAttempts(
                    $pdo,
                    'failed_login',
                    $windowSeconds,
                    null,
                    null,
                    $ipAddress
                );
        }

        if (
            $failedByEmail
            >= $maxFailedAttempts
            ||
            $failedByIp
            >= $maxFailedAttempts
        ) {

            $errors[] =
                'Too many failed login attempts. '
                . 'Please try again in 15 minutes.';
        } else {

            $user =
                findUserByCredentials(
                    $pdo,
                    $email,
                    $password
                );


            if ($user) {

                session_regenerate_id(
                    true
                );


                $_SESSION['user_id'] =
                    (int) $user['id'];


                $_SESSION['user_email'] =
                    $user['email'];


                header(
                    'Location: ../Dashboard/Dashboard.php'
                );

                exit;
            } else {

                $failedUserId =
                    findUserIdByEmail(
                        $pdo,
                        $email
                    );

                recordRateLimitAttempt(
                    $pdo,
                    'failed_login',
                    $failedUserId,
                    $email,
                    $ipAddress
                );

                logActivity(
                    $pdo,
                    $failedUserId,
                    'failed_login',
                    $failedUserId !== null
                        ? 'user'
                        : 'login',
                    $failedUserId,
                    $ipAddress
                );

                $failedByEmail =
                    countRecentAttempts(
                        $pdo,
                        'failed_login',
                        $windowSeconds,
                        null,
                        $email,
                        null
                    );


                $failedByIp = 0;


                if (
                    $ipAddress !== null
                    && $ipAddress !== ''
                ) {

                    $failedByIp =
                        countRecentAttempts(
                            $pdo,
                            'failed_login',
                            $windowSeconds,
                            null,
                            null,
                            $ipAddress
                        );
                }


                if (
                    $failedByEmail
                    >= $maxFailedAttempts
                    ||
                    $failedByIp
                    >= $maxFailedAttempts
                ) {

                    $errors[] =
                        'Too many failed login attempts. '
                        . 'Please try again in 15 minutes.';
                } else {

                    $errors[] =
                        'Invalid email or password.';
                }
            }
        }
    }
}


require_once __DIR__
    . '/Login.html.php';

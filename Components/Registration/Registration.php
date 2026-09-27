<?php

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/RegisterVal.php';
require_once __DIR__ . '/RegisterDB.php';
require_once __DIR__ . '/../../config/mail.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/csrf.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/rate_limit.php';



if (isset($_SESSION['user_id'])) {
    header('Location: ../Dashboard/Dashboard.php');
    exit;
}

$errors = [];

$firstName = '';
$lastName = '';
$email = '';

$csrfToken =
    csrfToken();

if (
    $_SERVER['REQUEST_METHOD']
    === 'POST'
) {

    if (
        !verifyCsrfToken(
            $_POST['csrf_token']
                ?? null
        )
    ) {

        http_response_code(403);

        exit('Invalid CSRF token.');
    }


    $ipAddress =
        $_SERVER['REMOTE_ADDR']
        ?? null;


    if (
        $ipAddress !== null
        && countRecentAttempts(
            $pdo,
            'registration',
            3600,
            null,
            null,
            $ipAddress
        ) >= 5
    ) {

        $errors[] =
            'Too many registration attempts. '
            . 'Please try again later.';
    }


    $firstName =
        trim(
            $_POST['first_name']
                ?? ''
        );


    $lastName =
        trim(
            $_POST['last_name']
                ?? ''
        );


    $email =
        strtolower(
            trim(
                $_POST['email']
                    ?? ''
            )
        );


    $password =
        $_POST['password']
        ?? '';


    if (empty($errors)) {

        $errors =
            validateRegistrationInput(
                $firstName,
                $lastName,
                $email,
                $password
            );
    }


    if (
        empty($errors)
        && emailAlreadyExists(
            $pdo,
            $email
        )
    ) {

        $errors[] =
            'An account with this email already exists.';
    }


    if (empty($errors)) {

        try {

            $token =
                bin2hex(
                    random_bytes(32)
                );


            $publicSlug =
                generatePublicSlug(
                    $firstName,
                    $lastName
                );


            $pdo->beginTransaction();


            createUser(
                $pdo,
                $firstName,
                $lastName,
                $email,
                $password,
                $token,
                $publicSlug
            );


            sendVerificationLinkEmail(
                $email,
                $firstName,
                $token
            );


            recordRateLimitAttempt(
                $pdo,
                'registration',
                null,
                $email,
                $ipAddress
            );


            $pdo->commit();


            header(
                'Location: Verify.php?email='
                    . urlencode($email)
            );

            exit;
        } catch (Throwable $exception) {

            if (
                $pdo->inTransaction()
            ) {

                $pdo->rollBack();
            }


            error_log(
                $exception->getMessage()
            );


            $errors[] =
                'Account could not be created. '
                . 'Please try again.';
        }
    }
}

require_once __DIR__ . '/Registration.html.php';

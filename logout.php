<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/auth.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/csrf.php';


requireLogin();


if (
    $_SERVER['REQUEST_METHOD']
    !== 'POST'
) {

    http_response_code(405);

    exit('Method not allowed.');
}


if (
    !verifyCsrfToken(
        $_POST['csrf_token']
        ?? null
    )
) {

    http_response_code(403);

    exit('Invalid CSRF token.');
}


logoutUser();


header(
    'Location: /Components/Login/Login.php'
);

exit;
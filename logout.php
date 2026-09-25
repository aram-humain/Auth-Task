<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/auth.php';

logoutUser();

header(
    'Location: /Components/Login/Login.php'
);

exit;
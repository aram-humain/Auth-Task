<?php

require_once __DIR__ . '/includes/auth.php';

if (isLoggedIn()) {
    header('Location: ./Components/Dashboard/Dashboard.php');
    exit;
}

header('Location: ./Components/Login/Login.php');
exit;

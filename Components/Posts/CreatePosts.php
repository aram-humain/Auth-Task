<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';


use Ramsey\Uuid\Uuid;

$userId = currentUserId();
$publicId = Uuid::uuid7()->getBytes();


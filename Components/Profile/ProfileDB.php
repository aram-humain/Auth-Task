<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/config/db.php';


function getProfileByUserId (PDO $pdo, int $userId): ?array 
{
    $statement = $pdo->prepare('
    SELECT 
        id, 
        user_id, 
        first_name,
        last_name,
        phone,
        location, 
        date_of_birth, 
        bio, 
        profile_picture, 
        created_at, 
        updated_at
    FROM profiles 
    WHERE user_id = :user_id
    LIMIT 1
    ');

    $statement->execute([
        'user_id' => $userId
        ]);
    
    $profile = $statement->fetch(PDO::FETCH_ASSOC);
    return $profile ?: null;
}
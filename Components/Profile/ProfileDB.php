<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/config/db.php';


function getProfileBySlug(PDO $pdo, string $slug): ?array
{
    $statement = $pdo->prepare(
    'SELECT 
        p.id AS profile_id, 
        p.user_id, 
        p.public_slug,
        p.first_name,
        p.last_name,
        p.phone,
        p.location, 
        p.date_of_birth, 
        p.bio, 
        p.profile_picture,
        p.profile_picture_public_id, 
        p.created_at, 
        p.updated_at,
        u.email
    FROM profiles p
    INNER JOIN users u
        ON u.id = p.user_id
    WHERE p.public_slug = :slug
    LIMIT 1'
    );

    $statement->execute(['slug' => $slug]);

    $profile = $statement->fetch(PDO::FETCH_ASSOC);

    return $profile ?: null;
}

function getProfileByUserId (PDO $pdo, int $userId): ?array 
{
    $statement = $pdo->prepare(
    'SELECT 
        p.id AS profile_id, 
        p.user_id, 
        p.public_slug,
        p.first_name,
        p.last_name,
        p.phone,
        p.location, 
        p.date_of_birth, 
        p.bio, 
        p.profile_picture, 
        p.profile_picture_public_id,
        u.email,
        p.created_at, 
        p.updated_at
    FROM profiles p
    INNER JOIN users u
        ON u.id = p.user_id
    WHERE p.user_id = :user_id
    LIMIT 1
    ');

    $statement->execute([
        'user_id' => $userId
        ]);
    
    $profile = $statement->fetch(PDO::FETCH_ASSOC);
    return $profile ?: null;
}


function updateProfile (
    PDO $pdo,
    int $userId,
    string $firstName,
    string $lastName,
    string $phone,
    string $location,
    ?string $dateOfBirth,
    string $bio
): void {
    
    $statement = $pdo->prepare(
        'UPDATE profiles
        SET 
            first_name = :first_name,
            last_name = :last_name,
            phone = :phone,
            location = :location,
            date_of_birth = :date_of_birth,
            bio = :bio,
            updated_at = CURRENT_TIMESTAMP
        WHERE user_id = :user_id'
    );

    $statement->execute([
        'first_name' => $firstName,
        'last_name' => $lastName,
        'phone' => $phone,
        'location' => $location,
        'date_of_birth' => $dateOfBirth,
        'bio' => $bio,
        'user_id' => $userId
    ]);
}
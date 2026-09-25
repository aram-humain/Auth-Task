<?php

function getAdminCategories(PDO $pdo): array
{
    $statement = $pdo->prepare(
        "SELECT 
        c.id,
        c.name,
        COUNT(p.id) AS posts_count
        
        FROM categories c
        
        LEFT JOIN posts p
        ON p.category_id = c.id
        
        GROUP BY c.id, c.name
        
        ORDER BY c.name"
    );

    $statement->execute();

    return $statement->fetchAll(PDO::FETCH_ASSOC);
}

function getCategoryById(PDO $pdo, int $categoryId): ?array
{
    $statement = $pdo->prepare(
        "SELECT id, name
        
        FROM categories
        
        WHERE id = :id
        
        LIMIT 1"
    );

    $statement->execute(['id' => $categoryId]);

    return $statement->fetch(PDO::FETCH_ASSOC) ?: null;
}

function categoryNameExists(PDO $pdo, string $name, ?int $excludeCategoryId = null): bool
{
    $sql = "
    SELECT 1
    FROM categories
    WHERE LOWER(name) = LOWER(:name)";

    $params = [
        'name' => $name
    ];

    if($excludeCategoryId !== null) {
        $sql .= "
        AND id <> :exclude_id";

        $params['exclude_id'] = $excludeCategoryId;
    }

    $sql .= "LIMIT 1";

    $statement = $pdo->prepare($sql);
    $statement->execute($params);

    return (bool) $statement->fetchColumn();
}

function createCategory(PDO $pdo, string $name): int 
{
    $statement = $pdo->prepare(
        "INSERT INTO categories (name) VALUES (:name)"
    );

    $statement->execute(['name' => $name]);

    return (int) $pdo->lastInsertId();
}

function renameCategory(PDO $pdo, int $categoryId, string $name): bool
{
    $statement = $pdo->prepare(
        "UPDATE categories
        
        SET name = :name
        
        WHERE id = :id"
    );

    $statement->execute([
        'name' => $name,
        'id' => $categoryId
    ]);

    return (bool) $statement->rowCount() > 0;
}

function countCategoryPosts(
    PDO $pdo, 
    int $categoryId
): int {
    $statement = $pdo->prepare(
        "SELECT COUNT(*)
        FROM posts
        WHERE category_id = :category_id"
    );

    $statement->execute([
        'category_id' => $categoryId
    ]);

    return (int) $statement->fetchColumn();
}

function deleteCategory(
    PDO $pdo,
    int $categoryId
): bool {
    if(countCategoryPosts($pdo, $categoryId) > 0) {
        return false;
    }

    $statement = $pdo->prepare("DELETE FROM categories WHERE id=:id");
    $statement->execute(['id' => $categoryId]);

    return $statement->rowCount() > 0;
}
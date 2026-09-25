<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/config/db.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/auth.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/upload.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/cloudinary.php';


function createPost(PDO $pdo, string $publicId, int $userId, string  $title, int $categoryId, string $content, string $status): int
{
    $statement = $pdo->prepare(
        'INSERT INTO posts(
        public_id,
        user_id,
        title,
        category_id,
        content,
        status
        )
        VALUES (
        :public_id,
        :user_id,
        :title,
        :category_id,
        :content,
        :status
        )
        '
    );

    $statement->execute([
        'public_id' => $publicId,
        'user_id' => $userId,
        'title' => $title,
        'category_id' => $categoryId,
        'content' => $content,
        'status' => $status
    ]);

    return (int) $pdo->lastInsertId();
}

function getAllCategories(PDO $pdo): array
{
    $statement = $pdo->prepare(
        'SELECT id, name FROM categories ORDER BY name'
    );
    $statement->execute();
    return $statement->fetchAll(PDO::FETCH_ASSOC);
}

function getTagByName(PDO $pdo, string $name): ?array
{
    $statement = $pdo->prepare(
        'SELECT id, name FROM tags WHERE name = :name LIMIT 1'
    );

    $statement->execute(['name' => $name]);


    $tag = $statement->fetch();
    return $tag ?: null;
}


function createTag(PDO $pdo, string $name): int
{
    $statement = $pdo->prepare(
        'INSERT INTO tags(name) VALUES (:name)'
    );

    $statement->execute(['name' => $name]);

    return $pdo->lastInsertId();
}

function attachTagToPost(
    PDO $pdo,
    int $postId,
    int $tagId
): void {
    $statement = $pdo->prepare(
        'INSERT INTO post_tag (
            post_id,
            tag_id
        )
        VALUES (
            :post_id,
            :tag_id
        )'
    );

    $statement->execute([
        'post_id' => $postId,
        'tag_id' => $tagId
    ]);
}

function processPostTags(PDO $pdo, int $postId, string $tagsInput): void
{
    $tags = explode(',', $tagsInput);

    $normalizedTags = [];

    foreach ($tags as $tag) {
        $tag = strtolower(trim($tag));

        if ($tag === '') {
            continue;
        }

        if (strlen($tag) > 100) {
            throw new RuntimeException('Tag name is too long, maximal length -> 100');
        }

        $normalizedTags[] = $tag;
    }

    $normalizedTags = array_unique($normalizedTags);

    foreach ($normalizedTags as $tagName) {
        $existingTag = getTagByName($pdo, $tagName);

        if ($existingTag !== null) {
            $tagId = (int) $existingTag['id'];
        } else {
            $tagId = createTag($pdo, $tagName);
        }

        attachTagToPost($pdo, $postId, $tagId);
    }
}


function addPostImage(
    PDO $pdo,
    int $postId,
    string $imageUrl,
    string $imagePublicId,
    int $sortOrder = 0
): void {
    $statement = $pdo->prepare(
        'INSERT INTO post_images(
        post_id,
        image_url,
        image_public_id,
        sort_order
        )
        VALUES 
        (
        :post_id,
        :image_url,
        :image_public_id,
        :sort_order
        )'
    );

    $statement->execute([
        'post_id' => $postId,
        'image_url' => $imageUrl,
        'image_public_id' => $imagePublicId,
        'sort_order' => $sortOrder
    ]);
}

function isValidPostStatus(string $status): bool
{
    $allowedStatus = [
        'draft',
        'published',
        'archived'
    ];

    return in_array($status, $allowedStatus, true);
}


function isUserVerified(PDO $pdo, int $userId): bool
{
    $statement = $pdo->prepare(
        'SELECT 1
        FROM email_verifications
        WHERE user_id = :user_id
            AND verified_at IS NOT NULL
        LIMIT 1'
    );

    $statement->execute([
        'user_id' => $userId
    ]);

    return (bool) $statement->fetchColumn();
}

function getPostByPublicId(PDO $pdo, string $publicId): ?array
{
    $statement = $pdo->prepare(
        'SELECT 
        p.id,
        p.public_id,
        p.user_id,
        p.title,
        p.content,
        p.status,
        p.created_at,
        p.updated_at,
        p.deleted_at,
        
        c.id AS category_id,
        c.name AS category_name,
        
        pr.first_name,
        pr.last_name,
        pr.public_slug
        
        FROM posts p
        JOIN categories c
        ON c.id = p.category_id
        
        JOIN profiles pr
        ON pr.user_id = p.user_id
        
        WHERE p.public_id = :public_id
        
        LIMIT 1'
    );

    $statement->execute([
        'public_id' => $publicId
    ]);

    $post = $statement->fetch(PDO::FETCH_ASSOC);

    return $post ?: null;
}

function getPostImages(
    PDO $pdo,
    int $postId
): array {
    $statement = $pdo->prepare(
        'SELECT
        id,
        image_url,
        image_public_id,
        sort_order
        FROM post_images
        WHERE post_id = :post_id
        ORDER BY sort_order'
    );

    $statement->execute([
        'post_id' => $postId
    ]);

    return $statement->fetchAll(PDO::FETCH_ASSOC);
}


function getPostTags(PDO $pdo, int $postId): array
{
    $statement = $pdo->prepare(
        'SELECT
        t.id,
        t.name
        FROM tags t
        
        JOIN post_tag pt
        ON pt.tag_id = t.id
        
        WHERE pt.post_id = :post_id
        
        ORDER BY t.name'
    );

    $statement->execute([
        'post_id' => $postId
    ]);

    return $statement->fetchAll(PDO::FETCH_ASSOC);
}


function countFeedPosts(
    PDO $pdo,
    string $search = '',
    ?int $categoryId = null,
    ?int $tagId = null,
    ?int $authorId = null
): int {
    $sql = "
        SELECT COUNT(*)

        FROM posts p

        WHERE p.status = 'published'
          AND p.deleted_at IS NULL
    ";

    $params = [];

    if ($search !== '') {
        $sql .= "
            AND (
                p.title LIKE :search
                OR p.content LIKE :search
            )
        ";

        $params['search'] =
            '%' . $search . '%';
    }

    if ($categoryId !== null) {
        $sql .= "
            AND p.category_id = :category_id
        ";

        $params['category_id'] =
            $categoryId;
    }

    if ($tagId !== null) {
        $sql .= "
            AND EXISTS (
                SELECT 1

                FROM post_tag pt_filter

                WHERE pt_filter.post_id = p.id
                  AND pt_filter.tag_id = :tag_id
            )
        ";

        $params['tag_id'] = $tagId;
    }

    if ($authorId !== null) {
        $sql .= "
        AND p.user_id = :author_id
    ";

        $params['author_id'] = $authorId;
    }

    $statement = $pdo->prepare($sql);

    $statement->execute($params);

    return (int) $statement->fetchColumn();
}

function getFeedPosts(
    PDO $pdo,
    int $limit,
    int $offset,
    string $search = '',
    ?int $categoryId = null,
    string $sort = 'newest',
    ?int $tagId = null,
    ?int $authorId = null
): array {
    $sql = "
        SELECT
            p.id,
            p.public_id,
            p.user_id,
            p.title,
            p.content,
            p.created_at,

            c.name AS category_name,

            pr.first_name,
            pr.last_name,
            pr.public_slug,
            pr.profile_picture,

            COALESCE(l.likes_count, 0) AS likes_count,
            COALESCE(cm.comments_count, 0) AS comments_count

        FROM posts p

        JOIN categories c
            ON c.id = p.category_id

        JOIN profiles pr
            ON pr.user_id = p.user_id

        LEFT JOIN (
            SELECT
                post_id,
                COUNT(*) AS likes_count
            FROM post_likes
            GROUP BY post_id
        ) l
            ON l.post_id = p.id

        LEFT JOIN (
            SELECT
                post_id,
                COUNT(*) AS comments_count
            FROM comments
            WHERE deleted_at IS NULL
            GROUP BY post_id
        ) cm
            ON cm.post_id = p.id

        WHERE p.status = 'published'
          AND p.deleted_at IS NULL
    ";

    if ($search !== '') {
        $sql .= "
            AND (
                p.title LIKE :search
                OR p.content LIKE :search
            )
        ";
    }

    if ($categoryId !== null) {
        $sql .= "
            AND p.category_id = :category_id
        ";
    }

    if ($tagId !== null) {
        $sql .= "
            AND EXISTS (
                SELECT 1

                FROM post_tag pt_filter

                WHERE pt_filter.post_id = p.id
                AND pt_filter.tag_id = :tag_id
            )
        ";
    }

    if ($authorId !== null) {
        $sql .= "
            AND p.user_id = :author_id
        ";
    }

    $orderBy = match ($sort) {
        'liked' => 'likes_count DESC, p.created_at DESC',
        'commented' => 'comments_count DESC, p.created_at DESC',
        default => 'p.created_at DESC'
    };



    $sql .= "
    ORDER BY $orderBy
    LIMIT :limit
    OFFSET :offset
    ";

    $statement = $pdo->prepare($sql);

    if ($authorId !== null) {
        $statement->bindValue(
            ':author_id',
            $authorId,
            PDO::PARAM_INT
        );
    }

    if ($tagId !== null) {
        $statement->bindValue(
            ':tag_id',
            $tagId,
            PDO::PARAM_INT
        );
    }

    if ($search !== '') {
        $statement->bindValue(
            ':search',
            '%' . $search . '%',
            PDO::PARAM_STR
        );
    }

    if ($categoryId !== null) {
        $statement->bindValue(
            ':category_id',
            $categoryId,
            PDO::PARAM_INT
        );
    }

    $statement->bindValue(
        ':limit',
        $limit,
        PDO::PARAM_INT
    );

    $statement->bindValue(
        ':offset',
        $offset,
        PDO::PARAM_INT
    );

    $statement->execute();

    return $statement->fetchAll(
        PDO::FETCH_ASSOC
    );
}

function getFeedPostImages(
    PDO $pdo,
    array $postIds
): array {
    if (empty($postIds)) {
        return [];
    }

    $placeholders = implode(
        ',',
        array_fill(0, count($postIds), '?')
    );

    $statement = $pdo->prepare(
        "SELECT 
        post_id,
        image_url,
        sort_order
        
        FROM post_images
        WHERE post_id IN ($placeholders)
        ORDER BY post_id, sort_order"
    );

    $statement->execute(array_values($postIds));
    $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

    $images = [];

    foreach ($rows as $row) {
        $postId = (int) $row['post_id'];
        $images[$postId][] = $row;
    }

    return $images;
}

function getFeedPostTags(PDO $pdo, array $postIds): array
{
    if (empty($postIds)) {
        return [];
    }

    $placeholders = implode(',', array_fill(0, count($postIds), '?'));

    $statement = $pdo->prepare(
        "SELECT 
        pt.post_id,
        t.id,
        t.name
        
        FROM post_tag pt
        
        JOIN tags t 
        ON t.id = pt.tag_id
        
        WHERE pt.post_id IN ($placeholders)
        
        ORDER BY
        pt.post_id,
        t.name"
    );

    $statement->execute(array_values($postIds));
    $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
    $tags = [];

    foreach ($rows as $row) {
        $postId = (int) $row['post_id'];

        $tags[$postId][] = $row;
    }

    return $tags;
}

function isCategoryExists(PDO $pdo, int $categoryId): bool
{
    $statement = $pdo->prepare(
        "SELECT 1
        FROM categories
        WHERE id = :id
        LIMIT 1"
    );

    $statement->execute(['id' => $categoryId]);

    return (bool) $statement->fetchColumn();
}

function getAllTags(PDO $pdo): array
{
    $statement = $pdo->query(
        "SELECT id, name
         FROM tags
         ORDER BY name"
    );

    return $statement->fetchAll(PDO::FETCH_ASSOC);
}

function tagExists(PDO $pdo, int $tagId): bool
{
    $statement = $pdo->prepare(
        "SELECT 1 FROM tags WHERE id = :id LIMIT 1"
    );

    $statement->execute(['id' => $tagId]);

    return (bool) $statement->fetchColumn();
}

function feedAuthorExists(PDO $pdo, string $publicSlug): bool
{
    $statement = $pdo->prepare(
        "SELECT 1
         FROM profiles pr
         JOIN posts p
             ON p.user_id = pr.user_id
         WHERE pr.public_slug = :public_slug
           AND p.status = 'published'
           AND p.deleted_at IS NULL
         LIMIT 1"
    );

    $statement->execute([
        'public_slug' => $publicSlug
    ]);

    return (bool) $statement->fetchColumn();
}

function getFeedAuthorIdBySlug(
    PDO $pdo,
    string $publicSlug
): ?int {
    $statement = $pdo->prepare(
        "SELECT user_id
         FROM profiles
         WHERE public_slug = :public_slug
         LIMIT 1"
    );

    $statement->execute([
        'public_slug' => $publicSlug
    ]);

    $userId = $statement->fetchColumn();

    return $userId === false
        ? null
        : (int) $userId;
}

function getFeedAuthors(PDO $pdo): array
{
    $statement = $pdo->query(
        "SELECT DISTINCT
        pr.user_id,
        pr.first_name,
        pr.last_name,
        pr.public_slug
        
        FROM profiles pr
        
        JOIN posts p
         ON p.user_id = pr.user_id
         
         WHERE p.status = 'published'
         AND p.deleted_at IS NULL
         
        ORDER BY pr.first_name, pr.last_name"
    );

    return $statement->fetchAll(PDO::FETCH_ASSOC);
}


function getProfilePostsByUserId(
    PDO $pdo,
    int $userId,
    bool $includePrivate = false
): array {
    $sql = "
        SELECT
            p.id,
            p.public_id,
            p.title,
            p.content,
            p.status,
            p.created_at,
            c.name AS category_name

        FROM posts p

        JOIN categories c
            ON c.id = p.category_id

        WHERE p.user_id = :user_id
          AND p.deleted_at IS NULL
    ";

    if (!$includePrivate) {
        $sql .= "
            AND p.status = 'published'
        ";
    }

    $sql .= "
        ORDER BY p.created_at DESC
    ";

    $statement = $pdo->prepare($sql);

    $statement->execute([
        'user_id' => $userId
    ]);

    return $statement->fetchAll(PDO::FETCH_ASSOC);
}

function getOwnedPostByPublicId(
    PDO $pdo,
    string $publicId,
    int $userId
): ?array {
    $statement = $pdo->prepare(
        "SELECT 
        p.id,
        p.public_id,
        p.user_id,
        p.title,
        p.content,
        p.status,
        p.category_id,
        p.created_at,
        p.updated_at
        
        FROM posts p
        
        WHERE p.public_id = :public_id
        AND p.user_id = :user_id
        AND p.deleted_at IS NULL
        
        LIMIT 1"
    );

    $statement->execute([
        'public_id' => $publicId,
        'user_id' => $userId
    ]);

    $post = $statement->fetch(PDO::FETCH_ASSOC);

    return $post ?: null;
}

function updateOwnedPost (
    PDO $pdo,
    int $postId,
    int $userId,
    string $title,
    int $categoryId,
    string $content,
    string $status
): bool {
    $statement = $pdo->prepare(
        "UPDATE posts
        SET title = :title,
        category_id = :category_id,
        content = :content,
        status = :status,
        updated_at = CURRENT_TIMESTAMP
        
        WHERE id = :post_id
        AND user_id = :user_id
        AND deleted_at IS NULL"
    );

    $statement->execute([
        'title' => $title,
        'user_id' => $userId,
        'post_id' => $postId,
        'category_id' => $categoryId,
        'content' => $content,
        'status' => $status
    ]);

    return $statement->rowCount() > 0;
}

function replacePostTags(
    PDO $pdo,
    int $postId,
    string $tagsInput
): void {
    $statement = $pdo->prepare(
        "DELETE FROM post_tag 
        WHERE post_id = :post_id"
    );

    $statement->execute(['post_id' => $postId]);

    if(trim($tagsInput) !== '') {
        processPostTags($pdo, $postId, $tagsInput);
    }
}

function softDeleteOwnedPost(
    PDO $pdo,
    int $postId,
    int $userId
): bool {
    $statement = $pdo->prepare(
        "UPDATE posts
        SET deleted_at = CURRENT_TIMESTAMP,
            updated_at = CURRENT_TIMESTAMP
        WHERE id = :post_id
        AND user_id = :user_id
        AND deleted_at IS NULL"
    );

    $statement->execute([
        'post_id' => $postId,
        'user_id' => $userId
    ]);

    return $statement->rowCount() > 0;
}

function getDeletedPosts(PDO $pdo): array {
    $statement = $pdo->prepare(
        "SELECT
            p.id,
            p.public_id,
            p.title,
            p.status,
            p.created_at,
            p.deleted_at,
            
            c.name AS category_name,
            
            pr.first_name,
            pr.last_name,
            pr.public_slug
            
        FROM posts p
        
        JOIN categories c 
            ON c.id = p.category_id
        JOIN profiles pr
            ON pr.user_id = p.user_id
            
        WHERE p.deleted_at IS NOT NULL
        
        ORDER BY p.deleted_at DESC"
    );

    $statement->execute();

    return $statement->fetchAll(PDO::FETCH_ASSOC);
}

function restoreDeletedPost(
    PDO $pdo,
    int $postId
): bool {
    $statement = $pdo->prepare(
        "UPDATE posts
        SET 
        deleted_at = NULL,
        updated_at = CURRENT_TIMESTAMP
        
        WHERE id = :post_id
            AND deleted_at IS NOT NULL"
    );

    $statement->execute([
        'post_id' => $postId
    ]);

    return $statement->rowCount() > 0;
}
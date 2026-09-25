<?php


function hasUserReportedPost(
    PDO $pdo,
    int $postId,
    int $userId
): bool {

    $statement = $pdo->prepare(
        "SELECT 1

         FROM post_reports

         WHERE post_id = :post_id
           AND reporter_user_id = :user_id

         LIMIT 1"
    );

    $statement->execute([
        'post_id' => $postId,
        'user_id' => $userId
    ]);

    return (bool) $statement->fetchColumn();
}


function createPostReport(
    PDO $pdo,
    int $postId,
    int $userId,
    string $reason,
    ?string $description = null
): int {

    $statement = $pdo->prepare(
        "INSERT INTO post_reports (
            post_id,
            reporter_user_id,
            reason,
            description
        )
        VALUES (
            :post_id,
            :user_id,
            :reason,
            :description
        )"
    );

    $statement->execute([
        'post_id' => $postId,
        'user_id' => $userId,
        'reason' => $reason,
        'description' => $description
    ]);

    return (int) $pdo->lastInsertId();
}


function hasUserReportedComment(
    PDO $pdo,
    int $commentId,
    int $userId
): bool {

    $statement = $pdo->prepare(
        "SELECT 1

         FROM comment_reports

         WHERE comment_id = :comment_id
           AND reporter_user_id = :user_id

         LIMIT 1"
    );

    $statement->execute([
        'comment_id' => $commentId,
        'user_id' => $userId
    ]);

    return (bool) $statement->fetchColumn();
}


function createCommentReport(
    PDO $pdo,
    int $commentId,
    int $userId,
    string $reason,
    ?string $description = null
): int {

    $statement = $pdo->prepare(
        "INSERT INTO comment_reports (
            comment_id,
            reporter_user_id,
            reason,
            description
        )
        VALUES (
            :comment_id,
            :user_id,
            :reason,
            :description
        )"
    );

    $statement->execute([
        'comment_id' => $commentId,
        'user_id' => $userId,
        'reason' => $reason,
        'description' => $description
    ]);

    return (int) $pdo->lastInsertId();
}

function getPendingPostReports(
    PDO $pdo
): array {

    $statement = $pdo->query(
        "SELECT
            r.id AS report_id,
            r.post_id,
            r.reporter_user_id,
            r.reason,
            r.description,
            r.status,
            r.created_at,

            p.public_id AS post_public_id,
            p.title,
            p.deleted_at AS post_deleted_at,
            p.user_id AS post_author_id,

            reporter.first_name AS reporter_first_name,
            reporter.last_name AS reporter_last_name,
            reporter.public_slug AS reporter_slug,

            author.first_name AS author_first_name,
            author.last_name AS author_last_name,
            author.public_slug AS author_slug

         FROM post_reports r

         JOIN posts p
            ON p.id = r.post_id

         JOIN profiles reporter
            ON reporter.user_id = r.reporter_user_id

         JOIN profiles author
            ON author.user_id = p.user_id

         WHERE r.status = 'pending'

         ORDER BY r.created_at DESC"
    );

    return $statement->fetchAll(
        PDO::FETCH_ASSOC
    );
}


function getPendingCommentReports(
    PDO $pdo
): array {

    $statement = $pdo->query(
        "SELECT
            r.id AS report_id,
            r.comment_id,
            r.reporter_user_id,
            r.reason,
            r.description,
            r.status,
            r.created_at,

            c.public_id AS comment_public_id,
            c.content,
            c.deleted_at AS comment_deleted_at,
            c.user_id AS comment_author_id,
            c.post_id,

            p.public_id AS post_public_id,

            reporter.first_name AS reporter_first_name,
            reporter.last_name AS reporter_last_name,
            reporter.public_slug AS reporter_slug,

            author.first_name AS author_first_name,
            author.last_name AS author_last_name,
            author.public_slug AS author_slug

         FROM comment_reports r

         JOIN comments c
            ON c.id = r.comment_id

         JOIN posts p
            ON p.id = c.post_id

         JOIN profiles reporter
            ON reporter.user_id = r.reporter_user_id

         JOIN profiles author
            ON author.user_id = c.user_id

         WHERE r.status = 'pending'

         ORDER BY r.created_at DESC"
    );

    return $statement->fetchAll(
        PDO::FETCH_ASSOC
    );
}


function getPostReportById(
    PDO $pdo,
    int $reportId
): ?array {

    $statement = $pdo->prepare(
        "SELECT
            r.*,
            p.user_id AS post_author_id,
            p.deleted_at AS post_deleted_at

         FROM post_reports r

         JOIN posts p
            ON p.id = r.post_id

         WHERE r.id = :id

         LIMIT 1"
    );

    $statement->execute([
        'id' => $reportId
    ]);

    $report = $statement->fetch(
        PDO::FETCH_ASSOC
    );

    return $report ?: null;
}


function getCommentReportById(
    PDO $pdo,
    int $reportId
): ?array {

    $statement = $pdo->prepare(
        "SELECT
            r.*,
            c.user_id AS comment_author_id,
            c.deleted_at AS comment_deleted_at

         FROM comment_reports r

         JOIN comments c
            ON c.id = r.comment_id

         WHERE r.id = :id

         LIMIT 1"
    );

    $statement->execute([
        'id' => $reportId
    ]);

    $report = $statement->fetch(
        PDO::FETCH_ASSOC
    );

    return $report ?: null;
}


function resolvePostReport(
    PDO $pdo,
    int $reportId,
    int $reviewerUserId
): bool {

    $statement = $pdo->prepare(
        "UPDATE post_reports

         SET
            status = 'action_taken',
            reviewed_by = :reviewed_by,
            reviewed_at = CURRENT_TIMESTAMP

         WHERE id = :id
           AND status = 'pending'"
    );

    $statement->execute([
        'reviewed_by' => $reviewerUserId,
        'id' => $reportId
    ]);

    return $statement->rowCount() > 0;
}


function resolveCommentReport(
    PDO $pdo,
    int $reportId,
    int $reviewerUserId
): bool {

    $statement = $pdo->prepare(
        "UPDATE comment_reports

         SET
            status = 'action_taken',
            reviewed_by = :reviewed_by,
            reviewed_at = CURRENT_TIMESTAMP

         WHERE id = :id
           AND status = 'pending'"
    );

    $statement->execute([
        'reviewed_by' => $reviewerUserId,
        'id' => $reportId
    ]);

    return $statement->rowCount() > 0;
}

function resolveAllPostReports(
    PDO $pdo,
    int $postId,
    int $reviewerUserId
): void {
    $statement = $pdo->prepare(
        "UPDATE post_reports
        
        SET status = 'action_taken',
        reviewed_by = :reviewed_by,
        reviewed_at = CURRENT_TIMESTAMP
        
        WHERE post_id = :post_id
        AND status = 'pending'"
    );

    $statement->execute([
        'reviewed_by' => $reviewerUserId,
        'post_id' => $postId
    ]);
}

function resolveAllCommentReports(PDO $pdo, int $commentId, int $reviewerUserId): void {
    $statement = $pdo->prepare(
        "UPDATE comment_reports
        
        SET status = 'action_taken',
        reviewed_by = :reviewed_by,
        reviewed_at = CURRENT_TIMESTAMP
        
        WHERE comment_id = :comment_id
        AND status = 'pending'"
    );

    $statement->execute([
        'reviewed_by' => $reviewerUserId,
        'comment_id' => $commentId
    ]);
}

function dismissPostReport(
    PDO $pdo,
    int $reportId,
    int $reviewerUserId
): bool {

    $statement = $pdo->prepare(
        "UPDATE post_reports

         SET
            status = 'dismissed',
            reviewed_by = :reviewed_by,
            reviewed_at = CURRENT_TIMESTAMP

         WHERE id = :report_id
           AND status = 'pending'"
    );

    $statement->execute([
        'reviewed_by' => $reviewerUserId,
        'report_id' => $reportId
    ]);

    return $statement->rowCount() > 0;
}


function dismissCommentReport(
    PDO $pdo,
    int $reportId,
    int $reviewerUserId
): bool {

    $statement = $pdo->prepare(
        "UPDATE comment_reports

         SET
            status = 'dismissed',
            reviewed_by = :reviewed_by,
            reviewed_at = CURRENT_TIMESTAMP

         WHERE id = :report_id
           AND status = 'pending'"
    );

    $statement->execute([
        'reviewed_by' => $reviewerUserId,
        'report_id' => $reportId
    ]);

    return $statement->rowCount() > 0;
}
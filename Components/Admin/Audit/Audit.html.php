<?php

/** @var array $auditLogs */

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Audit Log</title>

    <link
        rel="stylesheet"
        href="/Components/Admin/Audit/Audit.css"
    >

    <link
        rel="stylesheet"
        href="/assets/css/theme.css"
    >

    <script src="/assets/js/theme.js"></script>

</head>

<body>

<button
    type="button"
    id="theme-toggle"
    class="theme-toggle"
>
    Theme
</button>

<main class="audit-container">

    <section class="audit-card">

        <div class="audit-header">

            <div>

                <h1>
                    Audit Log
                </h1>

                <p>
                    History of administrative actions.
                </p>

            </div>

            <a
                href="/Components/Admin/admin.php"
                class="audit-button audit-button-secondary"
            >
                Back to Admin
            </a>

        </div>

        <div class="table-wrapper">

            <table>

                <thead>

                    <tr>
                        <th>ID</th>
                        <th>Actor</th>
                        <th>Target</th>
                        <th>Action</th>
                        <th>Old Value</th>
                        <th>New Value</th>
                        <th>Date</th>
                    </tr>

                </thead>

                <tbody>

                <?php if (empty($auditLogs)): ?>

                    <tr>

                        <td
                            colspan="7"
                            class="empty-state"
                        >
                            No audit records found.
                        </td>

                    </tr>

                <?php else: ?>

                    <?php foreach ($auditLogs as $log): ?>

                        <tr>

                            <td>
                                <?= (int) $log['id'] ?>
                            </td>

                            <td>

                                <strong>
                                    <?= htmlspecialchars(
                                        $log['actor_name'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </strong>

                                <div class="user-id">
                                    ID: <?= (int) $log['actor_id'] ?>
                                </div>

                            </td>

                            <td>

                                <?php if ($log['target_id'] !== null): ?>

                                    <strong>
                                        <?= htmlspecialchars(
                                            $log['target_name'] ?? 'Unknown',
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </strong>

                                    <div class="user-id">
                                        ID: <?= (int) $log['target_id'] ?>
                                    </div>

                                <?php else: ?>

                                    <span class="muted">
                                        —
                                    </span>

                                <?php endif; ?>

                            </td>

                            <td>

                                <span class="action-badge">
                                    <?= htmlspecialchars(
                                        str_replace(
                                            '_',
                                            ' ',
                                            $log['action']
                                        ),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </span>

                            </td>

                            <td>

                                <?= htmlspecialchars(
                                    $log['old_value'] ?? '—',
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>

                            </td>

                            <td>

                                <?= htmlspecialchars(
                                    $log['new_value'] ?? '—',
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>

                            </td>

                            <td>

                                <?= htmlspecialchars(
                                    $log['created_at'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                <?php endif; ?>

                </tbody>

            </table>

        </div>

    </section>

</main>

</body>

</html>
<?php

/** @var array $activityLogs */
/** @var array $activityUsers */
/** @var int|null $filterUserId */
/** @var int $page */
/** @var int $totalPages */
/** @var int $totalLogs */

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Activity Log</title>


    <link
        rel="stylesheet"
        href="/Components/Admin/Audit/Audit.css">


    <link
        rel="stylesheet"
        href="/assets/css/theme.css">


    <script
        src="/assets/js/theme.js">
    </script>

</head>


<body>


    <button
        type="button"
        id="theme-toggle"
        class="theme-toggle">
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
                        System activity and security events.
                    </p>

                </div>


                <a
                    href="/Components/Admin/Admin.php"
                    class="audit-button audit-button-secondary">
                    Back to Admin
                </a>


            </div>


            <!-- ============================= -->
            <!-- FILTER -->
            <!-- ============================= -->

            <div class="audit-toolbar">


                <form
                    method="GET"
                    class="audit-filter">


                    <label for="user_id">
                        User
                    </label>


                    <select
                        name="user_id"
                        id="user_id">


                        <option value="">
                            All users
                        </option>


                        <?php foreach ($activityUsers as $user): ?>


                            <?php

                            $fullName = trim(
                                ($user['first_name'] ?? '')
                                    . ' '
                                    . ($user['last_name'] ?? '')
                            );


                            $displayName =
                                $fullName !== ''
                                ? $fullName
                                : $user['email'];

                            ?>


                            <option
                                value="<?= (int) $user['id'] ?>"
                                <?= $filterUserId === (int) $user['id']
                                    ? 'selected'
                                    : '' ?>>

                                <?= htmlspecialchars(
                                    $displayName
                                        . ' ('
                                        . $user['email']
                                        . ')',
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>

                            </option>


                        <?php endforeach; ?>


                    </select>


                    <button
                        type="submit"
                        class="audit-button">
                        Filter
                    </button>


                    <?php if ($filterUserId !== null): ?>

                        <a
                            href="/Components/Admin/Audit/Audit.php"
                            class="audit-button audit-button-secondary">
                            Clear
                        </a>

                    <?php endif; ?>


                </form>


                <div class="audit-count">

                    <?= (int) $totalLogs ?>

                    event<?= $totalLogs === 1 ? '' : 's' ?>

                </div>


            </div>


            <!-- ============================= -->
            <!-- TABLE -->
            <!-- ============================= -->

            <div class="table-wrapper">


                <table>


                    <thead>

                        <tr>

                            <th>ID</th>

                            <th>User</th>

                            <th>Action</th>

                            <th>Target</th>

                            <th>IP Address</th>

                            <th>Date</th>

                        </tr>

                    </thead>


                    <tbody>


                        <?php if (empty($activityLogs)): ?>


                            <tr>

                                <td
                                    colspan="6"
                                    class="empty-state">
                                    No activity records found.
                                </td>

                            </tr>


                        <?php else: ?>


                            <?php foreach ($activityLogs as $log): ?>


                                <?php

                                $fullName = trim(
                                    ($log['first_name'] ?? '')
                                        . ' '
                                        . ($log['last_name'] ?? '')
                                );

                                ?>


                                <tr>


                                    <td>
                                        <?= (int) $log['id'] ?>
                                    </td>


                                    <td>


                                        <?php if ($log['user_id'] !== null): ?>


                                            <strong>

                                                <?= htmlspecialchars(
                                                    $fullName !== ''
                                                        ? $fullName
                                                        : ($log['email'] ?? 'User'),
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>

                                            </strong>


                                            <div class="user-id">

                                                ID:
                                                <?= (int) $log['user_id'] ?>

                                            </div>


                                            <?php if (!empty($log['email'])): ?>

                                                <div class="user-id">

                                                    <?= htmlspecialchars(
                                                        $log['email'],
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>

                                                </div>

                                            <?php endif; ?>


                                        <?php else: ?>


                                            <span class="muted">
                                                Unknown / Guest
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


                                        <?php if ($log['target_type'] !== null): ?>


                                            <strong>

                                                <?= htmlspecialchars(
                                                    $log['target_type'],
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>

                                            </strong>


                                            <?php if ($log['target_id'] !== null): ?>

                                                <div class="user-id">

                                                    ID:
                                                    <?= (int) $log['target_id'] ?>

                                                </div>

                                            <?php endif; ?>


                                        <?php else: ?>


                                            <span class="muted">
                                                —
                                            </span>


                                        <?php endif; ?>


                                    </td>


                                    <td>

                                        <?= htmlspecialchars(
                                            $log['ip_address'] ?? '—',
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


            <!-- ============================= -->
            <!-- PAGINATION -->
            <!-- ============================= -->

            <?php if ($totalPages > 1): ?>


                <nav
                    class="audit-pagination"
                    aria-label="Activity log pagination">


                    <?php if ($page > 1): ?>


                        <a
                            class="pagination-link"
                            href="?<?= htmlspecialchars(
                                        http_build_query([
                                            'user_id' =>
                                            $filterUserId,
                                            'page' =>
                                            $page - 1
                                        ]),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>">
                            Previous
                        </a>


                    <?php endif; ?>


                    <?php for (
                        $i = 1;
                        $i <= $totalPages;
                        $i++
                    ): ?>


                        <a
                            class="pagination-link <?= $i === $page
                                                        ? 'pagination-active'
                                                        : '' ?>"
                            href="?<?= htmlspecialchars(
                                        http_build_query([
                                            'user_id' =>
                                            $filterUserId,
                                            'page' =>
                                            $i
                                        ]),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>">
                            <?= $i ?>
                        </a>


                    <?php endfor; ?>


                    <?php if ($page < $totalPages): ?>


                        <a
                            class="pagination-link"
                            href="?<?= htmlspecialchars(
                                        http_build_query([
                                            'user_id' =>
                                            $filterUserId,
                                            'page' =>
                                            $page + 1
                                        ]),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>">
                            Next
                        </a>


                    <?php endif; ?>


                </nav>


            <?php endif; ?>


        </section>


    </main>


</body>

</html>
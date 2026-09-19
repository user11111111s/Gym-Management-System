<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/config/security.php';

start_secure_session();

if (
    ($_SESSION['role'] ?? '') !== 'trainer' ||
    !isset($_SESSION['trainer_id']) ||
    !is_numeric($_SESSION['trainer_id'])
) {
    header('Location: ../Login/index.php?error=Please%20login');
    exit();
}

$trainerId = (int) $_SESSION['trainer_id'];

require_once __DIR__ . '/db_config.php';

// Fetch only active sessions belonging to the logged-in trainer
$query = "SELECT
            u.id AS user_id,
            u.FirstName,
            u.LastName,
            u.number,
            b.booking_start_date,
            b.booking_end_date,
            b.default_session_time
          FROM trainer_bookings b
          JOIN users u ON b.user_id = u.id
          WHERE b.trainer_id = ?
            AND b.booking_status = 'active'
          ORDER BY b.booking_start_date ASC";

$stmt = $conn->prepare($query);
$stmt->bind_param('i', $trainerId);
$stmt->execute();

$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>My Sessions</title>

    <link
        href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css"
        rel="stylesheet"
    >
</head>

<body>

<?php include 'header.php'; ?>

<div class="flex">

    <!-- Sidebar -->
    <div class="w-64 bg-gray-200 h-screen p-4">
        <?php include 'sidebar.php'; ?>
    </div>

    <!-- Main Content -->
    <div class="flex-1 p-6">

        <h2 class="text-2xl font-semibold mb-4">
            My Active Sessions
        </h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">

            <?php while ($row = $result->fetch_assoc()): ?>

                <div class="bg-white p-4 shadow-md rounded-lg border">

                    <h3 class="text-lg font-bold">
                        <?= htmlspecialchars(
                            trim($row['FirstName'] . ' ' . $row['LastName']),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </h3>

                    <p class="text-gray-600 text-sm">
                        <?= htmlspecialchars(
                            (string) $row['number'],
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </p>

                    <p class="text-gray-800">
                        <strong>Start:</strong>
                        <?= htmlspecialchars(
                            date('Y-m-d', strtotime($row['booking_start_date'])),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </p>

                    <p class="text-gray-800">
                        <strong>End:</strong>
                        <?= htmlspecialchars(
                            date('Y-m-d', strtotime($row['booking_end_date'])),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </p>

                    <p class="text-blue-500 font-semibold mt-2">
                        <?= htmlspecialchars(
                            date('g:i A', strtotime($row['default_session_time'])),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </p>

                </div>

            <?php endwhile; ?>

        </div>

    </div>

</div>

</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
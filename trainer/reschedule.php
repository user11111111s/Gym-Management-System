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

$query = "SELECT
            trainer_reschedule_id,
            request_type,
            start_date,
            end_date,
            new_start_time,
            new_end_time,
            status,
            created_at
          FROM trainer_reschedules
          WHERE trainer_id = ?
          ORDER BY created_at DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param('i', $trainerId);
$stmt->execute();

$result = $stmt->get_result();

$csrfToken = csrf_token();

$successMessage = $_SESSION['success'] ?? '';
$errorMessage = $_SESSION['error'] ?? '';

unset($_SESSION['success'], $_SESSION['error']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Reschedule Request</title>

    <link
        href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css"
        rel="stylesheet"
    >
</head>

<body>

<?php include 'header.php'; ?>

<div class="flex min-h-screen">

    <div class="sidebar bg-gray-800 text-white p-4">
        <?php include 'sidebar.php'; ?>
    </div>

    <div class="flex-grow p-10 ml-80">

        <h2 class="text-2xl font-bold mb-4">
            Reschedule Request
        </h2>

        <?php if ($successMessage !== ''): ?>
            <div class="bg-green-500 text-white p-3 mb-4 rounded">
                <?= htmlspecialchars(
                    $successMessage,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </div>
        <?php endif; ?>

        <?php if ($errorMessage !== ''): ?>
            <div class="bg-red-500 text-white p-3 mb-4 rounded">
                <?= htmlspecialchars(
                    $errorMessage,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </div>
        <?php endif; ?>

        <form
            action="reschedule_backend/reschedule_submit.php"
            method="POST"
            class="bg-white p-8 rounded-lg shadow-md w-full max-w-2xl mb-10"
        >

            <input
                type="hidden"
                name="csrf_token"
                value="<?= htmlspecialchars(
                    $csrfToken,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>"
            >

            <label class="block mb-2">
                Request Type:
            </label>

            <select
                name="request_type"
                id="request_type"
                class="w-full p-2 border rounded mb-4"
                required
            >
                <option value="leave">Leave</option>
                <option value="part-time">Part-Time Shift</option>
                <option value="full-time">Full-Time Shift</option>
            </select>

            <label class="block mb-2">
                Start Date:
            </label>

            <input
                type="date"
                name="start_date"
                class="w-full p-2 border rounded mb-4"
                required
            >

            <label class="block mb-2">
                End Date:
            </label>

            <input
                type="date"
                name="end_date"
                class="w-full p-2 border rounded mb-4"
                required
            >

            <div id="shift_details">

                <label class="block mb-2">
                    New Start Time:
                </label>

                <input
                    type="time"
                    name="new_start_time"
                    id="new_start_time"
                    class="w-full p-2 border rounded mb-4"
                >

                <label class="block mb-2">
                    New End Time:
                </label>

                <input
                    type="time"
                    name="new_end_time"
                    id="new_end_time"
                    class="w-full p-2 border rounded mb-4"
                >

            </div>

            <button
                type="submit"
                class="bg-blue-500 text-white p-2 rounded w-full"
            >
                Submit Request
            </button>

        </form>

        <h2 class="text-2xl font-bold mb-4">
            Submitted Requests
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">

            <?php while ($row = $result->fetch_assoc()): ?>

                <div class="bg-white p-4 rounded-lg shadow-md">

                    <p class="font-bold text-lg">
                        Request ID:
                        <?= (int) $row['trainer_reschedule_id'] ?>
                    </p>

                    <p>
                        <strong>Type:</strong>
                        <?= htmlspecialchars(
                            ucfirst((string) $row['request_type']),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </p>

                    <p>
                        <strong>Submitted:</strong>
                        <?= htmlspecialchars(
                            date('d M Y', strtotime($row['created_at'])),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </p>

                    <p>
                        <strong>Start Date:</strong>
                        <?= htmlspecialchars(
                            date('d M Y', strtotime($row['start_date'])),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </p>

                    <p>
                        <strong>End Date:</strong>
                        <?= htmlspecialchars(
                            date('d M Y', strtotime($row['end_date'])),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </p>

                    <?php if ($row['request_type'] !== 'leave'): ?>

                        <p>
                            <strong>New Start Time:</strong>
                            <?= htmlspecialchars(
                                $row['new_start_time'] ?: '-',
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </p>

                        <p>
                            <strong>New End Time:</strong>
                            <?= htmlspecialchars(
                                $row['new_end_time'] ?: '-',
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </p>

                    <?php endif; ?>

                    <p class="mt-2">

                        <strong>Status:</strong>

                        <?php
                        $statusClass = match ($row['status']) {
                            'approved' => 'bg-green-500 text-white',
                            'rejected' => 'bg-red-500 text-white',
                            default => 'bg-yellow-500 text-white',
                        };
                        ?>

                        <span class="px-2 py-1 rounded-lg <?= $statusClass ?>">
                            <?= htmlspecialchars(
                                ucfirst((string) $row['status']),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </span>

                    </p>

                </div>

            <?php endwhile; ?>

        </div>

    </div>

</div>

<script>
document.getElementById('request_type').addEventListener('change', function () {
    const shiftDetails = document.getElementById('shift_details');
    const startTime = document.getElementById('new_start_time');
    const endTime = document.getElementById('new_end_time');

    const isLeave = this.value === 'leave';

    shiftDetails.style.display = isLeave ? 'none' : 'block';

    startTime.disabled = isLeave;
    endTime.disabled = isLeave;

    startTime.required = !isLeave;
    endTime.required = !isLeave;
});

document.getElementById('request_type').dispatchEvent(
    new Event('change')
);
</script>

</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/config/security.php';

start_secure_session();

if (
    ($_SESSION['role'] ?? '') !== 'trainer' ||
    !isset($_SESSION['trainer_id']) ||
    !is_numeric($_SESSION['trainer_id'])
) {
    header('Location: ../../Login/index.php?error=Please%20login');
    exit();
}

require_post_request();
require_csrf_token();

$trainerId = (int) $_SESSION['trainer_id'];

$requestType = trim((string) ($_POST['request_type'] ?? ''));
$startDate = trim((string) ($_POST['start_date'] ?? ''));
$endDate = trim((string) ($_POST['end_date'] ?? ''));
$newStartTime = trim((string) ($_POST['new_start_time'] ?? ''));
$newEndTime = trim((string) ($_POST['new_end_time'] ?? ''));

$allowedTypes = ['leave', 'part-time', 'full-time'];

if (!in_array($requestType, $allowedTypes, true)) {
    $_SESSION['error'] = 'Invalid request type.';
    header('Location: ../reschedule.php');
    exit();
}

$startDateObject = DateTime::createFromFormat('Y-m-d', $startDate);
$endDateObject = DateTime::createFromFormat('Y-m-d', $endDate);

if (
    !$startDateObject ||
    $startDateObject->format('Y-m-d') !== $startDate ||
    !$endDateObject ||
    $endDateObject->format('Y-m-d') !== $endDate
) {
    $_SESSION['error'] = 'Invalid date provided.';
    header('Location: ../reschedule.php');
    exit();
}

if ($startDate > $endDate) {
    $_SESSION['error'] = 'End date must be on or after the start date.';
    header('Location: ../reschedule.php');
    exit();
}

if ($requestType === 'leave') {
    $newStartTime = null;
    $newEndTime = null;
} else {
    if ($newStartTime === '' || $newEndTime === '') {
        $_SESSION['error'] = 'Start and end times are required for shift requests.';
        header('Location: ../reschedule.php');
        exit();
    }

    $startTimeObject = DateTime::createFromFormat('H:i', $newStartTime);
    $endTimeObject = DateTime::createFromFormat('H:i', $newEndTime);

    if (
        !$startTimeObject ||
        $startTimeObject->format('H:i') !== $newStartTime ||
        !$endTimeObject ||
        $endTimeObject->format('H:i') !== $newEndTime
    ) {
        $_SESSION['error'] = 'Invalid time provided.';
        header('Location: ../reschedule.php');
        exit();
    }

    if ($newStartTime >= $newEndTime) {
        $_SESSION['error'] = 'End time must be after start time.';
        header('Location: ../reschedule.php');
        exit();
    }
}

require_once dirname(__DIR__) . '/db_config.php';

// Prevent overlapping pending/approved requests.
$checkQuery = "SELECT COUNT(*)
               FROM trainer_reschedules
               WHERE trainer_id = ?
                 AND status IN ('pending', 'approved')
                 AND start_date <= ?
                 AND end_date >= ?";

$checkStmt = $conn->prepare($checkQuery);
$checkStmt->bind_param(
    'iss',
    $trainerId,
    $endDate,
    $startDate
);
$checkStmt->execute();
$checkStmt->bind_result($existingCount);
$checkStmt->fetch();
$checkStmt->close();

if ($existingCount > 0) {
    $_SESSION['error'] = 'You already have a request covering part of these dates.';
    header('Location: ../reschedule.php');
    exit();
}

$insertQuery = "INSERT INTO trainer_reschedules
                (
                    trainer_id,
                    request_type,
                    start_date,
                    end_date,
                    new_start_time,
                    new_end_time,
                    status,
                    created_at
                )
                VALUES (?, ?, ?, ?, ?, ?, 'pending', NOW())";

$stmt = $conn->prepare($insertQuery);

$stmt->bind_param(
    'isssss',
    $trainerId,
    $requestType,
    $startDate,
    $endDate,
    $newStartTime,
    $newEndTime
);

$stmt->execute();

$stmt->close();
$conn->close();

$_SESSION['success'] = 'Your reschedule request has been submitted for approval.';

header('Location: ../reschedule.php');
exit();
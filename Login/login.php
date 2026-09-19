<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/config/security.php';

start_secure_session();

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    header('Location: index.php');
    exit();
}

$username = trim((string) ($_POST['username'] ?? ''));
$password = (string) ($_POST['password'] ?? '');

if ($username === '' || $password === '') {
    header('Location: index.php?error=Missing%20Fields');
    exit();
}

try {
    $conn = db_connect();

    /*
     * 1. Member authentication
     */
    $stmt = $conn->prepare(
        'SELECT id, FirstName, LastName, number, gender, email, dob, username, password_hash
         FROM users
         WHERE username = ?
         LIMIT 1'
    );

    $stmt->bind_param('s', $username);
    $stmt->execute();

    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    $stmt->close();

    if ($user !== null && !empty($user['password_hash'])) {
        if (password_verify($password, $user['password_hash'])) {
            $_SESSION = [];
            regenerate_session();

            $_SESSION['role'] = 'member';
            $_SESSION['username'] = $user['username'];
            $_SESSION['FirstName'] = $user['FirstName'];
            $_SESSION['LastName'] = $user['LastName'];
            $_SESSION['id'] = (int) $user['id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['number'] = $user['number'];

            $subscriptionStmt = $conn->prepare(
                'SELECT 1
                 FROM plan_bookings
                 WHERE user_id = ?
                   AND CURRENT_DATE BETWEEN start_date AND end_date
                 LIMIT 1'
            );

            $subscriptionStmt->bind_param('i', $user['id']);
            $subscriptionStmt->execute();

            $subscriptionResult = $subscriptionStmt->get_result();
            $hasSubscription = $subscriptionResult->num_rows > 0;

            $subscriptionStmt->close();

            if ($hasSubscription) {
                header('Location: ../Gym User Management/');
            } else {
                header('Location: ../plan_section/index.html');
            }

            exit();
        }
    }

    /*
     * 2. Trainer authentication
     *
     * Only password hashes are accepted.
     * Plaintext trainer passwords are intentionally no longer accepted.
     */
    $stmt = $conn->prepare(
        'SELECT trainer_id, FirstName, trainer_username, password_hash
         FROM trainers
         WHERE trainer_username = ?
         LIMIT 1'
    );

    $stmt->bind_param('s', $username);
    $stmt->execute();

    $result = $stmt->get_result();
    $trainer = $result->fetch_assoc();

    $stmt->close();

    if ($trainer !== null && !empty($trainer['password_hash'])) {
        if (password_verify($password, $trainer['password_hash'])) {
            $_SESSION = [];
            regenerate_session();

            $_SESSION['role'] = 'trainer';
            $_SESSION['trainer_id'] = (int) $trainer['trainer_id'];
            $_SESSION['trainer_name'] = $trainer['FirstName'];
            $_SESSION['trainer_username'] = $trainer['trainer_username'];

            header('Location: ../trainer/dashboard.php');
            exit();
        }
    }

    /*
     * 3. Admin authentication
     *
     * Only password hashes are accepted.
     */
    $stmt = $conn->prepare(
        'SELECT admin_id, name, password_hash
         FROM admin
         WHERE admin_id = ?
         LIMIT 1'
    );

    $stmt->bind_param('s', $username);
    $stmt->execute();

    $result = $stmt->get_result();
    $admin = $result->fetch_assoc();

    $stmt->close();

    if ($admin !== null && !empty($admin['password_hash'])) {
        if (password_verify($password, $admin['password_hash'])) {
            $_SESSION = [];
            regenerate_session();

            $_SESSION['role'] = 'admin';
            $_SESSION['admin_id'] = (int) $admin['admin_id'];
            $_SESSION['username'] = $admin['name'];

            header('Location: ../admin/index.php');
            exit();
        }
    }
} catch (Throwable $e) {
    error_log('Login error: ' . $e->getMessage());
}

header('Location: index.php?error=Incorrect%20Credentials');
exit();
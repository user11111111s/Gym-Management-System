<?php

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

function db_connect(): mysqli
{
    static $connection = null;

    if ($connection instanceof mysqli) {
        return $connection;
    }

    $connection = new mysqli(
        app_env_required('DB_HOST'),
        app_env_required('DB_USER'),
        app_env('DB_PASSWORD', '') ?? '',
        app_env_required('DB_NAME'),
        (int) app_env('DB_PORT', '3306')
    );

    $connection->set_charset('utf8mb4');

    return $connection;
}
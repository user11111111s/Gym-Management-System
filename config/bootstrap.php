<?php

declare(strict_types=1);

if (defined('GYMSHARK_BOOTSTRAPPED')) {
    return;
}

define('GYMSHARK_BOOTSTRAPPED', true);

$envFile = dirname(__DIR__) . DIRECTORY_SEPARATOR . '.env';

if (is_file($envFile) && is_readable($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    if ($lines !== false) {
        foreach ($lines as $line) {
            $line = trim($line);

            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }

            $separator = strpos($line, '=');

            if ($separator === false) {
                continue;
            }

            $name = trim(substr($line, 0, $separator));
            $value = trim(substr($line, $separator + 1));

            if ($name === '') {
                continue;
            }

            if (
                strlen($value) >= 2 &&
                (
                    ($value[0] === '"' && $value[strlen($value) - 1] === '"') ||
                    ($value[0] === "'" && $value[strlen($value) - 1] === "'")
                )
            ) {
                $value = substr($value, 1, -1);
            }

            if (getenv($name) === false) {
                putenv($name . '=' . $value);
            }
        }
    }
}

date_default_timezone_set(
    (string) (getenv('APP_TIMEZONE') ?: 'Asia/Kolkata')
);

function app_env(string $key, ?string $default = null): ?string
{
    $value = getenv($key);

    return $value === false ? $default : $value;
}

function app_env_required(string $key): string
{
    $value = app_env($key);

    if ($value === null || $value === '') {
        throw new RuntimeException(
            "Missing required environment variable: {$key}"
        );
    }

    return $value;
}
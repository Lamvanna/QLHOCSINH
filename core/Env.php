<?php
// core/Env.php

class Env {
    private static bool $loaded = false;

    /**
     * Load environment variables from a .env file
     */
    public static function load(string $filePath): void {
        if (self::$loaded) {
            return;
        }

        if (!file_exists($filePath)) {
            return;
        }

        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lines === false) {
            return;
        }

        foreach ($lines as $line) {
            $line = trim($line);

            // Skip comments and empty lines
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }

            // Parse key=value
            if (str_contains($line, '=')) {
                list($key, $value) = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);

                // Strip quotes if present
                if ((str_starts_with($value, '"') && str_ends_with($value, '"')) ||
                    (str_starts_with($value, "'") && str_ends_with($value, "'"))) {
                    $value = substr($value, 1, -1);
                }

                // Parse boolean/null values
                $lower = strtolower($value);
                if ($lower === 'true') {
                    $parsedValue = true;
                } elseif ($lower === 'false') {
                    $parsedValue = false;
                } elseif ($lower === 'null') {
                    $parsedValue = null;
                } else {
                    $parsedValue = $value;
                }

                if (!array_key_exists($key, $_SERVER) && !array_key_exists($key, $_ENV)) {
                    putenv("$key=" . ($parsedValue === true ? 'true' : ($parsedValue === false ? 'false' : (string)$parsedValue)));
                    $_ENV[$key] = $parsedValue;
                    $_SERVER[$key] = $parsedValue;
                }
            }
        }

        self::$loaded = true;
    }

    /**
     * Get environment variable value
     */
    public static function get(string $key, mixed $default = null): mixed {
        if (!self::$loaded) {
            self::load(dirname(__DIR__) . '/.env');
        }

        if (array_key_exists($key, $_ENV)) {
            return $_ENV[$key];
        }

        if (array_key_exists($key, $_SERVER)) {
            return $_SERVER[$key];
        }

        $val = getenv($key);
        if ($val !== false) {
            $lower = strtolower($val);
            if ($lower === 'true') return true;
            if ($lower === 'false') return false;
            if ($lower === 'null') return null;
            return $val;
        }

        return $default;
    }
}

// Global helper function env()
if (!function_exists('env')) {
    function env(string $key, mixed $default = null): mixed {
        return Env::get($key, $default);
    }
}

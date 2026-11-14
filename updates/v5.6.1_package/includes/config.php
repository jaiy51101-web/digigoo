<?php
/**
 * Digiws - Main Configuration
 * Loads environment variables and defines constants
 */

// Prevent direct access
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}

// Load the environment file. New installations keep it at the application
// root so it can be protected in one place; the includes/.env fallback keeps
// previously installed copies working without a manual migration.
$envFiles = [BASE_PATH . '/.env', __DIR__ . '/.env'];
foreach ($envFiles as $envFile) {
if (is_readable($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line) || $line[0] === '#') continue;
        if (strpos($line, '=') === false) continue;
        list($key, $value) = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);
        if (!array_key_exists($key, $_ENV)) {
            $_ENV[$key] = $value;
            putenv("$key=$value");
        }
    }
}
}

// Helper to get env values
function env($key, $default = '') {
    if (array_key_exists($key, $_ENV)) {
        return $_ENV[$key];
    }
    $val = getenv($key);
    return $val !== false ? $val : $default;
}

// Database Configuration
define('DB_HOST', env('DB_HOST', 'localhost'));
define('DB_PORT', env('DB_PORT', '3306'));
define('DB_NAME', env('DB_NAME', ''));
define('DB_USER', env('DB_USER', ''));
define('DB_PASS', env('DB_PASS', ''));

// Application Configuration
define('APP_NAME', env('APP_NAME', 'Digiws'));

// Determine APP_URL dynamically for local development environments
$detectedAppUrl = rtrim(env('APP_URL', 'https://digiws.com'), '/');
$configuredBasePath = trim(env('APP_BASE_PATH', '/'));
if ($configuredBasePath === '') $configuredBasePath = '/';
if ($configuredBasePath !== '/') {
    $configuredBasePath = '/' . trim($configuredBasePath, '/');
}
if (isset($_SERVER['HTTP_HOST'])) {
    $host = strtolower(trim((string) $_SERVER['HTTP_HOST']));
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    // Only derive a URL from the Host header for explicitly local hosts.
    // Production always uses the configured APP_URL, preventing Host header
    // injection into generated links and redirects.
    $isLocalHost = preg_match('/^(?:localhost|127\.0\.0\.1|192\.168\.\d{1,3}\.\d{1,3}|10\.\d{1,3}\.\d{1,3}\.\d{1,3}|172\.(?:1[6-9]|2\d|3[0-1])\.\d{1,3}\.\d{1,3})(?::\d{1,5})?$/', $host) === 1;
    if ($isLocalHost) {
        $docRoot = str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']);
        $basePath = str_replace('\\', '/', BASE_PATH);
        $relativeFolder = '';
        if (strncasecmp($basePath, $docRoot, strlen($docRoot)) === 0) {
            $relativeFolder = substr($basePath, strlen($docRoot));
        }
        $detectedAppUrl = $protocol . $host . '/' . ltrim(str_replace('\\', '/', $relativeFolder), '/');
        $detectedAppUrl = rtrim($detectedAppUrl, '/');
    } elseif ($configuredBasePath !== '/' && !str_ends_with($detectedAppUrl, $configuredBasePath)) {
        $detectedAppUrl .= $configuredBasePath;
    }
} elseif ($configuredBasePath !== '/' && !str_ends_with($detectedAppUrl, $configuredBasePath)) {
    $detectedAppUrl .= $configuredBasePath;
}
define('APP_URL', $detectedAppUrl);

define('APP_ENV', env('APP_ENV', 'production'));
define('APP_DEBUG', env('APP_DEBUG', 'false') === 'true');
define('APP_TIMEZONE', env('APP_TIMEZONE', 'Asia/Kolkata'));

// Path constants
define('ASSETS_URL', APP_URL . '/assets');
define('UPLOADS_URL', APP_URL . '/uploads');
define('UPLOADS_PATH', BASE_PATH . '/uploads');
define('VIEWS_PATH', BASE_PATH . '/views');
define('TEMPLATES_PATH', BASE_PATH . '/templates');
define('INCLUDES_PATH', BASE_PATH . '/includes');

// Payment Gateway keys
define('RAZORPAY_KEY_ID', env('RAZORPAY_KEY_ID', ''));
define('RAZORPAY_KEY_SECRET', env('RAZORPAY_KEY_SECRET', ''));
define('STRIPE_PUBLISHABLE_KEY', env('STRIPE_PUBLISHABLE_KEY', ''));
define('STRIPE_SECRET_KEY', env('STRIPE_SECRET_KEY', ''));
define('PAYPAL_CLIENT_ID', env('PAYPAL_CLIENT_ID', ''));
define('PAYPAL_SECRET', env('PAYPAL_SECRET', ''));
define('PAYPAL_MODE', env('PAYPAL_MODE', 'sandbox'));

// SMTP
define('SMTP_HOST', env('SMTP_HOST', ''));
define('SMTP_PORT', env('SMTP_PORT', '587'));
define('SMTP_USERNAME', env('SMTP_USERNAME', ''));
define('SMTP_PASSWORD', env('SMTP_PASSWORD', ''));
define('SMTP_ENCRYPTION', env('SMTP_ENCRYPTION', 'tls'));
define('SMTP_FROM_EMAIL', env('SMTP_FROM_EMAIL', 'noreply@digiws.com'));
define('SMTP_FROM_NAME', env('SMTP_FROM_NAME', 'Digiws'));

// Social Login
define('GOOGLE_CLIENT_ID', env('GOOGLE_CLIENT_ID', ''));
define('GOOGLE_CLIENT_SECRET', env('GOOGLE_CLIENT_SECRET', ''));
define('FACEBOOK_APP_ID', env('FACEBOOK_APP_ID', ''));
define('FACEBOOK_APP_SECRET', env('FACEBOOK_APP_SECRET', ''));

// WhatsApp
define('WHATSAPP_NUMBER', env('WHATSAPP_NUMBER', ''));
define('WHATSAPP_MESSAGE', env('WHATSAPP_MESSAGE', 'Hello! I need help.'));

// Set timezone
date_default_timezone_set(APP_TIMEZONE);

// Error reporting based on environment
if (APP_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Session is started by Security::boot() with hardened settings.
// Do NOT call session_start() here — Security.php handles it.
// Security::boot() is the single session entry point. Starting a fallback
// session here would skip its strict-mode and secure-cookie settings.

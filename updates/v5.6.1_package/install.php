<?php
/**
 * Digiws - Graphical Web Installer Wizard (v5.0.0)
 * Facilitates license verification, database configuration, schema installation, and admin account setup.
 *
 * Anti-Copy Notice: This software is licensed. Unauthorized redistribution or
 * cloning of this website is strictly prohibited and protected by DigiWS license enforcement.
 */

// ──────────────────────────────────────────────────────────────────────────────
// ANTI-COPY / CODE-PROTECTION HEADERS
// ──────────────────────────────────────────────────────────────────────────────
header('X-Powered-By: DigiWS-Licensed');
header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');

// ──────────────────────────────────────────────────────────────────────────────
// SDK INTEGRATION — DigiWS License Server
// ──────────────────────────────────────────────────────────────────────────────
define('DIGIWS_LICENSE_SERVER',  getenv('LICENSE_SERVER') ?: 'https://license.digiws.com');
define('DIGIWS_PRODUCT_ID',      'PRD-ISV5JQOF7S');
define('DIGIWS_API_KEY',         'sk_Q7pl1yxWAxMflaux5jt1lAEd4fVmwKdXY0p8UOSG');
define('DIGIWS_AES_KEY',         '3683ZHHi0xV8HZHW1Nnu8QDgoZC2o4LM');
define('DIGIWS_CURRENT_VERSION', '5.6');

// Load SDK if available
$sdkPath = __DIR__ . '/sdk/DigiWSClientSDK.php';
$sdkAvailable = file_exists($sdkPath);
if ($sdkAvailable) {
    require_once $sdkPath;
}

// ──────────────────────────────────────────────────────────────────────────────
// BLOCK RE-INSTALLATION IF ALREADY INSTALLED
// ──────────────────────────────────────────────────────────────────────────────
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    $lines   = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $envData = [];
    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line) || $line[0] === '#') continue;
        if (strpos($line, '=') === false) continue;
        list($k, $v) = explode('=', $line, 2);
        $envData[trim($k)] = trim($v);
    }

    if (!empty($envData['DB_NAME'])) {
        die('<!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Already Installed - Digiws</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
            <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
            <style>
                body { background: #0f172a; color: #f8fafc; font-family: "Plus Jakarta Sans", sans-serif; display: flex; align-items: center; justify-content: center; height: 100vh; }
                .card { max-width: 500px; padding: 40px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.5); border: 1px solid #334155; background: #1e293b; }
            </style>
        </head>
        <body>
            <div class="card text-center">
                <div class="mb-3 text-warning"><svg width="48" height="48" fill="currentColor" class="bi bi-exclamation-triangle" viewBox="0 0 16 16"><path d="M7.938 2.016A.13.13 0 0 1 8.002 2a.13.13 0 0 1 .063.016.15.15 0 0 1 .054.057l6.857 11.667c.036.06.035.124.002.183a.2.2 0 0 1-.054.06.1.1 0 0 1-.066.017H1.146a.1.1 0 0 1-.066-.017.2.2 0 0 1-.054-.06.18.18 0 0 1 .002-.183L7.884 2.073a.15.15 0 0 1 .054-.057zm-1.02 5.07h1.6v3.2h-1.6zm0 4.8h1.6v1.6h-1.6z"/></svg></div>
                <h4 class="fw-bold mb-2 text-white">Digiws is Already Installed</h4>
                <p class="text-secondary small mb-4">To run the installation wizard again, please delete the <code>.env</code> file from your server File Manager first.</p>
                <a href="index.php" class="btn btn-primary rounded-pill py-2.5 px-4" style="background:#7c3aed; border:none; font-weight:600;">Go to Homepage</a>
            </div>
        </body>
        </html>');
    }
}

// ──────────────────────────────────────────────────────────────────────────────
// SERVER REQUIREMENTS CHECK
// ──────────────────────────────────────────────────────────────────────────────
$phpVersion        = phpversion();
$isPhpOk           = version_compare($phpVersion, '8.1.0', '>=');
$isPdoOk           = extension_loaded('pdo_mysql');
$isGdOk            = extension_loaded('gd');
$isCurlOk          = function_exists('curl_init');
$isUploadWritable  = is_writable(__DIR__ . '/uploads')  || (!file_exists(__DIR__ . '/uploads')  && is_writable(__DIR__));
$isIncludesWritable= is_writable(__DIR__ . '/includes') || (!file_exists(__DIR__ . '/includes') && is_writable(__DIR__));
$allOk             = $isPhpOk && $isPdoOk && $isGdOk;

// Auto-detect base path & url
$detectedUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . str_replace('/install.php', '', $_SERVER['SCRIPT_NAME']);
$detectedUrl = rtrim($detectedUrl, '/');
$scriptDir   = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
$detectedBasePath = $scriptDir === '/' ? '/' : rtrim($scriptDir, '/');

// ──────────────────────────────────────────────────────────────────────────────
// SESSION + STEP MANAGEMENT
// ──────────────────────────────────────────────────────────────────────────────
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', '1');
    ini_set('session.use_trans_sid', '0');
    session_start();
}

$step    = (int)($_GET['step'] ?? 1);
$error   = '';
$success = false;

// ──────────────────────────────────────────────────────────────────────────────
// STEP 1: LICENSE KEY VERIFICATION (POST handler)
// ──────────────────────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $step === 1) {
    $licenseKey = trim($_POST['license_key'] ?? '');

    if (empty($licenseKey)) {
        $error = 'Please enter your license key to continue.';
    } else {
        $verifyResult = ['valid' => false, 'message' => 'SDK not found. Cannot verify license.'];

        if ($sdkAvailable) {
            $sdk          = new DigiWSClientSDK(
                LICENSE_SERVER  : DIGIWS_LICENSE_SERVER,
                PRODUCT_ID      : DIGIWS_PRODUCT_ID,
                API_KEY         : DIGIWS_API_KEY,
                CURRENT_VERSION : DIGIWS_CURRENT_VERSION
            );
            $verifyResult = $sdk->checkLicense($licenseKey);
        }

        if ($verifyResult['valid']) {
            $_SESSION['license_key']      = $licenseKey;
            $_SESSION['license_verified'] = true;
            header('Location: install.php?step=2');
            exit;
        } else {
            $error = '🔒 License Verification Failed: ' . ($verifyResult['message'] ?? 'Invalid license key.');
        }
    }
}

// ──────────────────────────────────────────────────────────────────────────────
// GUARD: Steps 2+ require license verification
// ──────────────────────────────────────────────────────────────────────────────
if ($step >= 2 && empty($_SESSION['license_verified'])) {
    header('Location: install.php?step=1');
    exit;
}

// ──────────────────────────────────────────────────────────────────────────────
// STEP 3: DATABASE CONNECTION TEST (POST handler)
// ──────────────────────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $step === 3) {
    $dbHost = trim($_POST['db_host'] ?? 'localhost');
    $dbPort = trim($_POST['db_port'] ?? '3306');
    $dbName = trim($_POST['db_name'] ?? '');
    $dbUser = trim($_POST['db_user'] ?? '');
    $dbPass = trim($_POST['db_pass'] ?? '');

    try {
        $pdo = new PDO("mysql:host={$dbHost};port={$dbPort};charset=utf8mb4", $dbUser, $dbPass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_TIMEOUT => 5
        ]);

        $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $pdo->exec("USE `{$dbName}`");

        $_SESSION['db_host'] = $dbHost;
        $_SESSION['db_port'] = $dbPort;
        $_SESSION['db_name'] = $dbName;
        $_SESSION['db_user'] = $dbUser;
        $_SESSION['db_pass'] = $dbPass;

        header('Location: install.php?step=4');
        exit;
    } catch (PDOException $e) {
        $error = 'Database Connection Failed: ' . $e->getMessage();
    }
}

// ──────────────────────────────────────────────────────────────────────────────
// STEP 4: FULL INSTALLATION (POST handler)
// ──────────────────────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $step === 4) {
    $dbHost = $_SESSION['db_host'] ?? 'localhost';
    $dbPort = $_SESSION['db_port'] ?? '3306';
    $dbName = $_SESSION['db_name'] ?? 'digiws';
    $dbUser = $_SESSION['db_user'] ?? 'root';
    $dbPass = $_SESSION['db_pass'] ?? '';

    if (empty($dbHost) || empty($dbName)) {
        header('Location: install.php?step=3');
        exit;
    }

    $siteTitle  = trim($_POST['site_title']  ?? 'DigiWS');
    $siteUrl    = trim($_POST['site_url']    ?? $detectedUrl);
    $basePath   = trim($_POST['base_path']   ?? $detectedBasePath);
    $adminEmail = trim($_POST['admin_email'] ?? '');
    $adminPass  = trim($_POST['admin_pass']  ?? '');

    if (empty($adminEmail) || empty($adminPass)) {
        $error = 'Please fill in admin credentials.';
    } else {
        try {
            $pdo = new PDO("mysql:host={$dbHost};port={$dbPort};dbname={$dbName};charset=utf8mb4", $dbUser, $dbPass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);

            $runSqlFile = function($filePath) use ($pdo) {
                if (!file_exists($filePath)) return;
                $sql = (string)file_get_contents($filePath);
                $statements = []; $buffer = ''; $quote = null; $length = strlen($sql);
                for ($i = 0; $i < $length; $i++) {
                    $char = $sql[$i];
                    if ($quote !== null) {
                        $buffer .= $char;
                        if ($char === '\\' && $i + 1 < $length) { $buffer .= $sql[++$i]; continue; }
                        if ($char === $quote) $quote = null;
                        continue;
                    }
                    if ($char === "'" || $char === '"' || $char === '`') { $quote = $char; $buffer .= $char; continue; }
                    if ($char === ';') { if (trim($buffer) !== '') $statements[] = $buffer; $buffer = ''; continue; }
                    $buffer .= $char;
                }
                if (trim($buffer) !== '') $statements[] = $buffer;

                foreach ($statements as $q) {
                    if (trim(preg_replace('/(?:^|\n)\s*(?:--[^\n]*|#[^\n]*)/', '', $q)) === '') continue;
                    try {
                        $pdo->exec($q);
                    } catch (PDOException $ex) {
                        $errCode = $ex->errorInfo[1] ?? 0;
                        if ($ex->getCode() !== '42S01' && $errCode !== 1050 && $errCode !== 1060) {
                            throw $ex;
                        }
                    }
                }
            };

            if (file_exists(__DIR__ . '/database/schema.sql')) $runSqlFile(__DIR__ . '/database/schema.sql');
            if (file_exists(__DIR__ . '/database/blog_migration.sql')) $runSqlFile(__DIR__ . '/database/blog_migration.sql');
            if (file_exists(__DIR__ . '/database/blog_advanced_blocks.sql')) $runSqlFile(__DIR__ . '/database/blog_advanced_blocks.sql');
            if (file_exists(__DIR__ . '/database/managed_feedback.sql')) $runSqlFile(__DIR__ . '/database/managed_feedback.sql');

            // Upgrade marketing migration
            if (file_exists(__DIR__ . '/run_marketing_migration.php')) {
                define('DIGIWS_INSTALLER', true);
                require_once __DIR__ . '/run_marketing_migration.php';
            }

            // Create/update admin account
            $hashedPass = password_hash($adminPass, PASSWORD_DEFAULT);
            $stmt       = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$adminEmail]);
            $user = $stmt->fetch();

            if ($user) {
                $stmt = $pdo->prepare("UPDATE users SET password = ?, role = 'admin', status = 'active', email_verified = 1 WHERE id = ?");
                $stmt->execute([$hashedPass, $user['id']]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role, status, email_verified) VALUES ('Admin', ?, ?, 'admin', 'active', 1)");
                $stmt->execute([$adminEmail, $hashedPass]);
            }

            $licenseKey = $_SESSION['license_key'] ?? '';
            $envLines = [
                '# Generated by DigiWS installer 5.6',
                'APP_VERSION=' . DIGIWS_CURRENT_VERSION,
                'PRODUCT_ID=' . DIGIWS_PRODUCT_ID,
                'API_KEY=' . DIGIWS_API_KEY,
                'AES_KEY=' . DIGIWS_AES_KEY,
                'LICENSE_KEY=' . $licenseKey,
                'LICENSE_SERVER=' . DIGIWS_LICENSE_SERVER,
                'STORAGE_REPOSITORY_FOLDER=products/premium-digital-product-selling-website',
                'DIGIWS_LICENSE_KEY=' . $licenseKey,
                'DIGIWS_PRODUCT_ID=' . DIGIWS_PRODUCT_ID,
                'DB_HOST=' . $dbHost,
                'DB_PORT=' . $dbPort,
                'DB_NAME=' . $dbName,
                'DB_USER=' . $dbUser,
                'DB_PASS=' . $dbPass,
                'APP_NAME=' . $siteTitle,
                'APP_URL=' . $siteUrl,
                'APP_BASE_PATH=' . $basePath,
                'APP_ENV=production',
                'APP_DEBUG=false',
                'APP_TIMEZONE=Asia/Kolkata',
                'SMTP_HOST=',
                'SMTP_PORT=587',
                'SMTP_USERNAME=',
                'SMTP_PASSWORD=',
                'SMTP_ENCRYPTION=tls',
                'SMTP_FROM_EMAIL=' . $adminEmail,
                'SMTP_FROM_NAME=' . $siteTitle,
            ];
            file_put_contents($envFile, implode(PHP_EOL, $envLines) . PHP_EOL);

            // Bind System Update Center settings in database
            try {
                $dbSettings = [
                    'license_key' => $licenseKey,
                    'product_id' => DIGIWS_PRODUCT_ID,
                    'api_key' => DIGIWS_API_KEY,
                    'license_server' => DIGIWS_LICENSE_SERVER,
                    'app_version' => DIGIWS_CURRENT_VERSION,
                    'site_name' => $siteTitle,
                    'site_email' => $adminEmail
                ];
                foreach ($dbSettings as $sKey => $sVal) {
                    $stmt = $pdo->prepare("INSERT INTO settings (`key`, `value`, `group_name`) VALUES (?, ?, 'system') ON DUPLICATE KEY UPDATE `value` = ?");
                    $stmt->execute([$sKey, $sVal, $sVal]);
                }
            } catch (Throwable $se) {
                // Ignore if database schema varies
            }

            // Initialize update history log
            $tmpDir = __DIR__ . '/tmp';
            if (!is_dir($tmpDir)) {
                @mkdir($tmpDir, 0755, true);
            }
            $historyFile = $tmpDir . '/update_history.json';
            $initialHistory = [
                [
                    'timestamp' => date('Y-m-d H:i:s'),
                    'action'    => 'Initial Software Installation',
                    'status'    => 'Success',
                    'version'   => DIGIWS_CURRENT_VERSION,
                    'message'   => 'System installed & bound with Product ID ' . DIGIWS_PRODUCT_ID . ' and License Server.'
                ]
            ];
            file_put_contents($historyFile, json_encode($initialHistory, JSON_PRETTY_PRINT));

            session_destroy();
            $success = true;
            $step    = 5;

        } catch (Exception $e) {
            $error = 'Installation Failed: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DigiWS Setup Wizard (PRD-ISV5JQOF7S)</title>
    <meta name="robots" content="noindex, nofollow">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #7c3aed;
            --primary-dark: #6d28d9;
            --primary-soft: rgba(124,58,237,0.08);
            --bg: #0f172a;
            --card-bg: #1e293b;
            --text: #f8fafc;
            --border: #334155;
            --success: #16a34a;
            --danger: #dc2626;
        }
        * { box-sizing: border-box; }
        body {
            background: var(--bg);
            font-family: 'Inter', sans-serif;
            color: var(--text);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px 15px;
        }
        input, textarea, select, code { -webkit-user-select: text; user-select: text; }

        .install-card {
            background: var(--card-bg);
            border-radius: 24px;
            box-shadow: 0 24px 60px rgba(0,0,0,0.5);
            border: 1px solid var(--border);
            max-width: 680px;
            width: 100%;
            overflow: hidden;
        }
        .install-header {
            background: linear-gradient(135deg, #7c3aed 0%, #2563eb 100%);
            padding: 36px 40px;
            color: #ffffff;
            text-align: center;
            position: relative;
        }
        .install-header h3 {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 800;
            font-size: 1.6rem;
            margin: 0;
        }
        .install-header .badge-version {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            background: rgba(255,255,255,0.2);
            border-radius: 30px;
            padding: 4px 14px;
            font-size: 0.78rem;
            font-weight: 600;
            color: #e0d9ff;
            margin-top: 8px;
        }
        .install-body {
            padding: 40px;
        }

        .step-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 36px;
            position: relative;
        }
        .step-row::before {
            content: '';
            position: absolute;
            top: 15px;
            left: 10%;
            right: 10%;
            height: 2px;
            background: var(--border);
            z-index: 1;
        }
        .step-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
            position: relative;
            z-index: 2;
            flex: 1;
        }
        .step-dot {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #0f172a;
            border: 2px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.82rem;
            color: #94a3b8;
            transition: all 0.3s;
        }
        .step-dot.active {
            border-color: var(--primary);
            background: var(--primary);
            color: #ffffff;
            box-shadow: 0 0 0 4px var(--primary-soft);
        }
        .step-dot.completed {
            border-color: var(--success);
            background: var(--success);
            color: #ffffff;
        }
        .step-label {
            font-size: 0.7rem;
            font-weight: 600;
            color: #94a3b8;
            text-align: center;
        }
        .step-label.active { color: #38bdf8; }
        .step-label.completed { color: var(--success); }

        .requirement-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 13px 16px;
            background: #0f172a;
            border-radius: 12px;
            margin-bottom: 10px;
            border: 1px solid var(--border);
        }
        .btn-install {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border: none;
            color: #ffffff;
            font-weight: 700;
            padding: 11px 28px;
            border-radius: 30px;
            transition: all 0.3s;
        }
        .btn-install:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(124,58,237,0.35);
            color: #fff;
        }
        .form-label { font-weight: 600; font-size: 0.88rem; color: #cbd5e1; }
        .form-control, .form-select {
            background: #0f172a;
            color: #fff;
            border: 1.5px solid var(--border);
            padding: 11px 15px;
            border-radius: 12px;
            font-size: 0.92rem;
        }
        .form-control:focus {
            background: #0f172a;
            color: #fff;
            border-color: #38bdf8;
            box-shadow: 0 0 0 3px rgba(56,189,248,0.25);
        }
        .license-box {
            background: rgba(124,58,237,0.1);
            border: 1.5px solid rgba(124,58,237,0.3);
            border-radius: 16px;
            padding: 24px 28px;
            margin-bottom: 28px;
        }
        .license-box h6 { font-weight: 700; color: #c084fc; margin-bottom: 4px; }
        code { background: #0f172a; color: #38bdf8; padding: 2px 6px; border-radius: 4px; }
    </style>
</head>
<body>

<div class="install-card">
    <div class="install-header">
        <h3><i class="bi bi-shield-lock-fill me-2"></i>DigiWS Setup Wizard</h3>
        <div class="badge-version"><i class="bi bi-patch-check-fill me-1"></i>Licensed Edition · v5.0.0</div>
    </div>

    <div class="install-body">
        <div class="step-row">
            <?php
            $steps = [
                1 => 'License',
                2 => 'Requirements',
                3 => 'Database',
                4 => 'Configure',
                5 => 'Done',
            ];
            foreach ($steps as $n => $label):
                $dotClass   = $step === $n ? 'active' : ($step > $n ? 'completed' : '');
                $labelClass = $step === $n ? 'active' : ($step > $n ? 'completed' : '');
            ?>
            <div class="step-item">
                <div class="step-dot <?= $dotClass ?>">
                    <?= $step > $n ? '<i class="bi bi-check-lg"></i>' : $n ?>
                </div>
                <span class="step-label <?= $labelClass ?>"><?= $label ?></span>
            </div>
            <?php endforeach; ?>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert d-flex align-items-center gap-2 border-0 rounded-3 mb-4" style="background:#7f1d1d; color:#fecaca;">
                <i class="bi bi-exclamation-octagon-fill fs-5"></i>
                <div class="small fw-semibold"><?= htmlspecialchars($error) ?></div>
            </div>
        <?php endif; ?>

        <!-- STEP 1: LICENSE VERIFICATION -->
        <?php if ($step === 1): ?>
            <h5 class="fw-bold mb-1 text-white">Software License Authorization</h5>
            <p class="text-secondary small mb-4">Please enter your purchase license key to unlock the installation wizard.</p>

            <div class="license-box">
                <h6><i class="bi bi-shield-check me-2"></i>Product License Verification</h6>
                <p class="text-secondary small mb-0">Enter the official license key received with your purchase order. A valid license key is required to activate and install this software.</p>
            </div>

            <form method="POST" id="licenseForm">
                <div class="mb-4">
                    <label class="form-label">Purchase License Key <span class="text-danger">*</span></label>
                    <input
                        type="text"
                        name="license_key"
                        class="form-control"
                        placeholder="e.g. XXXX-XXXX-XXXX-XXXX"
                        autocomplete="off"
                        required
                        value="<?= htmlspecialchars($_POST['license_key'] ?? 'MYUA-HOWM-L73Z-1TVH') ?>"
                    >
                </div>

                <div class="d-flex justify-content-between align-items-center mt-4">
                    <span class="text-secondary small">DigiWS Platform v5.6</span>
                    <button type="submit" class="btn btn-install">
                        <i class="bi bi-shield-lock me-2"></i>Verify License & Continue
                    </button>
                </div>
            </form>

        <!-- STEP 2: SERVER CHECKLIST -->
        <?php elseif ($step === 2): ?>
            <h5 class="fw-bold mb-1 text-white">Requirements &amp; Server Checklist</h5>
            <p class="text-secondary small mb-4">Verifying server hosting compatibility.</p>

            <?php
            $reqs = [
                ['PHP Version (≥ 8.1.0)', 'Current: ' . $phpVersion, $isPhpOk, 'Upgrade Required'],
                ['PDO MySQL Extension',    'Required for database',         $isPdoOk,  'Disabled'],
                ['GD Library Extension',   'Required for image processing',  $isGdOk,   'Disabled'],
                ['cURL Extension',         'Required for license & updates', $isCurlOk, 'Disabled'],
            ];
            foreach ($reqs as [$name, $desc, $ok, $failLabel]):
            ?>
            <div class="requirement-row">
                <div>
                    <span class="fw-semibold small text-white"><?= $name ?></span>
                    <div class="text-secondary" style="font-size:0.78rem;"><?= $desc ?></div>
                </div>
                <div>
                    <?php if ($ok): ?>
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded-pill"><i class="bi bi-check me-1"></i>OK</span>
                    <?php else: ?>
                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 rounded-pill"><i class="bi bi-x me-1"></i><?= $failLabel ?></span>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>

            <div class="d-flex justify-content-between mt-4">
                <a href="?step=1" class="btn btn-outline-secondary rounded-pill px-4 py-2"><i class="bi bi-arrow-left me-2"></i>Back</a>
                <a href="?step=3" class="btn btn-install <?= !$allOk ? 'disabled' : '' ?>">Configure Database<i class="bi bi-arrow-right ms-2"></i></a>
            </div>

        <!-- STEP 3: DATABASE CONNECTION -->
        <?php elseif ($step === 3): ?>
            <h5 class="fw-bold mb-1 text-white">Database Connection</h5>
            <p class="text-secondary small mb-4">Provide MySQL database credentials.</p>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Database Host</label>
                    <input type="text" name="db_host" class="form-control" value="localhost" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Database Port</label>
                    <input type="text" name="db_port" class="form-control" value="3306" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Database Name</label>
                    <input type="text" name="db_name" class="form-control" value="digiws" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Database User</label>
                    <input type="text" name="db_user" class="form-control" value="root" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Database Password</label>
                    <input type="password" name="db_pass" class="form-control" value="">
                </div>
                <div class="d-flex justify-content-between mt-4">
                    <a href="?step=2" class="btn btn-outline-secondary rounded-pill px-4 py-2"><i class="bi bi-arrow-left me-2"></i>Back</a>
                    <button type="submit" class="btn btn-install">Test &amp; Proceed<i class="bi bi-arrow-right ms-2"></i></button>
                </div>
            </form>

        <!-- STEP 4: SITE SETUP -->
        <?php elseif ($step === 4): ?>
            <h5 class="fw-bold mb-1 text-white">Site &amp; Administrator Setup</h5>
            <p class="text-secondary small mb-4">Configure website details and admin credentials.</p>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Website Name</label>
                    <input type="text" name="site_title" class="form-control" value="DigiWS" required>
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-md-8">
                        <label class="form-label">Application URL</label>
                        <input type="url" name="site_url" class="form-control" value="<?= htmlspecialchars($detectedUrl) ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Base Path</label>
                        <input type="text" name="base_path" class="form-control" value="<?= htmlspecialchars($detectedBasePath) ?>" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Admin Email</label>
                    <input type="email" name="admin_email" class="form-control" value="admin@example.com" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Admin Password (minimum 8 characters)</label>
                    <input type="password" name="admin_pass" class="form-control" value="admin12345" minlength="8" required>
                </div>
                <div class="d-flex justify-content-between mt-4">
                    <a href="?step=3" class="btn btn-outline-secondary rounded-pill px-4 py-2"><i class="bi bi-arrow-left me-2"></i>Back</a>
                    <button type="submit" class="btn btn-install"><i class="bi bi-rocket-takeoff me-2"></i>Complete Installation</button>
                </div>
            </form>

        <!-- STEP 5: SUCCESS -->
        <?php elseif ($step === 5): ?>
            <div class="text-center">
                <div class="mb-3" style="font-size:4rem; color:#16a34a;">
                    <i class="bi bi-patch-check-fill"></i>
                </div>
                <h4 class="fw-bold mb-2 text-white">Installation Complete! 🎉</h4>
                <p class="text-secondary small mb-4">DigiWS Platform is fully installed, licensed, and ready to use.</p>

                <div class="card bg-dark border-secondary p-3 rounded-4 mb-4 text-start">
                    <span class="fw-bold small mb-2 d-block text-secondary"><i class="bi bi-shield-lock me-1"></i>Admin Access Credentials:</span>
                    <div class="small mb-1"><strong>Admin URL:</strong> <a href="admin/login" class="text-decoration-none text-info">/admin/login</a></div>
                    <div class="small mb-1"><strong>Admin Email:</strong> <code>admin@example.com</code></div>
                    <div class="small"><strong>Admin Password:</strong> <code>admin12345</code></div>
                </div>

                <div class="d-flex gap-3 justify-content-center">
                    <a href="index.php" class="btn btn-outline-secondary rounded-pill py-2 px-4"><i class="bi bi-globe me-2"></i>View Website</a>
                    <a href="admin/login" class="btn btn-install rounded-pill py-2 px-4"><i class="bi bi-speedometer2 me-2"></i>Admin Panel</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

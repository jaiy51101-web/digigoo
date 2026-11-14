<?php
/**
 * DigiWS - Admin System Update Center Controller (v5.6)
 * Product ID: PRD-ISV5JQOF7S
 */
declare(strict_types=1);

require_once BASE_PATH . '/sdk/DigiWSClientSDK.php';

class AdminUpdateController {
    private DigiWSClientSDK $sdk;

    public function __construct() {
        requireAdmin();
        $this->sdk = new DigiWSClientSDK(
            LICENSE_SERVER  : env('LICENSE_SERVER', 'https://license.digiws.com'),
            PRODUCT_ID      : env('PRODUCT_ID', 'PRD-ISV5JQOF7S'),
            API_KEY         : env('API_KEY', 'sk_Q7pl1yxWAxMflaux5jt1lAEd4fVmwKdXY0p8UOSG'),
            CURRENT_VERSION : env('APP_VERSION', '5.6')
        );
    }

    /**
     * Display Update Center Dashboard
     */
    public function index(): void {
        $licenseKey = env('LICENSE_KEY', 'MYUA-HOWM-L73Z-1TVH');
        $licenseInfo = $this->sdk->checkLicense($licenseKey);
        $updateInfo = $this->sdk->checkForUpdates($licenseKey);

        $currentVersion = env('APP_VERSION', '5.6');
        $productId = env('PRODUCT_ID', 'PRD-ISV5JQOF7S');

        // System Diagnostics
        $diagnostics = [
            'php_version'      => PHP_VERSION,
            'curl_enabled'     => extension_loaded('curl'),
            'zip_enabled'      => class_exists('ZipArchive'),
            'writable'         => is_writable(BASE_PATH),
            'server_reachable' => !empty($licenseInfo) && isset($licenseInfo['valid'])
        ];

        // Update Activity History
        $logFile = BASE_PATH . '/tmp/update_history.json';
        $updateHistory = [];
        if (file_exists($logFile)) {
            $updateHistory = json_decode((string)file_get_contents($logFile), true) ?: [];
        }

        $pageTitle = 'System Update Center - Admin';
        $adminPage = 'system_updates';
        $contentView = VIEWS_PATH . '/admin/updates/index.php';
        include VIEWS_PATH . '/admin/layout.php';
    }

    /**
     * Check for remote updates from DigiWS License Server
     */
    public function check(): void {
        $licenseKey = env('LICENSE_KEY', 'MYUA-HOWM-L73Z-1TVH');
        $updateInfo = $this->sdk->checkForUpdates($licenseKey);

        if (!empty($updateInfo['has_update'])) {
            $msg = 'New Update Available: Version ' . h($updateInfo['latest_version']);
            if (!empty($updateInfo['zip_file_attached'])) {
                $msg .= ' (ZIP Package file ready on server!)';
            } else {
                $msg .= ' (Notice: ZIP package file has not been uploaded to server yet by admin).';
            }
            setFlash('info', $msg);
        } else {
            setFlash('success', 'Your system is running the latest version (' . env('APP_VERSION', '5.6') . ').');
        }

        redirect(url('admin/updates'));
    }

    /**
     * Save/Update License Key in .env file
     */
    public function saveLicense(): void {
        if (!verifyCsrf()) {
            setFlash('danger', 'Invalid security token.');
            redirect(url('admin/updates'));
        }

        $newKey = trim($_POST['license_key'] ?? '');
        if (empty($newKey)) {
            setFlash('danger', 'License key cannot be empty.');
            redirect(url('admin/updates'));
        }

        $envPath = BASE_PATH . '/.env';
        if (file_exists($envPath)) {
            $content = file_get_contents($envPath);
            if (preg_match('/^LICENSE_KEY=/m', $content)) {
                $content = preg_replace('/^LICENSE_KEY=.*$/m', 'LICENSE_KEY=' . $newKey, $content);
            } else {
                $content .= "\nLICENSE_KEY=" . $newKey;
            }
            file_put_contents($envPath, $content);
        }

        setFlash('success', 'License key updated successfully and saved to configuration!');
        redirect(url('admin/updates'));
    }

    /**
     * 1-Click Remote Download & Auto-Apply System Update
     */
    public function downloadRemote(): void {
        $licenseKey = env('LICENSE_KEY', 'MYUA-HOWM-L73Z-1TVH');

        // Create pre-update backup snapshot
        $this->createBackup('Pre-RemoteUpdate-v' . env('APP_VERSION', '5.6'));

        $res = $this->sdk->downloadAndApplyUpdate($licenseKey, BASE_PATH);

        if ($res['success']) {
            $this->logUpdate('Remote Update Download', true, $res['message']);
            setFlash('success', '✅ Remote System Update Downloaded & Applied Successfully! ' . h($res['message']));
        } else {
            $this->logUpdate('Remote Update Download', false, $res['message']);
            setFlash('danger', '❌ Remote Update Failed: ' . h($res['message']));
        }

        redirect(url('admin/updates'));
    }

    /**
     * Upload and Apply System Update Package (.zip) Manually
     */
    public function upload(): void {
        if (!verifyCsrf()) {
            setFlash('danger', 'Invalid CSRF security token.');
            redirect(url('admin/updates'));
        }

        if (empty($_FILES['update_zip']['tmp_name'])) {
            setFlash('danger', 'Please select a valid update ZIP package to upload.');
            redirect(url('admin/updates'));
        }

        $tmpFile = $_FILES['update_zip']['tmp_name'];
        $fileName = $_FILES['update_zip']['name'];
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if ($ext !== 'zip') {
            setFlash('danger', 'Only .zip update packages are supported.');
            redirect(url('admin/updates'));
        }

        // Create pre-update backup snapshot
        $this->createBackup('Pre-ManualUpload-' . pathinfo($fileName, PATHINFO_FILENAME));

        $targetPath = BASE_PATH;
        $res = $this->sdk->applyUpdatePackage($tmpFile, $targetPath);

        if ($res['success']) {
            $this->logUpdate('Manual ZIP Package Upload (' . $fileName . ')', true, $res['message']);
            setFlash('success', 'System Update Package Uploaded & Applied Successfully! ' . h($res['message']));
        } else {
            $this->logUpdate('Manual ZIP Package Upload (' . $fileName . ')', false, $res['message']);
            setFlash('danger', 'Update Failed: ' . h($res['message']));
        }

        redirect(url('admin/updates'));
    }

    /**
     * Create automated system backup snapshot before update
     */
    private function createBackup(string $prefix = 'Backup'): void {
        $backupDir = BASE_PATH . '/tmp/backups';
        if (!is_dir($backupDir)) {
            @mkdir($backupDir, 0755, true);
        }

        if (!class_exists('ZipArchive')) return;

        $zipName = $backupDir . '/' . $prefix . '_' . date('Y-m-d_H-i-s') . '.zip';
        $zip = new ZipArchive();
        if ($zip->open($zipName, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            $filesToBackup = ['index.php', 'install.php', '.env', 'sdk/DigiWSClientSDK.php'];
            foreach ($filesToBackup as $f) {
                $full = BASE_PATH . '/' . $f;
                if (file_exists($full)) {
                    $zip->addFile($full, $f);
                }
            }
            $zip->close();
        }
    }

    /**
     * Log update action history
     */
    private function logUpdate(string $action, bool $status, string $message): void {
        $tmpDir = BASE_PATH . '/tmp';
        if (!is_dir($tmpDir)) {
            @mkdir($tmpDir, 0755, true);
        }

        $logFile = $tmpDir . '/update_history.json';
        $history = [];
        if (file_exists($logFile)) {
            $history = json_decode((string)file_get_contents($logFile), true) ?: [];
        }

        array_unshift($history, [
            'timestamp' => date('Y-m-d H:i:s'),
            'action'    => $action,
            'status'    => $status ? 'Success' : 'Failed',
            'message'   => $message,
            'version'   => env('APP_VERSION', '5.6')
        ]);

        $history = array_slice($history, 0, 25);
        file_put_contents($logFile, json_encode($history, JSON_PRETTY_PRINT));
    }
}

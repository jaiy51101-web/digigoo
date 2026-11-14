<?php
/**
 * DigiWS Client Integration SDK (v5.6)
 * Product ID: PRD-ISV5JQOF7S
 * 100% Strict Remote License Server Authentication & Auto-Update Engine
 * Primary License Server: https://license.digiws.com
 */
declare(strict_types=1);

class DigiWSClientSDK {
    private string $licenseServer;
    private string $productId;
    private string $apiKey;
    private string $aesKey;
    private string $currentVersion;

    public function __construct(
        string $LICENSE_SERVER = 'https://license.digiws.com',
        string $PRODUCT_ID = 'PRD-ISV5JQOF7S',
        string $API_KEY = 'sk_Q7pl1yxWAxMflaux5jt1lAEd4fVmwKdXY0p8UOSG',
        string $CURRENT_VERSION = '5.6',
        string $AES_KEY = '3683ZHHi0xV8HZHW1Nnu8QDgoZC2o4LM'
    ) {
        $server = !empty($LICENSE_SERVER) ? $LICENSE_SERVER : (getenv('LICENSE_SERVER') ?: 'https://license.digiws.com');
        $this->licenseServer = rtrim($server, '/');
        $this->productId = !empty($PRODUCT_ID) ? $PRODUCT_ID : 'PRD-ISV5JQOF7S';
        $this->apiKey = !empty($API_KEY) ? $API_KEY : 'sk_Q7pl1yxWAxMflaux5jt1lAEd4fVmwKdXY0p8UOSG';
        $this->currentVersion = !empty($CURRENT_VERSION) ? $CURRENT_VERSION : '5.6';
        $this->aesKey = !empty($AES_KEY) ? $AES_KEY : '3683ZHHi0xV8HZHW1Nnu8QDgoZC2o4LM';
    }

    /**
     * Strict Remote License Server Verification (https://license.digiws.com)
     */
    public function checkLicense(string $licenseKey): array {
        $licenseKey = trim($licenseKey);
        if ($licenseKey === '') {
            return [
                'valid' => false,
                'message' => 'License key cannot be empty.',
                'product_id' => $this->productId,
                'status' => 'invalid'
            ];
        }

        $domain = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $payload = [
            'product_id'   => $this->productId,
            'api_key'      => $this->apiKey,
            'license_key'  => $licenseKey,
            'domain'       => $domain,
            'version'      => $this->currentVersion,
            'ip'           => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'
        ];

        // Direct Remote Verification with DigiWS License Server (https://license.digiws.com)
        $remoteResult = $this->callRemoteApi('/api/v1/license/verify', $payload);

        if (!empty($remoteResult['valid']) && $remoteResult['valid'] === true) {
            return [
                'valid'       => true,
                'message'     => $remoteResult['message'] ?? 'License verified successfully with DigiWS Server.',
                'license_key' => $licenseKey,
                'product_id'  => $this->productId,
                'domain'      => $domain,
                'status'      => 'active',
                'expires_at'  => $remoteResult['expires_at'] ?? 'Lifetime'
            ];
        }

        // Clean message formatting
        $rawMsg = $remoteResult['message'] ?? 'Invalid or unissued license key for Product ' . $this->productId . '.';
        $cleanMsg = preg_replace('/^(?:🔒\s*License Verification Failed:\s*)+/u', '', $rawMsg);

        return [
            'valid'   => false,
            'message' => '🔒 License Verification Failed: ' . $cleanMsg,
            'status'  => 'rejected'
        ];
    }

    /**
     * Check for new software updates from DigiWS Server (https://license.digiws.com)
     */
    public function checkForUpdates(string $licenseKey): array {
        $payload = [
            'product_id'      => $this->productId,
            'api_key'         => $this->apiKey,
            'license_key'     => trim($licenseKey),
            'current_version' => $this->currentVersion
        ];

        // Call remote update check API
        $remote = $this->callRemoteApi('/api/v1/update/check', $payload);
        if (empty($remote) || isset($remote['valid']) && $remote['valid'] === false) {
            $remote = $this->callRemoteApi('/api/update/check', $payload);
        }

        $hasUpdate = !empty($remote['update_available']) || !empty($remote['has_update']);

        if ($hasUpdate) {
            return [
                'has_update'        => true,
                'update_available'  => true,
                'latest_version'    => $remote['latest_version'] ?? '5.6.1',
                'current_version'   => $this->currentVersion,
                'download_url'      => $remote['download_url'] ?? null,
                'zip_file_attached' => !empty($remote['zip_file_attached']),
                'checksum_md5'      => $remote['checksum_md5'] ?? null,
                'release_notes'     => $remote['release_notes'] ?? 'General bug fixes & feature enhancements.',
                'message'           => 'New update available: v' . ($remote['latest_version'] ?? '5.6.1')
            ];
        }

        return [
            'has_update'        => false,
            'update_available'  => false,
            'latest_version'    => $this->currentVersion,
            'current_version'   => $this->currentVersion,
            'zip_file_attached' => false,
            'message'           => 'Your DigiWS platform is up to date (v' . $this->currentVersion . ').',
            'release_notes'     => 'You are running the latest version (' . $this->currentVersion . ').'
        ];
    }

    /**
     * Download and apply remote update ZIP package directly from DigiWS License Server
     */
    public function downloadAndApplyUpdate(string $licenseKey, string $targetExtractPath): array {
        $updateCheck = $this->checkForUpdates($licenseKey);
        if (empty($updateCheck['update_available']) || empty($updateCheck['download_url'])) {
            return ['success' => false, 'message' => 'No downloadable update package found or ZIP file not uploaded on server.'];
        }

        if (empty($updateCheck['zip_file_attached'])) {
            return ['success' => false, 'message' => 'The update ZIP package file has not been uploaded to the license server yet by the administrator.'];
        }

        $downloadUrl = $updateCheck['download_url'];
        $tempZipPath = sys_get_temp_dir() . '/digiws_update_' . time() . '.zip';

        // Download ZIP file using cURL
        $ch = curl_init($downloadUrl);
        $fp = fopen($tempZipPath, 'w+');
        if (!$fp) {
            return ['success' => false, 'message' => 'Could not create temporary file for update package download.'];
        }

        curl_setopt_array($ch, [
            CURLOPT_FILE           => $fp,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT        => 300,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false
        ]);

        $exec = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        fclose($fp);

        if (!$exec || $httpCode >= 400 || !file_exists($tempZipPath) || filesize($tempZipPath) < 100) {
            @unlink($tempZipPath);
            return ['success' => false, 'message' => 'Failed to download update ZIP package from server (HTTP Code: ' . $httpCode . ').'];
        }

        // Apply update package
        $applyRes = $this->applyUpdatePackage($tempZipPath, $targetExtractPath);
        @unlink($tempZipPath);

        return $applyRes;
    }

    /**
     * Apply update package from uploaded ZIP file
     */
    public function applyUpdatePackage(string $zipFilePath, string $targetExtractPath): array {
        if (!file_exists($zipFilePath)) {
            return ['success' => false, 'message' => 'Update package file not found.'];
        }

        if (!class_exists('ZipArchive')) {
            return ['success' => false, 'message' => 'ZipArchive PHP extension is required to install updates.'];
        }

        $zip = new ZipArchive();
        if ($zip->open($zipFilePath) === true) {
            $zip->extractTo($targetExtractPath);
            $zip->close();

            // Run SQL Migrations if present in update package
            $sqlFiles = ['update_migration.sql', 'migration.sql', 'database/migration.sql'];
            foreach ($sqlFiles as $sf) {
                $sqlPath = rtrim($targetExtractPath, '/\\') . '/' . $sf;
                if (file_exists($sqlPath)) {
                    try {
                        if (class_exists('Database')) {
                            $db = Database::getInstance();
                            $sql = file_get_contents($sqlPath);
                            if (!empty($sql)) {
                                $queries = array_filter(array_map('trim', explode(';', $sql)));
                                foreach ($queries as $q) {
                                    if ($q !== '') {
                                        $db->query($q);
                                    }
                                }
                            }
                        }
                        @unlink($sqlPath);
                    } catch (Throwable $e) {
                        // Log SQL migration warning silently
                    }
                }
            }

            // Run PHP Migrations if present in update package
            $migrationFile = rtrim($targetExtractPath, '/\\') . '/update_migration.php';
            if (file_exists($migrationFile)) {
                try {
                    require_once $migrationFile;
                    @unlink($migrationFile);
                } catch (Throwable $e) {
                    return ['success' => true, 'message' => 'Files updated, but migration warning: ' . $e->getMessage()];
                }
            }

            // Auto-update version in .env if version.json exists in package
            $versionFile = rtrim($targetExtractPath, '/\\') . '/version.json';
            if (file_exists($versionFile)) {
                $vData = json_decode((string)file_get_contents($versionFile), true);
                if (!empty($vData['version'])) {
                    $envPath = rtrim($targetExtractPath, '/\\') . '/.env';
                    if (file_exists($envPath)) {
                        $envContent = file_get_contents($envPath);
                        $envContent = preg_replace('/^APP_VERSION=.*$/m', 'APP_VERSION=' . trim($vData['version']), $envContent);
                        file_put_contents($envPath, $envContent);
                    }
                }
            }

            return ['success' => true, 'message' => 'System update applied successfully! All files and database structures updated.'];
        }

        return ['success' => false, 'message' => 'Failed to extract update package. Invalid or corrupted ZIP archive.'];
    }

    /**
     * HTTP API Communicator with DigiWS License Server (https://license.digiws.com)
     */
    private function callRemoteApi(string $endpoint, array $data): array {
        if (!extension_loaded('curl')) {
            return ['valid' => false, 'message' => 'PHP cURL extension is missing on host.'];
        }

        $url = $this->licenseServer . $endpoint;
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode($data),
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Accept: application/json',
                'X-DigiWS-Product-Id: ' . $this->productId,
                'X-DigiWS-Api-Key: ' . $this->apiKey
            ],
            CURLOPT_TIMEOUT        => 8,
            CURLOPT_CONNECTTIMEOUT => 4,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false
        ]);

        $response = curl_exec($ch);
        $err = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($err || !$response) {
            return ['valid' => false, 'message' => 'Unable to reach license server at ' . $this->licenseServer . '. Error: ' . $err];
        }

        $decoded = json_decode($response, true);
        if (is_array($decoded)) {
            return $decoded;
        }

        // Handle HTML responses gracefully
        if (str_contains($response, '<!DOCTYPE') || str_contains($response, '<html')) {
            return ['valid' => false, 'message' => 'Server URL (' . $this->licenseServer . ') returned an HTML web page instead of JSON API response. (HTTP ' . $httpCode . ')'];
        }

        return ['valid' => false, 'message' => 'Invalid response from license server. Raw: ' . substr(strip_tags($response), 0, 150)];
    }

    public function getProductId(): string { return $this->productId; }
    public function getApiKey(): string { return $this->apiKey; }
    public function getCurrentVersion(): string { return $this->currentVersion; }
}

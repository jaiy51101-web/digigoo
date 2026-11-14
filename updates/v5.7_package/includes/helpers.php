<?php
/**
 * Digiws - Helper Functions
 * Utility functions used across the application
 */

/**
 * Generate SEO-friendly slug from text
 */
function generateSlug(string $text): string {
    $text = mb_strtolower($text, 'UTF-8');
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    $text = preg_replace('/[\s-]+/', '-', $text);
    return trim($text, '-');
}

/**
 * Generate unique slug checking against existing DB slugs
 */
function uniqueSlug(string $text, string $table, ?int $excludeId = null): string {
    $db = Database::getInstance();
    $slug = generateSlug($text);
    $original = $slug;
    $counter = 1;

    while (true) {
        $sql = "SELECT COUNT(*) FROM `{$table}` WHERE slug = ?";
        $params = [$slug];
        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        if ($db->fetchColumn($sql, $params) == 0) break;
        $slug = $original . '-' . $counter;
        $counter++;
    }
    return $slug;
}

/**
 * Sanitize input string for safe database storage.
 * NOTE: Do NOT use htmlspecialchars() here — that causes double-encoding
 * when the view also calls htmlspecialchars() on output.
 * HTML escaping must only happen at the output/display layer.
 */
function sanitize(string $input): string {
    return trim(strip_tags($input));
}

/**
 * HTML Escape Helper Function (h)
 * Safely escapes values for HTML rendering and prevents XSS.
 */
function h(mixed $value): string {
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Sanitize array of inputs
 */
function sanitizeArray(array $data): array {
    return array_map(function ($val) {
        return is_string($val) ? sanitize($val) : $val;
    }, $data);
}

/**
 * Generate CSRF token
 */
function csrfToken(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * CSRF hidden input field
 */
function csrfField(): string {
    return '<input type="hidden" name="csrf_token" value="' . csrfToken() . '">';
}

/**
 * Verify CSRF token
 */
function verifyCsrf(): bool {
    $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    $sessionToken = $_SESSION['csrf_token'] ?? '';

    // Reject malformed array inputs before hash_equals() to avoid a TypeError
    // and ensure every failed request follows the normal CSRF failure path.
    if (!is_string($token) || !is_string($sessionToken) || $sessionToken === '') {
        return false;
    }

    return hash_equals($sessionToken, $token);
}

/**
 * Redirect to URL
 */
function redirect(string $url): void {
    header("Location: " . $url);
    exit;
}

/**
 * Set flash message
 */
function setFlash(string $type, string $message): void {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

/**
 * Get and clear flash message
 */
function getFlash(): ?array {
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $flash;
}

/**
 * Check if user is logged in
 */
function isLoggedIn(): bool {
    return !empty($_SESSION['user_id']);
}

/**
 * Check if current user is admin
 */
function isAdmin(): bool {
    return ($_SESSION['user_role'] ?? '') === 'admin';
}

/**
 * Get current user ID
 */
function currentUserId(): ?int {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Get current user data from session
 */
function currentUser(): ?array {
    if (!isLoggedIn()) return null;
    
    // Lazy load phone number into session for compatibility with older sessions
    if (!isset($_SESSION['user_phone'])) {
        $user = (new User())->find($_SESSION['user_id']);
        $_SESSION['user_phone'] = $user['phone'] ?? '';
    }
    
    return [
        'id' => $_SESSION['user_id'],
        'name' => $_SESSION['user_name'] ?? '',
        'email' => $_SESSION['user_email'] ?? '',
        'role' => $_SESSION['user_role'] ?? 'user',
        'avatar' => $_SESSION['user_avatar'] ?? '',
        'phone' => $_SESSION['user_phone'] ?? ''
    ];
}

/**
 * Require user to be logged in
 */
function requireLogin(): void {
    if (!isLoggedIn()) {
        setFlash('warning', 'Please login to continue.');
        redirect(APP_URL . '/login');
    }
    $user = (new User())->find(currentUserId());
    if (!$user || $user['status'] !== 'active') {
        (new User())->destroySession();
        setFlash('danger', $user && $user['status'] === 'banned' ? 'Your account has been banned. Please contact support.' : 'Your account is suspended. Please contact support.');
        redirect(APP_URL . '/login');
    }
}

/**
 * Require admin role
 */
function requireAdmin(): void {
    if (!isAdmin()) {
        setFlash('danger', 'Access denied.');
        redirect(APP_URL . '/admin/login');
    }

    // Re-check the account on protected requests so a disabled or demoted
    // administrator loses access immediately, even with an old session.
    $user = (new User())->find((int) currentUserId());
    if (!$user || ($user['status'] ?? '') !== 'active' || ($user['role'] ?? '') !== 'admin') {
        (new User())->destroySession();
        setFlash('danger', 'Your administrator session is no longer valid.');
        redirect(APP_URL . '/admin/login');
    }
}

/**
 * Get live exchange rate (cached for 24 hours)
 */
function getUsdExchangeRate(): float {
    $cacheFile = sys_get_temp_dir() . '/digiws_inr_usd_rate.json';
    if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < 86400) {
        $data = json_decode(file_get_contents($cacheFile), true);
        if (isset($data['rate'])) return $data['rate'];
    }
    
    // Fetch live rate
    $json = @file_get_contents('https://v6.exchangerate-api.com/v6/f90f88f01317762eb488c8bb/latest/USD');
    if ($json) {
        $result = json_decode($json, true);
        if (isset($result['conversion_rates']['INR'])) {
            $inrRate = (float)$result['conversion_rates']['INR'];
            if ($inrRate > 0) {
                $rate = 1 / $inrRate;
                file_put_contents($cacheFile, json_encode(['rate' => $rate]));
                return $rate;
            }
        }
    }
    return 1 / 83.5; // Fallback approximate rate
}

/**
 * Format price with currency symbol and auto convert
 */
function formatPrice(float $amount): string {
    if ($amount <= 0.0) {
        return 'FREE';
    }
    $currency = $_COOKIE['currency'] ?? 'INR';
    if ($currency === 'USD') {
        $usdAmount = $amount * getUsdExchangeRate();
        $formatted = '$' . number_format($usdAmount, 2);
    } else {
        $formatted = '₹' . number_format($amount, 2);
    }
    return str_replace('.00', '', $formatted);
}

/**
 * Calculate GST amount
 */
function calculateGST(float $amount, float $gstPercent): float {
    return round($amount * ($gstPercent / 100), 2);
}

/**
 * Generate unique order number
 */
function generateOrderNumber(): string {
    return 'DW' . date('Ymd') . strtoupper(substr(uniqid(), -6));
}

/**
 * Generate license key
 */
function generateLicenseKey(): string {
    $segments = [];
    for ($i = 0; $i < 4; $i++) {
        $segments[] = strtoupper(bin2hex(random_bytes(2)));
    }
    return implode('-', $segments);
}

/**
 * Truncate text
 */
function truncateText(string $text, int $length = 100): string {
    if (mb_strlen($text) <= $length) return $text;
    return mb_substr($text, 0, $length) . '...';
}

/**
 * Time ago formatter
 */
function timeAgo(string $datetime): string {
    $now = new DateTime();
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    if ($diff->y > 0) return $diff->y . ' year' . ($diff->y > 1 ? 's' : '') . ' ago';
    if ($diff->m > 0) return $diff->m . ' month' . ($diff->m > 1 ? 's' : '') . ' ago';
    if ($diff->d > 0) return $diff->d . ' day' . ($diff->d > 1 ? 's' : '') . ' ago';
    if ($diff->h > 0) return $diff->h . ' hour' . ($diff->h > 1 ? 's' : '') . ' ago';
    if ($diff->i > 0) return $diff->i . ' minute' . ($diff->i > 1 ? 's' : '') . ' ago';
    return 'Just now';
}

/**
 * Format date
 */
function formatDate(string $date, string $format = 'd M Y'): string {
    return date($format, strtotime($date));
}

function statusBadge(string $status): string {
    $statusClass = strtolower($status);
    return '<span class="status-badge-custom status-' . $statusClass . '">' . htmlspecialchars(ucfirst($status)) . '</span>';
}

/**
 * Generate pagination HTML
 */
function pagination(int $totalItems, int $perPage, int $currentPage, string $baseUrl): string {
    $totalPages = max(1, ceil($totalItems / $perPage));
    if ($totalPages <= 1) return '';

    // Build base query params preserving all existing filters except 'page'
    $params = $_GET ?? [];
    unset($params['page']);

    $buildUrl = function(int $page) use ($baseUrl, $params): string {
        $q = array_merge($params, ['page' => $page]);
        return $baseUrl . '?' . http_build_query($q);
    };

    $html = '<nav aria-label="Page navigation"><ul class="pagination justify-content-center">';

    // Previous
    $prevDisabled = $currentPage <= 1 ? ' disabled' : '';
    $html .= '<li class="page-item' . $prevDisabled . '"><a class="page-link" href="' . $buildUrl($currentPage - 1) . '"><i class="bi bi-chevron-left"></i></a></li>';

    // Pages
    $start = max(1, $currentPage - 2);
    $end = min($totalPages, $currentPage + 2);

    if ($start > 1) {
        $html .= '<li class="page-item"><a class="page-link" href="' . $buildUrl(1) . '">1</a></li>';
        if ($start > 2) $html .= '<li class="page-item disabled"><span class="page-link">…</span></li>';
    }

    for ($i = $start; $i <= $end; $i++) {
        $active = $i === $currentPage ? ' active' : '';
        $html .= '<li class="page-item' . $active . '"><a class="page-link" href="' . $buildUrl($i) . '">' . $i . '</a></li>';
    }

    if ($end < $totalPages) {
        if ($end < $totalPages - 1) $html .= '<li class="page-item disabled"><span class="page-link">…</span></li>';
        $html .= '<li class="page-item"><a class="page-link" href="' . $buildUrl($totalPages) . '">' . $totalPages . '</a></li>';
    }

    // Next
    $nextDisabled = $currentPage >= $totalPages ? ' disabled' : '';
    $html .= '<li class="page-item' . $nextDisabled . '"><a class="page-link" href="' . $buildUrl($currentPage + 1) . '"><i class="bi bi-chevron-right"></i></a></li>';

    $html .= '</ul></nav>';
    return $html;
}

/**
 * Handle file upload — hardened with real MIME detection.
 * Uses finfo_file() to verify actual file content, not just the browser-reported type.
 */
function uploadFile(array $file, string $directory = 'products', array $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif']): ?string {
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK || empty($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) return null;

    // Prevent paths such as ../ from escaping the uploads directory.
    $directory = trim(str_replace('\\', '/', $directory), '/');
    if ($directory === '' || preg_match('#(^|/)\.\.?(/|$)#', $directory)) return null;

    // ── Block dangerous extensions (even if disguised as .jpg.php etc.) ───────
    $dangerousExtensions = ['php', 'php3', 'php4', 'php5', 'php7', 'phtml', 'phar',
                            'shtml', 'cgi', 'pl', 'py', 'rb', 'sh', 'bash',
                            'asp', 'aspx', 'jsp', 'exe', 'bat', 'cmd', 'com'];
    $originalName = strtolower($file['name']);
    foreach ($dangerousExtensions as $dangerous) {
        if (preg_match('/\.' . preg_quote($dangerous, '/') . '(\.|$)/i', $originalName)) {
            return null; // Reject disguised executables
        }
    }

    // ── Real MIME detection via finfo (not spoofable) ─────────────────────────
    if (function_exists('finfo_open')) {
        $finfo    = finfo_open(FILEINFO_MIME_TYPE);
        $realMime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
    } else {
        $realMime = mime_content_type($file['tmp_name']) ?: $file['type'];
    }

    if (!in_array($realMime, $allowedTypes)) return null;

    // ── Ensure upload directory exists ────────────────────────────────────────
    $uploadDir = UPLOADS_PATH . '/' . $directory;
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // ── Generate a safe random filename (no original name kept) ──────────────
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $ext = preg_replace('/[^a-zA-Z0-9]/', '', $ext); // strip anything weird from ext
    $filename    = bin2hex(random_bytes(12)) . '_' . time() . ($ext ? '.' . strtolower($ext) : '');
    $destination = $uploadDir . '/' . $filename;

    if (move_uploaded_file($file['tmp_name'], $destination)) {
        // Make file non-executable after upload
        @chmod($destination, 0644);
        return $directory . '/' . $filename;
    }
    return null;
}

/**
 * Delete uploaded file
 */
function deleteFile(string $path): bool {
    $uploadsRoot = realpath(UPLOADS_PATH);
    if ($uploadsRoot === false || $path === '' || str_contains($path, "\0")) return false;

    $fullPath = realpath($uploadsRoot . DIRECTORY_SEPARATOR . ltrim(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path), DIRECTORY_SEPARATOR));
    if ($fullPath === false || !str_starts_with($fullPath, $uploadsRoot . DIRECTORY_SEPARATOR) || !is_file($fullPath)) return false;

    return unlink($fullPath);
}

/**
 * Get JSON POST body
 */
function getJsonBody(): array {
    $json = file_get_contents('php://input');
    return json_decode($json, true) ?? [];
}

/**
 * Send JSON response
 */
function jsonResponse(array $data, int $code = 200): void {
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

/**
 * Get asset URL
 */
function asset(string $path): string {
    return ASSETS_URL . '/' . ltrim($path, '/');
}

/**
 * Get upload URL
 */
function uploadUrl(string $path): string {
    if (strpos($path, 'uploads/') === 0) {
        $path = substr($path, 8);
    }
    return UPLOADS_URL . '/' . ltrim($path, '/');
}

/**
 * URL helper
 */
function url(string $path = ''): string {
    return APP_URL . '/' . ltrim($path, '/');
}

/**
 * Get cart count from session
 */
function cartCount(): int {
    return $_SESSION['cart_count'] ?? 0;
}

/**
 * Update cart count in session
 */
function updateCartCount(): void {
    if (isLoggedIn()) {
        $db = Database::getInstance();
        $_SESSION['cart_count'] = $db->count('cart', 'user_id = ?', [currentUserId()]);
    } else {
        $sessionId = session_id();
        $db = Database::getInstance();
        $_SESSION['cart_count'] = $db->count('cart', 'session_id = ?', [$sessionId]);
    }
}

/**
 * Get wishlist count
 */
function wishlistCount(): int {
    $wishlist = new Wishlist();
    return $wishlist->getCount();
}
/**
 * Check if a product is in the current user's wishlist
 */
function is_in_wishlist(int $productId): bool {
    $wishlist = new Wishlist();
    return $wishlist->isInWishlist($productId);
}

/**
 * Rate limiting - returns true if allowed, false if blocked.
 * Uses session-based counters to avoid DB overhead.
 */
function rateLimit(string $key, int $maxAttempts = 5, int $windowSeconds = 900): bool {
    $sessionKey = 'rl_' . md5($key . $_SERVER['REMOTE_ADDR']);
    $now = time();

    if (!isset($_SESSION[$sessionKey])) {
        $_SESSION[$sessionKey] = ['count' => 0, 'reset_at' => $now + $windowSeconds];
    }

    // Reset window if expired
    if ($now > $_SESSION[$sessionKey]['reset_at']) {
        $_SESSION[$sessionKey] = ['count' => 0, 'reset_at' => $now + $windowSeconds];
    }

    $_SESSION[$sessionKey]['count']++;

    return $_SESSION[$sessionKey]['count'] <= $maxAttempts;
}

/**
 * Get remaining seconds for a rate-limit window
 */
function rateLimitRemaining(string $key): int {
    $sessionKey = 'rl_' . md5($key . $_SERVER['REMOTE_ADDR']);
    if (!isset($_SESSION[$sessionKey])) return 0;
    return max(0, $_SESSION[$sessionKey]['reset_at'] - time());
}

// Note: sanitize() is defined earlier in this file

/**
 * Validate password strength (min 8 chars, 1 uppercase, 1 number)
 */
function isStrongPassword(string $password): bool {
    return strlen($password) >= 8
        && preg_match('/[A-Z]/', $password)
        && preg_match('/[0-9]/', $password);
}
/**
 * Check if the current request is an AJAX request
 */
function isAjax(): bool {
    return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

/**
 * Set security headers for production
 */
function setSecurityHeaders(): void {
    if (!headers_sent()) {
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: SAMEORIGIN');
        header('X-XSS-Protection: 1; mode=block');
        header('Referrer-Policy: strict-origin-when-cross-origin');
        if (env('APP_ENV') === 'production') {
            header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
        }
    }
}

/**
 * Get human-readable support validity message
 */
function getSupportValidityText(?string $expiresAt): string {
    if (empty($expiresAt)) {
        return '<span class="text-danger fw-semibold"><i class="bi bi-shield-slash me-1"></i>No active support</span>';
    }
    
    $expiry = strtotime($expiresAt);
    $now = time();
    
    if ($now >= $expiry) {
        return '<span class="text-danger fw-semibold"><i class="bi bi-exclamation-triangle me-1"></i>Expired</span>';
    }
    
    $diff = $expiry - $now;
    
    // Calculate months and days dynamically
    $diffDatetime = new DateTime();
    $diffDatetime->setTimestamp($now);
    $expiryDatetime = new DateTime();
    $expiryDatetime->setTimestamp($expiry);
    
    $interval = $diffDatetime->diff($expiryDatetime);
    
    $parts = [];
    if ($interval->y > 0) {
        $parts[] = $interval->y . ($interval->y > 1 ? ' years' : ' year');
    }
    if ($interval->m > 0) {
        $parts[] = $interval->m . ($interval->m > 1 ? ' months' : ' month');
    }
    if ($interval->d > 0) {
        $parts[] = $interval->d . ($interval->d > 1 ? ' days' : ' day');
    }
    
    if (empty($parts)) {
        $hours = floor($diff / 3600);
        if ($hours > 0) {
            $parts[] = $hours . ($hours > 1 ? ' hours' : ' hour');
        } else {
            $parts[] = 'Less than an hour';
        }
    }
    
    $text = implode(', ', $parts) . ' remaining';
    return '<span class="text-success fw-semibold"><i class="bi bi-shield-check me-1"></i>' . $text . '</span>';
}

/**
 * Maps download platform slug to names, bootstrap icons, background colors and descriptive texts
 */
function getPlatformDetails(string $platform, int $index = 0): array {
    switch ($platform) {
        case 'gdrive':
            return [
                'name' => 'Google Drive',
                'icon' => 'bi-google',
                'bg'   => '#10b981', // Emerald green
                'text' => 'Download (Google Drive)'
            ];
        case 'dropbox':
            return [
                'name' => 'Dropbox',
                'icon' => 'bi-dropbox',
                'bg'   => '#3b82f6', // Dropbox blue
                'text' => 'Download (Dropbox)'
            ];
        case 'mega':
            return [
                'name' => 'MEGA',
                'icon' => 'bi-cloud-arrow-down-fill',
                'bg'   => '#ef4444', // MEGA red
                'text' => 'Download (MEGA)'
            ];
        case 'onedrive':
            return [
                'name' => 'OneDrive',
                'icon' => 'bi-cloud-fill',
                'bg'   => '#0ea5e9', // OneDrive blue
                'text' => 'Download (OneDrive)'
            ];
        case 'server':
            return [
                'name' => 'Direct Server',
                'icon' => 'bi-download',
                'bg'   => '#4f46e5', // Direct indigo
                'text' => 'Direct ZIP Download'
            ];
        default:
            return [
                'name' => 'Download Link',
                'icon' => 'bi-box-arrow-up-right',
                'bg'   => '#64748b', // Slate gray
                'text' => 'Download Part #' . ($index + 1)
            ];
    }
}

/**
 * Clean and combine country code and phone number (no +)
 */
function cleanPhone(string $phone, string $countryCode = '91'): string {
    $phone = trim($phone);
    if (empty($phone)) return '';
    $phone = preg_replace('/^(\+' . $countryCode . '|' . $countryCode . ')/', '', $phone);
    return $countryCode . preg_replace('/[^0-9]/', '', $phone);
}

/**
 * Split phone number into country code and local number
 */
function splitPhone(string $phone): array {
    $phone = preg_replace('/[^0-9]/', '', $phone);
    $codes = ['91', '1', '44', '971', '62', '880', '92', '977', '60', '65', '61'];
    foreach ($codes as $code) {
        if (strpos($phone, $code) === 0) {
            return [
                'code' => $code,
                'number' => substr($phone, strlen($code))
            ];
        }
    }
    return [
        'code' => '91', // default India
        'number' => $phone
    ];
}

/**
 * Calculate dynamic fake views count based on daily growth range.
 */
function getDynamicFakeViews(array $product): int {
    $min = isset($product['fake_views_min']) ? (int)$product['fake_views_min'] : 0;
    $max = isset($product['fake_views_max']) ? (int)$product['fake_views_max'] : 0;
    if ($min <= 0 && $max <= 0) {
        return (int)($product['view_count'] ?? 0);
    }
    
    $createdTime = strtotime($product['created_at']);
    $days = max(1, (int)floor((time() - $createdTime) / 86400));
    
    $totalViews = 0;
    for ($i = 0; $i < $days; $i++) {
        $seed = (int)($product['id'] ?? 0) * 10000 + $i;
        mt_srand($seed);
        $totalViews += mt_rand($min, $max);
    }
    mt_srand();
    
    return $totalViews;
}

/**
 * Calculate dynamic fake sales count based on daily growth range.
 */
function getDynamicFakeSales(array $product): int {
    $min = isset($product['fake_sales_min']) ? (int)$product['fake_sales_min'] : 0;
    $max = isset($product['fake_sales_max']) ? (int)$product['fake_sales_max'] : 0;
    if ($min <= 0 && $max <= 0) {
        return (int)($product['total_sales'] ?? 0);
    }
    
    $createdTime = strtotime($product['created_at']);
    $days = max(1, (int)floor((time() - $createdTime) / 86400));
    
    $totalSales = 0;
    for ($i = 0; $i < $days; $i++) {
        $seed = (int)($product['id'] ?? 0) * 20000 + $i;
        mt_srand($seed);
        $totalSales += mt_rand($min, $max);
    }
    mt_srand();
    
    return $totalSales;
}

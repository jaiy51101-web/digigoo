<?php
/**
 * Digiws - Auth Controller
 */

class AuthController {

    public function loginForm(): void {
        if (isLoggedIn()) redirect(url('user/dashboard'));
        setSecurityHeaders();
        $pageTitle = 'Login - ' . APP_NAME;
        include TEMPLATES_PATH . '/header.php';
        include VIEWS_PATH . '/login.php';
        include TEMPLATES_PATH . '/footer.php';
    }

    public function login(): void {
        if (!verifyCsrf()) {
            setFlash('danger', 'Invalid request.');
            redirect(url('login'));
        }

        if (!Security::checkLoginAttempt()) {
            redirect(url('login'));
        }

        // Rate limit: 5 attempts per 15 minutes
        if (!rateLimit('login', 5, 900)) {
            $wait = ceil(rateLimitRemaining('login') / 60);
            setFlash('danger', "Too many login attempts. Please wait {$wait} minute(s) and try again.");
            redirect(url('login'));
        }

        $email = sanitize($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            setFlash('danger', 'Please fill in all fields.');
            redirect(url('login'));
        }

        $userModel = new User();
        $user = $userModel->authenticate($email, $password);

        if (!$user) {
            Security::recordFailedLogin();
            setFlash('danger', 'Invalid email or password.');
            redirect(url('login'));
        }

        if ($user['status'] === 'banned') {
            setFlash('danger', 'Your account has been banned. Please contact support.');
            redirect(url('login'));
        }

        if ($user['status'] === 'suspended') {
            setFlash('danger', 'Your account has been suspended. Please contact support.');
            redirect(url('login'));
        }

        $userModel->setSession($user);
        Security::ipRateLimitReset('login');

        // Sync and log to CRM
        try {
            $subModel = new MarketingSubscriber();
            $subModel->syncSubscriberCategory($user['email']);
            $sub = $subModel->findByEmail($user['email']);
            if ($sub) {
                $subModel->logTimelineActivity($sub['id'], 'Logged In', "Logged in to the website.");
            }
        } catch (Exception $e) {}

        // Transfer guest cart
        $cart = new Cart();
        $cart->transferToUser($user['id']);
        updateCartCount();

        if ($user['role'] === 'admin') {
            redirect(url('admin'));
        }

        $redirectTo = $_SESSION['redirect_after_login'] ?? url('user/dashboard');
        unset($_SESSION['redirect_after_login']);
        redirect($redirectTo);
    }

    public function registerForm(): void {
        if (isLoggedIn()) redirect(url('user/dashboard'));
        setSecurityHeaders();
        $pageTitle = 'Register - ' . APP_NAME;
        include TEMPLATES_PATH . '/header.php';
        include VIEWS_PATH . '/register.php';
        include TEMPLATES_PATH . '/footer.php';
    }

    public function register(): void {
        if (!verifyCsrf()) {
            setFlash('danger', 'Invalid request.');
            redirect(url('register'));
        }

        $name = sanitize($_POST['name'] ?? '');
        $email = sanitize($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        $phoneInput = sanitize($_POST['phone'] ?? '');
        $countryCode = sanitize($_POST['country_code'] ?? '91');
        $phone = cleanPhone($phoneInput, $countryCode);

        // Validation
        $errors = [];
        if (empty($name)) $errors[] = 'Name is required.';
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required.';
        if (strlen($password) < 8) $errors[] = 'Password must be at least 8 characters.';
        if ($password !== $confirmPassword) $errors[] = 'Passwords do not match.';

        $userModel = new User();
        if ($userModel->emailExists($email)) {
            $errors[] = 'Email already registered.';
        }

        if (!empty($errors)) {
            setFlash('danger', implode('<br>', $errors));
            redirect(url('register'));
        }

        $userId = $userModel->create([
            'name'     => $name,
            'email'    => $email,
            'password' => $password,
            'phone'    => $phone
        ]);

        $user = $userModel->find($userId);
        $userModel->setSession($user);

        // Sync and log to CRM
        try {
            $subModel = new MarketingSubscriber();
            $subModel->syncSubscriberCategory($user['email']);
            $sub = $subModel->findByEmail($user['email']);
            if ($sub) {
                $subModel->logTimelineActivity($sub['id'], 'Registered', "Registered a new user account.");
                MarketingSubscriber::triggerAutomation('user_signup', ['email' => $user['email']]);
            }
        } catch (Exception $e) {}

        // Transfer guest cart
        $cart = new Cart();
        $cart->transferToUser($userId);
        updateCartCount();

        // Send welcome email
        try {
            $mailer = new Mailer();
            $mailer->sendWelcome($user);
        } catch (Exception $e) {
            // Don't block registration if email fails
        }

        setFlash('success', 'Welcome to ' . APP_NAME . '! Your account has been created.');
        $_SESSION['show_register_animation'] = true;
        redirect(url('user/dashboard'));
    }

    public function logout(): void {
        $userModel = new User();
        $userModel->destroySession();
        $_SESSION['cart_count'] = 0;
        setFlash('success', 'You have been logged out.');
        redirect(url('login'));
    }

    public function forgotForm(): void {
        $pageTitle = 'Forgot Password - ' . APP_NAME;
        include TEMPLATES_PATH . '/header.php';
        include VIEWS_PATH . '/forgot-password.php';
        include TEMPLATES_PATH . '/footer.php';
    }

    public function forgotPassword(): void {
        if (!verifyCsrf()) {
            setFlash('danger', 'Invalid request.');
            redirect(url('forgot-password'));
        }

        $email = sanitize($_POST['email'] ?? '');
        $userModel = new User();
        $user = $userModel->findByEmail($email);

        if ($user) {
            if ($user['status'] === 'banned') {
                setFlash('danger', 'Your account has been banned. Please contact support.');
                redirect(url('forgot-password'));
            }
            $token = $userModel->createResetToken($user['id']);
            // Queue any configured marketing password-reset automation. The
            // reset token makes each request an independent, idempotent event.
            try {
                (new MarketingSubscriber())->syncSubscriberCategory($user['email']);
                MarketingSubscriber::triggerAutomation('password_reset', ['email' => $user['email'], 'reset_token' => $token]);
            } catch (Throwable $e) { error_log('DigiWS password-reset automation: ' . $e->getMessage()); }
            try {
                $mailer = new Mailer();
                $resetLink = url('reset-password/' . $token);
                $mailer->sendPasswordReset($user, $resetLink);
            } catch (Exception $e) {
                // Log error silently
            }
        }

        // Always show success to prevent email enumeration
        setFlash('success', 'If that email exists, a password reset link has been sent.');
        redirect(url('forgot-password'));
    }

    public function resetForm(string $token): void {
        $userModel = new User();
        $user = $userModel->findByResetToken($token);

        if (!$user) {
            setFlash('danger', 'Invalid or expired reset link.');
            redirect(url('forgot-password'));
        }

        $pageTitle = 'Reset Password - ' . APP_NAME;
        include TEMPLATES_PATH . '/header.php';
        include VIEWS_PATH . '/reset-password.php';
        include TEMPLATES_PATH . '/footer.php';
    }

    public function resetPassword(): void {
        if (!verifyCsrf()) {
            setFlash('danger', 'Invalid request.');
            redirect(url('login'));
        }

        $token = $_POST['token'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (strlen($password) < 8) {
            setFlash('danger', 'Password must be at least 8 characters.');
            redirect(url('reset-password/' . $token));
        }

        if ($password !== $confirmPassword) {
            setFlash('danger', 'Passwords do not match.');
            redirect(url('reset-password/' . $token));
        }

        $userModel = new User();
        $user = $userModel->findByResetToken($token);

        if (!$user) {
            setFlash('danger', 'Invalid or expired reset link.');
            redirect(url('forgot-password'));
        }

        $userModel->updatePassword($user['id'], $password);
        $userModel->clearResetToken($user['id']);

        setFlash('success', 'Password has been reset successfully. Please login.');
        redirect(url('login'));
    }
    public function googleRedirect(): void {
        $settingModel = new Setting();
        $clientId = $settingModel->get('google_client_id');
        $redirectUri = url('auth/google/callback');
        $scope = 'email profile openid';
        
        $_SESSION['google_oauth_state'] = bin2hex(random_bytes(32));
        $url = "https://accounts.google.com/o/oauth2/v2/auth?" . http_build_query([
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'response_type' => 'code',
            'scope' => $scope,
            'state' => $_SESSION['google_oauth_state'],
            'access_type' => 'offline',
            'prompt' => 'select_account'
        ]);
        
        redirect($url);
    }

    public function googleCallback(): void {
        $code = $_GET['code'] ?? '';
        $state = $_GET['state'] ?? '';
        $expectedState = $_SESSION['google_oauth_state'] ?? '';
        unset($_SESSION['google_oauth_state']);
        if (empty($code) || !is_string($state) || !is_string($expectedState) || !hash_equals($expectedState, $state)) {
            setFlash('danger', 'Authentication failed.');
            redirect(url('login'));
        }

        $settingModel = new Setting();
        $clientId = $settingModel->get('google_client_id');
        $clientSecret = $settingModel->get('google_client_secret');
        $redirectUri = url('auth/google/callback');

        // Exchange code for token
        $ch = curl_init('https://oauth2.googleapis.com/token');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
            'code' => $code,
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'redirect_uri' => $redirectUri,
            'grant_type' => 'authorization_code'
        ]));
        $response = curl_exec($ch);
        $curlError = curl_errno($ch) ? curl_error($ch) : null;
        $data = json_decode($response, true);
        curl_close($ch);

        if (empty($data['access_token'])) {
            $msg = 'Failed to get access token from Google.';
            if ($curlError) {
                $msg .= ' cURL Error: ' . $curlError;
            } elseif (isset($data['error'])) {
                $msg .= ' Google Error: ' . $data['error'] . ' - ' . ($data['error_description'] ?? '');
            } else {
                $msg .= ' Raw response: ' . substr(strip_tags((string)$response), 0, 150);
            }
            setFlash('danger', $msg);
            redirect(url('login'));
        }

        // Get user info
        $ch = curl_init('https://www.googleapis.com/oauth2/v3/userinfo');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $data['access_token']]);
        $userInfoResponse = curl_exec($ch);
        $userinfoCurlError = curl_errno($ch) ? curl_error($ch) : null;
        $userInfo = json_decode($userInfoResponse, true);
        curl_close($ch);

        if (empty($userInfo['sub'])) {
            $msg = 'Failed to retrieve user info from Google.';
            if ($userinfoCurlError) {
                $msg .= ' cURL Error: ' . $userinfoCurlError;
            } elseif (isset($userInfo['error'])) {
                $msg .= ' Google Error: ' . $userInfo['error'] . ' - ' . ($userInfo['error_description'] ?? '');
            } else {
                $msg .= ' Raw response: ' . substr(strip_tags((string)$userInfoResponse), 0, 150);
            }
            setFlash('danger', $msg);
            redirect(url('login'));
        }

        $googleId = $userInfo['sub'];
        $email = $userInfo['email'];
        $name = $userInfo['name'];
        $avatar = $userInfo['picture'] ?? null;

        $userModel = new User();
        
        // 1. Try find by google_id
        $user = $userModel->findByGoogleId($googleId);
        
        if (!$user) {
            // 2. Try find by email
            $user = $userModel->findByEmail($email);
            
            if ($user) {
                // Link account
                $userModel->update($user['id'], ['google_id' => $googleId]);
            } else {
                // Create account
                $userId = $userModel->create([
                    'name' => $name,
                    'email' => $email,
                    'password' => bin2hex(random_bytes(16)), // Dummy password
                    'google_id' => $googleId,
                    'email_verified' => 1,
                    'avatar' => $avatar
                ]);
                $user = $userModel->find($userId);

                // Sync and log to CRM + trigger signup automation
                try {
                    $subModel = new MarketingSubscriber();
                    $subModel->syncSubscriberCategory($user['email']);
                    $sub = $subModel->findByEmail($user['email']);
                    if ($sub) {
                        $subModel->logTimelineActivity($sub['id'], 'Registered', "Registered a new user account via Google.");
                        MarketingSubscriber::triggerAutomation('user_signup', ['email' => $user['email']]);
                    }
                } catch (Exception $e) {}
            }
        }

        if ($user['status'] === 'banned') {
            setFlash('danger', 'Your account has been suspended.');
            redirect(url('login'));
        }

        $userModel->setSession($user);
        
        // Transfer guest cart
        $cart = new Cart();
        $cart->transferToUser($user['id']);
        updateCartCount();

        setFlash('success', 'Successfully logged in with Google!');
        redirect(url('user/dashboard'));
    }
}

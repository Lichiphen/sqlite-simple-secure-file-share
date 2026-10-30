<?php
/**
 * Simple Secure File Share - Authentication Handler
 * 
 * @package SimpleSecureFileShare
 * @author  AI Generator, Direction: Lichiphen
 * @license Lichiphen Proprietary License v1.0
 * @link    https://gitlab.com/lichiphen/sqlite-simple-secure-file-share
 */

// Prevent direct access with cute error page
if (!defined('BASE_PATH')) {
    require_once __DIR__ . '/no-direct-access.php';
    showNoDirectAccessError();
}
class Auth {

    /** 初期値や推測容易なパスワード */
    private const WEAK_PASSWORDS = ['admin', 'password', 'changeme', 'your-secure-password', '123456', 'administrator'];

    /**
     * Start session if not already started
     */
    public static function startSession(): void {
        if (session_status() === PHP_SESSION_NONE) {
            // Ensure save path exists and is writable
            $savePath = ini_get('session.save_path');
            if ($savePath && !is_dir($savePath)) {
                @mkdir($savePath, 0700, true);
            }
            session_start();
        }
    }

    /**
     * Check if admin is logged in
     */
    public static function isAdminLoggedIn(): bool {
        self::startSession();

        if (empty($_SESSION['admin_authenticated']) || $_SESSION['admin_authenticated'] !== true) {
            return false;
        }

        // Check session expiry (absolute lifetime)
        if (!isset($_SESSION['admin_login_time']) || time() - (int) $_SESSION['admin_login_time'] > SESSION_LIFETIME) {
            self::logoutAdmin();
            return false;
        }

        // 設定のパスワードが変更されたら既存セッションを無効化
        if (!isset($_SESSION['admin_credential']) || !hash_equals(self::credentialFingerprint(), (string) $_SESSION['admin_credential'])) {
            self::logoutAdmin();
            return false;
        }

        return true;
    }

    /**
     * 管理者パスワード設定が安全かどうか
     */
    public static function isAdminPasswordConfigured(): bool {
        if (defined('ADMIN_PASSWORD_HASH') && is_string(ADMIN_PASSWORD_HASH) && ADMIN_PASSWORD_HASH !== '') {
            return !empty(password_get_info(ADMIN_PASSWORD_HASH)['algo']);
        }

        $plain = defined('ADMIN_PASSWORD') ? (string) ADMIN_PASSWORD : '';
        return mb_strlen($plain) >= 12 && !in_array(strtolower($plain), self::WEAK_PASSWORDS, true);
    }

    /**
     * 管理者ログインがロックされているか
     */
    public static function isAdminLoginLocked(): bool {
        return Security::isRateLimited(self::loginBucket(), LOGIN_MAX_ATTEMPTS, LOGIN_LOCKOUT_SECONDS);
    }

    private static function loginBucket(): string {
        return 'admin-login|' . Security::clientIp();
    }

    /**
     * 設定中の資格情報の指紋（パスワード変更検知用）
     */
    private static function credentialFingerprint(): string {
        $material = (defined('ADMIN_PASSWORD_HASH') && ADMIN_PASSWORD_HASH !== '')
            ? 'hash:' . ADMIN_PASSWORD_HASH
            : 'plain:' . (defined('ADMIN_PASSWORD') ? ADMIN_PASSWORD : '');
        return Security::hmac('admin-credential|' . $material);
    }

    /**
     * Login admin
     */
    public static function loginAdmin(string $password): bool {
        self::startSession();

        if (!self::isAdminPasswordConfigured() || self::isAdminLoginLocked()) {
            return false;
        }

        if (defined('ADMIN_PASSWORD_HASH') && ADMIN_PASSWORD_HASH !== '') {
            $valid = password_verify($password, ADMIN_PASSWORD_HASH);
        } else {
            // 長さに依存しない比較のため両辺をHMAC化してから比較
            $valid = hash_equals(
                Security::hmac('admin-pw|' . ADMIN_PASSWORD),
                Security::hmac('admin-pw|' . $password)
            );
        }

        if (!$valid) {
            Security::registerFailure(self::loginBucket(), LOGIN_LOCKOUT_SECONDS);
            return false;
        }

        Security::clearFailures(self::loginBucket());

        // セッション固定化対策: ID再生成後に認証情報を格納
        session_regenerate_id(true);
        $_SESSION = [];
        $_SESSION['admin_authenticated'] = true;
        $_SESSION['admin_login_time'] = time();
        $_SESSION['admin_credential'] = self::credentialFingerprint();
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        return true;
    }

    /**
     * Logout admin
     */
    public static function logoutAdmin(): void {
        self::startSession();

        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', [
                'expires'  => time() - 42000,
                'path'     => $params['path'],
                'domain'   => $params['domain'],
                'secure'   => $params['secure'],
                'httponly' => $params['httponly'],
                'samesite' => $params['samesite'] ?? 'Strict',
            ]);
        }

        session_destroy();
    }

    /**
     * Require admin login (redirect if not logged in)
     */
    public static function requireAdmin(string $adminPath = 'admin'): void {
        if (!self::isAdminLoggedIn()) {
            if (self::isAjaxRequest()) {
                http_response_code(401);
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                exit;
            }
            header('Location: ' . BASE_URL . '/' . $adminPath . '/login');
            exit;
        }
    }

    private static function isAjaxRequest(): bool {
        return strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'xmlhttprequest';
    }

    // =========================================================================
    // 共有ページのパスワード認証
    // =========================================================================

    private static function shareCookieName(string $token): string {
        return 'sfs_s_' . $token;
    }

    private static function shareCookiePath(string $token): string {
        return BASE_URL_PATH . '/s/' . $token;
    }

    private static function sharePurpose(int $shareId, string $token, string $passwordHash): string {
        // パスワードハッシュを含めることで、パスワード変更時に既存Cookieを失効させる
        return 'share-auth|' . $shareId . '|' . $token . '|' . $passwordHash;
    }

    /**
     * Check share password authentication via cookie
     */
    public static function isShareAuthenticated(string $token, int $shareId, string $passwordHash): bool {
        $cookie = $_COOKIE[self::shareCookieName($token)] ?? null;
        return Security::verifyWithExpiry(self::sharePurpose($shareId, $token, $passwordHash), is_string($cookie) ? $cookie : null);
    }

    /**
     * 共有パスワード試行がロックされているか
     */
    public static function isShareLocked(string $token): bool {
        return Security::isRateLimited(self::shareBucket($token), SHARE_MAX_ATTEMPTS, LOGIN_LOCKOUT_SECONDS)
            || Security::isRateLimited(self::shareIpBucket(), SHARE_MAX_ATTEMPTS * 5, LOGIN_LOCKOUT_SECONDS);
    }

    private static function shareBucket(string $token): string {
        return 'share|' . Security::clientIp() . '|' . $token;
    }

    private static function shareIpBucket(): string {
        return 'share-ip|' . Security::clientIp();
    }

    /**
     * Verify share password and set cookie
     */
    public static function authenticateShare(string $password, string $storedHash, string $token, int $shareId): bool {
        if (self::isShareLocked($token)) {
            return false;
        }

        if (!password_verify($password, $storedHash)) {
            Security::registerFailure(self::shareBucket($token), LOGIN_LOCKOUT_SECONDS);
            Security::registerFailure(self::shareIpBucket(), LOGIN_LOCKOUT_SECONDS);
            return false;
        }

        Security::clearFailures(self::shareBucket($token));

        $expires = time() + AUTH_COOKIE_LIFETIME;
        setcookie(self::shareCookieName($token), Security::signWithExpiry(self::sharePurpose($shareId, $token, $storedHash), $expires), [
            'expires'  => $expires,
            'path'     => self::shareCookiePath($token),
            'domain'   => '',
            'secure'   => Security::isHttps(),
            'httponly' => true,
            'samesite' => 'Lax',
        ]);

        return true;
    }

    // =========================================================================
    // ダウンロードリンク署名（直リンク防止）
    // =========================================================================

    public static function downloadSignature(string $token): string {
        // 10分単位で丸め、同一ページ内のリンクを安定させる
        $expires = (int) (ceil(time() / 600) * 600) + DOWNLOAD_LINK_LIFETIME;
        return Security::signWithExpiry('download|' . $token, $expires);
    }

    public static function verifyDownloadSignature(string $token, ?string $signature): bool {
        return Security::verifyWithExpiry('download|' . $token, $signature);
    }

    // =========================================================================
    // CSRF
    // =========================================================================

    /**
     * Generate CSRF token
     */
    public static function generateCsrfToken(): string {
        self::startSession();

        if (!isset($_SESSION['csrf_token']) || !is_string($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf_token'];
    }

    /**
     * Verify CSRF token
     */
    public static function verifyCsrfToken(string $token): bool {
        self::startSession();

        return isset($_SESSION['csrf_token']) && is_string($_SESSION['csrf_token'])
            && $token !== '' && hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Get session statistics
     */
    public static function getSessionStats(): array {
        $savePath = ini_get('session.save_path');
        if (!$savePath || !is_dir($savePath)) {
            return ['count' => 0, 'size' => 0];
        }

        $count = 0;
        $size = 0;
        $files = glob($savePath . '/sess_*');
        if ($files) {
            foreach ($files as $file) {
                 if (is_file($file)) {
                     $count++;
                     $size += filesize($file);
                 }
            }
        }

        return ['count' => $count, 'size' => $size];
    }

    /**
     * Clean up expired sessions
     */
    public static function cleanupSessions(): int {
        $savePath = ini_get('session.save_path');
        if (!$savePath || !is_dir($savePath)) {
            return 0;
        }

        $lifetime = max(SESSION_LIFETIME, 600);
        
        $deleted = 0;
        $now = time();
        
        $files = glob($savePath . '/sess_*');
        if ($files) {
            foreach ($files as $file) {
                if (is_file($file)) {
                    // 現在のセッションは削除しない
                    if (basename($file) === 'sess_' . session_id()) {
                        continue;
                    }
                    if ($now - filemtime($file) > $lifetime) {
                        @unlink($file);
                        $deleted++;
                    }
                }
            }
        }
        Security::cleanupRateLimits();
        return $deleted;
    }
    
    /**
     * Set flash message
     */
    public static function setFlash(string $message): void {
        self::startSession();
        $_SESSION['flash_message'] = $message;
    }
    
    /**
     * Get and clear flash message
     */
    public static function getFlash(): ?string {
        self::startSession();
        $message = $_SESSION['flash_message'] ?? null;
        unset($_SESSION['flash_message']);
        return $message;
    }
}

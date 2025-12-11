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
    
    /**
     * Start session if not already started
     */
    public static function startSession(): void {
        if (session_status() === PHP_SESSION_NONE) {
            // Ensure save path exists and is writable
            $savePath = ini_get('session.save_path');
            if ($savePath && !is_dir($savePath)) {
                @mkdir($savePath, 0755, true);
            }
            session_start();
        }
    }
    
    /**
     * Check if admin is logged in
     */
    public static function isAdminLoggedIn(): bool {
        self::startSession();
        
        // Check session expiry
        if (isset($_SESSION['admin_login_time'])) {
            if (time() - $_SESSION['admin_login_time'] > SESSION_LIFETIME) {
                self::logoutAdmin();
                return false;
            }
        }
        
        return isset($_SESSION['admin_authenticated']) && $_SESSION['admin_authenticated'] === true;
    }
    
    /**
     * Login admin
     */
    public static function loginAdmin(string $password): bool {
        self::startSession();
        
        if ($password === ADMIN_PASSWORD) {
            $_SESSION['admin_authenticated'] = true;
            $_SESSION['admin_login_time'] = time();
            // Regenerate session ID for security, but keep session data
            session_regenerate_id(true);
            // Force session write
            session_write_close();
            session_start();
            return true;
        }
        
        return false;
    }
    
    /**
     * Logout admin
     */
    public static function logoutAdmin(): void {
        self::startSession();
        
        $_SESSION = [];
        
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }
        
        session_destroy();
    }
    
    /**
     * Require admin login (redirect if not logged in)
     */
    public static function requireAdmin(string $adminPath = 'admin'): void {
        if (!self::isAdminLoggedIn()) {
            header('Location: ' . BASE_URL . '/' . $adminPath . '/login');
            exit;
        }
    }
    
    /**
     * Check share password authentication via cookie
     */
    public static function isShareAuthenticated(string $token, int $shareId): bool {
        $cookieName = 'sfs_auth_' . $token;
        $expectedToken = self::generateAuthToken($shareId, $token);
        
        return isset($_COOKIE[$cookieName]) && $_COOKIE[$cookieName] === $expectedToken;
    }
    
    /**
     * Verify share password and set cookie
     */
    public static function authenticateShare(string $password, string $storedHash, string $token, int $shareId): bool {
        if (password_verify($password, $storedHash)) {
            $cookieName = 'sfs_auth_' . $token;
            $authToken = self::generateAuthToken($shareId, $token);
            
            setcookie(
                $cookieName,
                $authToken,
                time() + AUTH_COOKIE_LIFETIME,
                '/',
                '',
                isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
                true
            );
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Generate auth token for share
     */
    private static function generateAuthToken(int $shareId, string $token): string {
        return hash('sha256', $shareId . $token . 'sfs_secret');
    }
    
    /**
     * Generate CSRF token
     */
    public static function generateCsrfToken(): string {
        self::startSession();
        
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        
        return $_SESSION['csrf_token'];
    }
    
    /**
     * Verify CSRF token
     */
    public static function verifyCsrfToken(string $token): bool {
        self::startSession();
        
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
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

        $lifetime = SESSION_LIFETIME;
        // Make sure lifetime is reasonable (e.g. at least 1 hour)
        if ($lifetime < 3600) $lifetime = 3600;
        
        $deleted = 0;
        $now = time();
        
        $files = glob($savePath . '/sess_*');
        if ($files) {
            foreach ($files as $file) {
                if (is_file($file)) {
                    if ($now - filemtime($file) > $lifetime) {
                        @unlink($file);
                        $deleted++;
                    }
                }
            }
        }
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

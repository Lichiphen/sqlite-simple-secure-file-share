<?php
/**
 * Simple Secure File Share - Security Helpers
 *
 * HTTPヘッダー / CSP nonce / HMAC署名 / レート制限 / パス検証 を集約する。
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

class Security
{
    private static ?string $nonce = null;
    private static ?string $secret = null;

    /**
     * CSP nonce (1リクエストにつき1つ)
     */
    public static function nonce(): string
    {
        if (self::$nonce === null) {
            self::$nonce = rtrim(strtr(base64_encode(random_bytes(18)), '+/', '-_'), '=');
        }
        return self::$nonce;
    }

    /**
     * nonce属性を出力するヘルパー
     */
    public static function nonceAttr(): string
    {
        return 'nonce="' . self::nonce() . '"';
    }

    /**
     * HTTPS判定
     */
    public static function isHttps(): bool
    {
        if (!empty($_SERVER['HTTPS']) && strtolower((string) $_SERVER['HTTPS']) !== 'off') {
            return true;
        }
        if ((string) ($_SERVER['SERVER_PORT'] ?? '') === '443') {
            return true;
        }
        return defined('BASE_URL') && strpos(BASE_URL, 'https://') === 0;
    }

    /**
     * 全レスポンス共通のセキュリティヘッダー
     */
    public static function sendHeaders(): void
    {
        if (headers_sent()) {
            return;
        }

        $nonce = self::nonce();
        $csp = implode('; ', [
            "default-src 'self'",
            "script-src 'self' 'nonce-{$nonce}'",
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com",
            "font-src 'self' https://fonts.gstatic.com",
            "img-src 'self' data: blob:",
            "connect-src 'self'",
            "form-action 'self'",
            "frame-ancestors 'none'",
            "base-uri 'none'",
            "object-src 'none'",
        ]);

        header('Content-Security-Policy: ' . $csp);
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: DENY');
        header('Referrer-Policy: same-origin');
        header('Cross-Origin-Opener-Policy: same-origin');
        header('Permissions-Policy: camera=(), microphone=(), geolocation=(), payment=()');
        header('X-Robots-Tag: noindex, nofollow');
        if (self::isHttps()) {
            header('Strict-Transport-Security: max-age=31536000');
        }
        header_remove('X-Powered-By');
    }

    /**
     * 動的ページはキャッシュさせない
     */
    public static function sendNoCacheHeaders(): void
    {
        if (headers_sent()) {
            return;
        }
        header('Cache-Control: no-store, no-cache, must-revalidate, private, max-age=0');
        header('Pragma: no-cache');
        header('Expires: 0');
    }

    /**
     * <script> 内に安全に埋め込める JSON 文字列を返す
     */
    public static function js($value): string
    {
        return (string) json_encode($value, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE);
    }

    // =========================================================================
    // 秘密鍵 / HMAC
    // =========================================================================

    /**
     * インストール固有の秘密鍵 (初回に storage/.app_secret を生成)
     */
    public static function secret(): string
    {
        if (self::$secret !== null) {
            return self::$secret;
        }

        if (defined('APP_SECRET') && is_string(APP_SECRET) && strlen(APP_SECRET) >= 32) {
            return self::$secret = APP_SECRET;
        }

        $file = STORAGE_PATH . '/.app_secret';
        if (is_file($file)) {
            $value = trim((string) file_get_contents($file));
            if (strlen($value) >= 64) {
                return self::$secret = $value;
            }
        }

        self::ensureDirectory(STORAGE_PATH);
        $value = bin2hex(random_bytes(32));
        $fp = fopen($file, 'c+');
        if ($fp === false) {
            throw new RuntimeException('Unable to create application secret.');
        }
        flock($fp, LOCK_EX);
        $existing = trim((string) stream_get_contents($fp));
        if (strlen($existing) >= 64) {
            $value = $existing; // 別リクエストが先に生成済み
        } else {
            ftruncate($fp, 0);
            rewind($fp);
            fwrite($fp, $value);
            fflush($fp);
        }
        flock($fp, LOCK_UN);
        fclose($fp);
        @chmod($file, 0600);

        return self::$secret = $value;
    }

    public static function hmac(string $data): string
    {
        return hash_hmac('sha256', $data, self::secret());
    }

    /**
     * 有効期限付き署名 "expires.signature" を生成
     */
    public static function signWithExpiry(string $purpose, int $expires): string
    {
        return $expires . '.' . self::hmac($purpose . '|' . $expires);
    }

    /**
     * 有効期限付き署名を検証
     */
    public static function verifyWithExpiry(string $purpose, ?string $value): bool
    {
        if (!is_string($value) || !preg_match('/^(\d{1,12})\.([a-f0-9]{64})$/', $value, $m)) {
            return false;
        }
        $expires = (int) $m[1];
        if ($expires < time()) {
            return false;
        }
        return hash_equals(self::hmac($purpose . '|' . $expires), $m[2]);
    }

    // =========================================================================
    // 入力検証 / パス
    // =========================================================================

    /**
     * 共有トークン形式 (32桁hex)
     */
    public static function isValidToken(string $token): bool
    {
        return (bool) preg_match('/^[a-f0-9]{32}$/', $token);
    }

    /**
     * 保存ファイル名がディレクトリ区切り等を含まないか
     */
    public static function isSafeFileName(string $name): bool
    {
        return $name !== ''
            && strlen($name) <= 255
            && $name[0] !== '.'
            && strpbrk($name, "/\\\0") === false
            && basename($name) === $name;
    }

    /**
     * $path の実体が $baseDir 配下にあるか確認し、実パスを返す
     */
    public static function resolveWithin(string $baseDir, string $path): ?string
    {
        $realBase = realpath($baseDir);
        $realPath = realpath($path);
        if ($realBase === false || $realPath === false) {
            return null;
        }
        $realBase = rtrim($realBase, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        return strpos($realPath, $realBase) === 0 ? $realPath : null;
    }

    /**
     * 表示用テキストから制御文字・双方向制御文字を除去
     */
    public static function cleanText(string $text, int $maxLength): string
    {
        if (!mb_check_encoding($text, 'UTF-8')) {
            $text = mb_convert_encoding($text, 'UTF-8', 'UTF-8');
        }
        $text = preg_replace('/[\p{Cc}\x{200E}\x{200F}\x{202A}-\x{202E}\x{2066}-\x{2069}]/u', '', $text) ?? '';
        $text = trim($text);
        return mb_substr($text, 0, $maxLength);
    }

    /**
     * 外部から直接アクセスできないディレクトリを作成
     */
    public static function ensureDirectory(string $dir): void
    {
        if (!is_dir($dir) && !@mkdir($dir, 0750, true) && !is_dir($dir)) {
            throw new RuntimeException('Unable to create directory.');
        }

        $htaccess = $dir . '/.htaccess';
        if (!is_file($htaccess)) {
            @file_put_contents($htaccess, implode("\n", [
                '# Deny all direct access (generated by Simple Secure File Share)',
                '<IfModule mod_authz_core.c>',
                '    Require all denied',
                '</IfModule>',
                '<IfModule !mod_authz_core.c>',
                '    Order Deny,Allow',
                '    Deny from all',
                '</IfModule>',
                'Options -Indexes -ExecCGI',
                '<IfModule mod_php.c>',
                '    php_flag engine off',
                '</IfModule>',
                '<IfModule mod_php7.c>',
                '    php_flag engine off',
                '</IfModule>',
                '',
            ]));
        }
        if (!is_file($dir . '/index.html')) {
            @file_put_contents($dir . '/index.html', '');
        }
    }

    // =========================================================================
    // レート制限 (ファイルベース)
    // =========================================================================

    public static function clientIp(): string
    {
        // X-Forwarded-For は偽装可能なため使用しない
        return (string) ($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0');
    }

    private static function rateLimitFile(string $bucket): string
    {
        $dir = STORAGE_PATH . '/ratelimit';
        self::ensureDirectory($dir);
        return $dir . '/' . hash('sha256', $bucket) . '.json';
    }

    /**
     * 直近 $window 秒間の失敗回数を返す
     */
    private static function failures(string $bucket, int $window): array
    {
        $file = self::rateLimitFile($bucket);
        if (!is_file($file)) {
            return [];
        }
        $data = json_decode((string) @file_get_contents($file), true);
        if (!is_array($data)) {
            return [];
        }
        $threshold = time() - $window;
        return array_values(array_filter($data, function ($ts) use ($threshold) {
            return is_int($ts) && $ts > $threshold;
        }));
    }

    public static function isRateLimited(string $bucket, int $maxAttempts, int $window): bool
    {
        return count(self::failures($bucket, $window)) >= $maxAttempts;
    }

    public static function registerFailure(string $bucket, int $window): void
    {
        $file = self::rateLimitFile($bucket);
        $fp = @fopen($file, 'c+');
        if ($fp === false) {
            return;
        }
        flock($fp, LOCK_EX);
        $data = json_decode((string) stream_get_contents($fp), true);
        $threshold = time() - $window;
        $data = is_array($data) ? array_values(array_filter($data, function ($ts) use ($threshold) {
            return is_int($ts) && $ts > $threshold;
        })) : [];
        $data[] = time();
        $data = array_slice($data, -100);
        ftruncate($fp, 0);
        rewind($fp);
        fwrite($fp, json_encode($data));
        fflush($fp);
        flock($fp, LOCK_UN);
        fclose($fp);
    }

    public static function clearFailures(string $bucket): void
    {
        $file = self::rateLimitFile($bucket);
        if (is_file($file)) {
            @unlink($file);
        }
    }

    /**
     * 古いレート制限ファイルを削除
     */
    public static function cleanupRateLimits(int $maxAge = 86400): void
    {
        $files = glob(STORAGE_PATH . '/ratelimit/*.json') ?: [];
        foreach ($files as $file) {
            if (is_file($file) && time() - (int) filemtime($file) > $maxAge) {
                @unlink($file);
            }
        }
    }
}

<?php
/**
 * Simple Secure File Share - System Bootstrap
 *
 * システム設定（config.php のユーザー設定以外）を担当する。
 * アップデートでは config.php が保持されるため、セキュリティ上重要な初期化は
 * すべてこのファイルで行い、旧 config.php を使い続けても安全側に倒す。
 *
 * @package SimpleSecureFileShare
 * @author  AI Generator, Direction: Lichiphen
 * @license Lichiphen Proprietary License v1.0
 * @link    https://gitlab.com/lichiphen/sqlite-simple-secure-file-share
 */

if (!defined('ADMIN_URL_PATH')) {
    http_response_code(403);
    exit;
}

if (defined('SFS_BOOTSTRAPPED')) {
    return;
}
define('SFS_BOOTSTRAPPED', true);

if (!preg_match('/^[A-Za-z0-9_-]{1,64}$/', (string) ADMIN_URL_PATH)) {
    http_response_code(500);
    exit('Configuration error: ADMIN_URL_PATH may only contain letters, numbers, "-" and "_".');
}

// ============================================================================
// エラー表示 (本番では画面に出さない)
// ============================================================================

error_reporting(E_ALL);
ini_set('display_errors', (defined('APP_DEBUG') && APP_DEBUG) ? '1' : '0');
ini_set('log_errors', '1');

if (!defined('APP_VERSION')) {
    require_once dirname(__DIR__) . '/version.php';
}

date_default_timezone_set(defined('APP_TIMEZONE') ? APP_TIMEZONE : 'Asia/Tokyo');

// ============================================================================
// パス設定
// ============================================================================

if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}
if (!defined('STORAGE_PATH')) {
    define('STORAGE_PATH', BASE_PATH . '/storage');
}
if (!defined('UPLOADS_PATH')) {
    define('UPLOADS_PATH', STORAGE_PATH . '/uploads');
}
if (!defined('DATABASE_PATH')) {
    define('DATABASE_PATH', STORAGE_PATH . '/database.sqlite');
}

// ============================================================================
// Host ヘッダー検証 (Host Header Injection 対策)
// ============================================================================

if (PHP_SAPI !== 'cli') {
    $sfsHost = (string) ($_SERVER['HTTP_HOST'] ?? '');
    $sfsHostPattern = '/^(?:[A-Za-z0-9](?:[A-Za-z0-9-]{0,61}[A-Za-z0-9])?(?:\.[A-Za-z0-9](?:[A-Za-z0-9-]{0,61}[A-Za-z0-9])?)*|\[[0-9A-Fa-f:.]+\])(?::\d{1,5})?$/';
    $sfsHostOk = $sfsHost !== '' && preg_match($sfsHostPattern, $sfsHost) === 1;

    if ($sfsHostOk && defined('TRUSTED_HOSTS') && is_array(TRUSTED_HOSTS) && TRUSTED_HOSTS !== []) {
        $sfsHostName = strtolower(preg_replace('/:\d+$/', '', $sfsHost));
        $sfsHostOk = in_array($sfsHostName, array_map('strtolower', TRUSTED_HOSTS), true);
    }

    if (!$sfsHostOk) {
        http_response_code(400);
        header('Content-Type: text/plain; charset=UTF-8');
        exit('Bad Request');
    }
    unset($sfsHost, $sfsHostPattern, $sfsHostOk, $sfsHostName);
}

// ============================================================================
// URL設定
// ============================================================================

if (!defined('BASE_URL')) {
    if (defined('APP_BASE_URL') && APP_BASE_URL !== '') {
        define('BASE_URL', rtrim(APP_BASE_URL, '/'));
    } else {
        $sfsProtocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $sfsHost = $_SERVER['HTTP_HOST'] ?? 'localhost:8080';
        $sfsScriptDir = '';
        // PHPビルトインサーバーではスクリプトディレクトリを含めない
        if (PHP_SAPI !== 'cli-server') {
            $sfsScriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
            if ($sfsScriptDir === '/' || $sfsScriptDir === '.') {
                $sfsScriptDir = '';
            }
        }
        define('BASE_URL', rtrim($sfsProtocol . '://' . $sfsHost . $sfsScriptDir, '/'));
        unset($sfsProtocol, $sfsHost, $sfsScriptDir);
    }
}

// Cookie の Path 用（例: "/share" / ルート配置時は ""）
if (!defined('BASE_URL_PATH')) {
    define('BASE_URL_PATH', rtrim((string) parse_url(BASE_URL, PHP_URL_PATH), '/'));
}

// ============================================================================
// その他の設定 (未定義時のデフォルト)
// ============================================================================

if (!defined('AUTH_COOKIE_LIFETIME')) {
    define('AUTH_COOKIE_LIFETIME', 3600);
}
if (!defined('SUPPORTED_LANGUAGES')) {
    define('SUPPORTED_LANGUAGES', ['en', 'ja']);
}
if (!defined('DOWNLOAD_LINK_LIFETIME')) {
    define('DOWNLOAD_LINK_LIFETIME', 3600);
}
if (!defined('MAX_FILES_PER_SHARE')) {
    define('MAX_FILES_PER_SHARE', 100);
}
if (!defined('LOGIN_MAX_ATTEMPTS')) {
    define('LOGIN_MAX_ATTEMPTS', 5);
}
if (!defined('LOGIN_LOCKOUT_SECONDS')) {
    define('LOGIN_LOCKOUT_SECONDS', 900);
}
if (!defined('SHARE_MAX_ATTEMPTS')) {
    define('SHARE_MAX_ATTEMPTS', 10);
}

// ============================================================================
// セッション設定 (session_start() はこの後、管理画面でのみ実行)
// ============================================================================

require_once BASE_PATH . '/src/Security.php';

$sfsSessionDir = STORAGE_PATH . '/sessions';
Security::ensureDirectory(STORAGE_PATH);
Security::ensureDirectory($sfsSessionDir);
Security::ensureDirectory(UPLOADS_PATH);
@chmod($sfsSessionDir, 0700);

if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.save_path', $sfsSessionDir);
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    ini_set('session.use_trans_sid', '0');
    ini_set('session.cookie_httponly', '1');
    ini_set('session.cookie_secure', Security::isHttps() ? '1' : '0');
    ini_set('session.cookie_samesite', 'Strict');
    ini_set('session.gc_maxlifetime', (string) max(SESSION_LIFETIME, 600));
    session_name('SFSSESSID');
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => (BASE_URL_PATH === '' ? '' : BASE_URL_PATH) . '/' . ADMIN_URL_PATH,
        'domain'   => '',
        'secure'   => Security::isHttps(),
        'httponly' => true,
        'samesite' => 'Strict',
    ]);
}
unset($sfsSessionDir);

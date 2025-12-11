<?php
/**
 * Simple Secure File Share - Configuration
 * 
 * @package SimpleSecureFileShare
 * @author  AI Generator, Direction: Lichiphen
 * @license Lichiphen Proprietary License v1.0
 * @link    https://gitlab.com/lichiphen/sqlite-simple-secure-file-share
 */

// アプリバージョン（CSS/JSキャッシュバスティング用）
define('APP_VERSION', '1.4.2');

// セキュリティ: 直接アクセス防止（可愛いエラーページを表示）
if (basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
    require_once __DIR__ . '/src/no-direct-access.php';
    showNoDirectAccessError();
}

// タイムゾーン設定
date_default_timezone_set('Asia/Tokyo');

// セッション設定
$session_dir = __DIR__ . '/storage/sessions';
if (!is_dir($session_dir)) {
    mkdir($session_dir, 0755, true);
}
ini_set('session.save_path', $session_dir);
ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);

// ============================================================================
// 管理者設定
// ============================================================================

// 管理者パスワード（本番環境では必ず変更してください）
define('ADMIN_PASSWORD', 'admin');

// 管理画面のURL（セキュリティのため変更を推奨）
// 例: 'admin' → 'my-secret-panel-xyz123'
// 変更後は /my-secret-panel-xyz123 でアクセス
define('ADMIN_URL_PATH', 'admin');

// セッションの有効期限（秒）
define('SESSION_LIFETIME', 3600); // 1時間

// ============================================================================
// パス設定
// ============================================================================

define('BASE_PATH', __DIR__);
define('STORAGE_PATH', BASE_PATH . '/storage');
define('UPLOADS_PATH', STORAGE_PATH . '/uploads');
define('DATABASE_PATH', STORAGE_PATH . '/database.sqlite');

// ============================================================================
// URL設定
// ============================================================================

// ベースURL（自動検出、必要に応じて手動設定）
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost:8080';
// PHPビルトインサーバーではスクリプトディレクトリを含めない
$script_dir = '';
if (php_sapi_name() !== 'cli-server') {
    $script_dir = dirname($_SERVER['SCRIPT_NAME']);
    if ($script_dir === '/' || $script_dir === '\\') {
        $script_dir = '';
    }
}
define('BASE_URL', rtrim($protocol . '://' . $host . $script_dir, '/'));

// ============================================================================
// アップロード設定
// ============================================================================

// 最大アップロードサイズ（バイト）- php.iniの設定も確認してください
define('MAX_UPLOAD_SIZE', 50 * 1024 * 1024); // 50MB

// 許可するファイル拡張子（空配列で全て許可）
define('ALLOWED_EXTENSIONS', []);

// ============================================================================
// Cookie認証設定
// ============================================================================

// パスワード認証のCookie有効期限（秒）
define('AUTH_COOKIE_LIFETIME', 3600); // 1時間

// ============================================================================
// 言語設定
// ============================================================================

// デフォルト言語
define('DEFAULT_LANGUAGE', 'ja');

// 対応言語
define('SUPPORTED_LANGUAGES', ['en', 'ja']);

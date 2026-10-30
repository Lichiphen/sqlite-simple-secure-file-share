<?php
/**
 * Simple Secure File Share - Configuration
 * 
 * @package SimpleSecureFileShare
 * @author  AI Generator, Direction: Lichiphen
 * @license Lichiphen Proprietary License v1.0
 * @link    https://gitlab.com/lichiphen/sqlite-simple-secure-file-share
 */

// ============================================================================
// ★ ユーザー設定（ここを編集してください）★
// ============================================================================

// 管理者パスワード
// ★推奨: ハッシュで設定してください（平文を config.php に置かない）
//   生成方法: php -r "echo password_hash('your-strong-password', PASSWORD_DEFAULT), PHP_EOL;"
//   ADMIN_PASSWORD_HASH を設定すると ADMIN_PASSWORD は無視されます。
// ※ 初期値 'admin' や 12文字未満のパスワードではログインできません（セキュリティ保護）
define('ADMIN_PASSWORD_HASH', '');
define('ADMIN_PASSWORD', 'admin');

// 管理画面のURL（セキュリティのため変更を推奨）
// 例: 'admin' → 'my-secret-panel-xyz123'
// 変更後は /my-secret-panel-xyz123 でアクセス
define('ADMIN_URL_PATH', 'admin');

// 最大アップロードサイズ（バイト）- php.iniの設定も確認してください
// 設定例:
//   50 * 1024 * 1024   = 50MB
//   100 * 1024 * 1024  = 100MB
//   500 * 1024 * 1024  = 500MB
//   1024 * 1024 * 1024 = 1GB
define('MAX_UPLOAD_SIZE', 50 * 1024 * 1024); // 50MB

// アップロードモード:
// 'IMAGE_ONLY'   - 画像ファイルのみ許可 (jpg, png, gif, webp, heic, avif, tiff)
// 'ARCHIVE_ONLY' - 圧縮ファイルのみ許可 (zip, 7z, rar, tar, gz)
// 'ALL'          - すべてのファイルを許可（デフォルト）
// 'CUSTOM'       - 下記のCUSTOM_UPLOAD_EXTENSIONSで指定した拡張子のみ許可
define('UPLOAD_MODE', 'ALL');

// CUSTOMモード時に許可する拡張子（カンマ区切り）
// 例: 'jpg,jpeg,png,psd,pdf,zip'
define('CUSTOM_UPLOAD_EXTENSIONS', 'jpg,jpeg,png,psd,pdf,zip');

// セッションの有効期限（秒）
define('SESSION_LIFETIME', 3600); // 1時間

// デフォルト言語 ('en' または 'ja')
define('DEFAULT_LANGUAGE', 'ja');

// 許可するホスト名（Host ヘッダー偽装対策）
// 例: ['files.example.com']  空配列の場合はホスト名の形式チェックのみ
define('TRUSTED_HOSTS', []);

// 公開URLを固定する場合に設定（例: 'https://files.example.com'）
// 空の場合はリクエストから自動検出
define('APP_BASE_URL', '');

// ============================================================================
// ★ 以下はシステム設定です（通常は変更不要）★
// ============================================================================

// バージョン情報を読み込み（version.phpはアップデート時に上書きされます）
require_once __DIR__ . '/version.php';

// セキュリティ: 直接アクセス防止（可愛いエラーページを表示）
if (basename($_SERVER['PHP_SELF'] ?? '') === basename(__FILE__)) {
    define('BASE_PATH', __DIR__);
    require_once __DIR__ . '/src/no-direct-access.php';
    showNoDirectAccessError();
}

// システム初期化（パス・URL・セッション・セキュリティ設定）
require_once __DIR__ . '/src/bootstrap.php';

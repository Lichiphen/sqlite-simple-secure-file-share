# Simple Secure File Share

A simple, secure, and self-hosted file sharing system with password protection.

[![License: Lichiphen Proprietary](https://img.shields.io/badge/License-Lichiphen%20Proprietary-purple.svg)](LICENSE)

## Features

- 🔒 **Password Protection** - Secure your shared files with optional password
- 📊 **Download Counter** - Track how many times files have been downloaded
- 📦 **ZIP Download** - Download multiple files as a single ZIP archive
- � **Upload Mode Control** - Restrict uploads to images only, archives only, or custom extensions
- �🛡️ **Direct Link Prevention** - Prevent unauthorized access via direct URLs
- 🔐 **Hidden Admin URL** - Customize admin panel URL to hide from attackers
- 🌙 **Dark Mode Support** - Automatic theme switching based on system preference
- 🌐 **Multi-language** - English and Japanese support
- 🐱 **Cute Error Pages** - Friendly error pages with cat mascot animations

## Requirements

- PHP 7.4 or higher
- SQLite3 extension enabled
- Web server (Apache, Nginx, etc.)

## Installation

1. Download or clone this repository
2. Upload all files to your web server
3. Access `/admin` and login with the default password: `admin`
4. **Change the default password immediately** in `config.php`

> **Note**: The `storage/` and `database/` directories are created automatically. If you encounter permission errors, check that your web server has write access to these directories.

## Configuration

Edit `config.php` to customize:

```php
// Admin password (CHANGE THIS!)
define('ADMIN_PASSWORD', 'your-secure-password');

// Custom admin URL (recommended for security)
// Example: 'admin' → 'my-secret-panel-xyz123'
define('ADMIN_URL_PATH', 'my-secret-panel');

// Maximum upload size (in bytes)
define('MAX_UPLOAD_SIZE', 100 * 1024 * 1024); // 100MB
// Examples:
//   50 * 1024 * 1024   = 50MB
//   100 * 1024 * 1024  = 100MB
//   500 * 1024 * 1024  = 500MB
//   1024 * 1024 * 1024 = 1GB

// Upload mode:
// 'IMAGE_ONLY'   - Only image files (jpg, png, gif, webp, heic, avif, tiff)
// 'ARCHIVE_ONLY' - Only archive files (zip, 7z, rar, tar, gz)
// 'ALL'          - All files allowed (default)
// 'CUSTOM'       - Only extensions specified in CUSTOM_UPLOAD_EXTENSIONS
define('UPLOAD_MODE', 'ALL');

// Custom allowed extensions (comma-separated, used when UPLOAD_MODE is 'CUSTOM')
// Example: 'jpg,jpeg,png,psd,pdf,zip'
define('CUSTOM_UPLOAD_EXTENSIONS', 'jpg,jpeg,png,psd,pdf,zip');

// Session lifetime (in seconds)
define('SESSION_LIFETIME', 3600); // 1 hour

// Default language ('en' or 'ja')
define('DEFAULT_LANGUAGE', 'ja');
```

> **Note**: For large file uploads, you may also need to adjust your server's `php.ini` settings:
> - `upload_max_filesize`
> - `post_max_size`
> - `max_execution_time`


## Updating

⚠️ **Important**: When updating, **DO NOT overwrite** these folders:
- `storage/` - Contains all uploaded files
- `database/` - Contains share information and settings

1. Backup your current installation
2. Upload new files (excluding `storage/` and `database/`)
3. Verify the admin panel and existing share links work correctly

## Directory Structure

```
simple-share/
├── assets/          # CSS and JavaScript files
├── database/        # SQLite database (auto-created)
├── lang/            # Language files (en.php, ja.php)
├── src/             # PHP source files
├── storage/         # Uploaded files (auto-created)
├── templates/       # HTML templates
├── config.php       # Configuration file
└── index.php        # Main entry point
```

## Security Features

- Passwords are hashed using bcrypt
- Direct file access is blocked via .htaccess
- Cookie-based authentication with expiration
- Referer check for download requests
- Customizable admin URL path to prevent discovery
- HTTPS redirect support (enable in .htaccess)
- Cute but firm error pages for unauthorized access attempts

## Changelog

### v1.4.2

- 🐛 **Bug Fix**: Fixed session cleanup button causing 404 error when using custom admin URL path
- 🔧 **Improvement**: All admin redirects now properly respect `ADMIN_URL_PATH` configuration

### v1.4.1

- 🛡️ Direct link prevention for downloads
- 🔐 Hidden admin URL feature (`ADMIN_URL_PATH`)
- 🐱 Cute error pages with cat mascot animations
- 🚫 Access denied page for unauthorized download attempts

## License

This project is licensed under the Lichiphen Proprietary License v1.0 - see the [LICENSE](LICENSE) file for details.

## Credits

- **Direction**: Lichiphen
- **Development**: AI Generator

---

# Simple Secure File Share（日本語）

シンプルで安全なセルフホスト型ファイル共有システムです。

## 特徴

- 🔒 **パスワード保護** - 共有ファイルをパスワードで保護
- 📊 **ダウンロードカウンター** - ダウンロード回数を自動追跡
- 📦 **ZIPダウンロード** - 複数ファイルをZIPで一括ダウンロード
- � **アップロードモード制限** - 画像のみ、圧縮ファイルのみ、またはカスタム拡張子に制限可能
- �🛡️ **直リンク防止** - URLの直接アクセスによる不正ダウンロードを防止
- 🔐 **管理画面URL隠蔽** - 管理パネルのURLをカスタマイズして攻撃者から隠蔽
- 🌙 **ダークモード対応** - システム設定に応じた自動テーマ切り替え
- 🌐 **多言語対応** - 英語・日本語をサポート
- 🐱 **可愛いエラーページ** - 猫マスコットアニメーション付きのフレンドリーなエラーページ

## 動作要件

- PHP 7.4 以上
- SQLite3 拡張機能が有効
- Webサーバー（Apache, Nginx など）

## インストール

1. リポジトリをダウンロードまたはクローン
2. 全ファイルをWebサーバーにアップロード
3. `/admin` にアクセスし、デフォルトパスワード `admin` でログイン
4. **すぐに `config.php` でパスワードを変更してください**

> **補足**: `storage/` と `database/` ディレクトリは自動作成されます。エラーが出る場合は、これらのディレクトリに書き込み権限があるか確認してください。

## 設定

`config.php` を編集してカスタマイズ：

```php
// 管理者パスワード（必ず変更してください！）
define('ADMIN_PASSWORD', 'your-secure-password');

// 管理画面のURL（セキュリティのため変更を推奨）
// 例: 'admin' → 'my-secret-panel-xyz123'
define('ADMIN_URL_PATH', 'my-secret-panel');

// 最大アップロードサイズ（バイト単位）
define('MAX_UPLOAD_SIZE', 100 * 1024 * 1024); // 100MB
// 設定例:
//   50 * 1024 * 1024   = 50MB
//   100 * 1024 * 1024  = 100MB
//   500 * 1024 * 1024  = 500MB
//   1024 * 1024 * 1024 = 1GB

// アップロードモード:
// 'IMAGE_ONLY'   - 画像ファイルのみ許可 (jpg, png, gif, webp, heic, avif, tiff)
// 'ARCHIVE_ONLY' - 圧縮ファイルのみ許可 (zip, 7z, rar, tar, gz)
// 'ALL'          - すべてのファイルを許可（デフォルト）
// 'CUSTOM'       - 下記のCUSTOM_UPLOAD_EXTENSIONSで指定した拡張子のみ許可
define('UPLOAD_MODE', 'ALL');

// CUSTOMモード時に許可する拡張子（カンマ区切り）
// 例: 'jpg,jpeg,png,psd,pdf,zip'
define('CUSTOM_UPLOAD_EXTENSIONS', 'jpg,jpeg,png,psd,pdf,zip');

// セッション有効時間（秒単位）
define('SESSION_LIFETIME', 3600); // 1時間

// デフォルト言語 ('en' または 'ja')
define('DEFAULT_LANGUAGE', 'ja');
```

> **補足**: 大容量ファイルをアップロードする場合は、サーバーの `php.ini` 設定も調整が必要な場合があります：
> - `upload_max_filesize`（アップロード可能な最大ファイルサイズ）
> - `post_max_size`（POSTデータの最大サイズ）
> - `max_execution_time`（スクリプト実行時間の上限）

## アップデート方法

⚠️ **重要**: アップデート時、以下のフォルダは**絶対に上書きしないでください**：
- `storage/` - アップロードされた全ファイルが保存されています
- `database/` - 共有情報や設定が保存されています

1. 現在のインストールをバックアップ
2. 新しいファイルをアップロード（`storage/` と `database/` を除く）
3. 管理画面と既存の共有リンクが正常に動作することを確認

## ディレクトリ構成

```
simple-share/
├── assets/          # CSSとJavaScriptファイル
├── database/        # SQLiteデータベース（自動作成）
├── lang/            # 言語ファイル（en.php, ja.php）
├── src/             # PHPソースファイル
├── storage/         # アップロードファイル（自動作成）
├── templates/       # HTMLテンプレート
├── config.php       # 設定ファイル
└── index.php        # メインエントリーポイント
```

## セキュリティ機能

- パスワードはbcryptでハッシュ化
- .htaccessによる直接ファイルアクセスのブロック
- 有効期限付きCookie認証
- ダウンロードリクエストのリファラーチェック
- 管理画面URLのカスタマイズで発見を防止
- HTTPSリダイレクト対応（.htaccessで有効化）
- 不正アクセス時の可愛くも厳しいエラーページ

## 変更履歴

### v1.4.2

- 🐛 **バグ修正**: カスタム管理URLパス使用時にセッションクリーンアップボタンで404エラーが発生する問題を修正
- 🔧 **改善**: すべての管理画面リダイレクトが `ADMIN_URL_PATH` 設定を正しく反映するように

### v1.4.1

- 🛡️ ダウンロードの直リンク防止機能
- 🔐 管理画面URL隠蔽機能（`ADMIN_URL_PATH`）
- 🐱 猫マスコットアニメーション付きの可愛いエラーページ
- 🚫 不正ダウンロード試行時のアクセス拒否ページ

## ライセンス

このプロジェクトはLichiphen Proprietary License v1.0の下でライセンスされています。詳細は[LICENSE](LICENSE)ファイルをご覧ください。

## クレジット

- **ディレクション**: Lichiphen
- **開発**: AI Generator

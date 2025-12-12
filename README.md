# Simple Secure File Share

A simple, secure, and self-hosted file sharing system with password protection.

[![License: Lichiphen Proprietary](https://img.shields.io/badge/License-Lichiphen%20Proprietary-purple.svg)](LICENSE)

## 📺 Demo Video

[![Demo Video](https://img.youtube.com/vi/QwwJOc5uCb4/0.jpg)](https://youtu.be/QwwJOc5uCb4)

👆 Click the image to watch the demo on YouTube

## Features

- 🔒 **Password Protection** - Secure your shared files with optional password
- 📊 **Download Counter** - Track how many times files have been downloaded
- 📦 **ZIP Download** - Download multiple files as a single ZIP archive
- 🛡️ **Direct Link Prevention** - Prevent unauthorized access via direct URLs
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
//   2 * 1024 * 1024 * 1024 = 2GB

// Session lifetime (in seconds)
define('SESSION_LIFETIME', 3600); // 1 hour
```

> **Note**: For large file uploads, you may also need to adjust your server's `php.ini` settings:
> - `upload_max_filesize`
> - `post_max_size`
> - `max_execution_time`

## Updating

### Download Packages

Two types of ZIP packages are available from [Releases](https://gitlab.com/lichiphen/sqlite-simple-secure-file-share/-/releases):

| Package | Description | Use Case |
|---------|-------------|----------|
| `simple-secure-file-share-vX.X.X-update.zip` | Excludes `config.php`, `storage/`, `database/` | For existing users updating |
| `simple-secure-file-share-vX.X.X-full.zip` | Complete package with all files | For new installations |

### ⚠️ Important: Files to Preserve

When updating, **DO NOT overwrite** these files/folders:
- `config.php` - Your custom configuration (password, admin URL, etc.)
- `storage/` - Contains all uploaded files
- `database/` - Contains share information and settings

### 🔄 Upgrading to v1.5.0 (Breaking Change)

**v1.5.0 introduces `version.php`** - Version info is now separated from `config.php`.

If you're upgrading from v1.4.x or earlier:

1. **Backup your current `config.php` settings** (note down these values):
   - `ADMIN_PASSWORD`
   - `ADMIN_URL_PATH`
   - `MAX_UPLOAD_SIZE`
2. **Upload all new files including `config.php`** (overwrite is OK this time)
3. **Edit the new `config.php`** and restore your settings from step 1

> **Why?** The new `config.php` includes `version.php` require statement and new settings (upload mode, etc.). Future updates won't require this step.

### Update Steps

1. Backup your current installation
2. Download the **update package** from [Releases](https://gitlab.com/lichiphen/sqlite-simple-secure-file-share/-/releases)
3. Extract and upload all files via FTP (these won't include config.php, storage/, database/)
4. Verify the admin panel and existing share links work correctly

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

## Subdirectory Installation (WordPress Coexistence)

If you install this in a subdirectory (e.g., `/simple-secure-file-share/`) alongside WordPress or other CMS:

1. Edit `.htaccess` in the application folder
2. Change `RewriteBase /` to match your subdirectory:

```apache
# Example: installed at /simple-secure-file-share/
RewriteBase /simple-secure-file-share/
```

> **Symptom**: If you see the WordPress "Page not found" error instead of the cat 404 page when accessing `/admin`, this is likely the cause.

## License

This project is licensed under the Lichiphen Proprietary License v1.0 - see the [LICENSE](LICENSE) file for details.

## Credits

- **Direction**: Lichiphen
- **Development**: AI Generator

---

# Simple Secure File Share（日本語）

シンプルで安全なセルフホスト型ファイル共有システムです。

## 📺 デモ動画

[![デモ動画](https://img.youtube.com/vi/QwwJOc5uCb4/0.jpg)](https://youtu.be/QwwJOc5uCb4)

👆 画像をクリックするとYouTubeでデモをご覧いただけます

## 特徴

- 🔒 **パスワード保護** - 共有ファイルをパスワードで保護
- 📊 **ダウンロードカウンター** - ダウンロード回数を自動追跡
- 📦 **ZIPダウンロード** - 複数ファイルをZIPで一括ダウンロード
- 🛡️ **直リンク防止** - URLの直接アクセスによる不正ダウンロードを防止
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
//   2 * 1024 * 1024 * 1024 = 2GB

// セッション有効時間（秒単位）
define('SESSION_LIFETIME', 3600); // 1時間
```

> **補足**: 大容量ファイルをアップロードする場合は、サーバーの `php.ini` 設定も調整が必要な場合があります：
> - `upload_max_filesize`（アップロード可能な最大ファイルサイズ）
> - `post_max_size`（POSTデータの最大サイズ）
> - `max_execution_time`（スクリプト実行時間の上限）

## アップデート方法

### ダウンロードパッケージ

[Releases](https://gitlab.com/lichiphen/sqlite-simple-secure-file-share/-/releases)から2種類のZIPパッケージをダウンロードできます：

| パッケージ | 内容 | 用途 |
|----------|------|------|
| `simple-secure-file-share-vX.X.X-update.zip` | `config.php`, `storage/`, `database/`を除外 | 既存ユーザーのアップデート用 |
| `simple-secure-file-share-vX.X.X-full.zip` | 全ファイル含む完全版 | 新規インストール用 |

### ⚠️ 重要：上書き禁止ファイル

アップデート時、以下のファイル・フォルダは**絶対に上書きしないでください**：
- `config.php` - あなたのカスタム設定（パスワード、管理画面URL等）が含まれています
- `storage/` - アップロードされた全ファイルが保存されています
- `database/` - 共有情報や設定が保存されています

### 🔄 v1.5.0へのアップグレード（重要な変更あり）

**v1.5.0で`version.php`が追加されました** - バージョン情報が`config.php`から分離されました。

v1.4.x以前からアップグレードする場合：

1. **現在の`config.php`の設定値をメモしてください**：
   - `ADMIN_PASSWORD`（管理者パスワード）
   - `ADMIN_URL_PATH`（管理画面URL）
   - `MAX_UPLOAD_SIZE`（最大アップロードサイズ）
2. **新しいファイル一式を`config.php`含めてアップロード**（今回は上書きOK）
3. **新しい`config.php`を編集**し、ステップ1でメモした設定値を復元

> **なぜ？** 新しい`config.php`には`version.php`のrequire文と新機能（アップロードモード等）の設定が含まれています。次回以降のアップデートではこの手順は不要です。

### アップデート手順

1. 現在のインストールをバックアップ
2. [Releases](https://gitlab.com/lichiphen/sqlite-simple-secure-file-share/-/releases)から**アップデート用パッケージ**をダウンロード
3. 解凍してFTPで全ファイルをアップロード（config.php, storage/, database/は含まれていません）
4. 管理画面と既存の共有リンクが正常に動作することを確認

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

## サブディレクトリ配置（WordPress共存）

WordPressなど他のCMSと同じサーバーで、サブディレクトリ（例：`/simple-secure-file-share/`）に配置する場合：

1. アプリケーションフォルダ内の`.htaccess`を編集
2. `RewriteBase /` をサブディレクトリに合わせて変更：

```apache
# 例: /simple-secure-file-share/ に配置した場合
RewriteBase /simple-secure-file-share/
```

> **症状**: `/admin`にアクセスした際、猫の404ページではなくWordPressの「ページが見つかりません」が表示される場合、これが原因です。

## ライセンス

このプロジェクトはLichiphen Proprietary License v1.0の下でライセンスされています。詳細は[LICENSE](LICENSE)ファイルをご覧ください。

## クレジット

- **ディレクション**: Lichiphen
- **開発**: AI Generator

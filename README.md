# Simple Secure File Share

A simple, secure, and self-hosted file sharing system with password protection.

[![GitLab](https://img.shields.io/badge/GitLab-312152?style=for-the-badge&logo=gitlab&logoColor=white)](https://gitlab.com/lichiphen/sqlite-simple-secure-file-share)
[![GitHub](https://img.shields.io/badge/GitHub-181717?style=for-the-badge&logo=github&logoColor=white)](https://github.com/Lichiphen/sqlite-simple-secure-file-share)

[![License: Lichiphen Proprietary](https://img.shields.io/badge/License-Lichiphen%20Proprietary-purple.svg)](LICENSE)

## 📺 Demo Video

[![Demo Video](https://img.youtube.com/vi/QwwJOc5uCb4/0.jpg)](https://youtu.be/QwwJOc5uCb4)

👆 Click the image to watch the demo on YouTube

## Features

- 🔒 **Password Protection** - Secure your shared files with optional password
- 📊 **Download Counter** - Track how many times files have been downloaded
- 📦 **ZIP Download** - Download multiple files as a single ZIP archive
- 📁 **Upload Mode** - Restrict uploads to images only, archives only, or custom extensions
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
3. **Set the admin password in `config.php` before first login.** The default `admin` (or any password shorter than 12 characters) is rejected and login stays disabled until you change it. Using a hash is recommended:
   ```bash
   php -r "echo password_hash('your-strong-password', PASSWORD_DEFAULT), PHP_EOL;"
   ```
   Paste the output into `ADMIN_PASSWORD_HASH`.
4. Set `TRUSTED_HOSTS` to your domain (e.g. `['files.example.com']`).
5. Access `/admin` (or your custom `ADMIN_URL_PATH`) and log in.
6. **Nginx users**: add the deny rules in [Nginx Configuration](#nginx-configuration). Without them the database, sessions and uploaded files are exposed.

> **Note**: The `storage/` and `database/` directories are created automatically. If you encounter permission errors, check that your web server has write access to these directories.

## Configuration

Edit `config.php` to customize your installation. All user settings are at the top of the file.

### 🔑 Basic Settings

```php
// Admin password hash (recommended). When set, ADMIN_PASSWORD is ignored.
define('ADMIN_PASSWORD_HASH', '$2y$10$...');
// Plain-text admin password (12+ characters, default 'admin' is rejected)
define('ADMIN_PASSWORD', 'your-secure-password');

// Allowed host names (Host header injection protection)
define('TRUSTED_HOSTS', ['files.example.com']);

// Custom admin URL (recommended for security)
// Example: 'admin' → 'my-secret-panel-xyz123'
// After changing, access via /my-secret-panel-xyz123
define('ADMIN_URL_PATH', 'my-secret-panel');

// Session lifetime (in seconds)
define('SESSION_LIFETIME', 3600); // 1 hour

// Default language ('en' or 'ja')
define('DEFAULT_LANGUAGE', 'ja');
```

### 📁 Upload Settings

```php
// Maximum upload size (in bytes)
define('MAX_UPLOAD_SIZE', 100 * 1024 * 1024); // 100MB
```

| Size | Value |
|------|-------|
| 50MB | `50 * 1024 * 1024` |
| 100MB | `100 * 1024 * 1024` |
| 500MB | `500 * 1024 * 1024` |
| 1GB | `1024 * 1024 * 1024` |

### 🎨 Upload Mode (File Type Restriction)

Control what types of files users can upload:

```php
define('UPLOAD_MODE', 'ALL');
```

| Mode | Description | Allowed Extensions |
|------|-------------|-------------------|
| `'ALL'` | All files allowed (default) | Any file type |
| `'IMAGE_ONLY'` | Images only | jpg, jpeg, png, gif, webp, heic, avif, tiff |
| `'ARCHIVE_ONLY'` | Archives only | zip, 7z, rar, tar, gz |
| `'CUSTOM'` | Custom extensions | Specify in `CUSTOM_UPLOAD_EXTENSIONS` |

**Custom Mode Example:**
```php
define('UPLOAD_MODE', 'CUSTOM');
define('CUSTOM_UPLOAD_EXTENSIONS', 'jpg,jpeg,png,psd,pdf,zip');
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

### 🛡️ Upgrading to v1.6.0 (Security Update)

- Your existing `config.php` keeps working — all security initialisation now lives in `src/bootstrap.php`.
- **Login is refused while `ADMIN_PASSWORD` is `admin` or shorter than 12 characters.** Set a strong password or `ADMIN_PASSWORD_HASH` before updating.
- Optionally copy the new settings (`ADMIN_PASSWORD_HASH`, `TRUSTED_HOSTS`, `APP_BASE_URL`) from the new `config.php`.
- Share-page visitors need to enter the share password once more (the authentication cookie format changed).
- Nginx users must add the [deny rules](#nginx-configuration).

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

- Share passwords are hashed with bcrypt (`password_hash`)
- Share authentication cookies are HMAC-signed with a per-installation secret (`storage/.app_secret`), expire, and are revoked when the password changes
- Brute-force protection: admin login (5 attempts / 15 min) and share passwords (10 attempts / 15 min per IP)
- Default / short admin passwords are rejected; `ADMIN_PASSWORD_HASH` supported
- CSRF protection on every state-changing request (including login and logout)
- Uploaded files are stored under random names with a `.dat` extension — never with the user-supplied name — so they can never be executed or interpreted by the web server
- Downloads are always `Content-Disposition: attachment` with `X-Content-Type-Options: nosniff` and a sandbox CSP
- Download links are HMAC-signed and time-limited (direct-link prevention without relying on the Referer header)
- Strict Content Security Policy with per-request nonces, `X-Frame-Options: DENY`, `Referrer-Policy`, HSTS (on HTTPS)
- Admin session cookie is `HttpOnly`, `SameSite=Strict`, `Secure` (on HTTPS) and scoped to the admin path
- Host header validation (`TRUSTED_HOSTS`)
- Path traversal protection for every file operation
- Errors are logged, never displayed (`APP_DEBUG` to enable display)
- Direct access to `storage/`, `database/`, `src/`, `templates/`, `lang/` and dot files is blocked (Apache `.htaccess`, Nginx sample below, PHP built-in server router)

### Nginx Configuration

```nginx
# Nginx では .htaccess が無効なため、必ず以下を server ブロックに追加してください
# (Nginx ignores .htaccess — these rules are REQUIRED)
location ~ ^/(storage|database|src|templates|lang)(/|$) { deny all; return 404; }
location ~ /\.(?!well-known/) { deny all; return 404; }
location ~ ^/(config|version)\.php$ { deny all; return 404; }
location /assets/ { try_files $uri =404; }
location / { try_files $uri /index.php$is_args$args; }
location = /index.php {
    include fastcgi_params;
    fastcgi_param SCRIPT_FILENAME $document_root/index.php;
    fastcgi_pass unix:/run/php/php-fpm.sock;
}
# index.php 以外の .php は実行しない
location ~ \.php$ { return 404; }
```

> The PHP built-in server (`php -S`) is for local development only.

## Subdirectory Installation (WordPress Coexistence)

If you install this in a subdirectory (e.g., `/simple-secure-file-share/`) alongside WordPress or other CMS:

1. Edit `.htaccess` in the application folder
2. Change `RewriteBase /` to match your subdirectory:

```apache
# Example: installed at /simple-secure-file-share/
RewriteBase /simple-secure-file-share/
```

> **Symptom**: If you see the WordPress "Page not found" error instead of the cat 404 page when accessing `/admin`, this is likely the cause.

**Nginx:** the [deny rules](#nginx-configuration) start with `^/storage` etc. and do **not** match `/simple-secure-file-share/storage/...`. Add the subdirectory to every path, otherwise the database, session files and uploads can be downloaded:

```nginx
location ~ ^/simple-secure-file-share/(storage|database|src|templates|lang)(/|$) { deny all; return 404; }
location ~ /\.(?!well-known/) { deny all; return 404; }
location ~ ^/simple-secure-file-share/(config|version)\.php$ { deny all; return 404; }
location /simple-secure-file-share/assets/ { try_files $uri =404; }
location /simple-secure-file-share/ { try_files $uri /simple-secure-file-share/index.php$is_args$args; }
location = /simple-secure-file-share/index.php {
    include fastcgi_params;
    fastcgi_param SCRIPT_FILENAME $document_root/simple-secure-file-share/index.php;
    fastcgi_pass unix:/run/php/php-fpm.sock;
}
location ~ \.php$ { return 404; }
```

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
- 📁 **アップロードモード** - 画像のみ、圧縮ファイルのみ、またはカスタム拡張子に制限可能
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
3. **初回ログイン前に `config.php` で管理者パスワードを設定してください。** 初期値 `admin` や12文字未満のパスワードは拒否され、変更するまでログインできません。ハッシュでの設定を推奨します:
   ```bash
   php -r "echo password_hash('your-strong-password', PASSWORD_DEFAULT), PHP_EOL;"
   ```
   出力を `ADMIN_PASSWORD_HASH` に貼り付けます。
4. `TRUSTED_HOSTS` に自分のドメインを設定（例: `['files.example.com']`）
5. `/admin`（または変更した `ADMIN_URL_PATH`）にアクセスしてログイン
6. **Nginx の場合**: [Nginx 設定](#nginx-設定) の拒否ルールを必ず追加してください。追加しないとデータベース・セッション・アップロードファイルが外部から取得可能になります。

> **補足**: `storage/` と `database/` ディレクトリは自動作成されます。エラーが出る場合は、これらのディレクトリに書き込み権限があるか確認してください。

## 設定

`config.php` を編集してカスタマイズできます。ユーザー設定はファイルの先頭にまとまっています。

### 🔑 基本設定

```php
// 管理者パスワードのハッシュ（推奨）。設定すると ADMIN_PASSWORD は無視されます
define('ADMIN_PASSWORD_HASH', '$2y$10$...');
// 平文の管理者パスワード（12文字以上。初期値 'admin' は拒否されます）
define('ADMIN_PASSWORD', 'your-secure-password');

// 許可するホスト名（Host ヘッダー偽装対策）
define('TRUSTED_HOSTS', ['files.example.com']);

// 管理画面のURL（セキュリティのため変更を推奨）
// 例: 'admin' → 'my-secret-panel-xyz123'
// 変更後は /my-secret-panel-xyz123 でアクセス
define('ADMIN_URL_PATH', 'my-secret-panel');

// セッション有効時間（秒単位）
define('SESSION_LIFETIME', 3600); // 1時間

// デフォルト言語 ('en' または 'ja')
define('DEFAULT_LANGUAGE', 'ja');
```

### 📁 アップロード設定

```php
// 最大アップロードサイズ（バイト単位）
define('MAX_UPLOAD_SIZE', 100 * 1024 * 1024); // 100MB
```

| サイズ | 設定値 |
|--------|--------|
| 50MB | `50 * 1024 * 1024` |
| 100MB | `100 * 1024 * 1024` |
| 500MB | `500 * 1024 * 1024` |
| 1GB | `1024 * 1024 * 1024` |

### 🎨 アップロードモード（ファイル種別の制限）

アップロードできるファイルの種類を制限できます：

```php
define('UPLOAD_MODE', 'ALL');
```

| モード | 説明 | 許可される拡張子 |
|--------|------|-----------------|
| `'ALL'` | すべてのファイルを許可（デフォルト） | 制限なし |
| `'IMAGE_ONLY'` | 画像ファイルのみ | jpg, jpeg, png, gif, webp, heic, avif, tiff |
| `'ARCHIVE_ONLY'` | 圧縮ファイルのみ | zip, 7z, rar, tar, gz |
| `'CUSTOM'` | カスタム指定 | `CUSTOM_UPLOAD_EXTENSIONS`で指定 |

**CUSTOMモードの使用例：**
```php
define('UPLOAD_MODE', 'CUSTOM');
define('CUSTOM_UPLOAD_EXTENSIONS', 'jpg,jpeg,png,psd,pdf,zip');
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

### 🛡️ v1.6.0へのアップグレード（セキュリティアップデート）

- 既存の `config.php` はそのまま使えます（セキュリティ関連の初期化は `src/bootstrap.php` に移動しました）。
- **`ADMIN_PASSWORD` が `admin` のまま、または12文字未満の場合はログインできません。** アップデート前に強力なパスワードか `ADMIN_PASSWORD_HASH` を設定してください。
- 必要に応じて新しい `config.php` の設定項目（`ADMIN_PASSWORD_HASH`、`TRUSTED_HOSTS`、`APP_BASE_URL`）を追記してください。
- 共有ページの閲覧者は、認証Cookie形式の変更により一度だけパスワードの再入力が必要です。
- Nginx をお使いの場合は [拒否ルール](#nginx-設定) を必ず追加してください。

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

- 共有パスワードは bcrypt（`password_hash`）でハッシュ化
- 共有認証Cookieはインストール固有の秘密鍵（`storage/.app_secret`）でHMAC署名。有効期限付きで、パスワード変更時に自動失効
- 総当たり対策: 管理者ログイン（15分間に5回）、共有パスワード（IPごとに15分間に10回）
- 初期値・短い管理者パスワードを拒否、`ADMIN_PASSWORD_HASH` に対応
- 状態を変更するすべてのリクエストにCSRF対策（ログイン・ログアウト含む）
- アップロードファイルはランダム名＋`.dat` 拡張子で保存し、利用者が指定したファイル名をディスク上で使わない（Webサーバーに実行・解釈されない）
- ダウンロードは常に `Content-Disposition: attachment`、`X-Content-Type-Options: nosniff`、sandbox CSP 付き
- ダウンロードリンクは有効期限付きのHMAC署名（Referer に依存しない直リンク防止）
- リクエスト毎の nonce を用いた厳格な CSP、`X-Frame-Options: DENY`、`Referrer-Policy`、HSTS（HTTPS時）
- 管理セッションCookieは `HttpOnly` / `SameSite=Strict` / `Secure`（HTTPS時）で、管理画面パスに限定
- Host ヘッダー検証（`TRUSTED_HOSTS`）
- すべてのファイル操作でパストラバーサル対策
- エラーは画面に表示せずログに記録（表示する場合は `APP_DEBUG`）
- `storage/` `database/` `src/` `templates/` `lang/` およびドットファイルへの直接アクセスを遮断（Apache の `.htaccess`、下記 Nginx 設定例、PHP ビルトインサーバー用ルーター）

### Nginx 設定

```nginx
# Nginx では .htaccess が無効なため、必ず以下を server ブロックに追加してください
# (Nginx ignores .htaccess — these rules are REQUIRED)
location ~ ^/(storage|database|src|templates|lang)(/|$) { deny all; return 404; }
location ~ /\.(?!well-known/) { deny all; return 404; }
location ~ ^/(config|version)\.php$ { deny all; return 404; }
location /assets/ { try_files $uri =404; }
location / { try_files $uri /index.php$is_args$args; }
location = /index.php {
    include fastcgi_params;
    fastcgi_param SCRIPT_FILENAME $document_root/index.php;
    fastcgi_pass unix:/run/php/php-fpm.sock;
}
# index.php 以外の .php は実行しない
location ~ \.php$ { return 404; }
```

> PHP ビルトインサーバー（`php -S`）はローカル開発専用です。

## サブディレクトリ配置（WordPress共存）

WordPressなど他のCMSと同じサーバーで、サブディレクトリ（例：`/simple-secure-file-share/`）に配置する場合：

1. アプリケーションフォルダ内の`.htaccess`を編集
2. `RewriteBase /` をサブディレクトリに合わせて変更：

```apache
# 例: /simple-secure-file-share/ に配置した場合
RewriteBase /simple-secure-file-share/
```

> **症状**: `/admin`にアクセスした際、猫の404ページではなくWordPressの「ページが見つかりません」が表示される場合、これが原因です。

**Nginx の場合:** [Nginx 設定](#nginx-設定) の拒否ルールは `^/storage` などルート前提のため、`/simple-secure-file-share/storage/...` には効きません。すべてのパスにサブディレクトリを付けてください。付けないとデータベース・セッションファイル・アップロードファイルが取得できてしまいます：

```nginx
location ~ ^/simple-secure-file-share/(storage|database|src|templates|lang)(/|$) { deny all; return 404; }
location ~ /\.(?!well-known/) { deny all; return 404; }
location ~ ^/simple-secure-file-share/(config|version)\.php$ { deny all; return 404; }
location /simple-secure-file-share/assets/ { try_files $uri =404; }
location /simple-secure-file-share/ { try_files $uri /simple-secure-file-share/index.php$is_args$args; }
location = /simple-secure-file-share/index.php {
    include fastcgi_params;
    fastcgi_param SCRIPT_FILENAME $document_root/simple-secure-file-share/index.php;
    fastcgi_pass unix:/run/php/php-fpm.sock;
}
location ~ \.php$ { return 404; }
```

## ライセンス

このプロジェクトはLichiphen Proprietary License v1.0の下でライセンスされています。詳細は[LICENSE](LICENSE)ファイルをご覧ください。

## クレジット

- **ディレクション**: Lichiphen
- **開発**: AI Generator

# Changelog / 変更履歴

All notable changes to this project will be documented in this file.  
このプロジェクトの主要な変更点をこのファイルに記録します。

The format is based on [Keep a Changelog](https://keepachangelog.com/).

---

## [1.5.0] - 2024-12-12

### Added / 追加
- **Upload Mode** - Control allowed file types / アップロードモード機能
  - `IMAGE_ONLY` - Images only (jpg, png, gif, webp, heic, avif, tiff)
  - `ARCHIVE_ONLY` - Archives only (zip, 7z, rar, tar, gz)
  - `ALL` - All files (default)
  - `CUSTOM` - Custom extensions via config
- **SQLite Maintenance** - Database optimization tools / SQLiteメンテナンス機能
  - WAL checkpoint
  - Optimize (index statistics)
  - VACUUM (rebuild database)
- **version.php** - Separated version info for update-safe versioning / バージョン情報の分離
- **Demo Video** - YouTube demo video link in README / デモ動画リンク追加
- **Subdirectory Guide** - Installation guide for WordPress coexistence / サブディレクトリ配置ガイド

### Fixed / 修正
- Session cleanup 404 redirect with custom admin URL / カスタム管理URLでのセッションクリーンアップ404エラー
- PHP 7.4 compatibility (str_contains/str_ends_with) / PHP 7.4互換性問題

### Changed / 変更
- `config.php` structure changed - version info moved to `version.php` / config.php構造変更
- `.htaccess` - Added RewriteBase documentation / RewriteBase説明追加

### ⚠️ Breaking Changes / 重要な変更
- Users upgrading from v1.4.x must update `config.php` (see README for migration guide)
- v1.4.xからアップグレードする場合は`config.php`の更新が必要（READMEの移行ガイド参照）

---

## [1.4.2] - 2024-12-11

### Initial Release / 初回リリース

#### Features / 機能
- 🔒 **Password Protection** - Secure shared files with optional password / パスワード保護
- 📊 **Download Counter** - Track download counts / ダウンロードカウンター
- 📦 **ZIP Download** - Download multiple files as ZIP archive / ZIP一括ダウンロード
- 🛡️ **Direct Link Prevention** - Block unauthorized direct access / 直接リンク防止
- 🔐 **Hidden Admin URL** - Customizable admin panel path / 管理画面URL変更機能
- 🌙 **Dark Mode** - Automatic theme switching / ダークモード対応
- 🌐 **Multi-language** - English and Japanese support / 日英2言語対応
- 🐱 **Cute Error Pages** - Friendly cat mascot error pages / 可愛いエラーページ

#### Security / セキュリティ
- bcrypt password hashing / bcryptパスワードハッシュ
- .htaccess protection for storage and database / ストレージ・データベース保護
- Cookie-based authentication with expiration / Cookie認証（有効期限付き）
- Referer validation for downloads / リファラー検証
- HTTPS redirect support / HTTPSリダイレクト対応
- Direct access prevention for PHP files / PHP直接アクセス防止

---

## Links / リンク

- **Repository**: https://gitlab.com/lichiphen/sqlite-simple-secure-file-share
- **Releases**: https://gitlab.com/lichiphen/sqlite-simple-secure-file-share/-/releases
- **Demo Video**: https://youtu.be/QwwJOc5uCb4

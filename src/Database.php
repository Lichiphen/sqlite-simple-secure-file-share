<?php
/**
 * Simple Secure File Share - Database Handler
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

class Database
{
    private static ?PDO $instance = null;

    /**
     * Get database connection (singleton)
     */
    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            self::$instance = self::connect();
        }
        return self::$instance;
    }

    /**
     * Create database connection
     */
    private static function connect(): PDO
    {
        // Ensure storage directory exists
        $storage_dir = dirname(DATABASE_PATH);
        if (!is_dir($storage_dir)) {
            mkdir($storage_dir, 0755, true);
        }

        $pdo = new PDO('sqlite:' . DATABASE_PATH);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        // Enable foreign keys
        $pdo->exec('PRAGMA foreign_keys = ON');

        // Initialize tables
        self::initTables($pdo);

        return $pdo;
    }

    /**
     * Initialize database tables
     */
    private static function initTables(PDO $pdo): void
    {
        $pdo->exec('
            CREATE TABLE IF NOT EXISTS shares (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                token TEXT UNIQUE NOT NULL,
                title TEXT NOT NULL,
                password_hash TEXT,
                download_count INTEGER DEFAULT 0,
                created_at TEXT DEFAULT CURRENT_TIMESTAMP
            )
        ');

        $pdo->exec('
            CREATE TABLE IF NOT EXISTS files (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                share_id INTEGER NOT NULL,
                system_name TEXT NOT NULL,
                original_name TEXT NOT NULL,
                size INTEGER NOT NULL,
                FOREIGN KEY (share_id) REFERENCES shares(id) ON DELETE CASCADE
            )
        ');

        // Create index for faster token lookup
        $pdo->exec('CREATE INDEX IF NOT EXISTS idx_shares_token ON shares(token)');
    }

    /**
     * Create a new share
     */
    public static function createShare(string $token, string $title, ?string $passwordHash = null): int
    {
        $pdo = self::getInstance();
        $stmt = $pdo->prepare('
            INSERT INTO shares (token, title, password_hash) 
            VALUES (:token, :title, :password_hash)
        ');
        $stmt->execute([
            ':token' => $token,
            ':title' => $title,
            ':password_hash' => $passwordHash
        ]);
        return (int) $pdo->lastInsertId();
    }

    /**
     * Add file to share
     */
    public static function addFile(int $shareId, string $systemName, string $originalName, int $size): int
    {
        $pdo = self::getInstance();
        $stmt = $pdo->prepare('
            INSERT INTO files (share_id, system_name, original_name, size) 
            VALUES (:share_id, :system_name, :original_name, :size)
        ');
        $stmt->execute([
            ':share_id' => $shareId,
            ':system_name' => $systemName,
            ':original_name' => $originalName,
            ':size' => $size
        ]);
        return (int) $pdo->lastInsertId();
    }

    /**
     * Get share by token
     */
    public static function getShareByToken(string $token): ?array
    {
        $pdo = self::getInstance();
        $stmt = $pdo->prepare('SELECT * FROM shares WHERE token = :token');
        $stmt->execute([':token' => $token]);
        $share = $stmt->fetch();
        return $share ?: null;
    }

    /**
     * Get files for share
     */
    public static function getFilesForShare(int $shareId): array
    {
        $pdo = self::getInstance();
        $stmt = $pdo->prepare('SELECT * FROM files WHERE share_id = :share_id');
        $stmt->execute([':share_id' => $shareId]);
        return $stmt->fetchAll();
    }

    /**
     * Get all shares
     */
    public static function getAllShares(): array
    {
        $pdo = self::getInstance();
        $stmt = $pdo->query('SELECT * FROM shares ORDER BY created_at DESC');
        return $stmt->fetchAll();
    }

    /**
     * Delete share by ID
     */
    public static function deleteShare(int $id): bool
    {
        $pdo = self::getInstance();
        $stmt = $pdo->prepare('DELETE FROM shares WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Increment download count
     */
    public static function incrementDownloadCount(int $shareId): void
    {
        $pdo = self::getInstance();
        $stmt = $pdo->prepare('UPDATE shares SET download_count = download_count + 1 WHERE id = :id');
        $stmt->execute([':id' => $shareId]);
    }

    /**
     * Get file count for share
     */
    public static function getFileCount(int $shareId): int
    {
        $pdo = self::getInstance();
        $stmt = $pdo->prepare('SELECT COUNT(*) as count FROM files WHERE share_id = :share_id');
        $stmt->execute([':share_id' => $shareId]);
        $result = $stmt->fetch();
        return (int) $result['count'];
    }

    /**
     * Get all files for share
     */
    public static function getFiles(int $shareId): array
    {
        $pdo = self::getInstance();
        $stmt = $pdo->prepare('SELECT * FROM files WHERE share_id = :share_id');
        $stmt->execute([':share_id' => $shareId]);
        return $stmt->fetchAll();
    }

    /**
     * Get all tokens from database
     */
    public static function getAllTokens(): array
    {
        $pdo = self::getInstance();
        $stmt = $pdo->query('SELECT token FROM shares');
        return array_column($stmt->fetchAll(), 'token');
    }

    /**
     * Update share password
     */
    public static function updatePassword(int $id, ?string $hash): bool
    {
        $pdo = self::getInstance();
        $stmt = $pdo->prepare('UPDATE shares SET password_hash = :hash WHERE id = :id');
        return $stmt->execute([':hash' => $hash, ':id' => $id]);
    }

    /**
     * Delete a single file record
     */
    public static function deleteFileRecord(int $shareId, string $systemName): bool
    {
        $pdo = self::getInstance();
        $stmt = $pdo->prepare('DELETE FROM files WHERE share_id = :share_id AND system_name = :system_name');
        return $stmt->execute([':share_id' => $shareId, ':system_name' => $systemName]);
    }

    /**
     * Delete all shares and reset ID sequence
     */
    public static function deleteAllShares(): bool
    {
        $pdo = self::getInstance();
        try {
            $pdo->beginTransaction();
            // Delete data
            $pdo->exec('DELETE FROM files');
            $pdo->exec('DELETE FROM shares');

            // Try to reset AUTOINCREMENT sequence, but ignore errors if table doesn't exist
            try {
                $pdo->exec("DELETE FROM sqlite_sequence WHERE name='shares'");
                $pdo->exec("DELETE FROM sqlite_sequence WHERE name='files'");
            } catch (\Throwable $e) {
                // Ignore sequence reset errors
            }

            $pdo->commit();
            return true;
        } catch (\Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $e;
        }
    }

    /**
     * Get orphan shares (shares without files)
     */
    public static function getOrphanRecords(array $existingFolders): array
    {
        $shares = self::getAllShares();
        $orphans = [];
        foreach ($shares as $share) {
            if (!in_array($share['token'], $existingFolders)) {
                $orphans[] = $share;
            }
        }
        return $orphans;
    }

    /**
     * SQLiteデータベースの軽量メンテナンスを実行
     * WALチェックポイントとoptimizeを実行
     * 
     * @return array 実行結果
     */
    public static function runMaintenance(): array
    {
        $pdo = self::getInstance();
        $results = [];

        try {
            // WALチェックポイント（ログを本体に反映し、WALファイルを縮める）
            $pdo->exec("PRAGMA wal_checkpoint(TRUNCATE)");
            $results['wal_checkpoint'] = true;
        } catch (\Throwable $e) {
            $results['wal_checkpoint'] = false;
            $results['wal_error'] = $e->getMessage();
        }

        try {
            // 軽量な最適化（インデックス統計など）
            $pdo->exec("PRAGMA optimize");
            $results['optimize'] = true;
        } catch (\Throwable $e) {
            $results['optimize'] = false;
            $results['optimize_error'] = $e->getMessage();
        }

        return $results;
    }

    /**
     * SQLiteデータベースのVACUUMを実行（DB再構築）
     * 注意: 大きなDBでは時間がかかる可能性あり
     * 
     * @return array 実行結果
     */
    public static function runVacuum(): array
    {
        $pdo = self::getInstance();
        $results = [];

        try {
            // DBファイルサイズ取得（実行前）
            $sizeBefore = file_exists(DATABASE_PATH) ? filesize(DATABASE_PATH) : 0;

            // VACUUM実行
            $pdo->exec("VACUUM");

            // DBファイルサイズ取得（実行後）
            clearstatcache(true, DATABASE_PATH);
            $sizeAfter = file_exists(DATABASE_PATH) ? filesize(DATABASE_PATH) : 0;

            $results['vacuum'] = true;
            $results['size_before'] = $sizeBefore;
            $results['size_after'] = $sizeAfter;
            $results['size_reduced'] = $sizeBefore - $sizeAfter;
        } catch (\Throwable $e) {
            $results['vacuum'] = false;
            $results['vacuum_error'] = $e->getMessage();
        }

        return $results;
    }

    /**
     * データベースファイルサイズを取得
     * 
     * @return int ファイルサイズ（バイト）
     */
    public static function getDatabaseSize(): int
    {
        if (!file_exists(DATABASE_PATH)) {
            return 0;
        }
        return filesize(DATABASE_PATH);
    }
}

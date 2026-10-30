<?php
/**
 * Simple Secure File Share - File Handler
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

class FileHandler
{

    /**
     * 許可されたアップロード拡張子を取得
     * 
     * @return array 許可拡張子の配列（空配列=全て許可）
     */
    public static function getAllowedUploadExtensions(): array
    {
        $mode = strtoupper(defined('UPLOAD_MODE') ? UPLOAD_MODE : 'ALL');

        switch ($mode) {
            case 'IMAGE_ONLY':
                return [
                    'jpg',
                    'jpeg',
                    'png',
                    'webp',
                    'heic',
                    'heif',
                    'avif',
                    'gif',
                    'tif',
                    'tiff',
                ];

            case 'ARCHIVE_ONLY':
                return [
                    'zip',
                    '7z',
                    'rar',
                    'tar',
                    'gz',
                    'tgz',
                    'tar.gz',
                    'tar.bz2',
                    'tar.xz',
                ];

            case 'CUSTOM':
                $raw = defined('CUSTOM_UPLOAD_EXTENSIONS') ? CUSTOM_UPLOAD_EXTENSIONS : '';
                $parts = array_filter(array_map(function ($item) {
                    return strtolower(trim($item));
                }, preg_split('/[,;]+/', $raw)));
                return array_values(array_unique($parts));

            case 'ALL':
            default:
                return []; // 空配列 = 拡張子による制限なし
        }
    }

    /**
     * ファイル拡張子が許可されているかチェック
     * 
     * @param string $filename ファイル名
     * @return bool 許可されていればtrue
     */
    public static function isExtensionAllowed(string $filename): bool
    {
        $allowedExtensions = self::getAllowedUploadExtensions();

        // 空配列 = 全て許可
        if (empty($allowedExtensions)) {
            return true;
        }

        $lowerName = strtolower($filename);
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        // 単純な拡張子チェック
        if (in_array($ext, $allowedExtensions, true)) {
            return true;
        }

        // "tar.gz" 等の複合拡張子にも対応 (PHP 7.4互換)
        foreach ($allowedExtensions as $pattern) {
            if (strpos($pattern, '.') !== false) {
                $suffix = '.' . $pattern;
                if (substr($lowerName, -strlen($suffix)) === $suffix) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Generate unique token
     */
    public static function generateToken(): string
    {
        return bin2hex(random_bytes(16));
    }

    /**
     * Process file upload
     * 
     * @param array $files $_FILES array
     * @param string $title Share title
     * @param string|null $password Optional password
     * @return array Result with success status and message/data
     */
    public static function processUpload(array $files, string $title, ?string $password = null): array
    {
        $title = Security::cleanText($title, 200);

        // Validate
        if ($title === '') {
            return ['success' => false, 'message' => __('title_required')];
        }

        // Normalize file array structure
        $fileList = [];
        if (isset($files['name'])) {
            if (is_array($files['name'])) {
                $count = count($files['name']);
                for ($i = 0; $i < $count; $i++) {
                    if (!empty($files['name'][$i]) && is_string($files['name'][$i])) {
                        $fileList[] = [
                            'name' => $files['name'][$i],
                            'tmp_name' => (string) ($files['tmp_name'][$i] ?? ''),
                            'error' => (int) ($files['error'][$i] ?? UPLOAD_ERR_NO_FILE),
                            'size' => (int) ($files['size'][$i] ?? 0)
                        ];
                    }
                }
            } else if (!empty($files['name']) && is_string($files['name'])) {
                $fileList[] = [
                    'name' => $files['name'],
                    'tmp_name' => (string) ($files['tmp_name'] ?? ''),
                    'error' => (int) ($files['error'] ?? UPLOAD_ERR_NO_FILE),
                    'size' => (int) ($files['size'] ?? 0)
                ];
            }
        }

        if (empty($fileList)) {
            return ['success' => false, 'message' => __('files_required')];
        }

        if (count($fileList) > MAX_FILES_PER_SHARE) {
            return ['success' => false, 'message' => __('upload_failed') . ' (max ' . MAX_FILES_PER_SHARE . ' files)'];
        }

        foreach ($fileList as $file) {
            // サーバー側でもサイズ上限を強制
            if ($file['error'] === UPLOAD_ERR_INI_SIZE || $file['error'] === UPLOAD_ERR_FORM_SIZE
                || (defined('MAX_UPLOAD_SIZE') && $file['size'] > MAX_UPLOAD_SIZE)) {
                return ['success' => false, 'message' => __('upload_failed') . ' (max ' . self::formatSize((int) MAX_UPLOAD_SIZE) . ')'];
            }
        }

        // 拡張子チェック
        $allowedExtensions = self::getAllowedUploadExtensions();
        if (!empty($allowedExtensions)) {
            foreach ($fileList as $file) {
                if (!self::isExtensionAllowed(self::cleanOriginalName($file['name']))) {
                    return [
                        'success' => false,
                        'message' => __('extension_not_allowed') . ': ' . implode(', ', $allowedExtensions)
                    ];
                }
            }
        }

        // Generate token and create directory
        $token = self::generateToken();
        $targetDir = UPLOADS_PATH . '/' . $token . '/';

        if (!@mkdir($targetDir, 0750, true)) {
            return ['success' => false, 'message' => __('directory_creation_failed')];
        }

        // Hash password if provided
        $passwordHash = null;
        if ($password !== null && $password !== '') {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        }

        $pdo = Database::getInstance();
        $savedFiles = [];

        try {
            $pdo->beginTransaction();

            // Create share record
            $shareId = Database::createShare($token, $title, $passwordHash);

            foreach ($fileList as $file) {
                if ($file['error'] !== UPLOAD_ERR_OK || !is_uploaded_file($file['tmp_name'])) {
                    continue;
                }

                $originalName = self::cleanOriginalName($file['name']);
                // ディスク上の名前はランダム値+固定拡張子にし、利用者の入力を一切使わない
                // （.php / .htaccess 等による実行・設定上書きを防止）
                $systemName = bin2hex(random_bytes(16)) . '.dat';
                $destination = $targetDir . $systemName;

                if (move_uploaded_file($file['tmp_name'], $destination)) {
                    @chmod($destination, 0640);
                    $size = (int) filesize($destination);
                    Database::addFile($shareId, $systemName, $originalName, $size);
                    $savedFiles[] = [
                        'system_name' => $systemName,
                        'original_name' => $originalName,
                        'size' => $size
                    ];
                }
            }

            if (empty($savedFiles)) {
                throw new RuntimeException('No files saved');
            }

            $pdo->commit();
        } catch (\Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            self::deleteDirectory(rtrim($targetDir, '/'));
            error_log('Upload Error: ' . $e->getMessage());
            return ['success' => false, 'message' => __('upload_failed')];
        }

        return [
            'success' => true,
            'token' => $token,
            'share_id' => $shareId,
            'files' => $savedFiles
        ];
    }

    /**
     * 表示・ダウンロード用の元ファイル名を正規化
     */
    public static function cleanOriginalName(string $filename): string
    {
        // パス区切り (/ と \) を除去
        $filename = preg_replace('#^.*[/\\\\]#', '', $filename) ?? '';
        $filename = Security::cleanText($filename, 200);
        $filename = ltrim($filename, '.');
        if ($filename === '') {
            $filename = 'file_' . date('Ymd_His');
        }
        return $filename;
    }

    /**
     * Sanitize filename for safe storage
     */
    public static function sanitizeFilename(string $filename): string
    {
        // Remove path components
        $filename = basename(str_replace('\\', '/', $filename));

        // Replace problematic characters
        $filename = preg_replace('/[^\w\-\.\x{3000}-\x{9FFF}\x{FF00}-\x{FFEF}]/u', '_', $filename) ?? '';

        // Collapse multiple underscores
        $filename = preg_replace('/_+/', '_', $filename);

        // 先頭のドットを除去（隠しファイル/.htaccess 対策）
        $filename = ltrim((string) $filename, '.');

        // Ensure not empty
        if ($filename === '') {
            $filename = 'file_' . time();
        }

        return $filename;
    }

    /**
     * 共有フォルダ内の実ファイルパスを安全に解決
     */
    private static function resolveSharePath(string $token, string $systemName): ?string
    {
        if (!Security::isValidToken($token) || !Security::isSafeFileName($systemName)) {
            return null;
        }
        $path = Security::resolveWithin(UPLOADS_PATH . '/' . $token, UPLOADS_PATH . '/' . $token . '/' . $systemName);
        return ($path !== null && is_file($path)) ? $path : null;
    }

    /**
     * Content-Disposition ヘッダー値を生成
     */
    private static function contentDisposition(string $filename): string
    {
        $filename = Security::cleanText($filename, 200);
        $encodedFilename = rawurlencode($filename);
        // Fallback filename for older browsers (ASCII only, no quotes/backslashes)
        $fallbackFilename = preg_replace('/[^\x20-\x7e]/', '_', $filename);
        $fallbackFilename = str_replace(['"', '\\', ';'], '', (string) $fallbackFilename);
        if ($fallbackFilename === '') {
            $fallbackFilename = 'download';
        }
        return "attachment; filename=\"{$fallbackFilename}\"; filename*=UTF-8''{$encodedFilename}";
    }

    /**
     * ダウンロード応答共通ヘッダー
     */
    private static function sendDownloadHeaders(string $contentType, string $filename, int $length): void
    {
        // Output buffering handling to prevent error pollution
        while (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Description: File Transfer');
        header('Content-Type: ' . $contentType);
        header('Content-Disposition: ' . self::contentDisposition($filename));
        header('Content-Length: ' . $length);
        header('X-Content-Type-Options: nosniff');
        header("Content-Security-Policy: default-src 'none'; sandbox");
        header('Cache-Control: private, no-store, no-cache, must-revalidate, max-age=0');
        header('Pragma: no-cache');
        header('Expires: 0');
    }

    /**
     * Download single file
     */
    public static function downloadFile(string $token, string $systemName, string $originalName): void
    {
        $filePath = self::resolveSharePath($token, $systemName);

        if ($filePath === null) {
            http_response_code(404);
            exit('File not found');
        }

        // 常にダウンロードとして扱い、ブラウザ内で実行・表示させない
        self::sendDownloadHeaders('application/octet-stream', $originalName, (int) filesize($filePath));

        readfile($filePath);
        exit;
    }

    /**
     * Download all files as ZIP
     */
    public static function downloadAsZip(string $token, array $files, string $title): void
    {
        if (!Security::isValidToken($token)) {
            http_response_code(404);
            exit('File not found');
        }

        $zip = new ZipArchive();
        $zipFilename = tempnam(sys_get_temp_dir(), 'sfs_zip');
        if ($zipFilename === false) {
            http_response_code(500);
            exit('Could not create archive');
        }

        // 途中切断時も一時ファイルを確実に削除
        ignore_user_abort(true);
        register_shutdown_function(function () use ($zipFilename) {
            if (is_file($zipFilename)) {
                @unlink($zipFilename);
            }
        });

        // Use OVERWRITE to prevent "Using empty file" deprecation warning in PHP 8.0+
        if ($zip->open($zipFilename, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            http_response_code(500);
            exit('Could not create archive');
        }

        $usedNames = [];
        foreach ($files as $file) {
            $filePath = self::resolveSharePath($token, (string) $file['system_name']);
            if ($filePath === null) {
                continue;
            }

            // Zip Slip 対策: エントリ名はパスを含まない一意な名前にする
            $entryName = self::sanitizeFilename(self::cleanOriginalName((string) $file['original_name']));
            $base = pathinfo($entryName, PATHINFO_FILENAME);
            $ext = pathinfo($entryName, PATHINFO_EXTENSION);
            $counter = 1;
            while (isset($usedNames[strtolower($entryName)])) {
                $entryName = $base . '_' . $counter . ($ext !== '' ? '.' . $ext : '');
                $counter++;
            }
            $usedNames[strtolower($entryName)] = true;

            $zip->addFile($filePath, $entryName);
        }
        $zip->close();

        if (!is_file($zipFilename)) {
            http_response_code(500);
            exit('Could not create archive');
        }

        self::sendDownloadHeaders('application/zip', $title . '.zip', (int) filesize($zipFilename));

        readfile($zipFilename);
        @unlink($zipFilename);
        exit;
    }

    /**
     * Delete single file
     */
    public static function deleteFile(string $token, string $systemName): bool
    {
        $filePath = self::resolveSharePath($token, $systemName);
        if ($filePath !== null) {
            return unlink($filePath);
        }
        return false;
    }

    /**
     * Delete share folder and all files
     */
    public static function deleteShareFolder(string $token): bool
    {
        // フォルダ名にパス区切りや "." "..", 隠しファイル名を許さない
        if (!Security::isSafeFileName($token)) {
            return false;
        }

        $dir = UPLOADS_PATH . '/' . $token;

        if (!is_dir($dir)) {
            return true;
        }

        if (Security::resolveWithin(UPLOADS_PATH, $dir) === null || is_link($dir)) {
            return false;
        }

        return self::deleteDirectory($dir);
    }

    /**
     * Recursively delete directory
     */
    private static function deleteDirectory(string $dir): bool
    {
        if (!is_dir($dir)) {
            return false;
        }

        $files = array_diff(scandir($dir) ?: [], ['.', '..']);
        foreach ($files as $file) {
            $path = $dir . '/' . $file;
            if (is_dir($path) && !is_link($path)) {
                self::deleteDirectory($path);
            } else {
                @unlink($path);
            }
        }
        return @rmdir($dir);
    }

    /**
     * Get all upload folder names
     */
    public static function getUploadFolders(): array
    {
        $folders = [];
        if (is_dir(UPLOADS_PATH)) {
            $dirs = glob(UPLOADS_PATH . '/*', GLOB_ONLYDIR) ?: [];
            foreach ($dirs as $dir) {
                $folders[] = basename($dir);
            }
        }
        return $folders;
    }

    /**
     * Get orphan folders (folders not in database)
     */
    public static function getOrphanFolders(array $dbTokens): array
    {
        $folders = self::getUploadFolders();
        return array_values(array_diff($folders, $dbTokens));
    }

    /**
     * Format file size for display
     */
    public static function formatSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 1) . ' ' . $units[$i];
    }

    /**
     * Delete all uploaded files and folders
     */
    public static function deleteAllFiles(): void
    {
        if (!is_dir(UPLOADS_PATH))
            return;

        $files = glob(UPLOADS_PATH . '/*') ?: [];
        foreach ($files as $file) {
            if (is_dir($file) && !is_link($file)) {
                self::deleteDirectory($file);
            }
        }
    }

    /**
     * Get file count for a share folder
     */
    public static function getFileCount(string $token): int
    {
        if (!Security::isSafeFileName($token)) {
            return 0;
        }
        $dir = UPLOADS_PATH . '/' . $token;
        if (!is_dir($dir)) {
            return 0;
        }
        $files = array_diff(scandir($dir), ['.', '..']);
        $count = 0;
        foreach ($files as $file) {
            if (is_file($dir . '/' . $file)) {
                $count++;
            }
        }
        return $count;
    }
}

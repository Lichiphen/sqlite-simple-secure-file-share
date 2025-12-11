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

class FileHandler {
    
    /**
     * Generate unique token
     */
    public static function generateToken(): string {
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
    public static function processUpload(array $files, string $title, ?string $password = null): array {
        // Validate
        if (empty($title)) {
            return ['success' => false, 'message' => __('title_required')];
        }
        
        // Normalize file array structure
        $fileList = [];
        if (isset($files['name'])) {
            if (is_array($files['name'])) {
                $count = count($files['name']);
                for ($i = 0; $i < $count; $i++) {
                    if (!empty($files['name'][$i])) {
                        $fileList[] = [
                            'name' => $files['name'][$i],
                            'type' => $files['type'][$i],
                            'tmp_name' => $files['tmp_name'][$i],
                            'error' => $files['error'][$i],
                            'size' => $files['size'][$i]
                        ];
                    }
                }
            } else if (!empty($files['name'])) {
                $fileList[] = $files;
            }
        }
        
        if (empty($fileList)) {
            return ['success' => false, 'message' => __('files_required')];
        }
        
        // Generate token and create directory
        $token = self::generateToken();
        $targetDir = UPLOADS_PATH . '/' . $token . '/';
        
        if (!mkdir($targetDir, 0755, true)) {
            return ['success' => false, 'message' => __('directory_creation_failed')];
        }
        
        // Hash password if provided
        $passwordHash = null;
        if (!empty($password)) {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        }
        
        // Create share record
        $shareId = Database::createShare($token, $title, $passwordHash);
        
        // Process each file
        $savedFiles = [];
        
        foreach ($fileList as $file) {
            if ($file['error'] === UPLOAD_ERR_OK) {
                $originalName = $file['name'];
                $systemName = self::sanitizeFilename($originalName);
                $destination = $targetDir . $systemName;
                
                // Handle duplicates
                $counter = 1;
                $pathInfo = pathinfo($destination);
                while (file_exists($destination)) {
                    $systemName = $pathInfo['filename'] . '_' . $counter . '.' . ($pathInfo['extension'] ?? '');
                    $destination = $targetDir . $systemName;
                    $counter++;
                }
                
                if (move_uploaded_file($file['tmp_name'], $destination)) {
                    Database::addFile($shareId, $systemName, $originalName, $file['size']);
                    $savedFiles[] = [
                        'system_name' => $systemName,
                        'original_name' => $originalName,
                        'size' => $file['size']
                    ];
                }
            }
        }
        
        if (empty($savedFiles)) {
            // Cleanup on failure
            rmdir($targetDir);
            Database::deleteShare($shareId);
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
     * Sanitize filename for safe storage
     */
    public static function sanitizeFilename(string $filename): string {
        // Remove path components
        $filename = basename($filename);
        
        // Replace problematic characters
        $filename = preg_replace('/[^\w\-\.\u3000-\u9FFF\uFF00-\uFFEF]/u', '_', $filename);
        
        // Collapse multiple underscores
        $filename = preg_replace('/_+/', '_', $filename);
        
        // Ensure not empty
        if (empty($filename) || $filename === '.') {
            $filename = 'file_' . time();
        }
        
        return $filename;
    }
    
    /**
     * Download single file
     */
    public static function downloadFile(string $token, string $systemName, string $originalName): void {
        $filePath = UPLOADS_PATH . '/' . $token . '/' . $systemName;
        
        if (!file_exists($filePath)) {
            http_response_code(404);
            exit('File not found');
        }
        
        // Output buffering handling to prevent error pollution
        while (ob_get_level()) {
            ob_end_clean();
        }
        
        // Send file
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        
        $encodedFilename = rawurlencode($originalName);
        // Fallback filename for older browsers (ASCII only, no quotes)
        $fallbackFilename = preg_replace('/[^\x20-\x7e]/', '_', $originalName);
        $fallbackFilename = str_replace('"', '', $fallbackFilename);
        
        header("Content-Disposition: attachment; filename=\"{$fallbackFilename}\"; filename*=UTF-8''{$encodedFilename}");
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filePath));
        
        readfile($filePath);
        exit;
    }
    
    /**
     * Download all files as ZIP
     */
    public static function downloadAsZip(string $token, array $files, string $title): void {
        $zip = new ZipArchive();
        $zipFilename = tempnam(sys_get_temp_dir(), 'sfs_zip');
        
        // Use OVERWRITE to prevent "Using empty file" deprecation warning in PHP 8.0+
        if ($zip->open($zipFilename, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            http_response_code(500);
            exit('Could not create archive');
        }
        
        $baseDir = UPLOADS_PATH . '/' . $token . '/';
        
        foreach ($files as $file) {
            $filePath = $baseDir . $file['system_name'];
            if (file_exists($filePath)) {
                $zip->addFile($filePath, $file['original_name']);
            }
        }
        $zip->close();
        
        if (file_exists($zipFilename)) {
            // Output buffering handling
            while (ob_get_level()) {
                ob_end_clean();
            }

            header('Content-Type: application/zip');
            
            $downloadName = $title . '.zip';
            $encodedFilename = rawurlencode($downloadName);
            $fallbackFilename = preg_replace('/[^\x20-\x7e]/', '_', $downloadName);
            $fallbackFilename = str_replace('"', '', $fallbackFilename);
            
            header("Content-Disposition: attachment; filename=\"{$fallbackFilename}\"; filename*=UTF-8''{$encodedFilename}");
            header('Content-Length: ' . filesize($zipFilename));
            header('Pragma: no-cache');
            header('Expires: 0');
            
            readfile($zipFilename);
            unlink($zipFilename);
            exit;
        }
    }
    
    /**
     * Delete single file
     */
    public static function deleteFile(string $token, string $systemName): bool {
        $filePath = UPLOADS_PATH . '/' . $token . '/' . $systemName;
        if (file_exists($filePath)) {
            return unlink($filePath);
        }
        return false;
    }
    
    /**
     * Delete share folder and all files
     */
    public static function deleteShareFolder(string $token): bool {
        $dir = UPLOADS_PATH . '/' . $token;
        
        if (!is_dir($dir)) {
            return true;
        }
        
        return self::deleteDirectory($dir);
    }
    
    /**
     * Recursively delete directory
     */
    private static function deleteDirectory(string $dir): bool {
        if (!is_dir($dir)) {
            return false;
        }
        
        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $path = $dir . '/' . $file;
            if (is_dir($path)) {
                self::deleteDirectory($path);
            } else {
                unlink($path);
            }
        }
        return rmdir($dir);
    }
    
    /**
     * Get all upload folder names
     */
    public static function getUploadFolders(): array {
        $folders = [];
        if (is_dir(UPLOADS_PATH)) {
            $dirs = glob(UPLOADS_PATH . '/*', GLOB_ONLYDIR);
            foreach ($dirs as $dir) {
                $folders[] = basename($dir);
            }
        }
        return $folders;
    }
    
    /**
     * Get orphan folders (folders not in database)
     */
    public static function getOrphanFolders(array $dbTokens): array {
        $folders = self::getUploadFolders();
        return array_diff($folders, $dbTokens);
    }
    
    /**
     * Format file size for display
     */
    public static function formatSize(int $bytes): string {
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
    public static function deleteAllFiles(): void {
        if (!is_dir(UPLOADS_PATH)) return;
        
        $files = glob(UPLOADS_PATH . '/*');
        foreach ($files as $file) {
            if (is_dir($file)) {
                self::deleteDirectory($file);
            }
        }
    }

    /**
     * Get file count for a share folder
     */
    public static function getFileCount(string $token): int {
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

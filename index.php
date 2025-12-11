<?php
/**
 * Simple Secure File Share - Main Entry Point
 * 
 * @package SimpleSecureFileShare
 * @author  AI Generator, Direction: Lichiphen
 * @license Lichiphen Proprietary License v1.0
 * @link    https://gitlab.com/lichiphen/sqlite-simple-secure-file-share
 */

// Error reporting (enable for debugging)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Load configuration
require_once __DIR__ . '/config.php';

// Load classes
require_once BASE_PATH . '/src/Database.php';
require_once BASE_PATH . '/src/FileHandler.php';
require_once BASE_PATH . '/src/Auth.php';
require_once BASE_PATH . '/src/I18n.php';

// Initialize language
I18n::init();

// Start session for admin
Auth::startSession();

// Get request path
$requestUri = $_SERVER['REQUEST_URI'];
$path = parse_url($requestUri, PHP_URL_PATH);

// For PHP built-in server: handle requests directly
if (php_sapi_name() === 'cli-server') {
    // If the file exists, let PHP serve it (for assets)
    if (is_file(__DIR__ . $path)) {
        return false;
    }
}

// Normalize script directory path for Windows compatibility
$scriptName = $_SERVER['SCRIPT_NAME'];
$scriptDir = dirname($scriptName);
// Convert backslashes to slashes
$scriptDir = str_replace('\\', '/', $scriptDir);

// Remove base path from request if needed
if ($scriptDir !== '/' && strpos($path, $scriptDir) === 0) {
    $path = substr($path, strlen($scriptDir));
}

// Normalize final path
$path = '/' . trim($path, '/');

// Parse route
$segments = array_values(array_filter(explode('/', $path)));
$route = $segments[0] ?? '';
$param = $segments[1] ?? '';

// Route handling
// Get admin URL path from config (default: 'admin')
$adminPath = defined('ADMIN_URL_PATH') ? ADMIN_URL_PATH : 'admin';

switch ($route) {
    case '':
    case 'index.php':
        // Root URL - show cute 404 (don't reveal admin exists)
        http_response_code(404);
        require BASE_PATH . '/templates/404.php';
        exit;
    
    case $adminPath:
        handleAdmin($param, $adminPath);
        break;
    
    case 's':
        // Share page
        handleShare($param);
        break;
    
    default:
        // 404
        http_response_code(404);
        require BASE_PATH . '/templates/404.php';
        exit;
}

/**
 * Handle admin routes
 */
function handleAdmin(string $action, string $adminPath = 'admin'): void {
    switch ($action) {
        case 'login':
            handleAdminLogin($adminPath);
            break;
        
        case 'logout':
            Auth::logoutAdmin();
            header('Location: ' . BASE_URL . '/' . $adminPath . '/login');
            exit;
        
        case 'advanced':
            Auth::requireAdmin($adminPath);
            handleAdvancedPage();
            break;
        
        case 'howto':
            Auth::requireAdmin($adminPath);
            require BASE_PATH . '/templates/howto.php';
            break;
        
        default:
            // Main admin dashboard
            Auth::requireAdmin($adminPath);
            handleAdminDashboard();
            break;
    }
}

/**
 * Handle admin login
 */
function handleAdminLogin(string $adminPath = 'admin'): void {
    $error = false;
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $password = $_POST['password'] ?? '';
        if (Auth::loginAdmin($password)) {
            header('Location: ' . BASE_URL . '/' . $adminPath);
            exit;
        }
        $error = true;
    }
    
    // If already logged in, redirect
    if (Auth::isAdminLoggedIn()) {
        header('Location: ' . BASE_URL . '/' . $adminPath);
        exit;
    }
    
    require BASE_PATH . '/templates/login.php';
}

/**
 * Handle admin dashboard
 */
function handleAdminDashboard(): void {
    // Process form submissions
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_POST['action'] ?? '';
        $csrfToken = $_POST['csrf_token'] ?? '';
        
        if (!Auth::verifyCsrfToken($csrfToken)) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => __('invalid_access')]);
            exit;
        }
        
        switch ($action) {
            case 'upload':
                handleUpload();
                break;
            
            case 'delete':
            case 'delete_share':
                $shareId = (int)($_POST['share_id'] ?? 0);
                if ($shareId > 0) {
                     // Try to find share to get token for file deletion
                     // (Though orphan cleanup handles it, immediate deletion is better)
                     $allShares = Database::getAllShares();
                     $token = '';
                     foreach ($allShares as $s) {
                         if ($s['id'] === $shareId) {
                             $token = $s['token'];
                             break;
                         }
                     }
                     
                     if ($token) {
                         FileHandler::deleteShareFolder($token);
                     }
                     
                     if (Database::deleteShare($shareId)) {
                         Auth::setFlash(__('success'));
                     } else {
                         Auth::setFlash(__('error') . ': Delete failed');
                     }
                }
                break;
                
            case 'delete_all_shares':
                try {
                    Database::deleteAllShares();
                    FileHandler::deleteAllFiles();
                    Auth::setFlash(__('cleanup_complete'));
                } catch (\Throwable $e) {
                    Auth::setFlash(__('error') . ': ' . $e->getMessage());
                }
                break;
                
            case 'update_share':
                // AJAX: Update password
                $shareId = (int)($_POST['share_id'] ?? 0);
                // If password is provided, hash it. If empty string, set to null (remove password).
                // Note: Frontend should send empty string to remove password.
                $password = $_POST['password'] ?? '';
                $passwordHash = $password !== '' ? password_hash($password, PASSWORD_DEFAULT) : null;
                
                header('Content-Type: application/json');
                if (Database::updatePassword($shareId, $passwordHash)) {
                    echo json_encode(['success' => true]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Update failed']);
                }
                exit;
                
            case 'delete_file':
                // AJAX: Delete single file
                $shareId = (int)($_POST['share_id'] ?? 0);
                $systemName = $_POST['system_name'] ?? '';
                
                $shares = Database::getAllShares();
                $token = '';
                foreach ($shares as $s) {
                    if ($s['id'] === $shareId) {
                        $token = $s['token'];
                        break;
                    }
                }
                
                header('Content-Type: application/json');
                if ($token && $systemName) {
                    // Try to delete file (ignore if missing) and record
                    $fileDeleted = FileHandler::deleteFile($token, $systemName);
                    $recordDeleted = Database::deleteFileRecord($shareId, $systemName);
                    
                    if ($recordDeleted) {
                        echo json_encode(['success' => true]);
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Database delete failed']);
                    }
                } else {
                    echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
                }
                exit;
        }
        
        // Redirect to avoid form resubmission
        header('Location: ' . BASE_URL . '/admin');
        exit;
    }
    
    require BASE_PATH . '/templates/admin.php';
}

/**
 * Handle file upload
 */
function handleUpload(): void {
    // Start buffering to catch any warnings/errors that might corrupt JSON
    ob_start();
    
    $title = trim($_POST['share_title'] ?? '');
    $password = $_POST['share_password'] ?? null;
    $files = $_FILES['files'] ?? [];
    
    try {
        $result = FileHandler::processUpload($files, $title, $password);
    } catch (\Throwable $e) {
        error_log('Upload Error: ' . $e->getMessage());
        $result = ['success' => false, 'message' => __('upload_failed') . ' (Server Error)'];
    }
    
    // Discard any output generated during processing (warnings, etc.)
    ob_end_clean();
    
    // Return JSON for AJAX
    header('Content-Type: application/json');
    echo json_encode($result);
    exit;
}

/**
 * Handle share deletion
 */
function handleDelete(): void {
    $shareId = (int)($_POST['share_id'] ?? 0);
    
    if ($shareId > 0) {
        // Get token before deleting
        $share = Database::getShareByToken('');
        $shares = Database::getAllShares();
        foreach ($shares as $s) {
            if ($s['id'] === $shareId) {
                $share = $s;
                break;
            }
        }
        
        if ($share) {
            // Delete files
            FileHandler::deleteShareFolder($share['token']);
            // Delete database record
            Database::deleteShare($shareId);
        }
    }
    
    header('Location: ' . BASE_URL . '/admin');
    exit;
}

/**
 * Handle advanced settings page
 */
function handleAdvancedPage(): void {
    $csrfToken = Auth::generateCsrfToken();
    
    // Process cleanup actions
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_POST['action'] ?? '';
        $postCsrfToken = $_POST['csrf_token'] ?? '';
        
        if (Auth::verifyCsrfToken($postCsrfToken)) {
            try {
                switch ($action) {
                    case 'delete_share':
                        if (isset($_POST['share_id'])) {
                            Database::deleteShare((int)$_POST['share_id']);
                            Auth::setFlash(__('success'));
                        }
                        break;
                        
                    case 'delete': // Alias for delete_share in some templates
                        if (isset($_POST['share_id'])) {
                            Database::deleteShare((int)$_POST['share_id']);
                            Auth::setFlash(__('success'));
                        }
                        break;
                        
                    case 'cleanup_folders':
                        $dbTokens = Database::getAllTokens();
                        $orphanFolders = FileHandler::getOrphanFolders($dbTokens);
                        foreach ($orphanFolders as $folder) {
                            FileHandler::deleteShareFolder($folder);
                        }
                        Auth::setFlash(__('success'));
                        break;
                        
                    case 'cleanup_records':
                        $existingFolders = FileHandler::getUploadFolders();
                        $orphanRecords = Database::getOrphanRecords($existingFolders);
                        foreach ($orphanRecords as $record) {
                            Database::deleteShare($record['id']);
                        }
                        Auth::setFlash(__('success'));
                        break;
                    
                    case 'cleanup_sessions':
                        Auth::cleanupSessions();
                        Auth::setFlash(__('success'));
                        break;
                        
                    case 'delete_all_shares':
                        Database::deleteAllShares();
                        FileHandler::deleteAllFiles();
                        Auth::setFlash(__('cleanup_complete'));
                        break;
                }
            } catch (Exception $e) {
                Auth::setFlash(__('error') . ': ' . $e->getMessage());
            }
        } else {
             Auth::setFlash(__('error') . ': Invalid CSRF Token');
        }
        
        // Redirect to avoid form resubmission
        $redirectUrl = BASE_URL . '/admin';
        if (strpos($_SERVER['REQUEST_URI'], 'advanced') !== false) {
            $redirectUrl .= '/advanced';
        }
        header('Location: ' . $redirectUrl);
        exit;
    }
    
    // Get statistics
    $shares = Database::getAllShares();
    $uploadFolders = FileHandler::getUploadFolders();
    $dbTokens = Database::getAllTokens();
    $orphanFolders = FileHandler::getOrphanFolders($dbTokens);
    $orphanRecords = Database::getOrphanRecords($uploadFolders);
    $sessionStats = Auth::getSessionStats();
    
    require BASE_PATH . '/templates/advanced.php';
}

/**
 * Handle share page
 */
function handleShare(string $token): void {
    if (empty($token)) {
        http_response_code(404);
        require BASE_PATH . '/templates/share-not-found.php';
        exit;
    }
    
    // Get share
    $share = Database::getShareByToken($token);
    if (!$share) {
        http_response_code(404);
        require BASE_PATH . '/templates/share-not-found.php';
        exit;
    }
    
    // Get files
    $files = Database::getFilesForShare($share['id']);
    if (empty($files)) {
        http_response_code(404);
        require BASE_PATH . '/templates/share-not-found.php';
        exit;
    }
    
    // Check password
    if ($share['password_hash']) {
        $error = false;
        
        // Handle password submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sfs_password'])) {
            if (Auth::authenticateShare($_POST['sfs_password'], $share['password_hash'], $token, $share['id'])) {
                // Redirect to clear POST
                header('Location: ' . BASE_URL . '/s/' . $token);
                exit;
            }
            $error = true;
        }
        
        // Check if authenticated
        if (!Auth::isShareAuthenticated($token, $share['id'])) {
            require BASE_PATH . '/templates/password.php';
            exit;
        }
    }
    
    // Handle download
    if (isset($_GET['download'])) {
        // Referer check - prevent direct URL access
        $referer = $_SERVER['HTTP_REFERER'] ?? '';
        $expectedReferer = BASE_URL . '/s/' . $token;
        
        // Check if referer is from our share page
        if (empty($referer) || strpos($referer, BASE_URL . '/s/' . $token) === false) {
            http_response_code(403);
            require BASE_PATH . '/templates/access-denied.php';
            exit;
        }
        
        $downloadParam = $_GET['download'];
        
        // Increment download count
        Database::incrementDownloadCount($share['id']);
        
        if ($downloadParam === 'zip') {
            // ZIP download
            FileHandler::downloadAsZip($token, $files, $share['title']);
        } else {
            // Single file download
            $systemName = basename($downloadParam);
            $originalName = null;
            
            foreach ($files as $file) {
                if ($file['system_name'] === $systemName) {
                    $originalName = $file['original_name'];
                    break;
                }
            }
            
            if ($originalName) {
                FileHandler::downloadFile($token, $systemName, $originalName);
            } else {
                http_response_code(404);
                echo __('file_not_found');
                exit;
            }
        }
    }
    
    // Calculate total size
    $totalSize = 0;
    foreach ($files as $file) {
        $totalSize += $file['size'];
    }
    
    // Render share page
    require BASE_PATH . '/templates/share.php';
}

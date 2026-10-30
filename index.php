<?php
/**
 * Simple Secure File Share - Main Entry Point
 *
 * @package SimpleSecureFileShare
 * @author  AI Generator, Direction: Lichiphen
 * @license Lichiphen Proprietary License v1.0
 * @link    https://gitlab.com/lichiphen/sqlite-simple-secure-file-share
 */

// For PHP built-in server: serve only public assets directly.
// storage/ や src/ などはビルトインサーバーでは .htaccess が効かないため配信しない
if (PHP_SAPI === 'cli-server') {
    $builtinPath = (string) parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    if (preg_match('#^/assets/[A-Za-z0-9._-]+$#', $builtinPath) && is_file(__DIR__ . $builtinPath)) {
        return false;
    }
    unset($builtinPath);
}

// Load configuration (user settings) and system bootstrap
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/src/bootstrap.php';

// Load classes
require_once BASE_PATH . '/src/Security.php';
require_once BASE_PATH . '/src/Database.php';
require_once BASE_PATH . '/src/FileHandler.php';
require_once BASE_PATH . '/src/Auth.php';
require_once BASE_PATH . '/src/I18n.php';

// Security headers for every dynamic response
Security::sendHeaders();
Security::sendNoCacheHeaders();

// Initialize language
I18n::init();

// Get request path
$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$path = (string) parse_url($requestUri, PHP_URL_PATH);

// Normalize script directory path for Windows compatibility
$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
$scriptDir = str_replace('\\', '/', dirname($scriptName));

// Remove base path from request if needed
if ($scriptDir !== '/' && $scriptDir !== '.' && strpos($path, $scriptDir) === 0) {
    $path = substr($path, strlen($scriptDir));
}

// Normalize final path
$path = '/' . trim($path, '/');

// Parse route
$segments = array_values(array_filter(explode('/', $path), 'strlen'));
$route = $segments[0] ?? '';
$param = $segments[1] ?? '';

// Route handling
$adminPath = ADMIN_URL_PATH;

switch ($route) {
    case $adminPath:
        // 管理画面でのみセッションを開始（共有ページ閲覧者のセッションファイル乱造を防ぐ）
        Auth::startSession();
        handleAdmin($param, $adminPath);
        break;

    case 's':
        // Share page
        handleShare($param);
        break;

    case '':
    case 'index.php':
    default:
        // Root URL / unknown - show cute 404 (don't reveal admin exists)
        http_response_code(404);
        require BASE_PATH . '/templates/404.php';
        exit;
}

/**
 * JSON応答して終了
 */
function sendJson(array $data, int $status = 200): void
{
    while (ob_get_level()) {
        ob_end_clean();
    }
    http_response_code($status);
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode($data);
    exit;
}

/**
 * 管理画面トップへリダイレクト
 */
function redirectToAdmin(string $suffix = ''): void
{
    header('Location: ' . BASE_URL . '/' . ADMIN_URL_PATH . $suffix);
    exit;
}

/**
 * POST以外を拒否
 */
function requirePost(): void
{
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
        http_response_code(405);
        header('Allow: POST');
        exit;
    }
}

/**
 * 共有と配下ファイルを削除
 */
function deleteShareCompletely(int $shareId): bool
{
    $share = Database::getShareById($shareId);
    if (!$share) {
        return false;
    }
    FileHandler::deleteShareFolder((string) $share['token']);
    return Database::deleteShare($shareId);
}

/**
 * Handle admin routes
 */
function handleAdmin(string $action, string $adminPath = 'admin'): void
{
    switch ($action) {
        case 'login':
            handleAdminLogin($adminPath);
            break;

        case 'logout':
            // CSRF対策: ログアウトはトークン必須
            $token = (string) ($_POST['csrf_token'] ?? $_GET['csrf'] ?? '');
            if (Auth::isAdminLoggedIn() && Auth::verifyCsrfToken($token)) {
                Auth::logoutAdmin();
            }
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

        case '':
            // Main admin dashboard
            Auth::requireAdmin($adminPath);
            handleAdminDashboard();
            break;

        default:
            http_response_code(404);
            require BASE_PATH . '/templates/404.php';
            exit;
    }
}

/**
 * Handle admin login
 */
function handleAdminLogin(string $adminPath = 'admin'): void
{
    $error = false;

    // If already logged in, redirect
    if (Auth::isAdminLoggedIn()) {
        header('Location: ' . BASE_URL . '/' . $adminPath);
        exit;
    }

    if (!Auth::isAdminPasswordConfigured()) {
        $error = 'admin_password_insecure';
    } elseif (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
        $password = $_POST['password'] ?? '';
        $csrfToken = (string) ($_POST['csrf_token'] ?? '');

        if (!is_string($password) || !Auth::verifyCsrfToken($csrfToken)) {
            $error = 'invalid_request';
        } elseif (Auth::isAdminLoginLocked()) {
            $error = 'too_many_attempts';
        } elseif (Auth::loginAdmin($password)) {
            header('Location: ' . BASE_URL . '/' . $adminPath);
            exit;
        } else {
            $error = Auth::isAdminLoginLocked() ? 'too_many_attempts' : 'login_failed';
        }
    }

    $csrfToken = Auth::generateCsrfToken();
    require BASE_PATH . '/templates/login.php';
}

/**
 * Handle admin dashboard
 */
function handleAdminDashboard(): void
{
    // Process form submissions
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
        $action = (string) ($_POST['action'] ?? '');
        $csrfToken = (string) ($_POST['csrf_token'] ?? '');

        if (!Auth::verifyCsrfToken($csrfToken)) {
            sendJson(['success' => false, 'message' => __('invalid_access')], 403);
        }

        switch ($action) {
            case 'upload':
                handleUpload();
                break;

            case 'delete':
            case 'delete_share':
                $shareId = (int) ($_POST['share_id'] ?? 0);
                if ($shareId > 0) {
                    if (deleteShareCompletely($shareId)) {
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
                    error_log('Delete all failed: ' . $e->getMessage());
                    Auth::setFlash(__('error'));
                }
                break;

            case 'update_share':
                // AJAX: Update password (empty string = remove password)
                $shareId = (int) ($_POST['share_id'] ?? 0);
                $password = $_POST['password'] ?? '';
                if (!is_string($password) || strlen($password) > 1024 || !Database::getShareById($shareId)) {
                    sendJson(['success' => false, 'message' => 'Invalid parameters'], 400);
                }
                $passwordHash = $password !== '' ? password_hash($password, PASSWORD_DEFAULT) : null;

                if (Database::updatePassword($shareId, $passwordHash)) {
                    sendJson(['success' => true]);
                }
                sendJson(['success' => false, 'message' => 'Update failed']);
                break;

            case 'delete_file':
                // AJAX: Delete single file (must belong to the share)
                $shareId = (int) ($_POST['share_id'] ?? 0);
                $systemName = (string) ($_POST['system_name'] ?? '');

                $share = Database::getShareById($shareId);
                $file = $share ? Database::getFile($shareId, $systemName) : null;

                if (!$share || !$file) {
                    sendJson(['success' => false, 'message' => 'Invalid parameters'], 400);
                }

                FileHandler::deleteFile((string) $share['token'], (string) $file['system_name']);
                if (Database::deleteFileRecord($shareId, (string) $file['system_name'])) {
                    sendJson(['success' => true]);
                }
                sendJson(['success' => false, 'message' => 'Database delete failed']);
                break;
        }

        // Redirect to avoid form resubmission
        redirectToAdmin();
    }

    require BASE_PATH . '/templates/admin.php';
}

/**
 * Handle file upload
 */
function handleUpload(): void
{
    // Start buffering to catch any warnings/errors that might corrupt JSON
    ob_start();

    $title = $_POST['share_title'] ?? '';
    $password = $_POST['share_password'] ?? null;
    $files = $_FILES['files'] ?? [];

    if (!is_string($title) || ($password !== null && (!is_string($password) || strlen($password) > 1024)) || !is_array($files)) {
        sendJson(['success' => false, 'message' => __('upload_failed')], 400);
    }

    try {
        $result = FileHandler::processUpload($files, $title, $password);
    } catch (\Throwable $e) {
        error_log('Upload Error: ' . $e->getMessage());
        $result = ['success' => false, 'message' => __('upload_failed') . ' (Server Error)'];
    }

    // Discard any output generated during processing (warnings, etc.)
    sendJson($result);
}

/**
 * Handle advanced settings page
 */
function handleAdvancedPage(): void
{
    $csrfToken = Auth::generateCsrfToken();

    // Process cleanup actions
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
        $action = (string) ($_POST['action'] ?? '');
        $postCsrfToken = (string) ($_POST['csrf_token'] ?? '');

        if (Auth::verifyCsrfToken($postCsrfToken)) {
            try {
                switch ($action) {
                    case 'delete_share':
                    case 'delete': // Alias for delete_share in some templates
                        if (isset($_POST['share_id'])) {
                            deleteShareCompletely((int) $_POST['share_id']);
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
                            Database::deleteShare((int) $record['id']);
                        }
                        Auth::setFlash(__('success'));
                        break;

                    case 'cleanup_sessions':
                        Auth::cleanupSessions();
                        // SQLite軽量メンテナンスも同時実行
                        Database::runMaintenance();
                        Auth::setFlash(__('maintenance_complete'));
                        break;

                    case 'run_vacuum':
                        $result = Database::runVacuum();
                        if ($result['vacuum']) {
                            $reduced = FileHandler::formatSize((int) $result['size_reduced']);
                            Auth::setFlash(__('vacuum_complete') . ' (' . __('size_reduced') . ': ' . $reduced . ')');
                        } else {
                            error_log('VACUUM failed: ' . ($result['vacuum_error'] ?? 'Unknown error'));
                            Auth::setFlash(__('error'));
                        }
                        break;

                    case 'delete_all_shares':
                        Database::deleteAllShares();
                        FileHandler::deleteAllFiles();
                        Auth::setFlash(__('cleanup_complete'));
                        break;
                }
            } catch (\Throwable $e) {
                error_log('Advanced action failed: ' . $e->getMessage());
                Auth::setFlash(__('error'));
            }
        } else {
            Auth::setFlash(__('error') . ': Invalid CSRF Token');
        }

        // Redirect to avoid form resubmission
        redirectToAdmin('/advanced');
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
function handleShare(string $token): void
{
    // トークン形式を検証（不正な値はDB/ファイルシステムに渡さない）
    if (!Security::isValidToken($token)) {
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
    $shareId = (int) $share['id'];

    // Get files
    $files = Database::getFilesForShare($shareId);
    if (empty($files)) {
        http_response_code(404);
        require BASE_PATH . '/templates/share-not-found.php';
        exit;
    }

    // Check password
    if (!empty($share['password_hash'])) {
        $error = false;
        $passwordHash = (string) $share['password_hash'];

        // Handle password submission
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST' && isset($_POST['sfs_password'])) {
            $submitted = $_POST['sfs_password'];
            if (Auth::isShareLocked($token)) {
                $error = 'too_many_attempts';
            } elseif (is_string($submitted) && strlen($submitted) <= 1024
                && Auth::authenticateShare($submitted, $passwordHash, $token, $shareId)) {
                // Redirect to clear POST
                header('Location: ' . BASE_URL . '/s/' . $token, true, 303);
                exit;
            } else {
                $error = Auth::isShareLocked($token) ? 'too_many_attempts' : 'incorrect_password';
            }
        }

        // Check if authenticated
        if (!Auth::isShareAuthenticated($token, $shareId, $passwordHash)) {
            if ($error) {
                http_response_code($error === 'too_many_attempts' ? 429 : 401);
            }
            require BASE_PATH . '/templates/password.php';
            exit;
        }
    }

    // Handle download
    if (isset($_GET['download'])) {
        // 直リンク防止: 共有ページで発行した署名付きリンクのみ許可
        $signature = $_GET['dl'] ?? null;
        if (!Auth::verifyDownloadSignature($token, is_string($signature) ? $signature : null)) {
            http_response_code(403);
            require BASE_PATH . '/templates/access-denied.php';
            exit;
        }

        $downloadParam = is_string($_GET['download']) ? $_GET['download'] : '';

        if ($downloadParam === 'zip') {
            Database::incrementDownloadCount($shareId);
            FileHandler::downloadAsZip($token, $files, (string) $share['title']);
        } else {
            // Single file download (must be registered for this share)
            $file = Security::isSafeFileName($downloadParam) ? Database::getFile($shareId, $downloadParam) : null;

            if ($file) {
                Database::incrementDownloadCount($shareId);
                FileHandler::downloadFile($token, (string) $file['system_name'], (string) $file['original_name']);
            }

            http_response_code(404);
            echo htmlspecialchars(__('file_not_found'), ENT_QUOTES, 'UTF-8');
            exit;
        }
    }

    // Calculate total size
    $totalSize = 0;
    foreach ($files as $file) {
        $totalSize += (int) $file['size'];
    }

    $downloadSignature = Auth::downloadSignature($token);

    // Render share page
    require BASE_PATH . '/templates/share.php';
}

<?php
/**
 * Footer Partial
 * Include at bottom of admin pages - contains scripts
 */
if (!defined('BASE_PATH')) exit('No direct access.');

// Get CSRF token if not already set
$csrfToken = $csrfToken ?? Auth::generateCsrfToken();
?>

    <!-- Include Modals -->
    <?php include __DIR__ . '/success-modal.php'; ?>
    <?php include __DIR__ . '/edit-modal.php'; ?>
    <?php include __DIR__ . '/mobile-menu.php'; ?>

    <!-- Data Injection -->
    <script <?php echo Security::nonceAttr(); ?>>
        // Inject PHP data into JS
        window.SFS_DATA = <?php echo Security::js(isset($shares) ? array_values(array_map(function($s) {
            // パスワードハッシュはブラウザに渡さない（有無のみ）
            return [
                'id' => (int) $s['id'],
                'token' => $s['token'],
                'title' => $s['title'],
                'has_password' => !empty($s['password_hash']),
                'download_count' => (int) $s['download_count'],
                'created_at' => $s['created_at'],
                'file_count' => FileHandler::getFileCount($s['token']),
                'files' => array_map(function ($f) {
                    return [
                        'system_name' => $f['system_name'],
                        'original_name' => $f['original_name'],
                        'size' => (int) $f['size'],
                    ];
                }, Database::getFiles((int) $s['id'])),
            ];
        }, $shares)) : []); ?>;
        window.SFS_CSRF = <?php echo Security::js($csrfToken); ?>;
        window.SFS_LANG = {
            'locked': <?php echo Security::js(__('locked')); ?>,
            'public': <?php echo Security::js(__('public')); ?>,
            'copy': <?php echo Security::js(__('copy')); ?>,
            'delete': <?php echo Security::js(__('delete')); ?>,
            'delete_confirm': <?php echo Security::js(__('confirm_delete')); ?>,
            'edit': <?php echo Security::js(__('edit_share')); ?>,
            'files': <?php echo Security::js(__('files')); ?>,
            'open': "Open"
        };
    </script>
    <script <?php echo Security::nonceAttr(); ?>>
        window.SFS_CONFIG = {
            baseUrl: <?php echo Security::js(BASE_URL); ?>,
            adminUrl: <?php echo Security::js(BASE_URL . '/' . ADMIN_URL_PATH); ?>
        };
        window.TRANSLATIONS = {
            title_required: <?php echo Security::js(__('title_required')); ?>,
            files_required: <?php echo Security::js(__('files_required')); ?>,
            uploading: <?php echo Security::js(__('uploading')); ?>,
            upload_and_create: <?php echo Security::js(__('upload_and_create')); ?>,
            password_copied: <?php echo Security::js(__('password_copied')); ?>,
            url_copied: <?php echo Security::js(__('url_copied')); ?>,
            confirm_delete: <?php echo Security::js(__('confirm_delete')); ?>,
            reloading: <?php echo Security::js(__('reloading')); ?>
        };
    </script>
    <script src="<?php echo BASE_URL; ?>/assets/app.js?v=<?php echo APP_VERSION; ?>"></script>
    <script <?php echo Security::nonceAttr(); ?>>
        <?php if ($flashMsg = Auth::getFlash()): ?>
        document.addEventListener('DOMContentLoaded', () => {
            if (window.SFS && window.SFS.Toast) {
                window.SFS.Toast.show(<?php echo Security::js($flashMsg); ?>);
            }
        });
        <?php endif; ?>
    </script>
    
    <!-- Site Footer -->
    <footer style="text-align: center; padding: 1.5rem 1rem; margin-top: 2rem;">
        <p style="margin: 0; font-size: 0.75rem; color: var(--color-text-muted);">
            Powered by 
            <a href="https://gitlab.com/lichiphen/sqlite-simple-secure-file-share" target="_blank" rel="noopener noreferrer" style="color: var(--color-text-tertiary); text-decoration: none;">
                Simple Secure File Share
            </a>
            <span style="margin-left: 0.5rem;">Ver.<?php echo APP_VERSION; ?></span>
        </p>
    </footer>
</body>
</html>

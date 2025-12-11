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
    <script>
        // Inject PHP data into JS
        window.SFS_DATA = <?php echo json_encode(isset($shares) ? array_values(array_map(function($s) {
            $s['file_count'] = FileHandler::getFileCount($s['token']);
            $s['files'] = Database::getFiles($s['id']);
            return $s;
        }, $shares)) : []); ?>;
        window.SFS_CSRF = "<?php echo $csrfToken; ?>";
        window.SFS_LANG = {
            'locked': "<?php _e('locked'); ?>",
            'public': "<?php _e('public'); ?>",
            'copy': "<?php _e('copy'); ?>",
            'delete': "<?php _e('delete'); ?>",
            'delete_confirm': "<?php echo addslashes(__('confirm_delete')); ?>",
            'edit': "<?php _e('edit_share'); ?>",
            'files': "<?php _e('files'); ?>",
            'open': "Open"
        };
    </script>
    <script>
        window.SFS_CONFIG = {
            baseUrl: "<?php echo BASE_URL; ?>"
        };
        window.TRANSLATIONS = {
            title_required: "<?php echo addslashes(__('title_required')); ?>",
            files_required: "<?php echo addslashes(__('files_required')); ?>",
            uploading: "<?php echo addslashes(__('uploading')); ?>",
            upload_and_create: "<?php echo addslashes(__('upload_and_create')); ?>",
            password_copied: "<?php echo addslashes(__('password_copied')); ?>",
            url_copied: "<?php echo addslashes(__('url_copied')); ?>",
            confirm_delete: "<?php echo addslashes(__('confirm_delete')); ?>",
            reloading: "<?php echo addslashes(__('reloading')); ?>"
        };
    </script>
    <script src="<?php echo BASE_URL; ?>/assets/app.js?v=<?php echo APP_VERSION; ?>"></script>
    <script>
        <?php if ($flashMsg = Auth::getFlash()): ?>
        document.addEventListener('DOMContentLoaded', () => {
            if (window.SFS && window.SFS.Toast) {
                window.SFS.Toast.show("<?php echo addslashes($flashMsg); ?>");
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

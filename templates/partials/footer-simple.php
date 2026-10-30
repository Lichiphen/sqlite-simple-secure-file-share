<?php
/**
 * Footer Partial (Simple)
 * For pages that don't need modals/data injection
 */
if (!defined('BASE_PATH')) exit('No direct access.');
?>

    <!-- Mobile Menu -->
    <?php include __DIR__ . '/mobile-menu.php'; ?>

    <!-- Scripts -->
    <script src="<?php echo BASE_URL; ?>/assets/app.js"></script>
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

<?php
/**
 * Admin Login Template
 */

// Ensure direct access is prevented
if (!defined('BASE_PATH')) exit('No direct access.');

// Get admin URL path from config
$adminUrlPath = defined('ADMIN_URL_PATH') ? ADMIN_URL_PATH : 'admin';
?>
<!DOCTYPE html>
<html lang="<?php echo I18n::getCurrentLang(); ?>" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title><?php _e('admin_login'); ?> - <?php _e('app_name'); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/style.css">
</head>
<body class="flex items-center justify-center min-h-screen" style="padding: 1rem;">
    
    <div class="card" style="width: 100%; max-width: 24rem;">
        <div class="card-header text-center">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin: 0 auto 1rem;">
                <path d="M4 12v8a2 2 0 002 2h12a2 2 0 002-2v-8"/>
                <polyline points="16 6 12 2 8 6"/>
                <line x1="12" y1="2" x2="12" y2="15"/>
            </svg>
            <h1 style="margin: 0; font-size: 1.5rem;"><?php _e('app_name'); ?></h1>
            <p style="margin: 0.5rem 0 0; opacity: 0.8; font-size: 0.875rem;"><?php _e('admin_login'); ?></p>
        </div>
        
        <div class="card-body">
            <?php if ($error): ?>
            <div class="alert alert-error mb-4">
                <?php _e('login_failed'); ?>
            </div>
            <?php endif; ?>
            
            <form method="post" action="<?php echo BASE_URL; ?>/<?php echo $adminUrlPath; ?>/login">
                <div class="form-group">
                    <label class="form-label" for="password"><?php _e('password'); ?></label>
                    <input type="password" id="password" name="password" class="form-input" required autofocus>
                </div>
                
                <button type="submit" class="btn btn-primary btn-block btn-lg">
                    <?php _e('login'); ?>
                </button>
            </form>
        </div>
        
        <!-- Language Switcher -->
        <div style="padding: 1rem; border-top: 1px solid var(--color-border); text-align: center; background: var(--color-bg-tertiary);">
            <?php foreach (I18n::getSupportedLanguages() as $lang): ?>
                <a href="?lang=<?php echo $lang; ?>" 
                   style="padding: 0.25rem 0.75rem; margin: 0 0.25rem; border-radius: var(--radius-md); 
                          font-size: 0.75rem; text-decoration: none;
                          <?php echo I18n::getCurrentLang() === $lang 
                              ? 'background: var(--color-accent-light); color: var(--color-accent);' 
                              : 'color: var(--color-text-muted);'; ?>">
                    <?php echo I18n::getLanguageName($lang); ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
    
    <script src="<?php echo BASE_URL; ?>/assets/app.js"></script>
    
    <!-- Site Footer -->
    <footer style="text-align: center; padding: 1.5rem 1rem; position: fixed; bottom: 0; left: 0; right: 0;">
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

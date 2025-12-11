<?php
/**
 * Password Authentication Template
 */

// Ensure direct access is prevented
if (!defined('BASE_PATH')) exit('No direct access.');
?>
<!DOCTYPE html>
<html lang="<?php echo I18n::getCurrentLang(); ?>" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title><?php _e('password_authentication'); ?> - <?php echo htmlspecialchars($share['title']); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/style.css">
</head>
<body class="flex items-center justify-center min-h-screen" style="padding: 1rem;">
    
    <div class="card" style="width: 100%; max-width: 28rem;">
        <!-- Header -->
        <div style="padding: 1.5rem; background: var(--color-bg-tertiary); text-align: center; border-bottom: 1px solid var(--color-border);">
            <div style="display: inline-flex; align-items: center; justify-content: center; width: 4rem; height: 4rem; 
                        background: var(--color-accent-light); border-radius: 50%; margin-bottom: 1rem;">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: var(--color-accent);">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                    <path d="M7 11V7a5 5 0 0110 0v4"/>
                </svg>
            </div>
            <h1 style="margin: 0; font-size: 1.25rem; color: var(--color-text-primary);">
                <?php _e('password_protected_area'); ?>
            </h1>
        </div>
        
        <!-- Body -->
        <div class="card-body">
            <p class="text-sm text-center mb-4" style="color: var(--color-text-secondary);">
                "<strong><?php echo htmlspecialchars($share['title']); ?></strong>"<br>
                <?php _e('password_required_message'); ?>
            </p>
            
            <?php if ($error): ?>
            <div class="alert alert-error mb-4">
                <?php _e('incorrect_password'); ?>
            </div>
            <?php endif; ?>
            
            <form method="post" autocomplete="off">
                <div class="form-group">
                    <label for="sfs-password" class="sr-only"><?php _e('password'); ?></label>
                    <div style="position: relative;">
                        <input type="text" name="sfs_password" id="sfs-password" required
                               class="form-input password-masked"
                               placeholder="<?php _e('enter_password'); ?>"
                               autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"
                               style="padding-right: 3rem;">
                        <button type="button" data-toggle-password="sfs-password"
                                style="position: absolute; right: 0.75rem; top: 50%; transform: translateY(-50%); 
                                       background: none; border: none; cursor: pointer; color: var(--color-text-muted);">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                        </button>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary btn-block btn-lg">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                        <path d="M7 11V7a5 5 0 0110 0v4"/>
                    </svg>
                    <?php _e('authenticate_and_view'); ?>
                </button>
            </form>
        </div>
        
        <!-- Footer -->
        <div style="padding: 1rem; border-top: 1px solid var(--color-border); text-align: center; background: var(--color-bg-tertiary);">
            <p class="text-xs" style="color: var(--color-text-muted);"><?php _e('app_name'); ?></p>
        </div>
    </div>
    
    <script src="<?php echo BASE_URL; ?>/assets/app.js"></script>
</body>
</html>

<?php
/**
 * Header Partial
 * Include at top of admin pages
 */
if (!defined('BASE_PATH')) exit('No direct access.');

// Get admin URL path from config
$adminUrlPath = defined('ADMIN_URL_PATH') ? ADMIN_URL_PATH : 'admin';
?>
<!DOCTYPE html>
<html lang="<?php echo I18n::getCurrentLang(); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="robots" content="noindex, nofollow">
    <meta name="color-scheme" content="dark light">
    <title><?php echo $pageTitle ?? __('dashboard'); ?> - <?php _e('app_name'); ?></title>

    <!-- Theme initialization: System-first + light/dark/system 3-mode -->
    <script>
        (function () {
            var media = window.matchMedia('(prefers-color-scheme: dark)');

            function resolveTheme(mode) {
                if (mode === 'light' || mode === 'dark') return mode;
                return media.matches ? 'dark' : 'light';
            }

            function applyTheme(mode) {
                var theme = resolveTheme(mode);
                document.documentElement.dataset.theme = theme;
                document.documentElement.style.colorScheme = theme;
            }

            var storedMode = localStorage.getItem('theme_mode') || 'system';
            applyTheme(storedMode);

            media.addEventListener('change', function () {
                var mode = localStorage.getItem('theme_mode') || 'system';
                if (mode === 'system') {
                    applyTheme('system');
                }
            });

            window.SFS_THEME = {
                setMode: function (mode) {
                    localStorage.setItem('theme_mode', mode);
                    applyTheme(mode);
                },
                getMode: function () {
                    return localStorage.getItem('theme_mode') || 'system';
                }
            };
        })();
    </script>

    <!-- Base colors for flash prevention (inline, before external CSS) -->
    <style>
        html[data-theme="dark"], html[data-theme="dark"] body {
            background-color: #0b0c10;
            color: #f5f5f5;
        }
        html[data-theme="light"], html[data-theme="light"] body {
            background-color: #f9fafb;
            color: #111827;
        }
    </style>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/style.css?v=<?php echo APP_VERSION; ?>">
</head>
<body>
    <!-- Header -->
    <header class="header relative z-40">
        <div class="container mx-auto px-4 flex justify-between items-center h-16">
            <div class="header-logo flex items-center gap-2">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M4 12v8a2 2 0 002 2h12a2 2 0 002-2v-8"/>
                    <polyline points="16 6 12 2 8 6"/>
                    <line x1="12" y1="2" x2="12" y2="15"/>
                </svg>
                <span class="font-bold text-lg"><?php _e('app_name'); ?></span>
            </div>
            
            <!-- Desktop Nav -->
            <nav class="header-nav hidden md:flex items-center gap-6">
                <a href="<?php echo BASE_URL; ?>/<?php echo $adminUrlPath; ?>" class="nav-link <?php echo ($currentPage ?? '') === 'dashboard' ? 'active' : ''; ?>"><?php _e('dashboard'); ?></a>
                <a href="<?php echo BASE_URL; ?>/<?php echo $adminUrlPath; ?>/advanced" class="nav-link <?php echo ($currentPage ?? '') === 'advanced' ? 'active' : ''; ?>"><?php _e('advanced_settings'); ?></a>
                <a href="<?php echo BASE_URL; ?>/<?php echo $adminUrlPath; ?>/howto" class="nav-link <?php echo ($currentPage ?? '') === 'howto' ? 'active' : ''; ?>"><?php _e('how_to_use'); ?></a>
                
                <div class="flex items-center gap-2 border-l pl-4 ml-2 border-border">
                    <!-- Theme Toggle (3 modes) -->
                    <button type="button" class="theme-toggle-btn" data-theme-mode="light" title="<?php _e('light_mode'); ?>">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg>
                    </button>
                    <button type="button" class="theme-toggle-btn" data-theme-mode="dark" title="<?php _e('dark_mode'); ?>">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
                    </button>
                    <button type="button" class="theme-toggle-btn" data-theme-mode="system" title="<?php _e('system'); ?>">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
                    </button>
                    
                    <!-- Desktop Language -->
                    <div style="display: flex; gap: 0.5rem; margin-left: 0.75rem;">
                        <?php foreach (I18n::getSupportedLanguages() as $lang): ?>
                            <a href="?lang=<?php echo $lang; ?>" style="padding: 0.25rem 0.75rem; font-size: 0.8rem; font-weight: 600; border-radius: 4px; <?php echo I18n::getCurrentLang() === $lang ? 'background: var(--color-accent); color: white;' : 'background: var(--color-bg-tertiary); color: var(--color-text-secondary);'; ?>">
                                <?php echo strtoupper($lang); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>

                    <a href="<?php echo BASE_URL; ?>/<?php echo $adminUrlPath; ?>/logout" class="btn btn-ghost btn-sm ml-2" title="<?php _e('logout'); ?>">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                    </a>
                </div>
            </nav>
        </div>
    </header>

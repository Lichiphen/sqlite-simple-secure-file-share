<?php
/**
 * Mobile Menu Partial
 * Full-screen modal menu for mobile devices
 */
if (!defined('BASE_PATH')) exit('No direct access.');

// Get admin URL path from config
$adminUrlPath = defined('ADMIN_URL_PATH') ? ADMIN_URL_PATH : 'admin';
?>
<!-- Mobile Menu Modal (Fixed full screen) -->
<div id="mobile-menu" class="hidden" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; z-index: 200; background: var(--color-bg-primary);">
    <div style="display: flex; flex-direction: column; height: 100%; padding: 1.5rem;">
        <!-- Top Row: Logout + Close -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <a href="<?php echo BASE_URL; ?>/<?php echo $adminUrlPath; ?>/logout" style="padding: 0.5rem 1.5rem; border: 1px solid var(--color-border); border-radius: 8px; font-size: 0.9rem; color: var(--color-text-primary); text-decoration: none;">
                <?php _e('logout'); ?>
            </a>
            <button onclick="document.getElementById('mobile-menu').classList.add('hidden')" style="padding: 0.5rem; background: none; border: none; cursor: pointer; color: var(--color-text-primary);">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"/>
                    <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </div>
        
        <!-- Navigation Links -->
        <nav style="flex: 1; display: flex; flex-direction: column; gap: 0;">
            <a href="<?php echo BASE_URL; ?>/<?php echo $adminUrlPath; ?>" style="font-size: 1.1rem; padding: 1.25rem 0; border-bottom: 1px solid var(--color-border); color: var(--color-text-primary); text-decoration: none;"><?php _e('dashboard'); ?></a>
            <a href="<?php echo BASE_URL; ?>/<?php echo $adminUrlPath; ?>/advanced" style="font-size: 1.1rem; padding: 1.25rem 0; border-bottom: 1px solid var(--color-border); color: var(--color-text-primary); text-decoration: none;"><?php _e('advanced_settings'); ?></a>
            <a href="<?php echo BASE_URL; ?>/<?php echo $adminUrlPath; ?>/howto" style="font-size: 1.1rem; padding: 1.25rem 0; border-bottom: 1px solid var(--color-border); color: var(--color-text-primary); text-decoration: none;"><?php _e('how_to_use'); ?></a>
        </nav>
        
        <!-- Bottom Settings -->
        <div style="display: flex; flex-direction: column; gap: 0;">
            <!-- Theme (3 modes) -->
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 1rem 0; border-bottom: 1px solid var(--color-border);">
                <span style="font-size: 1rem; color: var(--color-text-primary);"><?php _e('theme'); ?></span>
                <div style="display: flex; gap: 0.5rem;">
                    <button type="button" class="theme-toggle-btn" data-theme-mode="light" style="width: 44px; height: 44px; display: flex; align-items: center; justify-content: center; border: 1px solid var(--color-border); border-radius: 8px; background: var(--color-bg-secondary); cursor: pointer; color: var(--color-text-primary);" title="<?php _e('light_mode'); ?>">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg>
                    </button>
                    <button type="button" class="theme-toggle-btn" data-theme-mode="dark" style="width: 44px; height: 44px; display: flex; align-items: center; justify-content: center; border: 1px solid var(--color-border); border-radius: 8px; background: var(--color-bg-secondary); cursor: pointer; color: var(--color-text-primary);" title="<?php _e('dark_mode'); ?>">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
                    </button>
                    <button type="button" class="theme-toggle-btn" data-theme-mode="system" style="width: 44px; height: 44px; display: flex; align-items: center; justify-content: center; border: 1px solid var(--color-border); border-radius: 8px; background: var(--color-bg-secondary); cursor: pointer; color: var(--color-text-primary);" title="<?php _e('system'); ?>">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
                    </button>
                </div>
            </div>
            
            <!-- Language -->
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 1rem 0; border-bottom: 1px solid var(--color-border);">
                <span style="font-size: 1rem; color: var(--color-text-primary);"><?php _e('language'); ?></span>
                <div style="display: flex; gap: 0.5rem;">
                    <?php foreach (I18n::getSupportedLanguages() as $lang): ?>
                        <a href="?lang=<?php echo $lang; ?>" style="width: 44px; height: 44px; display: flex; align-items: center; justify-content: center; border: 1px solid var(--color-border); border-radius: 8px; font-weight: 600; text-decoration: none; <?php echo I18n::getCurrentLang() === $lang ? 'background: var(--color-accent); color: white;' : 'background: var(--color-bg-secondary); color: var(--color-text-primary);'; ?>">
                            <?php echo strtoupper($lang); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <!-- Close Button at bottom -->
            <button onclick="document.getElementById('mobile-menu').classList.add('hidden')" style="margin-top: 1.5rem; padding: 1rem; background: transparent; border: 2px solid var(--color-text-primary); border-radius: 8px; font-weight: 500; cursor: pointer; color: var(--color-text-primary); font-size: 1rem;">
                <?php _e('close'); ?>
            </button>
        </div>
    </div>
</div>

<!-- Floating Mobile Menu Button -->
<button id="floating-menu-btn" class="md:hidden" style="position: fixed;bottom: calc(env(safe-area-inset-bottom, 20px) + 90px);left: 1rem;z-index: 100;display: flex;flex-direction: column;align-items: center;gap: 4px;padding: 12px 16px;border-radius: 16px;background: var(--color-bg-secondary);border: 1px solid var(--color-border);box-shadow: 0 4px 12px rgba(0,0,0,0.15);cursor: pointer;color: var(--color-text-primary);opacity: 0.85;" onclick="document.getElementById('mobile-menu').classList.toggle('hidden')">
    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <line x1="4" y1="12" x2="20" y2="12"/>
        <line x1="4" y1="6" x2="20" y2="6"/>
        <line x1="4" y1="18" x2="20" y2="18"/>
    </svg>
    <span style="font-size: 0.65rem; font-weight: 600; letter-spacing: 0.5px;">MENU</span>
</button>

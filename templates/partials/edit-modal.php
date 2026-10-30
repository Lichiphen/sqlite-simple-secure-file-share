<?php
/**
 * Edit Modal Partial
 * For editing share password and deleting individual files
 */
if (!defined('BASE_PATH')) exit('No direct access.');
?>
<!-- Edit Share Modal -->
<div id="edit-modal" class="popup-overlay">
    <div class="popup-content" style="max-width: 500px; width: 90%; max-height: 90vh; overflow-y: auto; text-align: left;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h3 style="margin: 0; display: flex; align-items: center; gap: 0.5rem; font-size: 1.25rem;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                </svg>
                <span id="edit-modal-title"><?php _e('edit_share'); ?></span>
            </h3>
            <button data-sfs-close-modal="edit-modal" style="background: none; border: none; cursor: pointer; padding: 0.5rem; color: var(--color-text-primary);">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"/>
                    <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </div>
        
        <!-- Password Section -->
        <div style="margin-bottom: 2rem;">
            <label style="display: block; font-weight: 500; margin-bottom: 0.5rem; color: var(--color-text-secondary);"><?php _e('password'); ?></label>
            <div style="display: flex; gap: 0.5rem; align-items: center;">
                <div style="position: relative; flex: 1;">
                    <input type="text" id="edit-password" placeholder="<?php _e('enter_password'); ?>" class="form-input password-masked" style="width: 100%; padding-right: 40px;" autocomplete="off">
                    <button type="button" class="edit-password-toggle" style="position: absolute; right: 8px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: var(--color-text-muted); padding: 4px;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                    </button>
                </div>
                <!-- Generate Random Password (Dice icon) -->
                <button type="button" class="btn btn-secondary btn-sm" id="edit-generate-password" title="<?php _e('auto_generate'); ?>" style="padding: 0.5rem;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="2" y="2" width="20" height="20" rx="3" ry="3"/>
                        <circle cx="8" cy="8" r="1.5" fill="currentColor"/>
                        <circle cx="16" cy="8" r="1.5" fill="currentColor"/>
                        <circle cx="8" cy="16" r="1.5" fill="currentColor"/>
                        <circle cx="16" cy="16" r="1.5" fill="currentColor"/>
                        <circle cx="12" cy="12" r="1.5" fill="currentColor"/>
                    </svg>
                </button>
                <!-- Update Button -->
                <button id="update-password-btn" class="btn btn-primary btn-sm">
                    <?php _e('update'); ?>
                </button>
            </div>
            <p style="font-size: 0.75rem; color: var(--color-text-muted); margin-top: 0.5rem;">
                <?php _e('leave_empty_to_unlock'); ?>
            </p>
            <input type="hidden" id="edit-share-id">
        </div>
        
        <!-- Files Section -->
        <div>
            <label style="display: block; font-weight: 500; margin-bottom: 0.5rem; color: var(--color-text-secondary);"><?php _e('files'); ?></label>
            <div id="edit-file-list" style="border: 1px solid var(--color-border); border-radius: var(--radius-md); max-height: 200px; overflow-y: auto; background: var(--color-bg-tertiary); padding: 0.5rem;">
                <!-- File list rendered by JS -->
            </div>
        </div>
        
        <div style="margin-top: 2rem; display: flex; justify-content: space-between; align-items: center;">
            <!-- Delete Share Button -->
            <button id="delete-share-btn" class="btn btn-sm" style="background: var(--color-error); color: white;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                <?php _e('delete'); ?>
            </button>
            <button data-sfs-close-modal="edit-modal" class="btn btn-secondary">
                <?php _e('close'); ?>
            </button>
        </div>
    </div>
</div>

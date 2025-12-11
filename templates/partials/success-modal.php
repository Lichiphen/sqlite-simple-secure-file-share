<?php
/**
 * Success Modal Partial
 * Shown after successful upload
 */
if (!defined('BASE_PATH')) exit('No direct access.');
?>
<!-- Success Modal -->
<div id="success-modal" class="popup-overlay">
    <div class="popup-content text-center" style="max-width: 360px; padding: 2rem;">
        <div class="popup-icon success" style="margin: 0 auto 1.5rem auto;">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="20 6 9 17 4 12"/>
            </svg>
        </div>
        <h3 style="font-size: 1.25rem; font-weight: bold; margin-bottom: 0.5rem;"><?php _e('upload_complete'); ?></h3>
        <p style="color: var(--color-text-muted); font-size: 0.9rem; margin-bottom: 1.5rem;"><?php _e('files_saved_securely'); ?></p>
        
        <!-- URL + Copy in one row -->
        <div style="display: flex; gap: 0.5rem; margin-bottom: 1rem;">
            <button type="button" id="modal-url-btn" class="btn btn-secondary" style="flex: 1;" onclick="window.open(document.getElementById('modal-share-url').value, '_blank')">
                URL
            </button>
            <button type="button" id="modal-copy-btn" class="btn btn-secondary" style="flex: 1;">
                <?php _e('copy'); ?>
            </button>
        </div>
        
        <!-- Hidden URL input for copy functionality -->
        <input type="hidden" id="modal-share-url" value="">
        
        <!-- Close button -->
        <button type="button" onclick="window.location.reload()" class="btn w-full" style="background: transparent; border: 2px solid var(--color-text-primary); color: var(--color-text-primary);">
            <?php _e('close'); ?>
        </button>
    </div>
</div>

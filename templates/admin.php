<?php
/**
 * Admin Dashboard Template
 */

// Ensure direct access is prevented
if (!defined('BASE_PATH')) exit('No direct access.');

// Get all shares
$shares = Database::getAllShares();
$csrfToken = Auth::generateCsrfToken();
$pageTitle = __('dashboard');
$currentPage = 'dashboard';

// Include header
include __DIR__ . '/partials/header.php';
?>

    <main class="container" style="padding: 2rem 1rem;">
        <!-- Upload Form Card -->
        <div class="card mb-10">
            <div class="card-header">
                <h2 style="margin: 0; font-size: 1.25rem;"><?php _e('create_new_share'); ?></h2>
            </div>
            <div class="card-body">
                <form id="upload-form" method="post" enctype="multipart/form-data" autocomplete="off">
                    <input type="hidden" name="action" value="upload">
                    <input type="hidden" name="csrf_token" id="csrf-token" value="<?php echo htmlspecialchars($csrfToken); ?>">
                    
                    <!-- Share Title -->
                    <div class="form-group">
                        <label for="share-title" class="form-label"><?php _e('share_title'); ?></label>
                        <input type="text" id="share-title" name="title" class="form-input" placeholder="<?php _e('share_title_placeholder'); ?>" required>
                    </div>
                    
                    <!-- Password Protection -->
                    <div class="form-group">
                        <label class="form-label"><?php _e('password_protection'); ?></label>
                        <div style="display: flex; gap: 1.5rem; margin-bottom: 0.75rem;">
                            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                                <input type="radio" name="enable_password" value="0" checked>
                                <span><?php _e('none'); ?></span>
                            </label>
                            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                                <input type="radio" name="enable_password" value="1">
                                <span><?php _e('yes'); ?></span>
                            </label>
                        </div>
                        
                        <div id="password-container" class="hidden">
                            <div style="display: flex; gap: 0.5rem; align-items: center;">
                                <div style="position: relative; flex: 1;">
                                    <input type="text" id="share-password" name="password" 
                                           class="form-input password-masked" 
                                           placeholder="<?php _e('enter_password'); ?>"
                                           style="padding-right: 40px;">
                                    <button type="button" data-toggle-password="share-password" 
                                            style="position: absolute; right: 8px; top: 50%; transform: translateY(-50%);
                                                   background: none; border: none; cursor: pointer; color: var(--color-text-muted);">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                            <circle cx="12" cy="12" r="3"/>
                                        </svg>
                                    </button>
                                </div>
                                <button type="button" class="btn btn-secondary" data-generate-password="share-password">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M23 4v6h-6"/>
                                        <path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/>
                                    </svg>
                                    <?php _e('auto_generate'); ?>
                                </button>
                            </div>
                            <p class="text-xs mt-2" style="color: var(--color-text-muted);"><?php _e('password_note'); ?></p>
                        </div>
                    </div>
                    
                    <!-- File Drop Zone -->
                    <div class="form-group">
                        <label class="form-label"><?php _e('file_selection'); ?></label>
                        <div id="drop-zone" class="drop-zone">
                            <svg class="drop-zone-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/>
                                <polyline points="17 8 12 3 7 8"/>
                                <line x1="12" y1="3" x2="12" y2="15"/>
                            </svg>
                            <p class="font-medium" style="color: var(--color-text-secondary);"><?php _e('drag_drop_files'); ?></p>
                            <p class="text-sm mt-2" style="color: var(--color-text-muted);">
                                <?php _e('or'); ?> 
                                <span style="color: var(--color-accent); font-weight: 600; text-decoration: underline;"><?php _e('click_to_select'); ?></span>
                            </p>
                            <input type="file" id="file-input" multiple class="drop-zone-input">
                        </div>
                        
                        <!-- File List -->
                        <div id="file-list-container" class="hidden mt-4">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm font-bold"><?php _e('files_waiting'); ?></span>
                                <button type="button" id="clear-all" class="text-sm" style="color: var(--color-error); cursor: pointer; background: none; border: none;">
                                    <?php _e('clear_all'); ?>
                                </button>
                            </div>
                            <div id="file-list" class="file-list"></div>
                        </div>
                    </div>
                    
                    <!-- Progress Bar -->
                    <div id="progress-wrapper" class="hidden mb-4">
                        <div class="progress">
                            <div id="progress-bar" class="progress-bar" style="width: 0%"></div>
                        </div>
                        <p id="progress-text" class="text-sm text-center mt-2">0%</p>
                    </div>
                    
                    <!-- Submit Button -->
                    <button type="submit" id="submit-btn" class="btn btn-primary w-full" style="padding: 1rem;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/>
                            <polyline points="17 8 12 3 7 8"/>
                            <line x1="12" y1="3" x2="12" y2="15"/>
                        </svg>
                        <?php _e('upload_and_create'); ?>
                    </button>
                </form>
            </div>
        </div>

        <!-- Share List Card -->
        <?php if (count($shares) > 0): ?>
            <div class="card">
                <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/>
                        </svg>
                        <h2 style="margin: 0; font-size: 1.125rem; font-weight: 600;"><?php _e('active_shares'); ?></h2>
                    </div>
                    
                    <!-- Delete All -->
                    <form method="post" onsubmit="return confirm('<?php echo addslashes(__('confirm_delete_all')); ?>');">
                        <input type="hidden" name="action" value="delete_all_shares">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
                        <button type="submit" class="btn btn-sm" style="background: var(--color-error); color: white; display: flex; align-items: center; gap: 0.25rem;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                            <?php _e('delete_all'); ?>
                        </button>
                    </form>
                </div>
                
                <!-- List Controls -->
                <div id="list-controls" style="display: flex; justify-content: space-between; align-items: center; padding: 1rem; border-bottom: 1px solid var(--color-border); flex-wrap: wrap; gap: 1rem;">
                    <div class="search-box" style="flex: 1; min-width: 200px;">
                         <input type="text" id="share-search" placeholder="<?php _e('search'); ?>..." class="form-input" style="padding: 0.5rem; min-width: 200px; border-radius: var(--radius-md); border: 1px solid var(--color-border);">
                    </div>
                    <div class="pagination-controls" style="display: flex; gap: 1rem; align-items: center;">
                        <select id="per-page-select" class="form-select" style="padding: 0.5rem; border-radius: var(--radius-md); border: 1px solid var(--color-border); background: var(--color-bg-primary); color: var(--color-text-primary);">
                            <option value="10">10 / Page</option>
                            <option value="50">50 / Page</option>
                            <option value="100">100 / Page</option>
                        </select>
                        <div id="pagination-pages" class="btn-group" style="display: flex; gap: 0.25rem;">
                            <!-- Pages generated by JS -->
                        </div>
                    </div>
                </div>

                <!-- Table Wrapper for horizontal scroll -->
                <div class="table-wrapper" style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
                    <table class="table" id="shares-table">
                        <thead>
                            <tr>
                                <th data-sort="id" class="sortable" style="cursor:pointer; user-select: none;">ID <span class="sort-icon"></span></th>
                                <th data-sort="title" class="sortable" style="cursor:pointer; user-select: none;"><?php _e('title'); ?> <span class="sort-icon"></span></th>
                                <th data-sort="is_locked" class="sortable" style="cursor:pointer; user-select: none;"><?php _e('status'); ?> <span class="sort-icon"></span></th>
                                <th class="text-center sortable" data-sort="file_count" style="cursor:pointer; user-select: none;"><?php _e('files'); ?> <span class="sort-icon"></span></th>
                                <th class="text-center sortable" data-sort="download_count" style="cursor:pointer; user-select: none;"><?php _e('dl_count'); ?> <span class="sort-icon"></span></th>
                                <th data-sort="created_at" class="sortable" style="cursor:pointer; user-select: none;"><?php _e('created'); ?> <span class="sort-icon"></span></th>
                                <th class="text-right"><?php _e('actions'); ?></th>
                            </tr>
                        </thead>
                        <tbody id="share-list-body">
                            <!-- Content rendered by JS -->
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
    </main>

<?php
// Include footer (contains modals, mobile menu, and scripts)
include __DIR__ . '/partials/footer.php';
?>

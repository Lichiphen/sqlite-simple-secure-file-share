<?php
/**
 * Advanced Settings Template
 */

// Ensure direct access is prevented
if (!defined('BASE_PATH'))
    exit('No direct access.');

$pageTitle = __('advanced_settings');
$currentPage = 'advanced';
$csrfToken = Auth::generateCsrfToken();

// Include header
include __DIR__ . '/partials/header.php';
?>

<main class="container" style="padding: 2rem 1rem;">
    <div class="card">
        <div class="card-body">
            <h1
                style="margin: 0 0 0.5rem; font-size: 1.5rem; color: var(--color-text-primary); display: flex; align-items: center; gap: 0.5rem;">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    style="color: var(--color-warning);">
                    <circle cx="12" cy="12" r="3" />
                    <path
                        d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-2 2 2 2 0 01-2-2v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83 0 2 2 0 010-2.83l.06-.06a1.65 1.65 0 00.33-1.82 1.65 1.65 0 00-1.51-1H3a2 2 0 01-2-2 2 2 0 012-2h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 010-2.83 2 2 0 012.83 0l.06.06a1.65 1.65 0 001.82.33H9a1.65 1.65 0 001-1.51V3a2 2 0 012-2 2 2 0 012 2v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 0 2 2 0 010 2.83l-.06.06a1.65 1.65 0 00-.33 1.82V9a1.65 1.65 0 001.51 1H21a2 2 0 012 2 2 2 0 01-2 2h-.09a1.65 1.65 0 00-1.51 1z" />
                </svg>
                <?php _e('advanced_settings'); ?>
            </h1>

            <p style="color: var(--color-text-secondary); margin-bottom: 0.5rem;">
                <?php _e('advanced_intro'); ?>
            </p>
            <p style="color: var(--color-error); font-weight: 600; margin-bottom: 2rem;">
                <?php _e('warning_irreversible'); ?>
            </p>

            <!-- Statistics -->
            <div
                style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
                <div
                    style="padding: 1rem; background: var(--color-accent-light); border-radius: var(--radius-md); border: 1px solid var(--color-border);">
                    <div style="font-size: 1.5rem; font-weight: 700; color: var(--color-accent);">
                        <?php echo count($shares); ?></div>
                    <div style="font-size: 0.875rem; color: var(--color-accent);"><?php _e('registered_shares'); ?>
                    </div>
                </div>
                <div
                    style="padding: 1rem; background: var(--color-success-bg); border-radius: var(--radius-md); border: 1px solid var(--color-border);">
                    <div style="font-size: 1.5rem; font-weight: 700; color: var(--color-success);">
                        <?php echo count($uploadFolders); ?></div>
                    <div style="font-size: 0.875rem; color: var(--color-success);"><?php _e('upload_folders'); ?></div>
                </div>
                <div
                    style="padding: 1rem; background: var(--color-warning-bg); border-radius: var(--radius-md); border: 1px solid var(--color-border);">
                    <div style="font-size: 1.5rem; font-weight: 700; color: var(--color-warning);">
                        <?php echo count($orphanFolders); ?></div>
                    <div style="font-size: 0.875rem; color: var(--color-warning);"><?php _e('orphan_folders'); ?></div>
                </div>
                <div
                    style="padding: 1rem; background: var(--color-error-bg); border-radius: var(--radius-md); border: 1px solid var(--color-border);">
                    <div style="font-size: 1.5rem; font-weight: 700; color: var(--color-error);">
                        <?php echo count($orphanRecords); ?></div>
                    <div style="font-size: 0.875rem; color: var(--color-error);"><?php _e('orphan_records'); ?></div>
                </div>
            </div>

            <!-- Session Stats -->
            <div class="card mb-4"
                style="background: var(--color-bg-secondary); margin-bottom: 2rem; border-color: var(--color-border);">
                <div class="card-body">
                    <h2
                        style="margin: 0 0 1rem; font-size: 1.1rem; color: var(--color-text-primary); display: flex; align-items: center; gap: 0.5rem;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                            <path d="M7 11V7a5 5 0 0110 0v4" />
                        </svg>
                        <?php _e('session_stats'); ?>
                    </h2>

                    <div class="flex items-center gap-6 mb-4" style="flex-wrap: wrap;">
                        <div>
                            <div class="text-sm font-bold" style="color: var(--color-text-muted);">
                                <?php _e('session_files'); ?></div>
                            <div class="text-xl font-bold"><?php echo $sessionStats['count'] ?? 0; ?></div>
                        </div>
                        <div>
                            <div class="text-sm font-bold" style="color: var(--color-text-muted);">
                                <?php _e('total_size'); ?></div>
                            <div class="text-xl font-bold">
                                <?php echo FileHandler::formatSize($sessionStats['size'] ?? 0); ?></div>
                        </div>
                        <div style="margin-left: auto;">
                            <form method="post"
                                onsubmit="return confirm('<?php echo htmlspecialchars(__('confirm_cleanup_sessions')); ?>');"
                                style="display: flex; align-items: center; gap: 0.75rem;">
                                <input type="hidden" name="action" value="cleanup_sessions">
                                <input type="hidden" name="csrf_token"
                                    value="<?php echo htmlspecialchars($csrfToken); ?>">
                                <button type="submit" class="btn btn-secondary btn-sm" <?php echo ($sessionStats['count'] ?? 0) === 0 ? 'disabled' : ''; ?>>
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2">
                                        <polyline points="3 6 5 6 21 6"></polyline>
                                        <path
                                            d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2">
                                        </path>
                                    </svg>
                                    <?php _e('cleanup_sessions'); ?>
                                </button>
                                <span class="text-xs"
                                    style="color: var(--color-text-muted);"><?php _e('cleanup_sessions_desc'); ?></span>
                            </form>
                        </div>
                    </div>
                    <p class="text-xs" style="color: var(--color-text-muted);">
                        Config: <?php echo htmlspecialchars(session_save_path()); ?> (Lifetime:
                        <?php echo SESSION_LIFETIME / 60; ?> min)
                    </p>
                </div>
            </div>

            <!-- Orphan Folders -->
            <?php if (!empty($orphanFolders)): ?>
                <div class="alert alert-warning mb-4" style="padding: 1.5rem;">
                    <h3 style="margin: 0 0 0.5rem; display: flex; align-items: center; gap: 0.5rem;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                            <line x1="12" y1="9" x2="12" y2="13" />
                            <line x1="12" y1="17" x2="12.01" y2="17" />
                        </svg>
                        <?php _e('orphan_folders_found'); ?>
                    </h3>
                    <p class="text-sm mb-2"><?php _e('orphan_folders_desc'); ?><br><?php _e('orphan_folders_reason'); ?></p>

                    <ul
                        style="max-height: 10rem; overflow-y: auto; background: var(--color-bg-secondary); padding: 0.75rem; border-radius: var(--radius-sm); margin: 1rem 0; font-family: monospace; font-size: 0.75rem;">
                        <?php foreach ($orphanFolders as $folder): ?>
                            <li style="padding: 0.25rem 0; border-bottom: 1px solid var(--color-border);">
                                <?php echo htmlspecialchars($folder); ?></li>
                        <?php endforeach; ?>
                    </ul>

                    <form method="post"
                        onsubmit="return confirm('<?php echo htmlspecialchars(__('confirm_delete_folders')); ?>');">
                        <input type="hidden" name="action" value="cleanup_folders">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
                        <button type="submit" class="btn" style="background: var(--color-warning); color: white;">
                            <?php _e('delete_orphan_folders'); ?>
                        </button>
                    </form>
                </div>
            <?php endif; ?>

            <!-- Orphan Records -->
            <?php if (!empty($orphanRecords)): ?>
                <div class="alert alert-error mb-4" style="padding: 1.5rem;">
                    <h3 style="margin: 0 0 0.5rem; display: flex; align-items: center; gap: 0.5rem;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                            <line x1="12" y1="9" x2="12" y2="13" />
                            <line x1="12" y1="17" x2="12.01" y2="17" />
                        </svg>
                        <?php _e('orphan_records_found'); ?>
                    </h3>
                    <p class="text-sm mb-2"><?php _e('orphan_records_desc'); ?><br><?php _e('orphan_records_reason'); ?></p>

                    <ul
                        style="max-height: 10rem; overflow-y: auto; background: var(--color-bg-secondary); padding: 0.75rem; border-radius: var(--radius-sm); margin: 1rem 0; font-size: 0.875rem;">
                        <?php foreach ($orphanRecords as $record): ?>
                            <li style="padding: 0.25rem 0; border-bottom: 1px solid var(--color-border);">
                                ID: <?php echo $record['id']; ?> -
                                <strong><?php echo htmlspecialchars($record['title']); ?></strong>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                    <form method="post"
                        onsubmit="return confirm('<?php echo htmlspecialchars(__('confirm_delete_records')); ?>');">
                        <input type="hidden" name="action" value="cleanup_records">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
                        <button type="submit" class="btn btn-danger">
                            <?php _e('delete_orphan_records'); ?>
                        </button>
                    </form>
                </div>
            <?php endif; ?>

            <!-- All OK -->
            <?php if (empty($orphanFolders) && empty($orphanRecords)): ?>
                <div class="alert alert-success mb-4" style="padding: 1.5rem;">
                    <h3 style="margin: 0 0 0.5rem; display: flex; align-items: center; gap: 0.5rem;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M22 11.08V12a10 10 0 11-5.93-9.14" />
                            <polyline points="22 4 12 14.01 9 11.01" />
                        </svg>
                        <?php _e('no_issues'); ?>
                    </h3>
                    <p class="text-sm" style="margin: 0;"><?php _e('db_synced'); ?></p>
                </div>
            <?php endif; ?>

            <!-- Database Records -->
            <h2
                style="margin: 2rem 0 1rem; font-size: 1.25rem; color: var(--color-text-primary); display: flex; align-items: center; gap: 0.5rem;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <ellipse cx="12" cy="5" rx="9" ry="3" />
                    <path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3" />
                    <path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5" />
                </svg>
                <?php _e('share_info_db'); ?>
            </h2>

            <?php if (empty($shares)): ?>
                <p style="color: var(--color-text-muted);"><?php _e('no_share_info'); ?></p>
            <?php else: ?>
                <div class="table-wrapper">
                    <table class="table">
                        <thead>
                            <tr>
                                <th class="text-center">ID</th>
                                <th><?php _e('title'); ?></th>
                                <th><?php _e('token'); ?></th>
                                <th class="text-center"><?php _e('files_exist'); ?></th>
                                <th class="text-center"><?php _e('password'); ?></th>
                                <th class="text-center"><?php _e('dl_count'); ?></th>
                                <th><?php _e('created'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($shares as $share):
                                $folderExists = in_array($share['token'], $uploadFolders);
                                ?>
                                <tr>
                                    <td style="font-family: monospace; font-size: 0.75rem; text-align: center;">
                                        <?php echo $share['id']; ?></td>
                                    <td style="max-width: 200px;">
                                        <div class="font-medium"
                                            style="color: var(--color-text-primary); word-break: break-word;"
                                            title="<?php echo htmlspecialchars($share['title']); ?>">
                                            <?php echo htmlspecialchars($share['title']); ?>
                                        </div>
                                    </td>
                                    <td style="font-family: monospace; font-size: 0.75rem; color: var(--color-text-muted);">
                                        <?php echo htmlspecialchars(substr($share['token'], 0, 16)); ?>...
                                    </td>
                                    <td class="text-center">
                                        <?php if ($folderExists): ?>
                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2" style="color: var(--color-success);">
                                                <polyline points="20 6 9 17 4 12" />
                                            </svg>
                                        <?php else: ?>
                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2" style="color: var(--color-error);">
                                                <line x1="18" y1="6" x2="6" y2="18" />
                                                <line x1="6" y1="6" x2="18" y2="18" />
                                            </svg>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($share['password_hash']): ?>
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2" style="color: var(--color-success);">
                                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                                                <path d="M7 11V7a5 5 0 0110 0v4" />
                                            </svg>
                                        <?php else: ?>
                                            <span style="color: var(--color-text-muted);">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center font-bold"><?php echo $share['download_count']; ?></td>
                                    <td style="white-space: nowrap; font-size: 0.75rem;">
                                        <?php echo date('Y/m/d H:i', strtotime($share['created_at'])); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

            <!-- Upload Folder Path -->
            <div
                style="margin-top: 2rem; padding: 1rem; background: var(--color-bg-tertiary); border-radius: var(--radius-md);">
                <h3
                    style="margin: 0 0 0.5rem; font-size: 0.875rem; font-weight: 600; color: var(--color-text-secondary);">
                    <?php _e('upload_folder_path'); ?>
                </h3>
                <code
                    style="font-size: 0.75rem; background: var(--color-bg-secondary); padding: 0.5rem; border-radius: var(--radius-sm); display: block; word-break: break-all;">
                        <?php echo htmlspecialchars(UPLOADS_PATH); ?>
                    </code>
            </div>
        </div>
    </div>
</main>

<?php
// Include simple footer (no modals needed)
include __DIR__ . '/partials/footer-simple.php';
?>
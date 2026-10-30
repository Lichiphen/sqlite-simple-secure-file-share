<?php
/**
 * Share Page Template (Frontend)
 */

// Ensure direct access is prevented
if (!defined('BASE_PATH')) exit('No direct access.');

// 署名付きダウンロードURLを生成
$shareDownloadUrl = function (string $target) use ($share, $downloadSignature): string {
    return BASE_URL . '/s/' . $share['token'] . '?' . http_build_query(['download' => $target, 'dl' => $downloadSignature]);
};
?>
<!DOCTYPE html>
<html lang="<?php echo I18n::getCurrentLang(); ?>" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title><?php echo htmlspecialchars($share['title'], ENT_QUOTES, 'UTF-8'); ?> - <?php _e('file_sharing'); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/style.css">
</head>
<body class="flex items-center justify-center min-h-screen" style="padding: 1rem;">
    
    <div class="card" style="width: 100%; max-width: 42rem;">
        <!-- Header -->
        <div class="share-header">
            <div class="flex justify-between items-center">
                <div>
                    <p class="share-header-label"><?php _e('file_sharing'); ?></p>
                    <h1 class="share-header-title"><?php echo htmlspecialchars($share['title'], ENT_QUOTES, 'UTF-8'); ?></h1>
                </div>
                <div class="share-header-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M4 14.899A7 7 0 1 1 15.71 8h1.79a4.5 4.5 0 0 1 2.5 8.242"/>
                        <path d="M12 12v9"/>
                        <path d="m16 16-4-4-4 4"/>
                    </svg>
                </div>
            </div>
            <div class="share-header-meta">
                <span><strong><?php echo count($files); ?></strong> <?php _e('files'); ?></span>
                <span><?php _e('total'); ?> <strong><?php echo FileHandler::formatSize($totalSize); ?></strong></span>
            </div>
        </div>
        
        <?php 
        // Show top download button if 7+ files
        if (count($files) >= 7): 
            $downloadUrl = count($files) > 1 
                ? $shareDownloadUrl('zip')
                : $shareDownloadUrl($files[0]['system_name']);
        ?>
        <div style="padding: 1rem; background: var(--color-bg-tertiary); border-bottom: 1px solid var(--color-border); text-align: right;">
            <a href="<?php echo htmlspecialchars($downloadUrl, ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-dark">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/>
                    <polyline points="7 10 12 15 17 10"/>
                    <line x1="12" y1="15" x2="12" y2="3"/>
                </svg>
                <?php echo count($files) > 1 ? __('download_as_zip') : __('download_file'); ?>
            </a>
        </div>
        <?php endif; ?>
        
        <!-- File List -->
        <div style="padding: 1.5rem; background: var(--color-bg-tertiary); border-bottom: 1px solid var(--color-border);">
            <ul class="file-list">
                <?php foreach ($files as $file): 
                    $singleUrl = $shareDownloadUrl($file['system_name']);
                    $ext = strtolower(pathinfo($file['original_name'], PATHINFO_EXTENSION));
                    
                    // Icon colors by extension
                    $iconColor = 'var(--color-text-muted)';
                    if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'])) $iconColor = '#a855f7';
                    elseif ($ext === 'pdf') $iconColor = '#ef4444';
                    elseif (in_array($ext, ['zip', 'rar', '7z', 'tar', 'gz'])) $iconColor = '#eab308';
                    elseif (in_array($ext, ['doc', 'docx'])) $iconColor = '#3b82f6';
                    elseif (in_array($ext, ['xls', 'xlsx'])) $iconColor = '#22c55e';
                    elseif (in_array($ext, ['ppt', 'pptx'])) $iconColor = '#f97316';
                ?>
                <li class="file-item" style="padding: 0.75rem 1rem;">
                    <div class="file-info">
                        <svg class="file-icon" style="color: <?php echo $iconColor; ?>;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                            <polyline points="14 2 14 8 20 8"/>
                            <line x1="9" y1="15" x2="15" y2="15"/>
                        </svg>
                        <div style="min-width: 0;">
                            <div class="file-name truncate" title="<?php echo htmlspecialchars($file['original_name'], ENT_QUOTES, 'UTF-8'); ?>">
                                <?php echo htmlspecialchars($file['original_name'], ENT_QUOTES, 'UTF-8'); ?>
                            </div>
                            <div class="file-size"><?php echo FileHandler::formatSize((int) $file['size']); ?></div>
                        </div>
                    </div>
                    <a href="<?php echo htmlspecialchars($singleUrl, ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-sm" style="background: var(--color-accent-light); color: var(--color-accent);">
                        DL
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
        
        <!-- Footer -->
        <div style="padding: 1.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <p class="text-xs" style="color: var(--color-text-muted);">
                <?php _e('questions_contact'); ?>
            </p>
            
            <?php 
            $downloadUrl = count($files) > 1 
                ? $shareDownloadUrl('zip')
                : $shareDownloadUrl($files[0]['system_name']);
            ?>
            <a href="<?php echo htmlspecialchars($downloadUrl, ENT_QUOTES, 'UTF-8'); ?>" class="btn <?php echo count($files) > 1 ? 'btn-dark' : 'btn-primary'; ?>">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/>
                    <polyline points="7 10 12 15 17 10"/>
                    <line x1="12" y1="15" x2="12" y2="3"/>
                </svg>
                <?php echo count($files) > 1 ? __('download_as_zip') : __('download_file'); ?>
            </a>
        </div>
    </div>
    
    <script src="<?php echo BASE_URL; ?>/assets/app.js"></script>
</body>
</html>

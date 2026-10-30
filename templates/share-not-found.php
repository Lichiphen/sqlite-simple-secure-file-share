<?php
/**
 * Share Not Found Template
 * Cute error page with bouncing cat mascot
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
    <title><?php _e('share_not_found'); ?> - <?php _e('app_name'); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/style.css?v=<?php echo APP_VERSION; ?>">
    <script <?php echo Security::nonceAttr(); ?>>
        (function() {
            const saved = localStorage.getItem('sfs_theme');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            const theme = saved === 'dark' || saved === 'light' ? saved : (saved === 'system' || !saved ? (prefersDark ? 'dark' : 'light') : 'light');
            document.documentElement.dataset.theme = theme;
        })();
    </script>
    <style>
        .error-page {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            text-align: center;
            background: var(--color-bg-primary);
        }
        
        .cat-mascot {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            object-position: center 20%;
            border: 4px solid #f59e0b;
            box-shadow: 0 8px 32px rgba(245, 158, 11, 0.3);
            animation: bounce 2s ease-in-out infinite;
        }
        
        @keyframes bounce {
            0%, 100% {
                transform: translateY(0) scale(1);
            }
            25% {
                transform: translateY(-20px) scale(1.05);
            }
            50% {
                transform: translateY(0) scale(0.95);
            }
            75% {
                transform: translateY(-10px) scale(1.02);
            }
        }
        
        .error-icon {
            font-size: 4rem;
            margin: 1.5rem 0 0;
            animation: shake 0.5s ease-in-out infinite;
        }
        
        @keyframes shake {
            0%, 100% {
                transform: rotate(0deg);
            }
            25% {
                transform: rotate(-10deg);
            }
            75% {
                transform: rotate(10deg);
            }
        }
        
        .error-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--color-text-primary);
            margin: 1rem 0 0.5rem;
        }
        
        .error-message {
            color: var(--color-text-secondary);
            margin: 0.5rem 0 2rem;
            max-width: 400px;
            line-height: 1.6;
        }
        
        .floating-hearts {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            overflow: hidden;
            z-index: -1;
        }
        
        .heart {
            position: absolute;
            font-size: 1.5rem;
            opacity: 0.2;
            animation: floatHeart 8s ease-in-out infinite;
        }
        
        .heart:nth-child(1) { left: 5%; animation-delay: 0s; }
        .heart:nth-child(2) { left: 15%; animation-delay: 2s; }
        .heart:nth-child(3) { left: 25%; animation-delay: 4s; }
        .heart:nth-child(4) { left: 45%; animation-delay: 1s; }
        .heart:nth-child(5) { left: 65%; animation-delay: 3s; }
        .heart:nth-child(6) { left: 75%; animation-delay: 5s; }
        .heart:nth-child(7) { left: 85%; animation-delay: 2.5s; }
        .heart:nth-child(8) { left: 95%; animation-delay: 0.5s; }
        
        @keyframes floatHeart {
            0% {
                bottom: -50px;
                opacity: 0;
                transform: translateX(0) rotate(0deg);
            }
            10% {
                opacity: 0.3;
            }
            90% {
                opacity: 0.3;
            }
            100% {
                bottom: 100%;
                opacity: 0;
                transform: translateX(50px) rotate(360deg);
            }
        }
        
        .sad-cat-text {
            display: flex;
            gap: 0.5rem;
            margin-top: 1rem;
            font-size: 1.5rem;
            opacity: 0.6;
        }
        
        .tear {
            animation: tearDrop 2s ease-in-out infinite;
        }
        
        @keyframes tearDrop {
            0%, 100% {
                transform: translateY(0);
                opacity: 0.6;
            }
            50% {
                transform: translateY(5px);
                opacity: 1;
            }
        }
    </style>
</head>
<body>
    <!-- Floating hearts -->
    <div class="floating-hearts">
        <div class="heart">💔</div>
        <div class="heart">💔</div>
        <div class="heart">💔</div>
        <div class="heart">💔</div>
        <div class="heart">💔</div>
        <div class="heart">💔</div>
        <div class="heart">💔</div>
        <div class="heart">💔</div>
    </div>
    
    <div class="error-page">
        <!-- Bouncing Cat Mascot -->
        <img src="<?php echo BASE_URL; ?>/assets/cat-mascot.png" alt="Cat Mascot" class="cat-mascot">
        
        <!-- Sad Icon -->
        <div class="error-icon">😿</div>
        
        <!-- Error Title -->
        <h1 class="error-title"><?php _e('share_not_found'); ?></h1>
        
        <!-- Error Message -->
        <p class="error-message"><?php _e('share_not_found_message'); ?></p>
        
        <!-- Sad cat decoration -->
        <div class="sad-cat-text">
            <span class="tear">💧</span>
            <span>🐱</span>
            <span class="tear">💧</span>
        </div>
    </div>
    
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

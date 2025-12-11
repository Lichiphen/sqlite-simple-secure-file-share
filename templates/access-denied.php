<?php
/**
 * Access Denied Template
 * Cute but stern error page for unauthorized access attempts
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
    <title><?php _e('access_denied'); ?> - <?php _e('app_name'); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/style.css?v=<?php echo APP_VERSION; ?>">
    <script>
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
        
        .cat-mascot-container {
            position: relative;
            display: inline-block;
        }
        
        .cat-mascot {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            object-position: center 20%;
            border: 4px solid #ef4444;
            box-shadow: 0 8px 32px rgba(239, 68, 68, 0.3);
            animation: angryShake 0.5s ease-in-out infinite;
        }
        
        .angry-eyebrows {
            position: absolute;
            top: -30px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 2.5rem;
            animation: pumpun 0.3s ease-in-out infinite;
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2));
        }
        
        @keyframes pumpun {
            0%, 100% {
                transform: translateX(-50%) rotate(-10deg) translateY(0);
            }
            50% {
                transform: translateX(-50%) rotate(10deg) translateY(-5px);
            }
        }
        
        @keyframes angryShake {
            0%, 100% {
                transform: translateX(0) rotate(0deg);
            }
            25% {
                transform: translateX(-3px) rotate(-2deg);
            }
            75% {
                transform: translateX(3px) rotate(2deg);
            }
        }
        
        @keyframes eyebrowBounce {
            0%, 100% {
                transform: translateX(-50%) translateY(0);
            }
            50% {
                transform: translateX(-50%) translateY(-5px);
            }
        }
        
        .warning-icon {
            font-size: 4rem;
            margin: 1.5rem 0 0;
            animation: pulse 1s ease-in-out infinite;
        }
        
        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
                opacity: 1;
            }
            50% {
                transform: scale(1.1);
                opacity: 0.8;
            }
        }
        
        .error-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #ef4444;
            margin: 1rem 0 0.5rem;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        
        .error-message {
            color: var(--color-text-secondary);
            margin: 0.5rem 0 2rem;
            max-width: 400px;
            line-height: 1.6;
        }
        
        .barrier-tape {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            overflow: hidden;
            z-index: -1;
        }
        
        .tape-line {
            position: absolute;
            width: 200%;
            height: 30px;
            background: repeating-linear-gradient(
                45deg,
                #fbbf24,
                #fbbf24 20px,
                #000 20px,
                #000 40px
            );
            opacity: 0.1;
            animation: tapeMove 10s linear infinite;
        }
        
        .tape-line:nth-child(1) { top: 5%; left: -50%; animation-delay: 0s; }
        .tape-line:nth-child(2) { top: 95%; left: -50%; animation-delay: 5s; transform: rotate(-5deg); }
        
        @keyframes tapeMove {
            0% {
                transform: translateX(0);
            }
            100% {
                transform: translateX(-50%);
            }
        }
        
        .cat-says {
            background: var(--color-bg-secondary);
            border: 2px solid var(--color-border);
            border-radius: 16px;
            padding: 1rem 1.5rem;
            margin-top: 1.5rem;
            position: relative;
            max-width: 300px;
        }
        
        .cat-says::before {
            content: '';
            position: absolute;
            top: -10px;
            left: 50%;
            transform: translateX(-50%);
            border-left: 10px solid transparent;
            border-right: 10px solid transparent;
            border-bottom: 10px solid var(--color-border);
        }
        
        .cat-says::after {
            content: '';
            position: absolute;
            top: -7px;
            left: 50%;
            transform: translateX(-50%);
            border-left: 8px solid transparent;
            border-right: 8px solid transparent;
            border-bottom: 8px solid var(--color-bg-secondary);
        }
        
        .cat-says-text {
            font-size: 0.9rem;
            color: var(--color-text-secondary);
            margin: 0;
        }
        
        .nope-animation {
            display: inline-block;
            animation: nope 2s ease-in-out infinite;
        }
        
        @keyframes nope {
            0%, 100% {
                transform: rotate(0deg);
            }
            10% {
                transform: rotate(-15deg);
            }
            20% {
                transform: rotate(15deg);
            }
            30% {
                transform: rotate(-10deg);
            }
            40% {
                transform: rotate(10deg);
            }
            50%, 100% {
                transform: rotate(0deg);
            }
        }
    </style>
</head>
<body>
    <!-- Barrier tape decoration -->
    <div class="barrier-tape">
        <div class="tape-line"></div>
        <div class="tape-line"></div>
    </div>
    
    <div class="error-page">
        <!-- Angry Cat Mascot -->
        <div class="cat-mascot-container">
            <img src="<?php echo BASE_URL; ?>/assets/cat-mascot.png" alt="Cat Mascot" class="cat-mascot">
            <div class="angry-eyebrows">😾</div>
        </div>
        
        <!-- Warning Icon -->
        <div class="warning-icon">🚫</div>
        
        <!-- Error Title -->
        <h1 class="error-title"><?php _e('access_denied'); ?></h1>
        
        <!-- Error Message -->
        <p class="error-message"><?php _e('access_denied_message'); ?></p>
        
        <!-- Cat Speech Bubble -->
        <div class="cat-says">
            <p class="cat-says-text">
                <span class="nope-animation">🙅</span>
                <?php _e('cat_says_nope'); ?>
            </p>
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

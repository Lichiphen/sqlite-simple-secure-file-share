<?php
/**
 * 404 Not Found Template
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
    <title>404 - <?php _e('app_name'); ?></title>
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
        
        .cat-mascot {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            object-position: center 20%;
            border: 4px solid var(--color-accent);
            box-shadow: 0 8px 32px rgba(99, 102, 241, 0.3);
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
        
        .error-code {
            font-size: 6rem;
            font-weight: 800;
            margin: 1.5rem 0 0;
            background: linear-gradient(135deg, var(--color-accent) 0%, #a855f7 50%, #ec4899 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: shimmer 3s ease-in-out infinite;
        }
        
        @keyframes shimmer {
            0%, 100% {
                filter: brightness(1);
            }
            50% {
                filter: brightness(1.2);
            }
        }
        
        .error-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--color-text-primary);
            margin: 0.5rem 0;
        }
        
        .error-message {
            color: var(--color-text-secondary);
            margin: 0.5rem 0 2rem;
            max-width: 400px;
            line-height: 1.6;
        }
        
        .floating-stars {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            overflow: hidden;
            z-index: -1;
        }
        
        .star {
            position: absolute;
            width: 6px;
            height: 6px;
            background: var(--color-accent);
            border-radius: 50%;
            opacity: 0.3;
            animation: float 6s ease-in-out infinite;
        }
        
        .star:nth-child(1) { left: 10%; top: 20%; animation-delay: 0s; }
        .star:nth-child(2) { left: 20%; top: 80%; animation-delay: 1s; }
        .star:nth-child(3) { left: 30%; top: 40%; animation-delay: 2s; }
        .star:nth-child(4) { left: 50%; top: 10%; animation-delay: 3s; }
        .star:nth-child(5) { left: 70%; top: 60%; animation-delay: 4s; }
        .star:nth-child(6) { left: 80%; top: 30%; animation-delay: 5s; }
        .star:nth-child(7) { left: 90%; top: 70%; animation-delay: 2.5s; }
        .star:nth-child(8) { left: 15%; top: 50%; animation-delay: 1.5s; }
        
        @keyframes float {
            0%, 100% {
                transform: translateY(0) scale(1);
                opacity: 0.3;
            }
            50% {
                transform: translateY(-20px) scale(1.5);
                opacity: 0.6;
            }
        }
        
        .paw-prints {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
            opacity: 0.5;
        }
        
        .paw {
            font-size: 1.5rem;
            animation: pawStep 1.5s ease-in-out infinite;
        }
        
        .paw:nth-child(1) { animation-delay: 0s; }
        .paw:nth-child(2) { animation-delay: 0.3s; }
        .paw:nth-child(3) { animation-delay: 0.6s; }
        
        @keyframes pawStep {
            0%, 100% {
                transform: scale(1);
                opacity: 0.3;
            }
            50% {
                transform: scale(1.2);
                opacity: 0.8;
            }
        }
    </style>
</head>
<body>
    <!-- Floating decorations -->
    <div class="floating-stars">
        <div class="star"></div>
        <div class="star"></div>
        <div class="star"></div>
        <div class="star"></div>
        <div class="star"></div>
        <div class="star"></div>
        <div class="star"></div>
        <div class="star"></div>
    </div>
    
    <div class="error-page">
        <!-- Bouncing Cat Mascot -->
        <img src="<?php echo BASE_URL; ?>/assets/cat-mascot.png" alt="Cat Mascot" class="cat-mascot">
        
        <!-- Error Code -->
        <div class="error-code">404</div>
        
        <!-- Error Title -->
        <h1 class="error-title"><?php _e('page_not_found'); ?></h1>
        
        <!-- Error Message -->
        <p class="error-message"><?php _e('page_not_found_message'); ?></p>
        
        <!-- Paw Prints -->
        <div class="paw-prints">
            <span class="paw">🐾</span>
            <span class="paw">🐾</span>
            <span class="paw">🐾</span>
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

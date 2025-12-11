<?php
/**
 * Show cute "No direct access" error page
 * This function outputs a complete HTML page and exits
 */
function showNoDirectAccessError() {
    http_response_code(403);
    // Try to get cat image path
    $scriptDir = dirname($_SERVER['SCRIPT_NAME']);
    // Go up directories to find assets folder
    $catImage = '';
    if (strpos($scriptDir, '/templates') !== false) {
        $catImage = str_replace('/templates/partials', '', str_replace('/templates', '', $scriptDir)) . '/assets/cat-mascot.png';
    } elseif (strpos($scriptDir, '/src') !== false) {
        $catImage = str_replace('/src', '', $scriptDir) . '/assets/cat-mascot.png';
    } else {
        $catImage = $scriptDir . '/assets/cat-mascot.png';
    }
    
    echo <<<HTML
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>🙀 Access Denied</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #1e1b4b 0%, #312e81 50%, #1e1b4b 100%);
            color: white;
            padding: 2rem;
        }
        .container { text-align: center; max-width: 400px; }
        .cat-container { position: relative; display: inline-block; margin-bottom: 1.5rem; }
        .cat-img {
            width: 120px; height: 120px;
            border-radius: 50%;
            object-fit: cover;
            object-position: center 20%;
            border: 4px solid #ef4444;
            box-shadow: 0 0 30px rgba(239, 68, 68, 0.5);
            animation: shake 0.5s ease-in-out infinite;
        }
        .angry-face {
            position: absolute; top: -25px; left: 50%;
            transform: translateX(-50%);
            font-size: 2rem;
            animation: pumpun 0.3s ease-in-out infinite;
        }
        @keyframes shake {
            0%, 100% { transform: rotate(0deg); }
            25% { transform: rotate(-3deg); }
            75% { transform: rotate(3deg); }
        }
        @keyframes pumpun {
            0%, 100% { transform: translateX(-50%) rotate(-15deg); }
            50% { transform: translateX(-50%) rotate(15deg) translateY(-3px); }
        }
        .warning { font-size: 3rem; margin: 1rem 0; animation: pulse 1s infinite; }
        @keyframes pulse { 0%, 100% { transform: scale(1); } 50% { transform: scale(1.1); } }
        h1 { font-size: 1.5rem; margin-bottom: 0.5rem; color: #f87171; }
        p { color: #cbd5e1; line-height: 1.6; margin-bottom: 1.5rem; }
        .bubble {
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            padding: 1rem 1.5rem;
            border: 1px solid rgba(255,255,255,0.2);
        }
        .cat-says { font-size: 0.9rem; color: #fbbf24; }
    </style>
</head>
<body>
    <div class="container">
        <div class="cat-container">
            <img src="{$catImage}" alt="Cat" class="cat-img" onerror="this.style.display='none'">
            <div class="angry-face">😾</div>
        </div>
        <div class="warning">🚫</div>
        <h1>Direct Access Not Allowed</h1>
        <p>このファイルには直接アクセスできないにゃ！<br>This file cannot be accessed directly!</p>
        <div class="bubble">
            <span class="cat-says">🙅 ダメにゃ！ソースコードを覗こうとしてもダメだにゃん！</span>
        </div>
    </div>
</body>
</html>
HTML;
    exit;
}

// Auto-execute when this file is accessed directly
if (!defined('BASE_PATH') && basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
    showNoDirectAccessError();
}

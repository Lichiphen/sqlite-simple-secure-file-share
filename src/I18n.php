<?php
/**
 * Simple Secure File Share - Internationalization Handler
 * 
 * @package SimpleSecureFileShare
 * @author  AI Generator, Direction: Lichiphen
 * @license Lichiphen Proprietary License v1.0
 * @link    https://gitlab.com/lichiphen/sqlite-simple-secure-file-share
 */

// Prevent direct access with cute error page
if (!defined('BASE_PATH')) {
    require_once __DIR__ . '/no-direct-access.php';
    showNoDirectAccessError();
}

class I18n {
    private static ?string $currentLang = null;
    private static array $translations = [];
    
    /**
     * Initialize language based on browser/cookie/parameter
     */
    public static function init(): void {
        self::$currentLang = self::detectLanguage();
        self::loadTranslations();
    }
    
    /**
     * Detect language from various sources
     */
    private static function detectLanguage(): string {
        // 1. URL parameter (highest priority)
        if (isset($_GET['lang']) && in_array($_GET['lang'], SUPPORTED_LANGUAGES)) {
            $lang = $_GET['lang'];
            // Save to cookie for persistence
            setcookie('sfs_lang', $lang, time() + (86400 * 30), '/');
            return $lang;
        }
        
        // 2. Cookie
        if (isset($_COOKIE['sfs_lang']) && in_array($_COOKIE['sfs_lang'], SUPPORTED_LANGUAGES)) {
            return $_COOKIE['sfs_lang'];
        }
        
        // 3. Browser Accept-Language header
        $acceptLang = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '';
        
        // Parse Accept-Language header
        if (preg_match('/^([a-z]{2})/i', $acceptLang, $matches)) {
            $browserLang = strtolower($matches[1]);
            if (in_array($browserLang, SUPPORTED_LANGUAGES)) {
                return $browserLang;
            }
        }
        
        // 4. Default language
        return DEFAULT_LANGUAGE;
    }
    
    /**
     * Load translation file
     */
    private static function loadTranslations(): void {
        $langFile = BASE_PATH . '/lang/' . self::$currentLang . '.php';
        
        if (file_exists($langFile)) {
            self::$translations = require $langFile;
        } else {
            // Fallback to English
            $fallbackFile = BASE_PATH . '/lang/en.php';
            if (file_exists($fallbackFile)) {
                self::$translations = require $fallbackFile;
            }
        }
    }
    
    /**
     * Get translation
     */
    public static function translate(string $key): string {
        if (self::$currentLang === null) {
            self::init();
        }
        
        return self::$translations[$key] ?? $key;
    }
    
    /**
     * Get current language
     */
    public static function getCurrentLang(): string {
        if (self::$currentLang === null) {
            self::init();
        }
        
        return self::$currentLang;
    }
    
    /**
     * Get all supported languages
     */
    public static function getSupportedLanguages(): array {
        return SUPPORTED_LANGUAGES;
    }
    
    /**
     * Get language display name
     */
    public static function getLanguageName(string $code): string {
        $names = [
            'en' => 'English',
            'ja' => '日本語'
        ];
        return $names[$code] ?? $code;
    }
}

/**
 * Translation helper function
 */
function __(string $key): string {
    return I18n::translate($key);
}

/**
 * Echo translation helper
 */
function _e(string $key): void {
    echo I18n::translate($key);
}

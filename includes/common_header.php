<?php
// Common header functionality
require_once dirname(__DIR__) . '/includes/language_handler.php';

// Get user info if logged in
$isLoggedIn = isset($_SESSION['user_id']);
$username = $isLoggedIn ? $_SESSION['username'] : '';
$userRole = $isLoggedIn ? $_SESSION['role'] : '';

// Function to get user profile picture
function getUserProfilePicture($pdo, $userId) {
    try {
        $stmt = $pdo->prepare("SELECT profile_picture FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && !empty($user['profile_picture']) && file_exists($user['profile_picture'])) {
            return $user['profile_picture'];
        }
    } catch (PDOException $e) {
        // Log error or handle as needed
        error_log("Error fetching profile picture: " . $e->getMessage());
    }
    
    return null; // Return null if no profile picture or error
}

// Function to get appropriate dashboard link based on user role
function getUserDashboardLink($role) {
    switch ($role) {
        case 'buyer':
            return '../dashboards/buyer_dashboard.php';
        case 'seller':
            return '../dashboards/seller_dashboard.php';
        case 'agent':
            return '../dashboards/agent_dashboard.php';
        case 'admin':
            return '../dashboards/admin_dashboard.php';
        default:
            return '../index.php';
    }
}

// Function to highlight active navigation item
function isActivePage($page) {
    $currentFile = basename($_SERVER['PHP_SELF']);
    return $currentFile === $page;
}

// Get current language and theme for use in navigation
$currentLangNav = getCurrentLanguage();
$currentThemeNav = getCurrentTheme();

// Function to get language switcher URL
function getLanguageSwitcherUrlNav($lang) {
    $currentUrl = $_SERVER['REQUEST_URI'];
    $urlParts = parse_url($currentUrl);
    $queryParams = [];
    
    if (isset($urlParts['query'])) {
        parse_str($urlParts['query'], $queryParams);
    }
    
    $queryParams['lang'] = $lang;
    
    $newQuery = http_build_query($queryParams);
    $baseUrl = $urlParts['path'];
    
    return $baseUrl . '?' . $newQuery;
}

// Function to get theme switcher URL
function getThemeSwitcherUrlNav($theme) {
    $currentUrl = $_SERVER['REQUEST_URI'];
    $urlParts = parse_url($currentUrl);
    $queryParams = [];
    
    if (isset($urlParts['query'])) {
        parse_str($urlParts['query'], $queryParams);
    }
    
    // Preserve language parameter if it exists, otherwise use current language
    if (!isset($queryParams['lang'])) {
        $queryParams['lang'] = isset($GLOBALS['currentLang']) ? $GLOBALS['currentLang'] : 'fr';
    }
    
    $queryParams['theme'] = $theme;
    
    $newQuery = http_build_query($queryParams);
    $baseUrl = $urlParts['path'];
    
    return $baseUrl . '?' . $newQuery;
}
?>
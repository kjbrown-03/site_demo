<?php
session_start();

// Manually set session values for testing
if (isset($_GET['set_lang'])) {
    $_SESSION['language'] = $_GET['set_lang'];
    echo "<p>Set language to: " . $_GET['set_lang'] . "</p>";
}

if (isset($_GET['set_theme'])) {
    $_SESSION['theme'] = $_GET['set_theme'];
    echo "<p>Set theme to: " . $_GET['set_theme'] . "</p>";
}

// Include language handler after setting manual values
require_once 'includes/language_handler.php';

echo "<h1>Manual Test</h1>";
echo "<p>Current Language: " . $currentLang . "</p>";
echo "<p>Current Theme: " . $currentTheme . "</p>";
echo "<p>Welcome translation: " . t('welcome') . "</p>";

echo "<h2>Session Data:</h2>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

echo "<h2>Test Links:</h2>";
echo "<p><a href='?set_lang=fr'>Manually Set Language to French</a></p>";
echo "<p><a href='?set_lang=en'>Manually Set Language to English</a></p>";
echo "<p><a href='?set_theme=light'>Manually Set Theme to Light</a></p>";
echo "<p><a href='?set_theme=dark'>Manually Set Theme to Dark</a></p>";
echo "<p><a href='?set_lang=fr&set_theme=light'>Manually Set Language to French and Theme to Light</a></p>";
echo "<p><a href='?set_lang=en&set_theme=dark'>Manually Set Language to English and Theme to Dark</a></p>";
echo "<p><a href='?'>Clear Manual Parameters</a></p>";

echo "<h2>URL Parameter Test:</h2>";
echo "<p><a href='?lang=fr'>URL Parameter - Set Language to French</a></p>";
echo "<p><a href='?lang=en'>URL Parameter - Set Language to English</a></p>";
echo "<p><a href='?theme=light'>URL Parameter - Set Theme to Light</a></p>";
echo "<p><a href='?theme=dark'>URL Parameter - Set Theme to Dark</a></p>";
?>
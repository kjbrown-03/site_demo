<?php
session_start();
require_once 'includes/language_handler.php';

echo "<h1>Final Test - Language and Theme Switching</h1>";

echo "<p>Current Language: " . $currentLang . "</p>";
echo "<p>Current Theme: " . $currentTheme . "</p>";
echo "<p>Welcome translation: " . t('welcome') . "</p>";
echo "<p>Theme translation: " . t('theme') . "</p>";

echo "<h2>Session Data:</h2>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

echo "<h2>Test Links:</h2>";
echo "<p><a href='?lang=fr'>Set Language to French</a></p>";
echo "<p><a href='?lang=en'>Set Language to English</a></p>";
echo "<p><a href='?theme=light'>Set Theme to Light</a></p>";
echo "<p><a href='?theme=dark'>Set Theme to Dark</a></p>";
echo "<p><a href='?lang=fr&theme=light'>Set Language to French and Theme to Light</a></p>";
echo "<p><a href='?lang=en&theme=dark'>Set Language to English and Theme to Dark</a></p>";
?>
<?php
session_start();
require_once 'includes/language_handler.php';

// Test the language and theme functions
echo "<h1>Language and Theme Test</h1>";

echo "<p>Current Language: " . $currentLang . "</p>";
echo "<p>Current Theme: " . $currentTheme . "</p>";
echo "<p>HTML Lang Attribute: " . $htmlLang . "</p>";

echo "<h2>Test Translation</h2>";
echo "<p>Welcome in current language: " . t('welcome') . "</p>";
echo "<p>Theme in current language: " . t('theme') . "</p>";

echo "<h2>Test Switcher URLs</h2>";
echo "<p>Switch to French: <a href='" . getLanguageSwitcherUrl('fr') . "'>French</a></p>";
echo "<p>Switch to English: <a href='" . getLanguageSwitcherUrl('en') . "'>English</a></p>";
echo "<p>Switch to Light Theme: <a href='" . getThemeSwitcherUrl('light') . "'>Light</a></p>";
echo "<p>Switch to Dark Theme: <a href='" . getThemeSwitcherUrl('dark') . "'>Dark</a></p>";

echo "<h2>Session Data</h2>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";
?>
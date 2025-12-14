<?php
session_start();
require_once 'includes/language_handler.php';

echo "<h1>Comprehensive Language and Theme Test</h1>";

echo "<h2>Current State:</h2>";
echo "<p>Current Language: " . $currentLang . "</p>";
echo "<p>Current Theme: " . $currentTheme . "</p>";
echo "<p>HTML Lang Attribute: " . $htmlLang . "</p>";

echo "<h2>Translation Test:</h2>";
echo "<p>Welcome: " . t('welcome') . "</p>";
echo "<p>Theme: " . t('theme') . "</p>";
echo "<p>Language: " . t('language') . "</p>";

echo "<h2>Switcher URLs:</h2>";
echo "<p>French URL: <a href='" . getLanguageSwitcherUrl('fr') . "'>" . getLanguageSwitcherUrl('fr') . "</a></p>";
echo "<p>English URL: <a href='" . getLanguageSwitcherUrl('en') . "'>" . getLanguageSwitcherUrl('en') . "</a></p>";
echo "<p>Light Theme URL: <a href='" . getThemeSwitcherUrl('light') . "'>" . getThemeSwitcherUrl('light') . "</a></p>";
echo "<p>Dark Theme URL: <a href='" . getThemeSwitcherUrl('dark') . "'>" . getThemeSwitcherUrl('dark') . "</a></p>";

echo "<h2>Session Data:</h2>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

echo "<h2>GET Parameters:</h2>";
echo "<pre>";
print_r($_GET);
echo "</pre>";

echo "<h2>Direct Test Links:</h2>";
echo "<p><a href='?lang=fr'>Set Language to French</a></p>";
echo "<p><a href='?lang=en'>Set Language to English</a></p>";
echo "<p><a href='?theme=light'>Set Theme to Light</a></p>";
echo "<p><a href='?theme=dark'>Set Theme to Dark</a></p>";
echo "<p><a href='?lang=fr&theme=light'>Set Language to French and Theme to Light</a></p>";
echo "<p><a href='?lang=en&theme=dark'>Set Language to English and Theme to Dark</a></p>";
echo "<p><a href='?'>Clear Parameters</a></p>";
?>
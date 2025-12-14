<?php
session_start();
require_once 'includes/language_handler.php';

echo "<h2>Debug Session Information</h2>";
echo "<p>Current Language: " . getCurrentLanguage() . "</p>";
echo "<p>Current Theme: " . getCurrentTheme() . "</p>";
echo "<p>Session Language: " . (isset($_SESSION['language']) ? $_SESSION['language'] : 'Not set') . "</p>";
echo "<p>Session Theme: " . (isset($_SESSION['theme']) ? $_SESSION['theme'] : 'Not set') . "</p>";
echo "<p>User ID: " . (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'Not set') . "</p>";

if (isset($_GET['lang'])) {
    echo "<p>Language parameter: " . $_GET['lang'] . "</p>";
}

if (isset($_GET['theme'])) {
    echo "<p>Theme parameter: " . $_GET['theme'] . "</p>";
}

echo "<h3>All Session Data:</h3>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

echo "<h3>Test Links:</h3>";
echo "<p><a href='?lang=fr'>Set Language to French</a></p>";
echo "<p><a href='?lang=en'>Set Language to English</a></p>";
echo "<p><a href='?theme=light'>Set Theme to Light</a></p>";
echo "<p><a href='?theme=dark'>Set Theme to Dark</a></p>";
echo "<p><a href='?lang=fr&theme=dark'>Set Language to French and Theme to Dark</a></p>";
?>
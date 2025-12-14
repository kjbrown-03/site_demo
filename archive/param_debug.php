<?php
session_start();
echo "<h1>Parameter Debug</h1>";

echo "<h2>GET Parameters:</h2>";
echo "<pre>";
print_r($_GET);
echo "</pre>";

echo "<h2>Session Data:</h2>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

echo "<h2>Setting Parameters:</h2>";
if (isset($_GET['lang'])) {
    $_SESSION['language'] = $_GET['lang'];
    echo "Set language to: " . $_GET['lang'] . "<br>";
}

if (isset($_GET['theme'])) {
    $_SESSION['theme'] = $_GET['theme'];
    echo "Set theme to: " . $_GET['theme'] . "<br>";
}

echo "<h2>Updated Session Data:</h2>";
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
echo "<p><a href='?'>Clear Parameters</a></p>";
?>
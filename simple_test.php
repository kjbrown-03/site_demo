<?php
session_start();

// Test setting and getting session values
if (isset($_GET['lang'])) {
    $_SESSION['language'] = $_GET['lang'];
    echo "Set language to: " . $_GET['lang'] . "<br>";
}

if (isset($_GET['theme'])) {
    $_SESSION['theme'] = $_GET['theme'];
    echo "Set theme to: " . $_GET['theme'] . "<br>";
}

echo "Current language: " . (isset($_SESSION['language']) ? $_SESSION['language'] : 'Not set') . "<br>";
echo "Current theme: " . (isset($_SESSION['theme']) ? $_SESSION['theme'] : 'Not set') . "<br>";

echo "<a href='?lang=fr&theme=light'>Set FR + Light</a><br>";
echo "<a href='?lang=en&theme=dark'>Set EN + Dark</a><br>";
echo "<a href='?'>Clear</a><br>";
?>
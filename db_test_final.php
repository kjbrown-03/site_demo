<?php
session_start();
require_once 'config.php';
require_once 'includes/language_handler.php';

echo "<h1>Database Connection Test</h1>";

// Test if $pdo is available
if (isset($pdo)) {
    echo "<p>PDO variable is available: " . get_class($pdo) . "</p>";
    
    try {
        // Test a simple query
        $stmt = $pdo->prepare("SELECT 1 as test");
        $stmt->execute();
        $result = $stmt->fetch();
        echo "<p>Database query successful: " . $result['test'] . "</p>";
    } catch (PDOException $e) {
        echo "<p>Database query failed: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p>PDO variable is not available</p>";
}

echo "<h2>Current Language and Theme:</h2>";
echo "<p>Current Language: " . $currentLang . "</p>";
echo "<p>Current Theme: " . $currentTheme . "</p>";

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
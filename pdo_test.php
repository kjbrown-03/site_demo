<?php
session_start();
require_once 'config.php';

echo "<h1>PDO Test</h1>";

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

echo "<h2>Session Data:</h2>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";
?>
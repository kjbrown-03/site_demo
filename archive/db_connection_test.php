<?php
session_start();
require_once 'config.php';

echo "<h1>Database Connection Test</h1>";

try {
    // Test basic connection
    $stmt = $pdo->prepare("SELECT 1");
    $stmt->execute();
    $result = $stmt->fetch();
    echo "<p>Database connection: SUCCESS</p>";
    
    // Check if users table exists
    $stmt = $pdo->prepare("SHOW TABLES LIKE 'users'");
    $stmt->execute();
    $tableExists = $stmt->fetch();
    
    if ($tableExists) {
        echo "<p>Users table exists: YES</p>";
        
        // Check table structure
        $stmt = $pdo->prepare("DESCRIBE users");
        $stmt->execute();
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<h2>Users Table Columns:</h2>";
        echo "<ul>";
        foreach ($columns as $column) {
            echo "<li>" . $column['Field'] . " (" . $column['Type'] . ")</li>";
        }
        echo "</ul>";
        
        // If user is logged in, test user data
        if (isset($_SESSION['user_id'])) {
            echo "<h2>User Data Test:</h2>";
            $stmt = $pdo->prepare("SELECT id, username, language_preference, theme_preference FROM users WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user) {
                echo "<pre>";
                print_r($user);
                echo "</pre>";
            } else {
                echo "<p>No user found with ID " . $_SESSION['user_id'] . "</p>";
            }
        } else {
            echo "<p>User not logged in. Cannot test user-specific data.</p>";
        }
    } else {
        echo "<p>Users table exists: NO</p>";
    }
} catch(PDOException $e) {
    echo "<p>Database error: " . $e->getMessage() . "</p>";
}
?>
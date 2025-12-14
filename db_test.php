<?php
session_start();
require_once 'config.php';

echo "<h1>Database Test</h1>";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<p>User not logged in. Setting a test user ID for testing purposes.</p>";
    $_SESSION['user_id'] = 1; // Assuming user ID 1 exists for testing
}

try {
    // Check if the columns exist
    $stmt = $pdo->prepare("DESCRIBE users");
    $stmt->execute();
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h2>Users Table Columns:</h2>";
    echo "<ul>";
    foreach ($columns as $column) {
        echo "<li>" . $column['Field'] . " (" . $column['Type'] . ")</li>";
    }
    echo "</ul>";
    
    // Try to fetch user data
    $stmt = $pdo->prepare("SELECT id, first_name, last_name, language_preference, theme_preference FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        echo "<h2>User Data:</h2>";
        echo "<pre>";
        print_r($user);
        echo "</pre>";
        
        // Try to update user preferences
        echo "<h2>Testing Update:</h2>";
        $stmt = $pdo->prepare("UPDATE users SET language_preference = ?, theme_preference = ? WHERE id = ?");
        $result = $stmt->execute(['en', 'dark', $_SESSION['user_id']]);
        
        if ($result) {
            echo "<p>Successfully updated user preferences</p>";
        } else {
            echo "<p>Failed to update user preferences</p>";
        }
    } else {
        echo "<p>No user found with ID " . $_SESSION['user_id'] . "</p>";
    }
} catch(PDOException $e) {
    echo "<p>Error: " . $e->getMessage() . "</p>";
}
?>
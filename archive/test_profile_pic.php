<?php
// Test script to verify database connection and profile picture functionality

require_once 'config.php';

try {
    // Test if we can read from the users table
    $stmt = $pdo->prepare("SELECT id, username, profile_picture FROM users WHERE id = 1");
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        echo "User found:\n";
        echo "ID: " . $user['id'] . "\n";
        echo "Username: " . $user['username'] . "\n";
        echo "Profile Picture: " . ($user['profile_picture'] ?? 'None') . "\n";
    } else {
        echo "No user found with ID 1\n";
    }
    
    echo "\nDatabase connection and users table are working correctly!\n";
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
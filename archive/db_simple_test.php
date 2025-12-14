<?php
session_start();
require_once 'config.php';

echo "<h1>Simple Database Test</h1>";

try {
    // Test basic connection
    $stmt = $pdo->prepare("SELECT 1 as test");
    $stmt->execute();
    $result = $stmt->fetch();
    echo "<p>Database connection: <span style='color: green;'>SUCCESS</span></p>";
    echo "<p>Test query result: " . $result['test'] . "</p>";
    
    // If user is logged in, test user data
    if (isset($_SESSION['user_id'])) {
        echo "<h2>User Data Test:</h2>";
        $stmt = $pdo->prepare("SELECT id, username FROM users WHERE id = ? LIMIT 1");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            echo "<p>User found: " . htmlspecialchars($user['username']) . " (ID: " . $user['id'] . ")</p>";
        } else {
            echo "<p>No user found with ID " . $_SESSION['user_id'] . "</p>";
        }
    } else {
        echo "<p>User not logged in. Cannot test user-specific data.</p>";
    }
} catch(PDOException $e) {
    echo "<p>Database error: <span style='color: red;'>" . $e->getMessage() . "</span></p>";
}
?>
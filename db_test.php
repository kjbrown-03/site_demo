<?php
require_once 'config.php';

try {
    // Test database connection
    echo "Testing database connection...\n";
    
    // Check if properties table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'properties'");
    $tableExists = $stmt->fetch();
    
    if ($tableExists) {
        echo "Properties table exists\n";
        
        // Count properties
        $stmt = $pdo->query("SELECT COUNT(*) FROM properties");
        $count = $stmt->fetchColumn();
        echo "Number of properties: " . $count . "\n";
        
        // Try a simple query
        $stmt = $pdo->query("SELECT id, title, price FROM properties LIMIT 3");
        $properties = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "Sample properties:\n";
        foreach ($properties as $prop) {
            echo "- " . $prop['title'] . " (" . $prop['price'] . ")\n";
        }
    } else {
        echo "Properties table does not exist\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
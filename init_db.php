<?php
require_once 'config.php';

// Create database if it doesn't exist
try {
    $pdo->exec("CREATE DATABASE IF NOT EXISTS immohome");
    echo "Database created successfully<br>";
} catch(PDOException $e) {
    echo "Error creating database: " . $e->getMessage() . "<br>";
}

// Select the database
$pdo->exec("USE immohome");

// Create users table
try {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) UNIQUE NOT NULL,
            email VARCHAR(100) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            role ENUM('buyer', 'seller', 'agent', 'admin') DEFAULT 'buyer',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
    echo "Users table created successfully<br>";
} catch(PDOException $e) {
    echo "Error creating users table: " . $e->getMessage() . "<br>";
}

// Create properties table
try {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS properties (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            description TEXT,
            price DECIMAL(12, 2) NOT NULL,
            address VARCHAR(255) NOT NULL,
            city VARCHAR(100) NOT NULL,
            type ENUM('house', 'apartment', 'villa', 'land') NOT NULL,
            bedrooms INT,
            bathrooms INT,
            area_sqm INT,
            status ENUM('for_sale', 'for_rent', 'sold', 'rented') DEFAULT 'for_sale',
            agent_id INT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (agent_id) REFERENCES users(id)
        )
    ");
    echo "Properties table created successfully<br>";
} catch(PDOException $e) {
    echo "Error creating properties table: " . $e->getMessage() . "<br>";
}

// Create orders table
try {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS orders (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            property_id INT NOT NULL,
            order_type ENUM('purchase', 'rental', 'sale') NOT NULL,
            status ENUM('pending', 'confirmed', 'completed', 'cancelled') DEFAULT 'pending',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id),
            FOREIGN KEY (property_id) REFERENCES properties(id)
        )
    ");
    echo "Orders table created successfully<br>";
} catch(PDOException $e) {
    echo "Error creating orders table: " . $e->getMessage() . "<br>";
}

// Insert sample data
try {
    // Check if users already exist
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users");
    $stmt->execute();
    $userCount = $stmt->fetchColumn();
    
    if ($userCount == 0) {
        // Insert sample users
        $hashedPassword = password_hash('admin123', PASSWORD_DEFAULT);
        $pdo->exec("
            INSERT INTO users (username, email, password, role) VALUES
            ('john_doe', 'john@example.com', '$hashedPassword', 'buyer'),
            ('jane_smith', 'jane@example.com', '$hashedPassword', 'seller'),
            ('agent_miller', 'miller@example.com', '$hashedPassword', 'agent'),
            ('admin_user', 'admin@example.com', '$hashedPassword', 'admin')
        ");
        echo "Sample users inserted<br>";
    }
    
    // Check if properties already exist
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM properties");
    $stmt->execute();
    $propertyCount = $stmt->fetchColumn();
    
    if ($propertyCount == 0) {
        // Insert sample properties
        $pdo->exec("
            INSERT INTO properties (title, description, price, address, city, type, bedrooms, bathrooms, area_sqm, status, agent_id) VALUES
            ('Beautiful Family House', 'A lovely family house with garden', 450000, '123 Main St', 'Paris', 'house', 4, 3, 200, 'for_sale', 3),
            ('Modern Apartment', 'Luxury apartment in the city center', 320000, '456 City Ave', 'Lyon', 'apartment', 2, 2, 100, 'for_sale', 3),
            ('Spacious Villa', 'Large villa with swimming pool', 850000, '789 Hill Rd', 'Nice', 'villa', 5, 4, 350, 'for_sale', 3),
            ('Commercial Land', 'Prime location commercial land', 1200000, '101 Business Park', 'Marseille', 'land', 0, 0, 500, 'for_sale', 3)
        ");
        echo "Sample properties inserted<br>";
    }
    
} catch(PDOException $e) {
    echo "Error inserting sample data: " . $e->getMessage() . "<br>";
}

echo "Database initialization completed!";
?>
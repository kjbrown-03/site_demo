<?php
// Database setup script
echo "ImmoHome Database Setup\n";
echo "======================\n\n";

// Database configuration
$host = 'localhost';
$db_user = 'root';
$db_pass = ''; // No password as specified
$db_name = 'immohome';

try {
    // Connect to MySQL server
    echo "Connecting to MySQL server...\n";
    $pdo = new PDO("mysql:host=$host", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✓ Connected successfully\n\n";
    
    // Create database
    echo "Creating database '$db_name'...\n";
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$db_name`");
    echo "✓ Database created/verified\n\n";
    
    // Select database
    echo "Selecting database...\n";
    $pdo->exec("USE `$db_name`");
    echo "✓ Database selected\n\n";
    
    // Create tables
    echo "Creating tables...\n";
    
    // Users table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) UNIQUE NOT NULL,
            email VARCHAR(100) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            role ENUM('buyer', 'seller', 'agent', 'admin') DEFAULT 'buyer',
            
            -- User profile information
            first_name VARCHAR(50) DEFAULT NULL,
            last_name VARCHAR(50) DEFAULT NULL,
            phone VARCHAR(20) DEFAULT NULL,
            city VARCHAR(100) DEFAULT NULL,
            country VARCHAR(50) DEFAULT NULL,
            
            -- User preferences
            language_preference ENUM('fr', 'en') DEFAULT 'fr',
            theme_preference ENUM('light', 'dark') DEFAULT 'light',
            email_notifications BOOLEAN DEFAULT TRUE,
            search_alerts BOOLEAN DEFAULT TRUE,
            newsletter BOOLEAN DEFAULT FALSE,
            
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            
            INDEX idx_email (email),
            INDEX idx_role (role)
        )
    ");
    echo "✓ Users table created\n";
    
    // Properties table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS properties (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            description TEXT,
            price DECIMAL(12, 2) NOT NULL,
            address VARCHAR(255) NOT NULL,
            city VARCHAR(100) NOT NULL,
            type ENUM('house', 'apartment', 'villa', 'land') NOT NULL,
            bedrooms INT DEFAULT NULL,
            bathrooms INT DEFAULT NULL,
            area_sqm INT DEFAULT NULL,
            status ENUM('for_sale', 'for_rent', 'sold', 'rented') DEFAULT 'for_sale',
            agent_id INT DEFAULT NULL,
            seller_id INT DEFAULT NULL,
            image_url VARCHAR(500) DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            
            FOREIGN KEY (agent_id) REFERENCES users(id) ON DELETE SET NULL,
            FOREIGN KEY (seller_id) REFERENCES users(id) ON DELETE SET NULL,
            
            INDEX idx_type (type),
            INDEX idx_status (status),
            INDEX idx_city (city),
            INDEX idx_price (price),
            INDEX idx_agent (agent_id),
            INDEX idx_seller (seller_id)
        )
    ");
    echo "✓ Properties table created\n";
    
    // Orders table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS orders (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            property_id INT NOT NULL,
            order_type ENUM('purchase', 'rental', 'sale') NOT NULL,
            status ENUM('pending', 'confirmed', 'completed', 'cancelled') DEFAULT 'pending',
            total_amount DECIMAL(12, 2) DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE,
            
            INDEX idx_user (user_id),
            INDEX idx_property (property_id),
            INDEX idx_status (status),
            INDEX idx_type (order_type)
        )
    ");
    echo "✓ Orders table created\n";
    
    // Favorites table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS favorites (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            property_id INT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE,
            
            UNIQUE KEY unique_favorite (user_id, property_id),
            INDEX idx_user (user_id),
            INDEX idx_property (property_id)
        )
    ");
    echo "✓ Favorites table created\n";
    
    // Appointments table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS appointments (
            id INT AUTO_INCREMENT PRIMARY KEY,
            agent_id INT NOT NULL,
            client_id INT NOT NULL,
            property_id INT DEFAULT NULL,
            appointment_date DATE NOT NULL,
            appointment_time TIME NOT NULL,
            appointment_type ENUM('property_visit', 'contract_signing', 'project_discussion', 'follow_up') NOT NULL,
            location VARCHAR(255),
            notes TEXT,
            status ENUM('pending', 'confirmed', 'completed', 'cancelled') DEFAULT 'pending',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            
            FOREIGN KEY (agent_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (client_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE SET NULL,
            
            INDEX idx_agent (agent_id),
            INDEX idx_client (client_id),
            INDEX idx_date (appointment_date),
            INDEX idx_status (status)
        )
    ");
    echo "✓ Appointments table created\n";
    
    // Leads table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS leads (
            id INT AUTO_INCREMENT PRIMARY KEY,
            agent_id INT NOT NULL,
            first_name VARCHAR(50) NOT NULL,
            last_name VARCHAR(50) NOT NULL,
            email VARCHAR(100),
            phone VARCHAR(20),
            interest ENUM('house', 'apartment', 'rent', 'investment', 'other') NOT NULL,
            budget DECIMAL(12, 2),
            location_preference VARCHAR(100),
            notes TEXT,
            status ENUM('active', 'pending', 'converted', 'archived') DEFAULT 'active',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (agent_id) REFERENCES users(id) ON DELETE CASCADE
        )
    ");
    echo "✓ Leads table created\n\n";
    
    // Insert sample data
    echo "Inserting sample data...\n";
    
    // Check if users exist
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users");
    $stmt->execute();
    $userCount = $stmt->fetchColumn();
    
    if ($userCount == 0) {
        // Insert sample users with specified passwords
        $hashedPassword = password_hash('admin123', PASSWORD_DEFAULT);
        $pdo->exec("
            INSERT INTO users (username, email, password, role, first_name, last_name) VALUES
            ('john_doe', 'john@example.com', '$hashedPassword', 'buyer', 'John', 'Doe'),
            ('jane_smith', 'jane@example.com', '$hashedPassword', 'seller', 'Jane', 'Smith'),
            ('agent_miller', 'miller@example.com', '$hashedPassword', 'agent', 'Agent', 'Miller'),
            ('admin_user', 'admin@example.com', '$hashedPassword', 'admin', 'Admin', 'User')
        ");
        echo "✓ Sample users inserted\n";
    } else {
        echo "✓ Users already exist, skipping user insertion\n";
    }
    
    // Check if properties exist
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
            ('Commercial Land', 'Prime location commercial land', 1200000, '101 Business Park', 'Marseille', 'land', 0, 0, 500, 'for_sale', 3),
            ('Cozy Studio Apartment', 'Perfect for students or young professionals', 150000, '321 Student Lane', 'Paris', 'apartment', 1, 1, 30, 'for_rent', 3),
            ('Luxury Penthouse', 'Stunning penthouse with panoramic views', 950000, '555 Sky Tower', 'Nice', 'apartment', 3, 2, 150, 'for_rent', 3)
        ");
        echo "✓ Sample properties inserted\n";
    } else {
        echo "✓ Properties already exist, skipping property insertion\n";
    }
    
    // Check if orders exist
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM orders");
    $stmt->execute();
    $orderCount = $stmt->fetchColumn();
    
    if ($orderCount == 0) {
        // Insert sample orders
        $pdo->exec("
            INSERT INTO orders (user_id, property_id, order_type, status) VALUES
            (1, 2, 'purchase', 'confirmed'),
            (1, 5, 'rental', 'pending')
        ");
        echo "✓ Sample orders inserted\n";
    } else {
        echo "✓ Orders already exist, skipping order insertion\n";
    }
    
    echo "\nDatabase setup completed successfully!\n";
    echo "=====================================\n";
    echo "You can now access the application.\n";
    echo "Login with username 'admin_user' and password 'admin'\n";
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Please make sure:\n";
    echo "1. MySQL server is running\n";
    echo "2. Username 'root' exists with no password\n";
    echo "3. You have sufficient privileges to create databases\n";
}
?>
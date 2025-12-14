-- =====================================================
-- ImmoHome Database Schema
-- Complete database structure for the real estate platform
-- =====================================================

-- Create the database
CREATE DATABASE IF NOT EXISTS immohome;
USE immohome;

-- =====================================================
-- USERS TABLE
-- =====================================================
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
);

-- =====================================================
-- PROPERTIES TABLE
-- =====================================================
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
);

-- =====================================================
-- ORDERS TABLE
-- =====================================================
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
);

-- =====================================================
-- FAVORITES TABLE
-- =====================================================
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
);

-- =====================================================
-- APPOINTMENTS TABLE
-- =====================================================
CREATE TABLE IF NOT EXISTS appointments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    agent_id INT NOT NULL,
    client_id INT NOT NULL,
    property_id INT DEFAULT NULL,
    appointment_date DATETIME NOT NULL,
    status ENUM('pending', 'confirmed', 'completed', 'cancelled') DEFAULT 'pending',
    notes TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (agent_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (client_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE SET NULL,
    
    INDEX idx_agent (agent_id),
    INDEX idx_client (client_id),
    INDEX idx_date (appointment_date),
    INDEX idx_status (status)
);

-- =====================================================
-- LEADS TABLE (Client Leads for Agents)
-- =====================================================
CREATE TABLE IF NOT EXISTS leads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    agent_id INT NOT NULL,
    client_name VARCHAR(100) NOT NULL,
    client_email VARCHAR(100) NOT NULL,
    client_phone VARCHAR(20) DEFAULT NULL,
    property_type VARCHAR(50) DEFAULT NULL,
    budget_min DECIMAL(12, 2) DEFAULT NULL,
    budget_max DECIMAL(12, 2) DEFAULT NULL,
    notes TEXT DEFAULT NULL,
    status ENUM('new', 'contacted', 'qualified', 'converted', 'lost') DEFAULT 'new',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (agent_id) REFERENCES users(id) ON DELETE CASCADE,
    
    INDEX idx_agent (agent_id),
    INDEX idx_status (status)
);

-- =====================================================
-- SAMPLE DATA
-- =====================================================

-- Insert sample users
INSERT INTO users (username, email, password, role) VALUES
('john_doe', 'john@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'buyer'),
('jane_smith', 'jane@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'seller'),
('agent_miller', 'miller@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'agent'),
('admin_user', 'admin@example.com', '$2y$10$LKvJhxJV5QocIZFbZZ/RWuu6.E547kWMxIIMtQbQp9q8MBw9jXCiG', 'admin')
ON DUPLICATE KEY UPDATE username=username;

-- Insert sample properties
INSERT INTO properties (title, description, price, address, city, type, bedrooms, bathrooms, area_sqm, status, agent_id) VALUES
('Beautiful Family House', 'A lovely family house with garden', 450000, '123 Main St', 'Paris', 'house', 4, 3, 200, 'for_sale', 3),
('Modern Apartment', 'Luxury apartment in the city center', 320000, '456 City Ave', 'Lyon', 'apartment', 2, 2, 100, 'for_sale', 3),
('Spacious Villa', 'Large villa with swimming pool', 850000, '789 Hill Rd', 'Nice', 'villa', 5, 4, 350, 'for_sale', 3),
('Commercial Land', 'Prime location commercial land', 1200000, '101 Business Park', 'Marseille', 'land', 0, 0, 500, 'for_sale', 3),
('Cozy Studio Apartment', 'Perfect for students or young professionals', 150000, '321 Student Lane', 'Paris', 'apartment', 1, 1, 30, 'for_rent', 3),
('Luxury Penthouse', 'Stunning penthouse with panoramic views', 950000, '555 Sky Tower', 'Nice', 'apartment', 3, 2, 150, 'for_rent', 3)
ON DUPLICATE KEY UPDATE title=title;

-- Insert sample orders
INSERT INTO orders (user_id, property_id, order_type, status) VALUES
(1, 2, 'purchase', 'confirmed'),
(1, 5, 'rental', 'pending')
ON DUPLICATE KEY UPDATE user_id=user_id;



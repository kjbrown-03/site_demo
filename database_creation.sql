-- Database creation script for ImmoHome

-- Create the database
CREATE DATABASE IF NOT EXISTS immohome;
USE immohome;

-- Create users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('buyer', 'seller', 'agent', 'admin') DEFAULT 'buyer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create properties table
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
);

-- Create orders table
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    property_id INT NOT NULL,
    order_type ENUM('purchase', 'rental', 'sale') NOT NULL,
    status ENUM('pending', 'confirmed', 'completed', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (property_id) REFERENCES properties(id)
);

-- Create favorites table
CREATE TABLE IF NOT EXISTS favorites (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    property_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_property (user_id, property_id)
);

-- Create appointments table
CREATE TABLE IF NOT EXISTS appointments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    agent_id INT NOT NULL,
    client_id INT NOT NULL,
    property_id INT NULL,
    appointment_date DATE NOT NULL,
    appointment_time TIME NOT NULL,
    appointment_type ENUM('property_visit', 'contract_signing', 'project_discussion', 'follow_up') NOT NULL,
    location VARCHAR(255),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (agent_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (client_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE SET NULL
);

-- Create leads table
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
);

-- Insert sample users
INSERT INTO users (username, email, password, role) VALUES
('john_doe', 'john@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'buyer'),
('jane_smith', 'jane@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'seller'),
('agent_miller', 'miller@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'agent'),
('admin_user', 'admin@example.com', '$2y$10$LKvJhxJV5QocIZFbZZ/RWuu6.E547kWMxIIMtQbQp9q8MBw9jXCiG', 'admin');

-- Insert sample properties
INSERT INTO properties (title, description, price, address, city, type, bedrooms, bathrooms, area_sqm, status, agent_id) VALUES
('Beautiful Family House', 'A lovely family house with garden', 450000, '123 Main St', 'Paris', 'house', 4, 3, 200, 'for_sale', 3),
('Modern Apartment', 'Luxury apartment in the city center', 320000, '456 City Ave', 'Lyon', 'apartment', 2, 2, 100, 'for_sale', 3),
('Spacious Villa', 'Large villa with swimming pool', 850000, '789 Hill Rd', 'Nice', 'villa', 5, 4, 350, 'for_sale', 3),
('Commercial Land', 'Prime location commercial land', 1200000, '101 Business Park', 'Marseille', 'land', 0, 0, 500, 'for_sale', 3),
('Cozy Studio Apartment', 'Perfect for students or young professionals', 150000, '321 Student Lane', 'Paris', 'apartment', 1, 1, 30, 'for_rent', 3),
('Luxury Penthouse', 'Stunning penthouse with panoramic views', 950000, '555 Sky Tower', 'Nice', 'apartment', 3, 2, 150, 'for_rent', 3);

-- Insert sample orders
INSERT INTO orders (user_id, property_id, order_type, status) VALUES
(1, 2, 'purchase', 'confirmed'),
(1, 5, 'rental', 'pending');
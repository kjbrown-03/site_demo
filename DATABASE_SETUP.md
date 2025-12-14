# Database Setup Instructions

## Prerequisites

1. Install MySQL Server (version 5.7 or higher)
2. Install PHP (version 7.0 or higher) with PDO MySQL extension

## Installation Steps

### 1. Install MySQL Server

Download and install MySQL Server from:
https://dev.mysql.com/downloads/mysql/

During installation:
- Set root password to: `123456789`
- Make sure to add MySQL to system PATH

### 2. Create Database Manually

Open MySQL command line client and run:

```sql
CREATE DATABASE immohome;
USE immohome;

-- Create users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('buyer', 'seller', 'agent', 'admin') DEFAULT 'buyer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create properties table
CREATE TABLE properties (
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
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    property_id INT NOT NULL,
    order_type ENUM('purchase', 'rental', 'sale') NOT NULL,
    status ENUM('pending', 'confirmed', 'completed', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (property_id) REFERENCES properties(id)
);

-- Insert sample users
INSERT INTO users (username, email, password, role) VALUES
('john_doe', 'john@example.com', '$2y$10$rOzJQbT5bGdFvD5pD9xQkOuFvYzFvYzFvYzFvYzFvYzFvYzFvYzFv', 'buyer'),
('jane_smith', 'jane@example.com', '$2y$10$rOzJQbT5bGdFvD5pD9xQkOuFvYzFvYzFvYzFvYzFvYzFvYzFvYzFv', 'seller'),
('agent_miller', 'miller@example.com', '$2y$10$rOzJQbT5bGdFvD5pD9xQkOuFvYzFvYzFvYzFvYzFvYzFvYzFvYzFv', 'agent'),
('admin_user', 'admin@example.com', '$2y$10$rOzJQbT5bGdFvD5pD9xQkOuFvYzFvYzFvYzFvYzFvYzFvYzFvYzFv', 'admin');
```

### 3. Configure Database Connection

The database connection is already configured in `config.php`:
- Host: localhost
- Username: root
- Password: 123456789
- Database: immohome

### 4. Test the Setup

After setting up the database, you can test the connection by visiting:
- Login Page: http://localhost/login.php
- Registration Page: http://localhost/register.php

### 5. Default User Accounts

After importing the sample data, you can log in with:

1. Buyer Account:
   - Email: john@example.com
   - Password: password123

2. Seller Account:
   - Email: jane@example.com
   - Password: password123

3. Agent Account:
   - Email: miller@example.com
   - Password: password123

4. Admin Account:
   - Email: admin@example.com
   - Password: password123

## Troubleshooting

If you encounter connection issues:

1. Verify MySQL is running
2. Check if the credentials in `config.php` are correct
3. Ensure the PDO MySQL extension is enabled in PHP
4. Verify that the firewall is not blocking MySQL connections
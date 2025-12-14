-- Add user preferences columns to the users table
ALTER TABLE users 
ADD COLUMN first_name VARCHAR(50) DEFAULT NULL,
ADD COLUMN last_name VARCHAR(50) DEFAULT NULL,
ADD COLUMN phone VARCHAR(20) DEFAULT NULL,
ADD COLUMN city VARCHAR(100) DEFAULT NULL,
ADD COLUMN country VARCHAR(50) DEFAULT NULL,
ADD COLUMN language_preference ENUM('fr', 'en') DEFAULT 'fr',
ADD COLUMN theme_preference ENUM('light', 'dark') DEFAULT 'light',
ADD COLUMN email_notifications BOOLEAN DEFAULT TRUE,
ADD COLUMN search_alerts BOOLEAN DEFAULT TRUE,
ADD COLUMN newsletter BOOLEAN DEFAULT FALSE;
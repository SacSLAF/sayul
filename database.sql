-- Database: sayul_tours

CREATE DATABASE IF NOT EXISTS sayul_tours;
USE sayul_tours;

-- Admins Table
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Gallery Images Table
CREATE TABLE IF NOT EXISTS gallery_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    image_path VARCHAR(255) NOT NULL,
    caption VARCHAR(100),
    category VARCHAR(50) DEFAULT 'general',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Packages Table
CREATE TABLE IF NOT EXISTS packages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    price_note VARCHAR(100), -- e.g. "per person" or "transport only"
    duration VARCHAR(50), -- e.g. "5 Days"
    image_path VARCHAR(255),
    description TEXT,
    includes TEXT, -- Comma separated or JSON
    itinerary TEXT, -- JSON structure
    is_featured BOOLEAN DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Reviews Table
CREATE TABLE IF NOT EXISTS reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reviewer_name VARCHAR(100) NOT NULL,
    reviewer_country VARCHAR(100),
    review_text TEXT NOT NULL,
    rating INT DEFAULT 5,
    is_approved BOOLEAN DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert Default Admin (username: admin, password: password123)
-- Password hash generated with password_hash('password123', PASSWORD_DEFAULT)
INSERT INTO admins (username, password) 
SELECT 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi' 
WHERE NOT EXISTS (SELECT * FROM admins WHERE username = 'admin');

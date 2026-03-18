-- Create Database (Disabled for InfinityFree)
-- CREATE DATABASE IF NOT EXISTS car_rental_db;
-- USE car_rental_db;

-- Users Table (Customers & Agencies)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('customer', 'agency') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Cars Table
CREATE TABLE IF NOT EXISTS cars (
    id INT AUTO_INCREMENT PRIMARY KEY,
    agency_id INT NOT NULL,
    model VARCHAR(100) NOT NULL,
    vehicle_number VARCHAR(50) UNIQUE NOT NULL,
    seating_capacity INT NOT NULL,
    rent_per_day DECIMAL(10, 2) NOT NULL,
    image_path VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (agency_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Bookings Table
CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    car_id INT NOT NULL,
    customer_id INT NOT NULL,
    start_date DATE NOT NULL,
    days INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (car_id) REFERENCES cars(id) ON DELETE CASCADE,
    FOREIGN KEY (customer_id) REFERENCES users(id) ON DELETE CASCADE
);

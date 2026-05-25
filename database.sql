-- AeroBook MySQL Database Schema and Dummy Data
-- Run this file in your MySQL environment (e.g. phpMyAdmin, MySQL Workbench)

CREATE DATABASE IF NOT EXISTS aerobook;
USE aerobook;

-- 1. Create Users Table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(50) DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. Create Flights Table
CREATE TABLE IF NOT EXISTS flights (
    id INT AUTO_INCREMENT PRIMARY KEY,
    flight_number VARCHAR(50) NOT NULL,
    origin VARCHAR(100) NOT NULL,
    destination VARCHAR(100) NOT NULL,
    departure_time DATETIME NOT NULL,
    arrival_time DATETIME NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    total_seats INT NOT NULL,
    available_seats INT NOT NULL
);

-- 3. Create Bookings Table
CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    flight_id INT NOT NULL,
    booking_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status VARCHAR(50) DEFAULT 'Booked',
    FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY(flight_id) REFERENCES flights(id) ON DELETE CASCADE
);

-- Insert Dummy Flights
INSERT INTO flights (flight_number, origin, destination, departure_time, arrival_time, price, total_seats, available_seats) VALUES 
('FL101', 'New York', 'London', '2026-06-10 08:00:00', '2026-06-10 20:00:00', 450.00, 150, 150),
('FL102', 'London', 'New York', '2026-06-15 10:00:00', '2026-06-15 14:00:00', 500.00, 150, 150),
('FL201', 'Los Angeles', 'Tokyo', '2026-06-12 11:30:00', '2026-06-13 15:00:00', 800.00, 200, 200),
('FL301', 'Dubai', 'Paris', '2026-06-14 09:00:00', '2026-06-14 13:45:00', 350.00, 120, 120),
('FL401', 'Sydney', 'Singapore', '2026-06-20 07:00:00', '2026-06-20 15:30:00', 600.00, 180, 180),
('FL501', 'New York', 'Paris', '2026-06-11 18:00:00', '2026-06-12 07:00:00', 550.00, 160, 160);

-- Note on Admin Account:
-- To create the default admin user securely, please run the `admin_setup.php` script 
-- from your browser AFTER importing this database and configuring your config.php to connect to MySQL.
-- This ensures the password hash is generated correctly by your specific PHP environment.

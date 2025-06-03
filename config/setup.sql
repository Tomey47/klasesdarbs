CREATE DATABASE noliktava;

USE noliktava;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    is_employee TINYINT(1) DEFAULT 0,
    is_shelf_manager TINYINT(1) DEFAULT 0,
    is_admin TINYINT(1) DEFAULT 0
);

CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100),
    quantity INT
);

-- C:\xampp\mysql\bin\mysql.exe -u root -P 3307 -v < C:\xampp\htdocs\klasesdarbs\config\setup.sql
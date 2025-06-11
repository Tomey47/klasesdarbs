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
    category VARCHAR(50),
    price DECIMAL(10, 2),
    quantity INT
);

CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    status VARCHAR(50) DEFAULT 'pending',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

CREATE TABLE shelves (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    capacity INT NOT NULL
);

ALTER TABLE products ADD COLUMN shelf_id INT DEFAULT NULL, ADD FOREIGN KEY (shelf_id) REFERENCES shelves(id) ON DELETE SET NULL;
INSERT INTO shelves (name, capacity) VALUES ('Plaukts 1', 10), ('Plaukts 2', 10), ('Plaukts 3', 8);

INSERT INTO users (username, email, password, is_admin) VALUES
('admin', 'admin@example.com', '$2y$10$aJeqgJHwkiISRBIkrOFYd.iPFGGS2eNOUfVIKNyD.REFvNpFXiCvS', 1);

INSERT INTO users (username, email, password, is_employee) VALUES
('darbinieks', 'darbinieks@example.com', '$2y$10$aJeqgJHwkiISRBIkrOFYd.iPFGGS2eNOUfVIKNyD.REFvNpFXiCvS', 1);

INSERT INTO users (username, email, password, is_shelf_manager) VALUES
('krametajs', 'krametajs@example.com', '$2y$10$aJeqgJHwkiISRBIkrOFYd.iPFGGS2eNOUfVIKNyD.REFvNpFXiCvS', 1);

-- C:\xampp\mysql\bin\mysql.exe -u root -P 3306 -v < C:\xampp\htdocs\klasesdarbs\config\setup.sql
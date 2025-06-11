<?php
session_start();
require_once '../config/mysql.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $stmt = $dbh->prepare('INSERT INTO products (title, category, price, quantity) VALUES (?, ?, ?, ?)');
        $stmt->execute([
            $_POST['title'],
            $_POST['category'],
            $_POST['price'],
            $_POST['quantity']
        ]);
        $_SESSION['success_message'] = 'Produkts veiksmīgi pievienots!';
        header('Location: ../views/add_product.php');
        exit;
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Kļūda pievienojot produktu: " . $e->getMessage();
        header('Location: ../views/add_product.php');
        exit;
    }
}
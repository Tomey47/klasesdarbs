<?php
session_start();
require_once '../config/mysql.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Prepare and execute query
    $stmt = $dbh->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if user exists and password matches
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['is_admin'] = $user['is_admin'];
        $_SESSION['is_employee'] = $user['is_employee'];
        $_SESSION['is_shelf_manager'] = $user['is_shelf_manager'];
        
        header('Location: ../views/dashboard.php');
        exit;
    } else {
        echo "Nepareizs lietotājvārds vai parole.";
    }
}
?>
<?php
require_once '../config/mysql.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $is_employee = isset($_POST['is_employee']) ? 1 : 0;
    $is_shelf_manager = isset($_POST['is_shelf_manager']) ? 1 : 0;
    $is_admin = isset($_POST['is_admin']) ? 1 : 0;

    // Validate input
    if (empty($username) || empty($email) || empty($password)) {
        echo "Lūdzu, aizpildiet visus laukus!";
        exit;
    }

    // Check if username or email already exists
    $stmt = $dbh->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $email]);
    if ($stmt->fetch()) {
        echo "Lietotājvārds vai e-pasts jau eksistē!";
        exit;
    }

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert new user with selected roles
    $stmt = $dbh->prepare("INSERT INTO users (username, email, password, is_employee, is_shelf_manager, is_admin) VALUES (?, ?, ?, ?, ?, ?)");
    if ($stmt->execute([$username, $email, $hashedPassword, $is_employee, $is_shelf_manager, $is_admin])) {
        header('Location: ../views/index.php');
        exit;
    } else {
        echo "Reģistrācija neizdevās. Mēģiniet vēlreizZZZZZZZ!";
    }
}
?>
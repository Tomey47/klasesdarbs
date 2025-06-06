<?php
// filepath: c:\xampp\htdocs\klasesdarbs\controllers\edit_user.php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    header('Location: ../views/index.php');
    exit;
}

require_once '../config/mysql.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $is_employee = isset($_POST['is_employee']) ? 1 : 0;
    $is_shelf_manager = isset($_POST['is_shelf_manager']) ? 1 : 0;
    $is_admin = isset($_POST['is_admin']) ? 1 : 0;

    // Prevent admin from removing their own admin rights
    if ($id == $_SESSION['user_id'] && $is_admin != 1) {
        $_SESSION['delete_message'] = "Nevar noņemt sev administratora tiesības!";
        header('Location: ../views/users.php');
        exit;
    }

    // Check for unique username/email (excluding current user)
    $stmt = $dbh->prepare("SELECT id FROM users WHERE (username = ? OR email = ?) AND id != ?");
    $stmt->execute([$username, $email, $id]);
    if ($stmt->fetch()) {
        $_SESSION['delete_message'] = "Lietotājvārds vai e-pasts jau eksistē!";
        header('Location: ../views/users.php');
        exit;
    }

    // Update user
    $stmt = $dbh->prepare("UPDATE users SET username = ?, email = ?, is_employee = ?, is_shelf_manager = ?, is_admin = ? WHERE id = ?");
    $stmt->execute([$username, $email, $is_employee, $is_shelf_manager, $is_admin, $id]);

    $_SESSION['delete_message'] = "Lietotājs veiksmīgi atjaunināts!";
    header('Location: ../views/users.php');
    exit;
}
header('Location: ../views/users.php');
exit;
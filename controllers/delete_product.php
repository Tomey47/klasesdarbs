<?php
session_start();
require_once '../config/mysql.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if user is authorized
    if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
        $_SESSION['error_message'] = "Jums nav atļauts dzēst produktus!";
        header('Location: ../views/dashboard.php');
        exit;
    }

    // Sanitize input
    $id = filter_var($_POST['id'] ?? 0, FILTER_SANITIZE_NUMBER_INT);

    if (!$id) {
        $_SESSION['error_message'] = "Nederīgs produkta ID!";
        header('Location: ../views/dashboard.php');
        exit;
    }

    try {
        // Check if product exists
        $stmt = $dbh->prepare('SELECT id FROM products WHERE id = ?');
        $stmt->execute([$id]);
        if (!$stmt->fetch()) {
            $_SESSION['error_message'] = "Produkts nav atrasts!";
            header('Location: ../views/dashboard.php');
            exit;
        }

        // Delete product
        $stmt = $dbh->prepare('DELETE FROM products WHERE id = ?');
        $stmt->execute([$id]);
        
        $_SESSION['success_message'] = 'Produkts veiksmīgi dzēsts!';
        header('Location: ../views/dashboard.php');
        exit;
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Kļūda dzēšot produktu: " . $e->getMessage();
        header('Location: ../views/dashboard.php');
        exit;
    }
}
?> 
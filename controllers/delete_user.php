<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    header('Location: ../views/index.php');
    exit;
}

require_once '../config/mysql.php';

if (isset($_GET['id'])) {
    $user_id = intval($_GET['id']);

    // Prevent admin from deleting themselves
    if ($user_id == $_SESSION['user_id']) {
        $_SESSION['delete_message'] = "Nevar dzēst sevi!";
        header('Location: ../views/users.php');
        exit;
    }

    // Delete user
    $stmt = $dbh->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $_SESSION['delete_message'] = "Lietotājs veiksmīgi dzēsts!";
}

header('Location: ../views/users.php');
exit;
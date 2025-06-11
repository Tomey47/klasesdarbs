<?php

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    header('Location: ../views/index.php');
    exit;
}

require_once '../config/mysql.php';

function validateUsername($username) {
    if (empty($username)) {
        return "Lietotājvārds ir obligāts!";
    }
    if (strlen($username) < 5 || strlen($username) > 30) {
        return "Lietotājvārdam jābūt no 5 līdz 30 rakstzīmēm!";
    }
    if (!preg_match('/^[a-zA-Z0-9._]+$/', $username)) {
        return "Lietotājvārdam var saturēt tikai burtus, ciparus, punktus un pasvītrojuma zīmes!";
    }
    return null;
}

function validateEmail($email) {
    if (empty($email)) {
        return "E-pasts ir obligāts!";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return "Nederīgs e-pasta formāts!";
    }
    
    
    $domain = substr(strrchr($email, "@"), 1);
    if (!checkdnsrr($domain, 'MX') && !checkdnsrr($domain, 'A')) {
        return "E-pasta domēns neeksistē!";
    }
    return null;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);
    $username = trim(htmlspecialchars($_POST['username'] ?? ''));
    $email = trim(htmlspecialchars($_POST['email'] ?? ''));
    $is_employee = isset($_POST['is_employee']) ? 1 : 0;
    $is_shelf_manager = isset($_POST['is_shelf_manager']) ? 1 : 0;
    $is_admin = isset($_POST['is_admin']) ? 1 : 0;

    
    $usernameError = validateUsername($username);
    $emailError = validateEmail($email);

    if ($usernameError || $emailError) {
        $_SESSION['error_message'] = implode("<br>", array_filter([$usernameError, $emailError]));
        header('Location: ../views/users.php');
        exit;
    }

    
    if ($id == $_SESSION['user_id'] && $is_admin != 1) {
        $_SESSION['error_message'] = "Nevar noņemt sev administratora tiesības!";
        header('Location: ../views/users.php');
        exit;
    }

    
    $stmt = $dbh->prepare("SELECT id FROM users WHERE (username = ? OR email = ?) AND id != ?");
    $stmt->execute([$username, $email, $id]);
    if ($stmt->fetch()) {
        $_SESSION['error_message'] = "Lietotājvārds vai e-pasts jau eksistē!";
        header('Location: ../views/users.php');
        exit;
    }

    try {
        
        $stmt = $dbh->prepare("UPDATE users SET username = ?, email = ?, is_employee = ?, is_shelf_manager = ?, is_admin = ? WHERE id = ?");
        $stmt->execute([$username, $email, $is_employee, $is_shelf_manager, $is_admin, $id]);
        $_SESSION['success_message'] = "Lietotājs veiksmīgi atjaunināts!";
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Kļūda atjauninot lietotāju: " . $e->getMessage();
    }
    
    header('Location: ../views/users.php');
    exit;
}

header('Location: ../views/users.php');
exit;
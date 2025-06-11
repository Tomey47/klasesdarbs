<?php
require_once '../config/mysql.php';

function validateUsername($username) {
    // Username should be 5-30 characters, alphanumeric with dots and underscores
    if (!preg_match('/^[a-zA-Z0-9._]{5,30}$/', $username)) {
        return "Lietotājvārdam jābūt 5-30 rakstzīmēm garam un var saturēt tikai burtus, ciparus, punktus un pasvītrojuma zīmes!";
    }
    return null;
}

function validateEmail($email) {
    // Basic email format validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return "Nederīgs e-pasta formāts!";
    }
    
    // Check if email domain is valid
    $domain = substr(strrchr($email, "@"), 1);
    if (!checkdnsrr($domain, 'MX') && !checkdnsrr($domain, 'A')) {
        return "E-pasta domēns neeksistē!";
    }
    
    return null;
}

function validatePassword($password) {
    $errors = [];
    
    if (strlen($password) < 8) {
        $errors[] = "Parolei jābūt vismaz 8 rakstzīmēm garai!";
    }
    
    if (!preg_match('/[A-Z]/', $password)) {
        $errors[] = "Parolei jāsatur vismaz viens lielais burts!";
    }
    
    if (!preg_match('/[a-z]/', $password)) {
        $errors[] = "Parolei jāsatur vismaz viens mazs burts!";
    }
    
    if (!preg_match('/[0-9]/', $password)) {
        $errors[] = "Parolei jāsatur vismaz viens cipars!";
    }
    
    if (!preg_match('/[!@#$%^&*()\-_=+{};:,<.>]/', $password)) {
        $errors[] = "Parolei jāsatur vismaz viens speciālais simbols!";
    }
    
    return empty($errors) ? null : implode(" ", $errors);
}

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

    // Run all validations
    $usernameError = validateUsername($username);
    $emailError = validateEmail($email);
    $passwordError = validatePassword($password);

    if ($usernameError || $emailError || $passwordError) {
        $errorMessage = [];
        if ($usernameError) $errorMessage[] = $usernameError;
        if ($emailError) $errorMessage[] = $emailError;
        if ($passwordError) $errorMessage[] = $passwordError;
        echo implode("<br>", $errorMessage);
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
        echo "Reģistrācija neizdevās. Mēģiniet vēlreiz!";
    }
}
?>
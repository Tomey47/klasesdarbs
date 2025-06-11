<?php
session_start();
require_once '../config/mysql.php';

function validateProduct($data) {
    $errors = [];
    
    // Title validation
    if (empty($data['title'])) {
        $errors[] = "Produkta nosaukums ir obligāts!";
    } elseif (strlen($data['title']) < 3 || strlen($data['title']) > 100) {
        $errors[] = "Produkta nosaukumam jābūt no 3 līdz 100 rakstzīmēm!";
    } elseif (!preg_match('/^[a-zA-Z0-9\s\-_.,āčēģīķļņšūžĀČĒĢĪĶĻŅŠŪŽ]+$/', $data['title'])) {
        $errors[] = "Produkta nosaukums satur neatļautas rakstzīmes!";
    }

    // Category validation
    if (empty($data['category'])) {
        $errors[] = "Kategorija ir obligāta!";
    } elseif (strlen($data['category']) < 2 || strlen($data['category']) > 50) {
        $errors[] = "Kategorijai jābūt no 2 līdz 50 rakstzīmēm!";
    } elseif (!preg_match('/^[a-zA-Z0-9\s\-_.,āčēģīķļņšūžĀČĒĢĪĶĻŅŠŪŽ]+$/', $data['category'])) {
        $errors[] = "Kategorija satur neatļautas rakstzīmes!";
    }

    // Price validation
    if (!isset($data['price']) || $data['price'] === '') {
        $errors[] = "Cena ir obligāta!";
    } elseif (!is_numeric($data['price']) || $data['price'] <= 0) {
        $errors[] = "Cenai jābūt pozitīvam skaitlim!";
    } elseif ($data['price'] > 999999.99) {
        $errors[] = "Cena nevar būt lielāka par 999999.99 EUR!";
    }

    // Quantity validation
    if (!isset($data['quantity']) || $data['quantity'] === '') {
        $errors[] = "Daudzums ir obligāts!";
    } elseif (!is_numeric($data['quantity']) || $data['quantity'] < 0) {
        $errors[] = "Daudzumam jābūt nenegatīvam skaitlim!";
    } elseif ($data['quantity'] > 999999) {
        $errors[] = "Daudzums nevar būt lielāks par 999999!";
    }

    return $errors;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if user is authorized
    if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
        $_SESSION['error_message'] = "Jums nav atļauts rediģēt produktus!";
        header('Location: ../views/dashboard.php');
        exit;
    }

    // Sanitize input
    $id = filter_var($_POST['id'] ?? 0, FILTER_SANITIZE_NUMBER_INT);
    $title = trim(htmlspecialchars($_POST['title'] ?? ''));
    $category = trim(htmlspecialchars($_POST['category'] ?? ''));
    $price = filter_var($_POST['price'] ?? 0, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $quantity = filter_var($_POST['quantity'] ?? 0, FILTER_SANITIZE_NUMBER_INT);

    // Validate input
    $validationErrors = validateProduct([
        'title' => $title,
        'category' => $category,
        'price' => $price,
        'quantity' => $quantity
    ]);

    if (!empty($validationErrors)) {
        $_SESSION['error_message'] = implode("<br>", $validationErrors);
        header('Location: ../views/edit_product.php?id=' . $id);
        exit;
    }

    try {
        // Check for duplicate product (excluding current product)
        $stmt = $dbh->prepare('SELECT id FROM products WHERE title = ? AND category = ? AND id != ?');
        $stmt->execute([$title, $category, $id]);
        if ($stmt->fetch()) {
            $_SESSION['error_message'] = "Šāds produkts jau eksistē šajā kategorijā!";
            header('Location: ../views/edit_product.php?id=' . $id);
            exit;
        }

        // Update product
        $stmt = $dbh->prepare('UPDATE products SET title = ?, category = ?, price = ?, quantity = ? WHERE id = ?');
        $stmt->execute([$title, $category, $price, $quantity, $id]);
        
        $_SESSION['success_message'] = 'Produkts veiksmīgi atjaunināts!';
        header('Location: ../views/dashboard.php');
        exit;
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Kļūda atjauninot produktu: " . $e->getMessage();
        header('Location: ../views/edit_product.php?id=' . $id);
        exit;
    }
}
?> 
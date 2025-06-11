<?php
session_start();
require_once '../config/mysql.php';

function validateProduct($data, $shelfUsage = [], $shelves = []) {
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

    // Shelf validation
    if (empty($data['shelf_id'])) {
        $errors[] = "Plaukts ir obligāts!";
    } elseif (!isset($shelves[$data['shelf_id']])) {
        $errors[] = "Izvēlētais plaukts neeksistē!";
    } else {
        $capacity = $shelves[$data['shelf_id']]['capacity'];
        $used = $shelfUsage[$data['shelf_id']] ?? 0;
        if (($used + (int)$data['quantity']) > $capacity) {
            $errors[] = "Izvēlētajā plauktā nav pietiekami daudz vietas!";
        }
    }

    return $errors;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (
        (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) &&
        (!isset($_SESSION['is_shelf_manager']) || !$_SESSION['is_shelf_manager'])
    ) {
        $_SESSION['error_message'] = "Jums nav atļauts pievienot produktus!";
        header('Location: ../views/add_product.php');
        exit;
    }

    $title = trim(htmlspecialchars($_POST['title'] ?? ''));
    $category = trim(htmlspecialchars($_POST['category'] ?? ''));
    $price = filter_var($_POST['price'] ?? 0, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $quantity = filter_var($_POST['quantity'] ?? 0, FILTER_SANITIZE_NUMBER_INT);
    $shelf_id = $_POST['shelf_id'] ?? '';

    $shelvesArr = [];
    $shelfUsage = [];
    $shelvesQuery = $dbh->query("SELECT id, capacity FROM shelves");
    foreach ($shelvesQuery as $row) {
        $shelvesArr[$row['id']] = ['capacity' => $row['capacity']];
        $stmt = $dbh->prepare("SELECT SUM(quantity) as total FROM products WHERE shelf_id = ?");
        $stmt->execute([$row['id']]);
        $shelfUsage[$row['id']] = (int)($stmt->fetchColumn() ?? 0);
    }

    $validationErrors = validateProduct([
        'title' => $title,
        'category' => $category,
        'price' => $price,
        'quantity' => $quantity,
        'shelf_id' => $shelf_id
    ], $shelfUsage, $shelvesArr);

    if (!empty($validationErrors)) {
        $_SESSION['error_message'] = implode("  " ,$validationErrors);
        header('Location: ../views/add_product.php');
        exit;
    }

    try {
        // Check for duplicate product
        $stmt = $dbh->prepare('SELECT id FROM products WHERE title = ? AND category = ?');
        $stmt->execute([$title, $category]);
        if ($stmt->fetch()) {
            $_SESSION['error_message'] = "Šāds produkts jau eksistē šajā kategorijā!";
            header('Location: ../views/add_product.php');
            exit;
        }

        // Insert product with prepared statement
        $stmt = $dbh->prepare('INSERT INTO products (title, category, price, quantity, shelf_id) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([
            $title,
            $category,
            $price,
            $quantity,
            $_POST['shelf_id']
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
<?php
require_once '../config/mysql.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $stmt = $dbh->prepare('INSERT INTO products (title, category, price, company_id, quantity) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([
            $_POST['title'],
            $_POST['category'],
            $_POST['price'],
            $_POST['company_id'],
            $_POST['quantity']
        ]);
        header('Location: dashboard.php');
        exit;
    } catch (PDOException $e) {
        $error = "Kļūda pievienojot produktu: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <title>Pievienot Produktu - STASH</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<div class="main-layout">
    <aside class="sidebar">
        <div class="sidebar-header">
            <span>STASH</span>
        </div>
        <nav class="sidebar-nav">
            <ul>
                <li><a href="dashboard.php"><i class="fa-solid fa-home"></i> Sākums</a></li>
                <li class="active"><i class="fa-solid fa-plus"></i> Pievienot produktu</li>
                <li><i class="fa-solid fa-plus"></i> Pievienot lietotāju</li>
                <li><i class="fa-solid fa-user"></i> Lietotāji</li>
                <li><i class="fa-solid fa-right-from-bracket"></i> Iziet</li>
            </ul>
        </nav>
    </aside>
    <div class="container">
        <h1>Pievienot jaunu produktu</h1>
        
        <?php if (isset($error)): ?>
            <div class="error-message">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="product-form">
            <div class="form-group">
                <label for="title">Produkta nosaukums:</label>
                <input type="text" id="title" name="title" required>
            </div>

            <div class="form-group">
                <label for="category">Kategorija:</label>
                <input type="text" id="category" name="category" required>
            </div>

            <div class="form-group">
                <label for="price">Cena (EUR):</label>
                <input type="number" id="price" name="price" step="0.01" required>
            </div>

            <div class="form-group">
                <label for="company_id">Firmas ID:</label>
                <input type="number" id="company_id" name="company_id" required>
            </div>

            <div class="form-group">
                <label for="quantity">Daudzums:</label>
                <input type="number" id="quantity" name="quantity" required>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary">Pievienot produktu</button>
                <a href="dashboard.php" class="btn-secondary">Atcelt</a>
            </div>
        </form>
    </div>
</div>
</body>
</html> 
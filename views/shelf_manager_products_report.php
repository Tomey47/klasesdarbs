<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_shelf_manager']) || $_SESSION['is_shelf_manager'] != 1) {
    header('Location: index.php');
    exit;
}

require_once '../config/mysql.php';

$stmt = $dbh->query("SELECT id, title, category, price, quantity FROM products ORDER BY title ASC");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <title>Sagatavot atskaiti (Produkti)</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<div class="main-layout">
    <aside class="sidebar">
        <div class="sidebar-header">
            <span>Noliktava</span>
        </div>
        <nav class="sidebar-nav">
            <ul>
                <li><a href="dashboard.php"><i class="fa-solid fa-home"></i> Sākums</a></li>
                <li><a href="shelves.php"><i class="fa-solid fa-box"></i> Izvietot preces</a></li>
                <li><a href="shelf_manager_products_report.php"><i class="fa-solid fa-book"></i> Sagatavot atskaiti</a></li>
                <li><a href="add_product.php"><i class="fa-solid fa-user"></i> Datu ievade</a></li>
                <li><a href="../controllers/logout.php"><i class="fa-solid fa-right-from-bracket"></i> Iziet</a></li>
            </ul>
        </nav>
    </aside>
    <div class="container">
        <h1>Sagatavot atskaiti (Produkti)</h1>

        <div style="margin-bottom: 20px;">
            <a href="../controllers/export_products.php" class="update-btn" style="text-decoration: none;">
                <i class="fa-solid fa-file-excel"></i> Eksportēt produktus (Excel)
            </a>
        </div>
        
        <table border="1" width="100%" cellpadding="8" cellspacing="0">
            <tr>
                <th>ID</th>
                <th>Nosaukums</th>
                <th>Kategorija</th>
                <th>Cena</th>
                <th>Daudzums</th>
            </tr>
            <?php foreach ($products as $product): ?>
            <tr>
                <td><?= htmlspecialchars($product['id']) ?></td>
                <td><?= htmlspecialchars($product['title']) ?></td>
                <td><?= htmlspecialchars($product['category']) ?></td>
                <td><?= htmlspecialchars($product['price']) ?></td>
                <td><?= htmlspecialchars($product['quantity']) ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</div>
</body>
</html> 
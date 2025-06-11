<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

require_once '../config/mysql.php';

// If not admin, show only user's orders
$is_admin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;
if ($is_admin) {
    $stmt = $dbh->query("SELECT o.id, u.username, p.title, o.quantity, o.status 
                         FROM orders o
                         JOIN users u ON o.user_id = u.id
                         JOIN products p ON o.product_id = p.id
                         ORDER BY o.id DESC");
} else {
    $stmt = $dbh->prepare("SELECT o.id, u.username, p.title, o.quantity, o.status 
                           FROM orders o
                           JOIN users u ON o.user_id = u.id
                           JOIN products p ON o.product_id = p.id
                           WHERE o.user_id = ?
                           ORDER BY o.id DESC");
    $stmt->execute([$_SESSION['user_id']]);
}
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <title>Pasūtījumi</title>
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
                <?php if ($is_admin): ?>
                    <li><a href="dashboard.php"><i class="fa-solid fa-home"></i> Sākums</a></li>
                    <li><a href="add_product.php"><i class="fa-solid fa-plus"></i> Pievienot produktu</a></li>
                    <li><a href="registration.php"><i class="fa-solid fa-plus"></i> Pievienot lietotāju</a></li>
                    <li><a href="users.php"><i class="fa-solid fa-user"></i> Lietotāji</a></li>
                    <li><a href="../controllers/logout.php"><i class="fa-solid fa-right-from-bracket"></i> Iziet</a></li>
                <?php elseif ($is_employee): ?>
                    <li><a href="dashboard.php"><i class="fa-solid fa-home"></i> Sākums</a></li>
                    <li><a href="employee_orders.php"><i class="fa-solid fa-car"></i> Veikt pasūtījumu</a></li>
                    <li><a href="#"><i class="fa-solid fa-book"></i> Izveidot atskaiti</a></li>
                    <li><a href="../controllers/logout.php"><i class="fa-solid fa-right-from-bracket"></i> Iziet</a></li>
                <?php elseif ($is_shelf_manager): ?>
                    <li><a href="dashboard.php"><i class="fa-solid fa-home"></i> Sākums</a></li>
                    <li><a href="#"><i class="fa-solid fa-box"></i> Izvietot preces</a></li>
                    <li><a href="#"><i class="fa-solid fa-book"></i> Sagatavot atskaiti</a></li>
                    <li><a href="#"><i class="fa-solid fa-user"></i> Datu ievade</a></li>
                    <li><a href="../controllers/logout.php"><i class="fa-solid fa-right-from-bracket"></i> Iziet</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </aside>
    <div class="container">
        <h1>Pasūtījumi</h1>
        <table border="1" width="100%" cellpadding="8" cellspacing="0">
            <tr>
                <th>ID</th>
                <th>Lietotājs</th>
                <th>Produkts</th>
                <th>Daudzums</th>
                <th>Statuss</th>
            </tr>
            <?php foreach ($orders as $order): ?>
            <tr>
                <td><?= htmlspecialchars($order['id']) ?></td>
                <td><?= htmlspecialchars($order['username']) ?></td>
                <td><?= htmlspecialchars($order['title']) ?></td>
                <td><?= htmlspecialchars($order['quantity']) ?></td>
                <td><?= htmlspecialchars($order['status']) ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</div>
</body>
</html>
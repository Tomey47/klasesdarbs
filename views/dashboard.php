<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

require_once '../config/mysql.php';

$products = [];
$stmt = $dbh->query('SELECT * FROM products');
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
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
                <li><a href="#"><i class="fa-solid fa-home"></i> Sākums</a></li>
                <li><a href="#"><i class="fa-solid fa-plus"></i> Pievienot produktu</a></li>
                <?php if ($is_admin): ?>
                    <li><a href="registration.php"><i class="fa-solid fa-plus"></i> Pievienot lietotāju</a></li>
                <?php endif; ?>
                <?php if ($is_admin): ?>
                    <li><a href="users.php"><i class="fa-solid fa-user"></i> Lietotāji</a></li>
                <?php endif; ?>
                <li><a href="../controllers/logout.php"><i class="fa-solid fa-right-from-bracket"></i> Iziet</a></li>
            </ul>
        </nav>
    </aside>
    <div class="container">
        <h1>Produkti</h1>
        <div class="admin-section">
            <table border="1" width="100%" cellpadding="8" cellspacing="0">
                <tr>
                    <th>Produkts</th>
                    <th>Kategorija</th>
                    <th>Cena</th>
                    <th>Firmas ID</th>
                    <th>Daudzums</th>
                    <th>Darbības</th>
                </tr>
                <?php foreach ($products as $product): ?>
                <tr>
                    <td><?= htmlspecialchars($product['title']) ?></td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td><?= htmlspecialchars($product['quantity']) ?></td>
                    <td><button>Dzēst</button> <button>Rediģēt</button></td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>
</div>
</body>
</html>

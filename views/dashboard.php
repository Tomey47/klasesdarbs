<?php
// Simulate user role (replace with real logic later)
$is_admin = true; // Change to false to simulate non-admin

require_once '../config/mysql.php';

$products = [];
if ($is_admin) {
    $stmt = $dbh->query('SELECT * FROM products');
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
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
            <span>STASH</span>
        </div>
        <nav class="sidebar-nav">
            <ul>
                <li class="active"><i class="fa-solid fa-home"></i> Sākums</li>
                <li><a href="add_product.php"><i class="fa-solid fa-plus"></i> Pievienot produktu</a></li>
                <li><i class="fa-solid fa-plus"></i> Pievienot lietotāju</li>
                <li><i class="fa-solid fa-user"></i> Lietotāji</li>
                <li><i class="fa-solid fa-right-from-bracket"></i> Iziet</li>
            </ul>
        </nav>
    </aside>
    <div class="container">
        <h1>Produkti</h1>
        <?php if ($is_admin): ?>
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
        <?php else: ?>
            <div class="not-admin">
                Šī sadaļa ir pieejama tikai administratoriem.
            </div>
        <?php endif; ?>
    </div>
</div>
</body>
</html>

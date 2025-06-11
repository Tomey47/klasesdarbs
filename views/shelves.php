<?php
session_start();
require_once '../config/mysql.php';

$shelves = $dbh->query("SELECT * FROM shelves")->fetchAll(PDO::FETCH_ASSOC);

$productsByShelf = [];
foreach ($shelves as $shelf) {
    $stmt = $dbh->prepare("SELECT * FROM products WHERE shelf_id = ?");
    $stmt->execute([$shelf['id']]);
    $productsByShelf[$shelf['id']] = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'], $_POST['target_shelf'])) {
    $stmt = $dbh->prepare("UPDATE products SET shelf_id = ? WHERE id = ?");
    $stmt->execute([$_POST['target_shelf'], $_POST['product_id']]);
    header("Location: shelves.php");
    exit;
}

$is_admin = $_SESSION['is_admin'] ?? 0;
$is_employee = $_SESSION['is_employee'] ?? 0;
$is_shelf_manager = $_SESSION['is_shelf_manager'] ?? 0;
?>
<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <title>Plaukti - STASH</title>
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
                    <li><a href="../controllers/logout.php"><i class="fa-solid fa-right-from-bracket"></i> Iziet</a></li>
                <?php elseif ($is_shelf_manager): ?>
                    <li><a href="dashboard.php"><i class="fa-solid fa-home"></i> Sākums</a></li>
                    <li><a href="shelves.php"><i class="fa-solid fa-box"></i> Izvietot preces</a></li>
                    <li><a href="shelf_manager_products_report.php"><i class="fa-solid fa-book"></i> Sagatavot atskaiti</a></li>
                    <li><a href="add_product.php"><i class="fa-solid fa-user"></i> Datu ievade</a></li>
                    <li><a href="../controllers/logout.php"><i class="fa-solid fa-right-from-bracket"></i> Iziet</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </aside>
    <div class="container">
        <h1>Plaukti un produkti</h1>
        <?php foreach ($shelves as $shelf): ?>
            <?php
                $totalOnShelf = 0;
                foreach ($productsByShelf[$shelf['id']] as $product) {
                    $totalOnShelf += (int)$product['quantity'];
                }
            ?>
            <div class="admin-section" style="margin-bottom: 32px;">
                <h2>
                    <?= htmlspecialchars($shelf['name']) ?>
                    (<?= $totalOnShelf ?>/<?= htmlspecialchars($shelf['capacity']) ?>)
                </h2>
                <table border="1" width="100%" cellpadding="8" cellspacing="0">
                    <tr>
                        <th>Produkts</th>
                        <th>Kategorija</th>
                        <th>Cena</th>
                        <th>Daudzums</th>
                        <th>Pārvietot</th>
                    </tr>
                    <?php foreach ($productsByShelf[$shelf['id']] as $product): ?>
                    <tr>
                        <td><?= htmlspecialchars($product['title']) ?></td>
                        <td><?= htmlspecialchars($product['category']) ?></td>
                        <td><?= htmlspecialchars($product['price']) ?></td>
                        <td><?= htmlspecialchars($product['quantity']) ?></td>
                        <td>
                            <form method="POST" action="shelves.php" style="display:inline;">
                                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                <select name="target_shelf">
                                    <?php foreach ($shelves as $target): ?>
                                        <?php
                                            $targetTotal = 0;
                                            foreach ($productsByShelf[$target['id']] as $p) {
                                                $targetTotal += (int)$p['quantity'];
                                            }
                                            if (
                                                $target['id'] != $shelf['id'] &&
                                                ($targetTotal + (int)$product['quantity']) <= $target['capacity']
                                            ):
                                        ?>
                                            <option value="<?= $target['id'] ?>">
                                                <?= htmlspecialchars($target['name']) ?> (<?= $targetTotal ?>/<?= $target['capacity'] ?>)
                                            </option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </select>
                                <button type="submit" <?= count(array_filter($shelves, function($target) use ($shelf, $productsByShelf, $product) {
                                    if ($target['id'] == $shelf['id']) return false;
                                    $targetTotal = 0;
                                    foreach ($productsByShelf[$target['id']] as $p) {
                                        $targetTotal += (int)$p['quantity'];
                                    }
                                    return ($targetTotal + (int)$product['quantity']) <= $target['capacity'];
                                })) === 0 ? 'disabled' : '' ?>>Pārvietot</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        <?php endforeach; ?>
    </div>
</div>
</body>
</html>
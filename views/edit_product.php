<?php
session_start();
require_once '../config/mysql.php';

if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header('Location: dashboard.php');
    exit;
}

$id = filter_var($_GET['id'] ?? 0, FILTER_SANITIZE_NUMBER_INT);

if (!$id) {
    $_SESSION['error_message'] = "Nederīgs produkta ID!";
    header('Location: dashboard.php');
    exit;
}

try {
    $stmt = $dbh->prepare('SELECT * FROM products WHERE id = ?');
    $stmt->execute([$id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        $_SESSION['error_message'] = "Produkts nav atrasts!";
        header('Location: dashboard.php');
        exit;
    }
} catch (PDOException $e) {
    $_SESSION['error_message'] = "Kļūda ielādējot produktu: " . $e->getMessage();
    header('Location: dashboard.php');
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
    <title>Rediģēt produktu - STASH</title>
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
                    <li><a href="shelves.php"><i class="fa-solid fa-box"></i> Izvietot preces</a></li>
                    <li><a href="shelf_manager_products_report.php"><i class="fa-solid fa-book"></i> Sagatavot atskaiti</a></li>
                    <li><a href="add_product.php"><i class="fa-solid fa-user"></i> Datu ievade</a></li>
                    <li><a href="../controllers/logout.php"><i class="fa-solid fa-right-from-bracket"></i> Iziet</a></li>
                <?php elseif ($is_employee): ?>
                    <li><a href="dashboard.php"><i class="fa-solid fa-home"></i> Sākums</a></li>
                    <li><a href="employee_orders.php"><i class="fa-solid fa-box"></i> Pasūtījumi</a></li>
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
    <div class="add-product-card">
        <h1>Rediģēt produktu</h1>
        
        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="error-message">
                <?= htmlspecialchars($_SESSION['error_message']) ?>
            </div>
            <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>

        <form method="POST" class="product-form" action="../controllers/edit_product.php" onsubmit="return validateForm()">
            <input type="hidden" name="id" value="<?= htmlspecialchars($product['id']) ?>">
            
            <div class="form-group">
                <label for="title">Produkta nosaukums:</label>
                <input type="text" id="title" name="title" required
                       minlength="3" maxlength="100"
                       pattern="[a-zA-Z0-9\s\-_.,āčēģīķļņšūžĀČĒĢĪĶĻŅŠŪŽ]+"
                       value="<?= htmlspecialchars($product['title']) ?>"
                       title="Produkta nosaukumam jābūt no 3 līdz 100 rakstzīmēm. Atļauti burti, cipari un simboli: -_.,">
                <span class="error" id="titleError"></span>
            </div>

            <div class="form-group">
                <label for="category">Kategorija:</label>
                <input type="text" id="category" name="category" required
                       minlength="2" maxlength="50"
                       pattern="[a-zA-Z0-9\s\-_.,āčēģīķļņšūžĀČĒĢĪĶĻŅŠŪŽ]+"
                       value="<?= htmlspecialchars($product['category']) ?>"
                       title="Kategorijai jābūt no 2 līdz 50 rakstzīmēm. Atļauti burti, cipari un simboli: -_.,">
                <span class="error" id="categoryError"></span>
            </div>

            <div class="form-group">
                <label for="price">Cena (EUR):</label>
                <input type="number" id="price" name="price" required
                       min="0.01" max="999999.99" step="0.01"
                       value="<?= htmlspecialchars($product['price']) ?>">
                <span class="error" id="priceError"></span>
            </div>

            <div class="form-group">
                <label for="quantity">Daudzums:</label>
                <input type="number" id="quantity" name="quantity" required
                       min="0" max="999999"
                       value="<?= htmlspecialchars($product['quantity']) ?>">
                <span class="error" id="quantityError"></span>
            </div>

            <div class="form-actions">
                <button type="submit" class="update-btn">Saglabāt izmaiņas</button>
                <a href="dashboard.php" class="cancel-btn">Atcelt</a>
            </div>
        </form>
    </div>
</div>
</body>
</html> 
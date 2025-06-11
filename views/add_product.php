<?php
session_start();

require_once '../config/mysql.php';
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
    <div class="add-product-card">
        <h1>Pievienot jaunu produktu</h1>
        
        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="error-message">
                <?= htmlspecialchars($_SESSION['error_message']) ?>
            </div>
            <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>

        <form method="POST" class="product-form" action="../controllers/add_product.php" onsubmit="return validateForm()">
            <div class="form-group">
                <label for="title">Produkta nosaukums:</label>
                <input type="text" id="title" name="title"
                       minlength="3" maxlength="100"
                       pattern="[a-zA-Z0-9\s\-_.,āčēģīķļņšūžĀČĒĢĪĶĻŅŠŪŽ]+"
                       title="Produkta nosaukumam jābūt no 3 līdz 100 rakstzīmēm. Atļauti burti, cipari un simboli: -_.,">
                <span class="error" id="titleError"></span>
            </div>

            <div class="form-group">
                <label for="category">Kategorija:</label>
                <input type="text" id="category" name="category"
                       minlength="2" maxlength="50"
                       pattern="[a-zA-Z0-9\s\-_.,āčēģīķļņšūžĀČĒĢĪĶĻŅŠŪŽ]+"
                       title="Kategorijai jābūt no 2 līdz 50 rakstzīmēm. Atļauti burti, cipari un simboli: -_.,">
                <span class="error" id="categoryError"></span>
            </div>

            <div class="form-group">
                <label for="price">Cena (EUR):</label>
                <input type="number" id="price" name="price"
                       min="0.01" max="999999.99" step="0.01"
                       title="Cenai jābūt no 0.01 līdz 999999.99 EUR">
                <span class="error" id="priceError"></span>
            </div>

            <div class="form-group">
                <label for="quantity">Daudzums:</label>
                <input type="number" id="quantity" name="quantity"
                       min="0" max="999999"
                       title="Daudzumam jābūt no 0 līdz 999999">
                <span class="error" id="quantityError"></span>
            </div>

            <div class="form-group">
                <label for="shelf_id">Plaukts:</label>
                <select id="shelf_id" name="shelf_id">
                    <option value="">Izvēlieties plauktu</option>
                    <?php
                    $shelves = $dbh->query("SELECT id, name, capacity FROM shelves")->fetchAll(PDO::FETCH_ASSOC);

                    $shelfUsage = [];
                    foreach ($shelves as $shelf) {
                        $stmt = $dbh->prepare("SELECT SUM(quantity) as total FROM products WHERE shelf_id = ?");
                        $stmt->execute([$shelf['id']]);
                        $row = $stmt->fetch(PDO::FETCH_ASSOC);
                        $shelfUsage[$shelf['id']] = (int)($row['total'] ?? 0);
                    }

                    foreach ($shelves as $shelf) {
                        $free = $shelf['capacity'] - $shelfUsage[$shelf['id']];
                        echo '<option value="' . htmlspecialchars($shelf['id']) . '" data-free="' . $free . '">' .
                            htmlspecialchars($shelf['name']) . ' (' . $shelfUsage[$shelf['id']] . '/' . $shelf['capacity'] . ')' .
                            '</option>';
                    }
                    ?>
                </select>
                <span class="error" id="shelfError"></span>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary">Pievienot produktu</button>
            </div>
            <div class="form-group">
                <h3>Plauktu statistika</h3>
                <ul id="shelf-stats">
                    <?php foreach ($shelves as $shelf): ?>
                        <li>
                            <?= htmlspecialchars($shelf['name']) ?>: 
                            <?= $shelfUsage[$shelf['id']] ?>/<?= $shelf['capacity'] ?> aizņemts
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </form>
    </div>
</div>

<script>
    function updateShelfOptions() {
        const quantity = parseInt(document.getElementById('quantity').value, 10) || 0;
        document.querySelectorAll('#shelf_id option').forEach(option => {
            if (!option.value) return;
            const free = parseInt(option.getAttribute('data-free'), 10);
            option.style.display = (quantity > 0 && free >= quantity) ? '' : 'none';
        });
    }

    updateShelfOptions();

    document.getElementById('quantity').addEventListener('input', updateShelfOptions);

    window.addEventListener('DOMContentLoaded', updateShelfOptions);
</script>
</body>
</html>
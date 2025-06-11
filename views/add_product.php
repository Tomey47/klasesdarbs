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
                    <li><a href="#"><i class="fa-solid fa-book"></i> Sagatavot atskaiti</a></li>
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

        <form method="POST" class="product-form" action="../controllers/add_product.php">
            <div class="form-group">
                <label for="title">Produkta nosaukums:</label>
                <input type="text" id="title" name="title">
            </div>

            <div class="form-group">
                <label for="category">Kategorija:</label>
                <input type="text" id="category" name="category">
            </div>

            <div class="form-group">
                <label for="price">Cena (EUR):</label>
                <input type="number" id="price" name="price" step="0.01">
            </div>

            <div class="form-group">
                <label for="quantity">Daudzums:</label>
                <input type="number" id="quantity" name="quantity">
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary">Pievienot produktu</button>
            </div>
        </form>
    </div>
</div>
<script>
    <?php
        if (isset($_SESSION['success_message'])) {
            echo 'alert("' . addslashes($_SESSION['success_message']) . '");';
            unset($_SESSION['success_message']);
        }
    ?>
</script>
</body>
</html>
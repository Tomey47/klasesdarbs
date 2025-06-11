<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    header('Location: index.php');
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
    <title>Reģistrēties</title>
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
    <div class="login-card" style="width: 400px; height: 450px;">
        <h2>Jauns Lietotājs</h2>
        <form action="../controllers/register.php" method="post">
            <label for="username">Lietotājvārds:</label>
            <input type="text" id="username" name="username">

            <label for="email">E-pasts:</label>
            <input type="email" id="email" name="email">

            <label for="password">Parole:</label>
            <input type="password" id="password" name="password">

            <div class="role-checkbox-group">
                <label class="role-checkbox-row">
                    Administrators
                    <input type="checkbox" name="is_admin" value="1" class="role-checkbox">
                </label>
                <label class="role-checkbox-row">
                    Noliktavas darbinieks
                    <input type="checkbox" name="is_employee" value="1" class="role-checkbox">
                </label>
                <label class="role-checkbox-row">
                    Plauktu Kārtotājs
                    <input type="checkbox" name="is_shelf_manager" value="1" class="role-checkbox">
                </label>
            </div>
            <button type="submit">Reģistrēties</button>
        </form>
    </div>
</div>
</body>
</html>
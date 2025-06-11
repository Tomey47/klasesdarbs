<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_employee']) || $_SESSION['is_employee'] != 1) {
    header('Location: index.php');
    exit;
}

require_once '../config/mysql.php';

// Get logged-in user info
$logged_in_user_id = $_SESSION['user_id'];
$logged_in_username = '';
$stmt_user = $dbh->prepare("SELECT username FROM users WHERE id = ?");
$stmt_user->execute([$logged_in_user_id]);
$user_info = $stmt_user->fetch(PDO::FETCH_ASSOC);
if ($user_info) {
    $logged_in_username = $user_info['username'];
}

// Fetch products for the new order form
$products_stmt = $dbh->query("SELECT id, title, quantity FROM products WHERE quantity > 0 ORDER BY title ASC");
$products = $products_stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle status update if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id']) && isset($_POST['status'])) {
    $stmt = $dbh->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->execute([$_POST['status'], $_POST['order_id']]);
    header('Location: employee_orders.php');
    exit;
}

// Handle new order creation if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_order_submit'])) {
    $user_id = $logged_in_user_id; // Use logged-in user's ID
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];

    // Basic validation
    if ($user_id && $product_id && $quantity > 0) {
        // Check product availability
        $product_check_stmt = $dbh->prepare("SELECT quantity FROM products WHERE id = ?");
        $product_check_stmt->execute([$product_id]);
        $available_quantity = $product_check_stmt->fetchColumn();

        if ($available_quantity >= $quantity) {
            $dbh->beginTransaction();
            try {
                // Insert new order
                $insert_order_stmt = $dbh->prepare("INSERT INTO orders (user_id, product_id, quantity, status) VALUES (?, ?, ?, 'Pending')");
                $insert_order_stmt->execute([$user_id, $product_id, $quantity]);

                // Update product quantity
                $update_product_stmt = $dbh->prepare("UPDATE products SET quantity = quantity - ? WHERE id = ?");
                $update_product_stmt->execute([$quantity, $product_id]);

                $dbh->commit();
                $_SESSION['success_message'] = "Pasūtījums veiksmīgi izveidots!";
            } catch (PDOException $e) {
                $dbh->rollBack();
                $_SESSION['error_message'] = "Kļūda, veidojot pasūtījumu: " . $e->getMessage();
            }
        } else {
            $_SESSION['error_message'] = "Nepietiekams produkta daudzums noliktavā.";
        }
    } else {
        $_SESSION['error_message'] = "Lūdzu, aizpildiet visus laukus pareizi.";
    }
    header('Location: employee_orders.php');
    exit;
}

// Get all orders
$stmt = $dbh->query("SELECT o.id, u.username, p.title, o.quantity, o.status 
                     FROM orders o
                     JOIN users u ON o.user_id = u.id
                     JOIN products p ON o.product_id = p.id
                     ORDER BY o.id DESC");
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <title>Veikt pasūtījumu</title>
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
                <li><a href="employee_orders.php" class="active"><i class="fa-solid fa-car"></i> Veikt pasūtījumu</a></li>
                <li><a href="../controllers/logout.php"><i class="fa-solid fa-right-from-bracket"></i> Iziet</a></li>
            </ul>
        </nav>
    </aside>
    <div class="container">
        <h1>Veikt pasūtījumu</h1>

        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="success-message">
                <?= htmlspecialchars($_SESSION['success_message']) ?>
            </div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>
        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="error-message">
                <?= htmlspecialchars($_SESSION['error_message']) ?>
            </div>
            <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>

        <h2>Jauns Pasūtījums</h2>
        <form method="POST" class="new-order-form" onsubmit="return validateNewOrderForm();">
            <div class="form-group">
                <label>Lietotājs:</label>
                <p><strong><?= htmlspecialchars($logged_in_username) ?></strong></p>
                <input type="hidden" name="user_id" value="<?= htmlspecialchars($logged_in_user_id) ?>">
            </div>
            <div class="form-group">
                <label for="product_id">Produkts:</label>
                <select id="product_id" name="product_id" required>
                    <option value="">Izvēlieties produktu</option>
                    <?php foreach ($products as $product): ?>
                        <option value="<?= htmlspecialchars($product['id']) ?>" data-quantity="<?= htmlspecialchars($product['quantity']) ?>">
                            <?= htmlspecialchars($product['title']) ?> (Pieejams: <?= htmlspecialchars($product['quantity']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <span class="error-message" id="productError"></span>
            </div>
            <div class="form-group">
                <label for="quantity">Daudzums:</label>
                <input type="number" id="quantity" name="quantity" min="1" required>
                <span class="error-message" id="quantityError"></span>
            </div>
            <button type="submit" name="new_order_submit" class="update-btn">Izveidot pasūtījumu</button>
        </form>

        <hr>

        <h2>Esošie Pasūtījumi</h2>
        <div style="margin-bottom: 20px;">
            <a href="../controllers/export_orders.php" class="update-btn" style="text-decoration: none;">
                <i class="fa-solid fa-file-excel"></i> Eksportēt pasūtījumus (Excel)
            </a>
        </div>
        <table border="1" width="100%" cellpadding="8" cellspacing="0">
            <tr>
                <th>ID</th>
                <th>Lietotājs</th>
                <th>Produkts</th>
                <th>Daudzums</th>
                <th>Statuss</th>
                <th>Darbības</th>
            </tr>
            <?php foreach ($orders as $order): ?>
            <tr>
                <td><?= htmlspecialchars($order['id']) ?></td>
                <td><?= htmlspecialchars($order['username']) ?></td>
                <td><?= htmlspecialchars($order['title']) ?></td>
                <td><?= htmlspecialchars($order['quantity']) ?></td>
                <td class="status-<?= strtolower($order['status']) ?>">
                    <?= htmlspecialchars($order['status']) ?>
                </td>
                <td>
                    <form class="order-form" method="POST">
                        <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                        <select name="status" class="status-select">
                            <option value="Pending" <?= $order['status'] === 'Pending' ? 'selected' : '' ?>>Gaida</option>
                            <option value="Processing" <?= $order['status'] === 'Processing' ? 'selected' : '' ?>>Apstrādā</option>
                            <option value="Completed" <?= $order['status'] === 'Completed' ? 'selected' : '' ?>>Pabeigts</option>
                            <option value="Cancelled" <?= $order['status'] === 'Cancelled' ? 'selected' : '' ?>>Atcelts</option>
                        </select>
                        <button type="submit" class="update-btn">Atjaunināt</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</div>
<script>
    function validateNewOrderForm() {
        let isValid = true;

        // Clear previous errors
        document.getElementById('productError').textContent = '';
        document.getElementById('quantityError').textContent = '';

        // user_id is now hidden and always the logged-in user
        // const userId = document.getElementById('user_id').value;
        const productId = document.getElementById('product_id').value;
        const quantity = document.getElementById('quantity').value;

        // Removed user_id validation as it's handled by session
        /*
        if (userId === '') {
            document.getElementById('userError').textContent = 'Lūdzu, izvēlieties lietotāju.';
            isValid = false;
        }
        */

        if (productId === '') {
            document.getElementById('productError').textContent = 'Lūdzu, izvēlieties produktu.';
            isValid = false;
        }

        if (quantity === '' || parseInt(quantity) < 1) {
            document.getElementById('quantityError').textContent = 'Lūdzu, ievadiet derīgu daudzumu.';
            isValid = false;
        } else {
            const selectedProduct = document.querySelector(`#product_id option[value="${productId}"]`);
            if (selectedProduct) {
                const availableQuantity = parseInt(selectedProduct.dataset.quantity);
                if (parseInt(quantity) > availableQuantity) {
                    document.getElementById('quantityError').textContent = `Nav pietiekams daudzums noliktavā. Pieejams: ${availableQuantity}`;
                    isValid = false;
                }
            }
        }

        return isValid;
    }
</script>
</body>
</html> 
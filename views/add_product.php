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
                <input type="text" id="title" name="title" required
                       minlength="3" maxlength="100"
                       pattern="[a-zA-Z0-9\s\-_.,āčēģīķļņšūžĀČĒĢĪĶĻŅŠŪŽ]+"
                       title="Produkta nosaukumam jābūt no 3 līdz 100 rakstzīmēm. Atļauti burti, cipari un simboli: -_.,">
                <span class="error" id="titleError"></span>
            </div>

            <div class="form-group">
                <label for="category">Kategorija:</label>
                <input type="text" id="category" name="category" required
                       minlength="2" maxlength="50"
                       pattern="[a-zA-Z0-9\s\-_.,āčēģīķļņšūžĀČĒĢĪĶĻŅŠŪŽ]+"
                       title="Kategorijai jābūt no 2 līdz 50 rakstzīmēm. Atļauti burti, cipari un simboli: -_.,">
                <span class="error" id="categoryError"></span>
            </div>

            <div class="form-group">
                <label for="price">Cena (EUR):</label>
                <input type="number" id="price" name="price" required
                       min="0.01" max="999999.99" step="0.01"
                       title="Cenai jābūt no 0.01 līdz 999999.99 EUR">
                <span class="error" id="priceError"></span>
            </div>

            <div class="form-group">
                <label for="quantity">Daudzums:</label>
                <input type="number" id="quantity" name="quantity" required
                       min="0" max="999999"
                       title="Daudzumam jābūt no 0 līdz 999999">
                <span class="error" id="quantityError"></span>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary">Pievienot produktu</button>
            </div>
        </form>
    </div>
</div>

<script>
function validateForm() {
    let isValid = true;
    const title = document.getElementById('title');
    const category = document.getElementById('category');
    const price = document.getElementById('price');
    const quantity = document.getElementById('quantity');

    // Reset previous errors
    document.querySelectorAll('.error').forEach(error => error.textContent = '');

    // Title validation
    if (!title.value.match(/^[a-zA-Z0-9\s\-_.,āčēģīķļņšūžĀČĒĢĪĶĻŅŠŪŽ]+$/)) {
        document.getElementById('titleError').textContent = 'Produkta nosaukums satur neatļautas rakstzīmes!';
        isValid = false;
    }

    // Category validation
    if (!category.value.match(/^[a-zA-Z0-9\s\-_.,āčēģīķļņšūžĀČĒĢĪĶĻŅŠŪŽ]+$/)) {
        document.getElementById('categoryError').textContent = 'Kategorija satur neatļautas rakstzīmes!';
        isValid = false;
    }

    // Price validation
    if (price.value <= 0 || price.value > 999999.99) {
        document.getElementById('priceError').textContent = 'Cenai jābūt no 0.01 līdz 999999.99 EUR!';
        isValid = false;
    }

    // Quantity validation
    if (quantity.value < 0 || quantity.value > 999999) {
        document.getElementById('quantityError').textContent = 'Daudzumam jābūt no 0 līdz 999999!';
        isValid = false;
    }

    return isValid;
}

// Real-time validation
document.querySelectorAll('input').forEach(input => {
    input.addEventListener('input', function() {
        validateForm();
    });
});

<?php
    if (isset($_SESSION['success_message'])) {
        echo 'alert("' . addslashes($_SESSION['success_message']) . '");';
        unset($_SESSION['success_message']);
    }
?>
</script>

<style>
.error {
    color: #dc3545;
    font-size: 0.875rem;
    margin-top: 0.25rem;
    display: block;
}

.form-group {
    margin-bottom: 1rem;
}

input:invalid {
    border-color: #dc3545;
}

input:valid {
    border-color: #28a745;
}
</style>
</body>
</html>
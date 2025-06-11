<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1 ) {
    header('Location: index.php');
    exit;
}

require_once '../config/mysql.php';

// Only admins can see all users

$stmt = $dbh->query("SELECT id, username, email, is_employee, is_shelf_manager, is_admin FROM users");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <title>Lietotāji</title>
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
    <div class="container">
        <?php if (isset($_SESSION['delete_message'])): ?>
            <div style="color: #ff6d4d; font-weight: bold; margin-bottom: 16px;">
                <?= htmlspecialchars($_SESSION['delete_message']) ?>
            </div>
            <?php unset($_SESSION['delete_message']); ?>
        <?php endif; ?>
        <h1>Lietotāji</h1>
        <table border="1" width="100%" cellpadding="8" cellspacing="0">
            <tr>
                <th>ID</th>
                <th>Lietotājvārds</th>
                <th>E-pasts</th>
                <th>Darbinieks</th>
                <th>Plaukta pārvaldnieks</th>
                <th>Administrators</th>
                <th>Darbības</th>
            </tr>
            <?php foreach ($users as $user): ?>
            <tr>
                <td><?= htmlspecialchars($user['id']) ?></td>
                <td><?= htmlspecialchars($user['username']) ?></td>
                <td><?= htmlspecialchars($user['email']) ?></td>
                <td><?= $user['is_employee'] ? 'Jā' : 'Nē' ?></td>
                <td><?= $user['is_shelf_manager'] ? 'Jā' : 'Nē' ?></td>
                <td><?= $user['is_admin'] ? 'Jā' : 'Nē' ?></td>
                <td>
                    <a href="javascript:void(0);" title="Rediģēt"
                        onclick="openEditModal(
                            '<?= $user['id'] ?>',
                            '<?= htmlspecialchars($user['username'], ENT_QUOTES) ?>',
                            '<?= htmlspecialchars($user['email'], ENT_QUOTES) ?>',
                            '<?= $user['is_employee'] ?>',
                            '<?= $user['is_shelf_manager'] ?>',
                            '<?= $user['is_admin'] ?>'
                        )">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </a>
                    <a href="../controllers/delete_user.php?id=<?= $user['id'] ?>" title="Dzēst" onclick="return confirm('Vai tiešām dzēst šo lietotāju?');"><i class="fa-solid fa-trash"></i></a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <br>
    </div>
</div>
<div id="editUserModal" class="modal">
    <div class="modal-content login-card" style="max-width: 400px;">
        <span class="close" onclick="closeEditModal()">&times;</span>
        <h2 style="text-align:center; color:#333;">Rediģēt lietotāju</h2>
        <form id="editUserForm" method="post" action="../controllers/edit_user.php">
            <input type="hidden" name="id" id="editUserId">
            <label for="editUsername">Lietotājvārds:</label>
            <input type="text" name="username" id="editUsername" required>
            <label for="editEmail">E-pasts:</label>
            <input type="email" name="email" id="editEmail" required>
            <div class="role-checkbox-group">
                <label class="role-checkbox-row">
                    Administrators
                    <input type="checkbox" name="is_admin" id="editIsAdmin" value="1" class="role-checkbox">
                </label>
                <label class="role-checkbox-row">
                    Noliktavas darbinieks
                    <input type="checkbox" name="is_employee" id="editIsEmployee" value="1" class="role-checkbox">
                </label>
                <label class="role-checkbox-row">
                    Plauktu Kārtotājs
                    <input type="checkbox" name="is_shelf_manager" id="editIsShelfManager" value="1" class="role-checkbox">
                </label>
            </div>
            <button type="submit">Saglabāt</button>
        </form>
    </div>
</div>
<script>
function openEditModal(id, username, email, is_employee, is_shelf_manager, is_admin) {
    document.getElementById('editUserId').value = id;
    document.getElementById('editUsername').value = username;
    document.getElementById('editEmail').value = email;
    document.getElementById('editIsEmployee').checked = (is_employee == 1 || is_employee == '1');
    document.getElementById('editIsShelfManager').checked = (is_shelf_manager == 1 || is_shelf_manager == '1');
    document.getElementById('editIsAdmin').checked = (is_admin == 1 || is_admin == '1');
    document.getElementById('editUserModal').style.display = 'flex';
}
function closeEditModal() {
    document.getElementById('editUserModal').style.display = 'none';
}
window.onclick = function(event) {
    var modal = document.getElementById('editUserModal');
    if (event.target == modal) {
        closeEditModal();
    }
}
</script>
</body>
</html>
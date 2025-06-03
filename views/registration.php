<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reģistrēties</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <div class="login-card">
        <h2>Reģistrēties</h2>
        <form action="../controllers/register.php" method="post">
            <label for="username">Lietotājvārds:</label>
            <input type="text" id="username" name="username" required>

            <label for="email">E-pasts:</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Parole:</label>
            <input type="password" id="password" name="password" required>

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
</body>
</html>
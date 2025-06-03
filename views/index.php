<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../css/styles.css">
    <title>Pieslēgties</title>
</head>
<body>
    <div class="login-card">
        <h2>Pieslēgties</h2>
        <form action="../controllers/login.php" method="post">
            <label for="username">Lietotājvārds:</label>
            <input type="text" id="username" name="username" required>
            
            <label for="password">Parole:</label>
            <input type="password" id="password" name="password" required>
            
            <button type="submit">Ieiet</button>
        </form>
    </div>
</body>
</html>
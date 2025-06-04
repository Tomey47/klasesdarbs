<?php

$db = 'mysql:host=localhost;port=3306;dbname=noliktava';
$username = 'root';
$password = '';

$dbh = new PDO($db, $username, $password);

$is_admin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;
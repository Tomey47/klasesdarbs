<?php

$db = 'mysql:host=localhost;port=3306;dbname=noliktava';
$username = 'root';
$password = '';

$dbh = new PDO($db, $username, $password);

$is_admin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;
$is_employee = isset($_SESSION['is_employee']) && $_SESSION['is_employee'] == 1;
$is_shelf_manager = isset($_SESSION['is_shelf_manager']) && $_SESSION['is_shelf_manager'] == 1;
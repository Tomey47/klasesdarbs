<?php
session_start();

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/mysql.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Check if user is logged in and is a shelf manager
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_shelf_manager']) || $_SESSION['is_shelf_manager'] != 1) {
    header('Location: ../views/index.php');
    exit;
}

// Fetch all products
$stmt = $dbh->query("SELECT id, title, category, price, quantity FROM products ORDER BY title ASC");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Set header row
$sheet->setCellValue('A1', 'ID');
$sheet->setCellValue('B1', 'Nosaukums');
$sheet->setCellValue('C1', 'Kategorija');
$sheet->setCellValue('D1', 'Cena');
$sheet->setCellValue('E1', 'Daudzums');

// Populate data rows
$row = 2;
foreach ($products as $product) {
    $sheet->setCellValue('A' . $row, $product['id']);
    $sheet->setCellValue('B' . $row, $product['title']);
    $sheet->setCellValue('C' . $row, $product['category']);
    $sheet->setCellValue('D' . $row, $product['price']);
    $sheet->setCellValue('E' . $row, $product['quantity']);
    $row++;
}

// Auto-size columns
foreach (range('A', $sheet->getHighestColumn()) as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

// Set headers for download
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Produkti.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?> 
<?php
session_start();

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/mysql.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Check if user is logged in and is an employee
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_employee']) || $_SESSION['is_employee'] != 1) {
    header('Location: ../views/index.php');
    exit;
}

// Fetch all orders
$stmt = $dbh->query("SELECT o.id, u.username, p.title AS product_title, o.quantity, o.status 
                     FROM orders o
                     JOIN users u ON o.user_id = u.id
                     JOIN products p ON o.product_id = p.id
                     ORDER BY o.id DESC");
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Set header row
$sheet->setCellValue('A1', 'ID');
$sheet->setCellValue('B1', 'Lietotājs');
$sheet->setCellValue('C1', 'Produkts');
$sheet->setCellValue('D1', 'Daudzums');
$sheet->setCellValue('E1', 'Statuss');

// Populate data rows
$row = 2;
foreach ($orders as $order) {
    $sheet->setCellValue('A' . $row, $order['id']);
    $sheet->setCellValue('B' . $row, $order['username']);
    $sheet->setCellValue('C' . $row, $order['product_title']);
    $sheet->setCellValue('D' . $row, $order['quantity']);
    $sheet->setCellValue('E' . $row, $order['status']);
    $row++;
}

// Auto-size columns
foreach (range('A', $sheet->getHighestColumn()) as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

// Set headers for download
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Pasutijumi.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?> 
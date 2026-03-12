<?php
// check_invoice.php — AJAX endpoint: checks if invoice number already exists in DB
header('Content-Type: application/json');
session_start();

$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "LC";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(['error' => 'DB connection failed']);
    exit;
}

$invoiceNumber = intval($_GET['invoice_number'] ?? 0);
if ($invoiceNumber <= 0 || $invoiceNumber > 50000) {
    echo json_encode(['exists' => false]);
    $conn->close();
    exit;
}

$stmt = $conn->prepare("SELECT COUNT(*) FROM Invoices WHERE InvoiceNumber = ?");
$stmt->bind_param("i", $invoiceNumber);
$stmt->execute();
$stmt->bind_result($count);
$stmt->fetch();
$stmt->close();
$conn->close();

echo json_encode(['exists' => $count > 0, 'invoice_number' => $invoiceNumber]);

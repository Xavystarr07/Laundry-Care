<?php
require_once __DIR__ . '/db.php';
header('Content-Type: application/json');
header('Cache-Control: no-store');
try {
    if (isset($_GET['unit'])) {
        $s = $pdo->prepare('SELECT "HotelName" AS h FROM invoices WHERE "HotelNumber" = :u GROUP BY "HotelName" ORDER BY COUNT(*) DESC, MAX("DateReceived") DESC LIMIT 5');
        $s->execute([':u' => trim($_GET['unit'])]);
        echo json_encode(['hotels' => $s->fetchAll(PDO::FETCH_COLUMN)]);
        exit;
    }
    $d = preg_replace('/\D/', '', $_GET['inv'] ?? '');
    $n = strlen($d);
    if ($n < 4 || $n > 5) { echo json_encode(['rows' => []]); exit; }
    if ($n === 5) { $t = (int)$d; $lo = $t - 10; $hi = $t + 10; }
    else { $lo = (int)$d * 10; $hi = $lo + 9; $t = $lo + 5; }
    $s = $pdo->prepare('SELECT "HotelName" AS h, "HotelNumber" AS u, "InvoiceNumber" AS n FROM invoices WHERE "InvoiceNumber" BETWEEN :lo AND :hi ORDER BY ABS("InvoiceNumber" - CAST(:t AS INTEGER)), "InvoiceNumber" DESC LIMIT 5');
    $s->execute([':lo' => $lo, ':hi' => $hi, ':t' => $t]);
    echo json_encode(['rows' => $s->fetchAll(PDO::FETCH_ASSOC)]);
} catch (Exception $e) {
    echo json_encode(['rows' => [], 'hotels' => []]);
}
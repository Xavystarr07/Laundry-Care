<?php
require_once __DIR__ . '/db.php';
header('Content-Type: application/json'); header('Cache-Control: no-store');
try {
    $r = $pdo->query('SELECT COALESCE(SUM(i."Total"),0) t, COUNT(*) c FROM invoices i WHERE EXISTS (SELECT 1 FROM lc_line_period lp JOIN lc_periods p ON p.id = lp.period_id WHERE lp.invoice_id = i.id AND NOT p.is_closed)')->fetch(PDO::FETCH_ASSOC);
    echo json_encode(['ok' => true, 'total' => (float)$r['t'], 'count' => (int)$r['c']]);
} catch (Exception $e) { echo json_encode(['ok' => false]); }
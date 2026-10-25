<?php
require_once __DIR__.'/db.php';
date_default_timezone_set('Africa/Johannesburg');
header('Content-Type: application/json');
$end = $_POST['end'] ?? '';
try {
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $end)) throw new Exception('Pick a cut-off date.');
    $pdo->beginTransaction();
    $p = $pdo->query('SELECT id,start_date FROM lc_periods WHERE is_closed=FALSE ORDER BY id LIMIT 1 FOR UPDATE')->fetch(PDO::FETCH_ASSOC);
    if (!$p) throw new Exception('No open period.');
    if ($end < $p['start_date'] || $end > date('Y-m-d')) throw new Exception('Date must be between the period start and today.');
    $e = new DateTimeImmutable($end);
    $pdo->prepare('UPDATE lc_periods SET is_closed=TRUE, closed_at=now(), end_date=? WHERE id=?')->execute([$end, $p['id']]);
    $pdo->prepare('INSERT INTO lc_periods (name,start_date) VALUES (?,?)')
        ->execute([$e->modify('first day of next month')->format('F Y'), $e->modify('+1 day')->format('Y-m-d')]);
    $pdo->commit();
    echo json_encode(['ok'=>true,'id'=>$p['id']]);
} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    echo json_encode(['ok'=>false,'msg'=>$e->getMessage()]);
}
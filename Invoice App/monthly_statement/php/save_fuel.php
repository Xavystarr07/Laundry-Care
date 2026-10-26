<?php
require_once __DIR__.'/db.php';
header('Content-Type: application/json');
$id = (int)($_POST['id'] ?? 0); $days = (int)($_POST['days'] ?? -1);
if ($id < 1 || $days < 0 || $days > 366) { echo json_encode(['ok'=>false,'msg'=>'Enter days between 0 and 366.']); exit; }
$pdo->prepare('UPDATE lc_periods SET fuel_days=? WHERE id=?')->execute([$days, $id]);
echo json_encode(['ok'=>true]);
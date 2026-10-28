<?php
// Run ONCE in the browser, then delete this file.
require_once __DIR__ . '/php/db.php';
preg_match_all('/code:\s*"([^"]+)",\s*description:\s*"([^"]+)",\s*price:\s*([\d.]+)/', file_get_contents(__DIR__ . '/js/priceList.js'), $m, PREG_SET_ORDER);
$s = $pdo->prepare('INSERT INTO lc_prices (code, description, price) VALUES (?, ?, ?) ON CONFLICT (code) DO NOTHING');
foreach ($m as $r) $s->execute([$r[1], $r[2], $r[3]]);
echo count($m) . ' items imported. Now delete seed_prices.php';

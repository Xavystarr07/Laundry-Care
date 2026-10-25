<?php
function getPeriods($pdo) {
    return $pdo->query('SELECT id,name,start_date,end_date,is_closed FROM lc_periods ORDER BY id DESC')->fetchAll(PDO::FETCH_ASSOC);
}
function getInvoices($pdo, $f, $t) {
    $s = $pdo->prepare('SELECT "DateReceived","Total" FROM invoices WHERE "DateReceived" BETWEEN :f AND :t');
    $s->execute([':f'=>$f, ':t'=>$t]);
    return $s->fetchAll(PDO::FETCH_ASSOC);
}
function getUnits($pdo, $f, $t) {
    $s = $pdo->prepare('SELECT "HotelName","HotelNumber",SUM("Total") AS tot FROM invoices
        WHERE "DateReceived" BETWEEN :f AND :t GROUP BY "HotelName","HotelNumber"
        ORDER BY "HotelName", CAST("HotelNumber" AS INTEGER)');
    $s->execute([':f'=>$f, ':t'=>$t]);
    return $s->fetchAll(PDO::FETCH_ASSOC);
}
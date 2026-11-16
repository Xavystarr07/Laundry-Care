<?php
function getPeriods($pdo) {
    return $pdo->query('SELECT id,name,start_date,end_date,is_closed,fuel_days,fuel_rate FROM lc_periods ORDER BY start_date DESC, id DESC')->fetchAll(PDO::FETCH_ASSOC);
}
function getInvoices($pdo, $pid) {
    $s = $pdo->prepare('SELECT i."DateReceived", i."Total" FROM invoices i JOIN lc_line_period lp ON lp.invoice_id = i.id WHERE lp.period_id = :p');
    $s->execute([':p'=>$pid]);
    return $s->fetchAll(PDO::FETCH_ASSOC);
}
function getUnits($pdo, $pid) {
    $s = $pdo->prepare('SELECT i."HotelName", i."HotelNumber", SUM(i."Total") AS tot FROM invoices i
        JOIN lc_line_period lp ON lp.invoice_id = i.id WHERE lp.period_id = :p GROUP BY i."HotelName", i."HotelNumber"
        ORDER BY i."HotelName", CAST(i."HotelNumber" AS INTEGER)');
    $s->execute([':p'=>$pid]);
    return $s->fetchAll(PDO::FETCH_ASSOC);
}
function getDetail($pdo, $pid) {
    $s = $pdo->prepare('SELECT i."InvoiceNumber", i."HotelName", i."HotelNumber", i."DateReceived", i."Total", i."BillingType", i."DepCode" FROM invoices i
        JOIN lc_line_period lp ON lp.invoice_id = i.id WHERE lp.period_id = :p
        ORDER BY i."HotelName", CAST(i."HotelNumber" AS INTEGER), i."DateReceived"');
    $s->execute([':p'=>$pid]);
    return $s->fetchAll(PDO::FETCH_ASSOC);
}
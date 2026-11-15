<?php
require_once __DIR__.'/../php/db.php';
header('Content-Type: application/javascript'); header('Cache-Control: no-store');
$H=$pdo->query('SELECT name FROM lc_hotels ORDER BY name')->fetchAll(PDO::FETCH_COLUMN);
$M=[];$B=[];foreach($H as $h){$M[$h]=[];$B[$h]=new stdClass;}
foreach($pdo->query('SELECT hotel,unit,beds FROM lc_units ORDER BY unit') as $r){
  $M[$r['hotel']][]=(int)$r['unit'];
  $B[$r['hotel']]->{$r['unit']}=$r['beds']===null?null:(int)$r['beds'];
}
$P=new stdClass;
foreach($pdo->query('SELECT code,beds,label,price FROM lc_dep_types ORDER BY code') as $r)
  $P->{$r['code']}=['label'=>$r['label'],'beds'=>(int)$r['beds'],'price'=>(float)$r['price']];
echo 'const HOTELS='.json_encode($H).';const hotelUnitMap='.json_encode($M).';const DEP_BEDS='.json_encode($B).';const DEP_PRICES='.json_encode($P).';';
<?php
require_once __DIR__.'/php/db.php';
$pdo->exec('CREATE TABLE IF NOT EXISTS lc_hotels(name TEXT PRIMARY KEY);
CREATE TABLE IF NOT EXISTS lc_units(hotel TEXT REFERENCES lc_hotels(name) ON DELETE CASCADE, unit INT, beds INT, PRIMARY KEY(hotel,unit));
CREATE TABLE IF NOT EXISTS lc_dep_types(code TEXT PRIMARY KEY, beds INT, label TEXT, price NUMERIC(10,2));');
$D=[
'Bronze Bay'=>[1=>2,2=>2,3=>2,6=>2,8=>2,10=>2,11=>2,12=>2,15=>2,17=>2,19=>2,21=>1,24=>1,25=>3,26=>3],
'Bronze Beach'=>[1=>2,3=>2,5=>2,6=>2,7=>2,8=>2,9=>2,10=>2,11=>2,12=>2,14=>2,16=>null,17=>2,18=>2,19=>2,25=>3,26=>3],
'Breakers'=>[128=>2,131=>2,226=>2,228=>2,231=>2,311=>2,331=>2,423=>2,515=>2,516=>2,210=>3,422=>1,512=>1],
'Sea Lodge'=>[12=>3,14=>3,45=>null,53=>3,64=>3,72=>3,84=>3,92=>null],
'Terra Mare'=>[108=>3],'Cormoran'=>[10=>3,25=>3,31=>4],'Glitter Bay'=>[15=>3],'Kyalanga'=>[17=>3,27=>3],
'Lighthouse'=>[201=>2,204=>1],'Marine'=>[35=>4],'Oceans'=>[2106=>2],'Pearls'=>[14=>1,43=>2],
'Sea Breeze'=>[4=>2],'Bensiesta'=>[201=>null,302=>null],'Shades'=>[31=>2],
'Beacon-Rock'=>[],'Bermudas'=>[],'Malindi'=>[],'Oyster Rock'=>[]];
foreach($D as $h=>$us){
  $pdo->prepare('INSERT INTO lc_hotels VALUES(?) ON CONFLICT DO NOTHING')->execute([$h]);
  foreach($us as $u=>$b)$pdo->prepare('INSERT INTO lc_units VALUES(?,?,?) ON CONFLICT DO NOTHING')->execute([$h,$u,$b]);
}
foreach([1=>['1 full bedroom + towels',129.86],2=>['2 full bedrooms + towels',225.11],3=>['3 full bedrooms + towels',292.66],4=>['4 full bedrooms + towels',323.85]] as $b=>$x)
  $pdo->prepare('INSERT INTO lc_dep_types VALUES(?,?,?,?) ON CONFLICT DO NOTHING')->execute(['DEP'.$b,$b,$x[0],$x[1]]);
echo 'Done. Delete this file.';
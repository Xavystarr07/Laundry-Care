<?php
require_once __DIR__.'/db.php';
header('Content-Type: application/json');
function bad($m){ throw new Exception($m); }
try{
  $a=$_POST['a']??'';
  $nm=fn($k)=>preg_replace('/\s+/',' ',trim($_POST[$k]??''));
  $hotelOk=function($n){ if(!preg_match("/^[A-Za-z0-9 .&'\-]{2,60}$/",$n)) bad('Hotel name must be 2-60 characters (letters, numbers, space . & \' -).'); };
  $find=function($n)use($pdo){ $s=$pdo->prepare('SELECT name FROM lc_hotels WHERE LOWER(name)=LOWER(?)'); $s->execute([$n]); return $s->fetchColumn(); };
  $beds=function($v){ if($v===''||$v===null) return null; $b=(int)$v; if($b<1||$b>10) bad('Bedrooms must be 1-10.'); return $b; };

  switch($a){
    case 'list':
      $H=[]; foreach($pdo->query('SELECT name FROM lc_hotels ORDER BY name') as $r) $H[$r['name']]=['name'=>$r['name'],'units'=>[]];
      foreach($pdo->query('SELECT hotel,unit,beds FROM lc_units ORDER BY hotel,unit') as $r)
        if(isset($H[$r['hotel']])) $H[$r['hotel']]['units'][]=['unit'=>(int)$r['unit'],'beds'=>$r['beds']===null?null:(int)$r['beds']];
      $D=[]; foreach($pdo->query('SELECT code,beds,label,price FROM lc_dep_types ORDER BY beds') as $r)
        $D[]=['code'=>$r['code'],'beds'=>(int)$r['beds'],'label'=>$r['label'],'price'=>(float)$r['price']];
      echo json_encode(['ok'=>true,'hotels'=>array_values($H),'deps'=>$D]); exit;

    case 'hotel':
      $n=$nm('name'); $hotelOk($n);
      if($find($n)) bad('That hotel already exists.');
      $pdo->prepare('INSERT INTO lc_hotels VALUES(?)')->execute([$n]); break;

    case 'rename_hotel':
      $o=$nm('old'); $n=$nm('name'); $hotelOk($n);
      if(!$find($o)) bad('Hotel not found. Refresh the page.');
      if($n===$o) bad('That is already its name.');
      $f=$find($n); if($f && $f!==$o) bad('Another hotel already has that name.');
      $pdo->beginTransaction();
      $pdo->prepare('INSERT INTO lc_hotels VALUES(?)')->execute([$n]);
      $pdo->prepare('UPDATE lc_units SET hotel=? WHERE hotel=?')->execute([$n,$o]);
      $pdo->prepare('DELETE FROM lc_hotels WHERE name=?')->execute([$o]);
      $pdo->commit(); break;

    case 'del_hotel':
      $s=$pdo->prepare('DELETE FROM lc_hotels WHERE name=?'); $s->execute([$nm('name')]);
      if(!$s->rowCount()) bad('Hotel not found. Refresh the page.'); break;

    case 'unit':
      $h=$nm('hotel'); $u=(int)($_POST['unit']??0); $b=$beds($_POST['beds']??'');
      if(!$find($h)) bad('Pick a hotel first.');
      if($u<1||$u>99999) bad('Unit number must be 1-99999.');
      $c=$pdo->prepare('SELECT 1 FROM lc_units WHERE hotel=? AND unit=?'); $c->execute([$h,$u]);
      if($c->fetchColumn()) bad("Unit $u already exists in $h. Use Edit to change it.");
      $pdo->prepare('INSERT INTO lc_units VALUES(?,?,?)')->execute([$h,$u,$b]); break;

    case 'set_beds':
      $s=$pdo->prepare('UPDATE lc_units SET beds=? WHERE hotel=? AND unit=?');
      $s->execute([$beds($_POST['beds']??''),$nm('hotel'),(int)($_POST['unit']??0)]);
      break;

    case 'del_unit':
      $s=$pdo->prepare('DELETE FROM lc_units WHERE hotel=? AND unit=?'); $s->execute([$nm('hotel'),(int)($_POST['unit']??0)]);
      if(!$s->rowCount()) bad('Unit not found. Refresh the page.'); break;

    case 'dep':
      $b=(int)($_POST['beds']??0); $l=trim($_POST['label']??''); $p=round((float)($_POST['price']??-1),2);
      if($b<1||$b>10) bad('Bedrooms must be 1-10.');
      if($l===''||strlen($l)>80) bad('Enter a label (up to 80 characters).');
      if($p<0||$p>100000) bad('Enter a valid price.');
      $pdo->prepare('INSERT INTO lc_dep_types VALUES(?,?,?,?) ON CONFLICT (code) DO UPDATE SET label=EXCLUDED.label,price=EXCLUDED.price')->execute(['DEP'.$b,$b,$l,$p]); break;

    case 'del_dep':
      $c=trim($_POST['code']??'');
      $u=$pdo->prepare('SELECT COUNT(*) FROM invoices WHERE "DepCode"=?'); $u->execute([$c]);
      if((int)$u->fetchColumn()>0) bad("$c is used on saved invoices, so it can't be removed.");
      $s=$pdo->prepare('DELETE FROM lc_dep_types WHERE code=?'); $s->execute([$c]);
      if(!$s->rowCount()) bad('Departure type not found.'); break;

    default: bad('Unknown action.');
  }
  echo json_encode(['ok'=>true]);
}catch(Exception $e){
  if($pdo->inTransaction()) $pdo->rollBack();
  error_log('hotels_api: '.$e->getMessage());
  echo json_encode(['ok'=>false,'msg'=>$e instanceof PDOException?'Database error. Nothing was changed.':$e->getMessage()]);
}
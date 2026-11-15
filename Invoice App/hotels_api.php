<?php
require_once __DIR__.'/db.php';
header('Content-Type: application/json');
try{
  switch($_POST['a']??''){
    case 'hotel':
      $n=trim($_POST['name']??'');
      if($n===''||strlen($n)>60)throw new Exception('Enter a hotel name.');
      $pdo->prepare('INSERT INTO lc_hotels VALUES(?)')->execute([$n]);break;
    case 'unit':
      $u=(int)($_POST['unit']??0); $b=($_POST['beds']??'')===''?null:(int)$_POST['beds'];
      if($u<1)throw new Exception('Enter a unit number.');
      if($b!==null&&($b<1||$b>10))throw new Exception('Bedrooms must be 1-10.');
      $pdo->prepare('INSERT INTO lc_units VALUES(?,?,?) ON CONFLICT (hotel,unit) DO UPDATE SET beds=EXCLUDED.beds')->execute([$_POST['hotel']??'',$u,$b]);break;
    case 'dep':
      $b=(int)($_POST['beds']??0); $l=trim($_POST['label']??''); $p=round((float)($_POST['price']??-1),2);
      if($b<1||$b>10||$l===''||$p<0)throw new Exception('Fill in bedrooms, label and price.');
      $pdo->prepare('INSERT INTO lc_dep_types VALUES(?,?,?,?) ON CONFLICT (code) DO UPDATE SET label=EXCLUDED.label,price=EXCLUDED.price')->execute(['DEP'.$b,$b,$l,$p]);break;
    default: throw new Exception('Unknown action.');
  }
  echo json_encode(['ok'=>true]);
}catch(Exception $e){
  $m=$e->getMessage();
  echo json_encode(['ok'=>false,'msg'=>strpos($m,'duplicate')!==false?'That already exists.':(strpos($m,'foreign')!==false?'Pick a hotel.':$m)]);
}
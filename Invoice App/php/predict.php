<?php
require_once __DIR__.'/db.php';
header('Content-Type: application/json'); header('Cache-Control: no-store');
try {
    $inv=preg_replace('/\D/','',$_GET['inv']??''); $unit=trim($_GET['unit']??''); $n=strlen($inv); $now=time();
    $names=$pdo->query('SELECT DISTINCT "HotelName" FROM invoices')->fetchAll(PDO::FETCH_COLUMN);
    $alias=[]; foreach($names as $s) foreach($names as $l) if(strlen($l)>strlen($s)&&stripos($l,$s)===0){$alias[$s]=$l;break;}
    $A=fn($h)=>$alias[$h]??$h;
    $age=fn($d)=>($now-strtotime($d))/86400;
    $I=[];$U=[];
    if($n>=4&&$n<=5){
        if($n==5){$t=(int)$inv;$lo=$t-40;$hi=$t+40;}else{$lo=(int)$inv*10;$hi=$lo+9;$t=$lo+5;}
        $s=$pdo->prepare('SELECT "HotelName" h,"HotelNumber" u,"InvoiceNumber" n,"DateReceived" d FROM invoices WHERE "InvoiceNumber" BETWEEN :lo AND :hi');
        $s->execute([':lo'=>$lo,':hi'=>$hi]);
        foreach($s->fetchAll(PDO::FETCH_ASSOC) as $r){
            if($unit!==''&&$r['u']!==$unit)continue;
            $a=$age($r['d']); $w=1/pow(1+abs($r['n']-$t),1.3)*($a<=21?1.5:($a>90?.6:1));
            $k=$A($r['h']).'|'.$r['u']; $I[$k]=($I[$k]??0)+$w;
        }
    }
    if($unit!==''){
        $s=$pdo->prepare('SELECT "HotelName" h,COUNT(*) c,MAX("DateReceived") d FROM invoices WHERE "HotelNumber"=:u GROUP BY 1');
        $s->execute([':u'=>$unit]);
        foreach($s->fetchAll(PDO::FETCH_ASSOC) as $r){$k=$A($r['h']).'|'.$unit;$U[$k]=($U[$k]??0)+$r['c']*($age($r['d'])<=45?1.5:1);}
    }
    $norm=function($a){$t=array_sum($a);return $t?array_map(fn($v)=>$v/$t,$a):[];};
    $I=$norm($I);$U=$norm($U);$S=[];
    $wI=($I&&$U)?.65:1;$wU=($I&&$U)?.35:1;
    foreach($I as $k=>$v)$S[$k]=($S[$k]??0)+$v*$wI;
    foreach($U as $k=>$v)$S[$k]=($S[$k]??0)+$v*$wU;
    if(!$S){echo json_encode(['cands'=>[],'conf'=>0,'rows'=>[],'hotels'=>[]]);exit;}
    // ── learning from past mistakes ──
    $tot=[];$mis=[];$cal=[];
    foreach($pdo->query("SELECT pred_h,pred_u,final_h,final_u,hit,conf FROM lc_pred_log WHERE created_at>now()-interval '180 days'") as $r){
        $k=$A($r['pred_h']).'|'.$r['pred_u']; $tot[$k]=($tot[$k]??0)+1;
        if(!$r['hit']){$f=$A($r['final_h']).'|'.$r['final_u'];$mis[$k][$f]=($mis[$k][$f]??0)+1;}
        $b=(int)round($r['conf']*10);$cal[$b][0]=($cal[$b][0]??0)+1;$cal[$b][1]=($cal[$b][1]??0)+($r['hit']?1:0);
    }
    foreach($S as $k=>$s){ if(empty($mis[$k]))continue;
        foreach($mis[$k] as $f=>$c){ if($unit!==''&&explode('|',$f)[1]!==$unit)continue;
            $x=$s*min(.6,$c/($tot[$k]+1)); $S[$k]-=$x; $S[$f]=($S[$f]??0)+$x; } }
    arsort($S); $sum=array_sum($S); $top=array_slice($S,0,5,true); $first=reset($top); $p1=$first/$sum;
    $b=(int)round($p1*10); $cc=$p1*.9;
    if(($cal[$b][0]??0)>=5)$cc=($cal[$b][1]+$p1*3)/($cal[$b][0]+3);   // calibrated confidence
    $rem=$sum-$first;$out=[];$i=0;
    foreach($top as $k=>$v){[$h,$u]=explode('|',$k,2);
        $p=$i?($rem>0?(1-$cc)*$v/$rem:0):$cc; $out[]=['h'=>$h,'u'=>$u,'p'=>(int)round($p*100)];$i++;}
    echo json_encode(['cands'=>$out,'conf'=>$out[0]['p']/100,
        'rows'=>array_map(fn($o)=>['h'=>$o['h'],'u'=>$o['u'],'n'=>0],$out),
        'hotels'=>array_values(array_unique(array_column($out,'h')))]);
} catch (Exception $e) { echo json_encode(['cands'=>[],'conf'=>0,'rows'=>[],'hotels'=>[]]); }
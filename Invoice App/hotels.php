<?php require_once __DIR__.'/php/db.php'; $H=$pdo->query('SELECT name FROM lc_hotels ORDER BY name')->fetchAll(PDO::FETCH_COLUMN); ?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Add hotels — Laundry Care</title>
<link rel="stylesheet" href="monthly_statement/css/statement.css"><link rel="stylesheet" href="html_css/manage.css"></head>
<body><main class="wide">
<nav class="nav2"><a class="back" href="javascript:window.close()">✕ Close</a></nav>
<h1>Hotels, units &amp; departure types</h1>
<p id="msg" class="note" hidden></p>
<section class="panel"><h2>New hotel</h2><div class="tools">
<input class="in" name="name" placeholder="Hotel name"><button type="button" class="btn" data-f="hotel">Add</button></div></section>
<section class="panel"><h2>New unit</h2><div class="tools">
<select class="in" name="hotel"><?php foreach($H as $h) echo '<option>'.htmlspecialchars($h).'</option>'; ?></select>
<input class="in" name="unit" type="number" min="1" placeholder="Unit number">
<select class="in" name="beds"><option value="">Bedrooms unknown</option><?php for($i=1;$i<=6;$i++) echo "<option>$i</option>"; ?></select>
<button type="button" class="btn" data-f="unit">Add</button></div></section>
<section class="panel"><h2>Departure type (code = DEP + bedrooms)</h2><div class="tools">
<input class="in" name="beds" type="number" min="1" max="10" placeholder="Bedrooms">
<input class="in" name="label" placeholder="e.g. 5 full bedrooms + towels">
<input class="in" name="price" type="number" step="0.01" min="0" placeholder="Price">
<button type="button" class="btn" data-f="dep">Add / update</button></div></section>
</main>
<script>
document.querySelectorAll('[data-f]').forEach(b=>b.onclick=async()=>{
  const box=b.closest('.tools'),d={a:b.dataset.f},m=document.getElementById('msg');
  box.querySelectorAll('[name]').forEach(i=>d[i.name]=i.value.trim());
  const r=await fetch('php/hotels_api.php',{method:'POST',body:new URLSearchParams(d)}).then(r=>r.json());
  if(r.ok&&d.a==='hotel')return location.reload();
  m.hidden=false;m.textContent=r.ok?'Saved ✓':r.msg;
  if(r.ok)box.querySelectorAll('input').forEach(i=>i.value='');
});
</script></body></html>
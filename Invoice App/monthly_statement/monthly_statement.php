<?php
session_start();
require_once 'php/db.php';
require_once 'php/queries.php';
date_default_timezone_set('Africa/Johannesburg');
function h($s){return htmlspecialchars((string)$s);}
function d($s){return $s?date('d M Y',strtotime($s)):'';}
try { $periods = getPeriods($pdo); } catch (Exception $e) { die('Run the lc_periods SQL first.'); }
$open = $cur = null; $pid = (int)($_GET['p'] ?? 0); $today = date('Y-m-d');
foreach ($periods as $p) { if (!$p['is_closed']) $open = $p; if ($p['id'] == $pid) $cur = $p; }
$openRows = $open ? getInvoices($pdo, $open['start_date'], $today) : [];
$end   = $cur ? ($cur['end_date'] ?: $today) : '';
$units = $cur ? getUnits($pdo, $cur['start_date'], $end) : [];
$det   = $cur ? getDetail($pdo, $cur['start_date'], $end) : [];
$grp = []; $total = 0; $fd = $cur ? (int)$cur['fuel_days'] : 0; $fr = $cur ? (float)$cur['fuel_rate'] : 0; $fuel = round($fd * $fr, 2);
foreach ($units as $u) { $grp[$u['HotelName']][$u['HotelNumber']] = (float)$u['tot']; $total += (float)$u['tot']; }
$dep  = (($_SESSION['mode'] ?? '') === 'departure');
$back = $dep ? '../departure_generator.php' : '../invoice_generator.php';
?>
<!DOCTYPE html>
<html lang="en"><head><meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Statements — Laundry Care</title>
<link rel="stylesheet" href="css/statement.css"></head><body>

<?php if (!$cur): ?>
<main class="home">
  <a class="back" href="<?= $back ?>">⟵ Back</a>
  <h1>Laundry Care <span>Statements</span></h1>
  <?php if ($open): ?><p class="now">Current period: <b><?= h($open['name']) ?></b> · from <?= d($open['start_date']) ?> · <?= count($openRows) ?> invoice(s) · <b>R <?= number_format(array_sum(array_column($openRows,'Total')),2) ?></b></p><?php endif; ?>
  <div class="cards">
    <button type="button" class="card" data-panel="existing"><b>Show existing statement</b><small>Choose a month</small></button>
    <?php if ($open): ?><button type="button" class="card" data-panel="capture"><b>Capture bill</b><small>Pick the cut-off date</small></button><?php endif; ?>
  </div>
  <section class="panel" id="existing" hidden>
    <?php foreach ($periods as $p): ?>
    <a class="prow" href="?p=<?= (int)$p['id'] ?>"><b><?= h($p['name']) ?></b>
      <span><?= d($p['start_date']) ?> — <?= $p['end_date'] ? d($p['end_date']) : 'ongoing' ?></span>
      <em class="chip <?= $p['is_closed']?'closed':'open' ?>"><?= $p['is_closed']?'Closed':'Open' ?></em></a>
    <?php endforeach; ?>
  </section>
  <?php if ($open): ?>
  <section class="panel" id="capture" hidden><div class="cap"><div>
    <p class="lbl">Period starts</p><p class="big"><?= d($open['start_date']) ?></p>
    <p class="lbl">Cut-off date</p><p class="big" id="cutLbl">Pick a date</p>
    <div id="capInfo"></div>
    <button type="button" class="btn" id="capGo" disabled style="margin-top:16px">Close period &amp; start next</button>
  </div><div id="cal"></div></div></section>
  <script>window.CAP = <?= json_encode(['start'=>$open['start_date'],'rows'=>array_map(fn($r)=>['d'=>$r['DateReceived'],'t'=>(float)$r['Total']],$openRows)], JSON_HEX_TAG) ?>;</script>
  <?php endif; ?>
</main>
<script src="js/statement.js"></script>

<?php else: ?>
<main class="view">
  <div class="bar noprint">
    <a class="btn ghost" href="monthly_statement.php">⟵ Statements</a>
    <a class="btn ghost" href="<?= $back ?>">Invoice</a>
    <?php if (!$cur['is_closed']): ?><em class="chip open">Period still open</em><?php endif; ?>
    <label style="display:flex;align-items:center;gap:8px;font-weight:700;font-size:14px">Fuel days <input id="fuelDays" type="number" min="0" max="366" value="<?= $fd ?>" style="width:70px;padding:8px;border-radius:10px;border:1.5px solid rgba(255,255,255,.2);background:rgba(255,255,255,.08);color:#fff"> × R <?= number_format($fr,2) ?></label><button type="button" class="btn" id="fuelSave" data-id="<?= (int)$cur['id'] ?>">Save fuel</button><span class="sp"></span>
    <button type="button" class="act pdf" onclick="window.print()"><i><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v12m0 0-4-4m4 4 4-4M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-2"/></svg></i>Print / Save PDF</button>
    <button type="button" class="act xls" id="xl"><i><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="16" rx="2"/><path d="M3 10h18M3 15h18M9 4v16"/></svg></i>Download Excel</button>
    <button type="button" class="act mail" id="xlMail"><i><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg></i>Send Email</button>
  </div>
  <article id="paper">
    <div class="bar2"></div>
    <header class="ph">
      <div class="brand"><h1>Laundry Care</h1><div class="tag">A FRESH START IN EVERY WASH</div><p>2 Tottenham Road<br>Parkgate<br>Cornubia</p></div>
      <div class="contact"><div class="web">www.laundrycare.co.za</div><div>donovanmark14@gmail.com</div><div>laundry_care@outlook.com</div><div>062 283 9374</div><div>069 780 0340</div></div>
    </header>
    <div class="title"><h2>Statement</h2><div class="pno"><span>Month</span><strong><?= h($cur['name']) ?></strong></div></div>
    <div class="meta">
      <div><span>To</span><strong>Umhlanga Accommodation</strong><em>Penny Underwood, Shop 14, Chartwell Centre, Umhlanga Rocks</em></div>
      <div><span>Period</span><strong><?= d($cur['start_date']) ?> — <?= d($end) ?></strong></div>
      <div><span>Units</span><strong><?= count($units) ?></strong></div>
    </div>
    <table>
      <thead><tr><th>Hotel / Unit</th><th class="n">Total</th></tr></thead><tbody>
      <?php if ($grp): foreach ($grp as $hotel => $us): $ht = 0; ?>
        <tr class="hb"><td colspan="2"><?= h($hotel) ?></td></tr>
        <?php foreach ($us as $unit => $t): $ht += $t; ?>
        <tr><td>Unit <?= h($unit) ?></td><td class="n">R <?= number_format($t,2) ?></td></tr>
        <?php endforeach; ?>
        <tr class="ht"><td><?= h($hotel) ?> total</td><td class="n">R <?= number_format($ht,2) ?></td></tr>
      <?php endforeach; else: ?><tr><td colspan="2">No invoices in this period.</td></tr><?php endif; ?>
      </tbody>
    </table>
    <label style="display:flex;align-items:center;gap:8px;font-weight:700;font-size:14px">Fuel days <input id="fuelDays" type="number" min="0" max="366" value="<?= $fd ?>" style="width:70px;padding:8px;border-radius:10px;border:1.5px solid rgba(255,255,255,.2);background:rgba(255,255,255,.08);color:#fff"> × R <?= number_format($fr,2) ?></label><button type="button" class="btn" id="fuelSave" data-id="<?= (int)$cur['id'] ?>">Save fuel</button><span class="sp"></span>
    <div class="bank"><b>Banking details</b>Bank: FNB<br>Account Holder: Laundry Care<br>Account Number: 63179920872<br>Account Type: Cheque</div>
    <div class="foot"><strong>Thank you for your business</strong></div>
  </article>
</main>
<?php endif; ?>
<?php if ($cur): ?>
<script>window.STMT = <?= json_encode(['start'=>$cur['start_date'],'end'=>$end,'total'=>$total+$fuel,'fuel'=>['days'=>$fd,'rate'=>$fr,'amt'=>$fuel],'rows'=>$det], JSON_HEX_TAG) ?>;</script>
<script src="https://cdn.jsdelivr.net/npm/exceljs@4.4.0/dist/exceljs.min.js"></script>
<script src="js/excel.js"></script>
<?php endif; ?>
</body></html>
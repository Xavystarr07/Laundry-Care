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
$grp = []; $total = 0;
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
    <span class="sp"></span>
    <button type="button" class="act pdf" onclick="window.print()"><i></i>Print / Save PDF</button>
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
    <div class="tot"><div class="tbox"><div class="tgrand"><span>Grand Total</span><strong>R <?= number_format($total,2) ?></strong></div></div></div>
    <div class="bank"><b>Banking details</b>Bank: FNB<br>Account Holder: Laundry Care<br>Account Number: 63179920872<br>Account Type: Cheque</div>
    <div class="foot"><strong>Thank you for your business</strong></div>
  </article>
</main>
<?php endif; ?>
</body></html>
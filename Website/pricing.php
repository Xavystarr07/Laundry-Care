<?php
$page   = 'pricing';
$title  = 'Pricing | Laundry Care';
$desc   = 'Laundry Care official 2026 price list for linen, towels, duvets, curtains and more.';
$js     = ['pricing.js'];
$prices = require __DIR__ . '/inc/prices.php';
$fmt    = function ($p) { return is_numeric($p) ? 'R ' . number_format($p, 2, '.', ' ') : htmlspecialchars($p, ENT_QUOTES, 'UTF-8'); };
include __DIR__ . '/inc/header.php';
?>
<main id="main">

<section class="page-hero" aria-labelledby="page-heading">
<div class="container">
<div class="section-tag">Pricing</div>
<h1 id="page-heading" class="section-title">Our 2026 Price <em>List</em></h1>
<p>Clear, per-item pricing for hotels, lodges and private clients.</p>
</div>
</section>

<section id="pricing" class="pricing-section" aria-label="Price list">
<div class="container">

<div class="price-tools">
<div class="price-search"><i class="fa-solid fa-magnifying-glass"></i><input type="search" id="price-q" placeholder="Search items, e.g. towel, duvet, sheet" aria-label="Search price list" /></div>
<a href="Laundry_Care_Price_List_2026.pdf" download class="btn btn-gold"><i class="fa-solid fa-download"></i> Download PDF</a>
</div>

<div class="price-chips" role="group" aria-label="Filter by category">
<button type="button" class="pchip active" data-cat="all">All</button>
<?php $i = 0; foreach ($prices as $cat => $rows): ?>
<button type="button" class="pchip" data-cat="c<?= $i++ ?>"><?= htmlspecialchars($cat) ?></button>
<?php endforeach; ?>
</div>

<div class="price-list">
<?php $i = 0; foreach ($prices as $cat => $rows): ?>
<div class="price-cat" data-cat="c<?= $i++ ?>">
<h3><?= htmlspecialchars($cat) ?></h3>
<table class="price-table">
<tbody>
<?php foreach ($rows as $r): ?>
<tr><td class="pc"><?= htmlspecialchars($r[0]) ?></td><td><?= htmlspecialchars($r[1]) ?></td><td class="pp"><?= $fmt($r[2]) ?></td></tr>
<?php endforeach; ?>
</tbody>
</table>
</div>
<?php endforeach; ?>
<p id="price-none" class="price-empty is-hidden">No items match your search. Call us on 062 283 9374 and we will help.</p>
</div>

<p class="price-note"><i class="fa-solid fa-circle-info"></i> Prices are per item, as per our official 2026 price list. For bulk loads, contracts or delivery fees, <a href="contact.php">get a quote</a>.</p>

</div>
</section>

<section class="cta-band" aria-labelledby="cta-heading">
<div class="container">
<h2 id="cta-heading" class="section-title">Need a custom quote?</h2>
<p>Tell us about your volumes and schedule and we will come back to you.</p>
<div class="cta-actions"><a href="contact.php" class="btn btn-gold">Get a Quote</a></div>
</div>
</section>

</main>
<?php include __DIR__ . '/inc/footer.php'; ?>

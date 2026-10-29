<?php
$page  = 'clients';
$snap  = true;
$title = 'Private Clients | Laundry Care';
$desc  = 'Everyday washing, school uniforms, suits and delicate garments for families and individuals in Durban. Pickup and drop-off included.';
$js    = ['tabs.js'];

/* key => [icon, title, lead, what we do[], great for[]] */
$tabs = [
 'general' => ['fa-shirt', 'General washes', 'Your everyday laundry, taken care of from pickup to drop-off.',
    ['Washed, dried and pressed', 'Folded and packaged to stay fresh', 'Weekly, regular or once-off', 'Pickup and drop-off anywhere in Durban'],
    ['Busy families', 'Working professionals', 'Students', 'Holiday homes']],
 'uniforms' => ['fa-graduation-cap', 'School uniforms', 'Start every school week looking sharp.',
    ['Shirts, pants, skirts and jerseys', 'Everyday stains treated at no extra charge', 'Pressed to a crisp finish', 'Returned folded and ready for the week'],
    ['Primary school', 'High school', 'Sports kit', 'Term-time routines']],
 'suits' => ['fa-vest', 'Suits & formal wear', 'Careful handling for the pieces that matter most.',
    ['Each garment assessed for care needs first', 'Suits, jackets, trousers and dresses', 'Professionally pressed and presented', 'Protective packaging for transport'],
    ['Business wear', 'Weddings & events', 'Matric dances', 'Church wear']],
 'delicates' => ['fa-hands-bubbles', 'Delicate garments', 'Gentle attention for fabrics that need it.',
    ['Inspected for damage and care needs', 'Specialist de-staining where needed', 'Washed with appropriate care', 'Re-wash at no charge if you are not happy'],
    ['Fine fabrics', 'Special occasion pieces', 'Items with stains', 'Favourites']],
 'home' => ['fa-bed', 'Bedding & household', 'Refresh your home from bed to bathroom.',
    ['Sheets, duvet covers and pillowcases', 'Duvet inners, blankets and throws', 'Towels, bath mats and gowns', 'Curtains and cushion covers'],
    ['Seasonal duvet clean', 'Guest rooms', 'Moving house', 'Holiday rentals']],
];
include __DIR__ . '/inc/header.php';
?>
<main id="main">

<section class="page-hero flow-sec big" aria-labelledby="page-heading">
<div class="container">
<div class="section-tag" data-fx="up">Private Clients</div>
<h1 id="page-heading" class="section-title" data-fx="up" style="--d:.1s">Everyday washing. <em>Handled for you.</em></h1>
<p data-fx="up" style="--d:.25s">Weekly washing, school uniforms, suits and delicate garments. We collect, clean, press and bring it back, at the same hotel-grade standard our lodges rely on.</p>
<div class="hero-btns" data-fx="up" style="--d:.4s"><a href="#wash" class="btn btn-gold">What We Wash</a><a href="https://wa.me/27622839374?text=Hi%20Laundry%20Care%2C%20I'm%20a%20private%20client%20and%20I'd%20like%20a%20quote." target="_blank" rel="noopener" class="btn btn-ghost"><i class="fa-brands fa-whatsapp"></i> WhatsApp Us</a></div>
</div>
<a href="#wash" class="scroll-cue" aria-label="Scroll down"><span>Scroll</span><div class="scroll-line"></div></a>
</section>

<section id="wash" class="flow-sec alt" data-no="01" aria-labelledby="wash-heading">
<div class="container">
<div class="sec-head"><div class="section-tag" data-fx="up">What We Wash</div><h2 id="wash-heading" class="big-title" data-fx="up" style="--d:.1s">Pick what you need, <em>we tailor the rest</em></h2><p class="lead" data-fx="up" style="--d:.2s">Choose a category to see exactly what we do for it.</p></div>
<div class="ctabs" data-fx="up" style="--d:.3s">
<div class="ctab-list" role="tablist">
<?php $first = true; foreach ($tabs as $k => $t): ?>
<button type="button" class="ctab<?= $first ? ' active' : '' ?>" data-t="<?= $k ?>" role="tab"><i class="fa-solid <?= $t[0] ?>"></i><?= htmlspecialchars($t[1]) ?></button>
<?php $first = false; endforeach; ?>
</div>
<div class="ctab-panels">
<?php $first = true; foreach ($tabs as $k => $t): ?>
<div class="cpanel<?= $first ? ' active' : '' ?>" data-p="<?= $k ?>" role="tabpanel">
<h3><?= htmlspecialchars($t[1]) ?></h3>
<p class="lead"><?= htmlspecialchars($t[2]) ?></p>
<div class="cp-cols">
<div><h4>What we do</h4><ul class="cp-list"><?php foreach ($t[3] as $li): ?><li><i class="fa-solid fa-check"></i><?= htmlspecialchars($li) ?></li><?php endforeach; ?></ul></div>
<div><h4>Great for</h4><div class="cp-chips"><?php foreach ($t[4] as $c): ?><span><?= htmlspecialchars($c) ?></span><?php endforeach; ?></div></div>
</div>
<div class="cp-actions"><a href="https://wa.me/27622839374?text=<?= rawurlencode("Hi Laundry Care, I'd like to enquire about " . strtolower($t[1]) . ".") ?>" target="_blank" rel="noopener" class="btn btn-gold"><i class="fa-brands fa-whatsapp"></i> Ask about this</a><a href="pricing.php" class="btn btn-ghost">See prices</a></div>
</div>
<?php $first = false; endforeach; ?>
</div>
</div>
</div>
</section>

<section class="flow-sec dark-sec" data-no="02" aria-labelledby="how-heading">
<div class="container">
<div class="sec-head"><div class="section-tag" data-fx="up">How It Works</div><h2 id="how-heading" class="big-title" data-fx="up" style="--d:.1s">Four simple <em>steps</em></h2></div>
<div class="step-row" data-stagger="up">
<div class="hstep"><div class="n">01</div><h3>Message us</h3><p>WhatsApp, call or email us what you need washed and where you are.</p></div>
<div class="hstep"><div class="n">02</div><h3>We collect</h3><p>Scheduled pickup from your door. Your items are logged on collection.</p></div>
<div class="hstep"><div class="n">03</div><h3>We clean and press</h3><p>Inspected, treated, washed and finished to a flawless standard.</p></div>
<div class="hstep"><div class="n">04</div><h3>Delivered back</h3><p>Folded, packaged and dropped off, ready to wear or ready to use.</p></div>
</div>
</div>
</section>

<section class="flow-sec" aria-labelledby="tailor-heading">
<div class="container">
<div class="sec-head"><div class="section-tag" data-fx="up">Tailored To You</div><h2 id="tailor-heading" class="big-title" data-fx="up" style="--d:.1s">Your schedule, <em>your needs</em></h2><p class="lead" data-fx="up" style="--d:.2s">Tell us what suits your household and we will shape the service around it.</p></div>
<div class="val-grid three" data-stagger="up">
<div class="val-card"><span class="val-no">01</span><div class="val-ico"><i class="fa-solid fa-bolt"></i></div><h3>Once-off</h3><p>A big load, a duvet clean, a special outfit or a house move. Book it when you need it.</p></div>
<div class="val-card"><span class="val-no">02</span><div class="val-ico"><i class="fa-solid fa-rotate"></i></div><h3>Regular</h3><p>Set a routine, like a weekly or regular collection, and never think about laundry day again.</p></div>
<div class="val-card"><span class="val-no">03</span><div class="val-ico"><i class="fa-solid fa-sliders"></i></div><h3>Your way</h3><p>Special care instructions or unusual items? Tell us and we will work it out with you.</p></div>
</div>
</div>
</section>

<section class="flow-sec alt" aria-labelledby="faq-heading">
<div class="container">
<div class="sec-head"><div class="section-tag" data-fx="up">Good To Know</div><h2 id="faq-heading" class="big-title" data-fx="up" style="--d:.1s">Common <em>questions</em></h2></div>
<div class="faq" data-stagger="up">
<details><summary>How do I get a quote?</summary><p>Call, WhatsApp or email us with what you need washed. We will come back to you with a quote.</p></details>
<details><summary>Do you collect and deliver?</summary><p>Yes. We offer pickup and drop-off anywhere in Durban, on a regular schedule or once-off.</p></details>
<details><summary>How am I charged?</summary><p>Pricing is per item. You can see every item on our <a href="pricing.php" style="color:#0f6f6f;font-weight:700;text-decoration:underline">2026 price list</a>.</p></details>
<details><summary>What about stains?</summary><p>Stain treatment is part of our process and carries no extra charge on our price list.</p></details>
<details><summary>What if I'm not happy with the result?</summary><p>Re-washes carry no charge on our price list. Tell us and we will put it right.</p></details>
</div>
</div>
</section>

<section class="cta-band flow-sec" aria-labelledby="cta-heading">
<div class="container">
<h2 id="cta-heading" class="big-title" data-fx="up">Let's get your laundry <em>sorted</em></h2>
<p class="lead" data-fx="up" style="--d:.15s;margin:0 auto 2.25rem">Message us on WhatsApp or give us a call.</p>
<div class="cta-actions" data-fx="up" style="--d:.3s"><a href="https://wa.me/27622839374?text=Hi%20Laundry%20Care%2C%20I'm%20a%20private%20client%20and%20I'd%20like%20a%20quote." target="_blank" rel="noopener" class="btn btn-gold"><i class="fa-brands fa-whatsapp"></i> WhatsApp</a><a href="tel:+27622839374" class="btn btn-ghost"><i class="fa-solid fa-phone"></i> 062 283 9374</a></div>
</div>
</section>

</main>
<?php include __DIR__ . '/inc/footer.php'; ?>

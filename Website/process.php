<?php
$page  = 'process';
$title = 'Our Process | Laundry Care';
$desc  = 'Our 6-stage promise: collection, inspection, treatment, finishing, packaging and delivery.';
$js    = ['process.js'];
include __DIR__ . '/inc/header.php';
?>
<main id="main">

<section class="page-hero" aria-labelledby="page-heading">
<div class="container">
<div class="section-tag">How It Works</div>
<h1 id="page-heading" class="section-title">Our 6-Stage <em>Promise</em></h1>
<p>From the moment we collect your laundry to the moment it is back on your shelves.</p>
</div>
</section>

<section id="process" class="process-section" aria-label="Our process">
<div class="container">
<div class="process-track" id="process-track">
<div class="process-line-wrap" aria-hidden="true"><div class="process-line" id="process-line"></div></div>
<div class="process-step"><div class="step-node"><div class="pa p1"><i class="fa-solid fa-truck-pickup"></i></div></div><div class="step-content"><h3>1. Collection</h3><p>Standardised, scheduled pickup from your property. Items logged on collection.</p></div></div>
<div class="process-step"><div class="step-node"><div class="pa p2"><i class="fa-solid fa-shirt bg"></i><i class="fa-solid fa-magnifying-glass"></i></div></div><div class="step-content"><h3>2. Inspection</h3><p>Every garment assessed for damage, staining and care requirements before processing.</p></div></div>
<div class="process-step"><div class="step-node"><div class="pa p3"><i class="fa-solid fa-hands-bubbles"></i><b></b><b></b><b></b></div></div><div class="step-content"><h3>3. Treatment</h3><p>Specialist de-staining and industrial washing with hotel-approved detergents and sanitisers.</p></div></div>
<div class="process-step"><div class="step-node"><div class="pa p4"><i class="fa-solid fa-iron"></i><b></b><b></b><b></b><u></u></div></div><div class="step-content"><h3>4. Finishing</h3><p>Professional tumble drying, then industrial pressing and ironing to a flawless finish.</p></div></div>
<div class="process-step"><div class="step-node"><div class="pa p5"><i class="fa-solid fa-box-open"></i><i class="fa-solid fa-circle-check ok"></i></div></div><div class="step-content"><h3>5. Packaging</h3><p>Hotel-grade folding and protective packaging to preserve quality during transport.</p></div></div>
<div class="process-step"><div class="step-node"><div class="pa p6"><i class="fa-solid fa-truck"></i><i class="fa-solid fa-hotel dest"></i><u></u></div></div><div class="step-content"><h3>6. Delivery</h3><p>On-time, scheduled delivery back to your property, ready for immediate use.</p></div></div>
</div>
</div>
</section>

<section class="cta-band" aria-labelledby="cta-heading">
<div class="container">
<h2 id="cta-heading" class="section-title">Book your first collection</h2>
<p>Get in touch and we will arrange a pickup that suits your schedule.</p>
<div class="cta-actions"><a href="contact.php" class="btn btn-gold">Get a Quote</a><a href="pricing.php" class="btn btn-ghost">View Pricing</a></div>
</div>
</section>

</main>
<?php include __DIR__ . '/inc/footer.php'; ?>

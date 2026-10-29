<?php
$page  = 'process';
$snap  = true;
$title = 'Our Process | Laundry Care';
$desc  = 'Our 6-stage promise: collection, inspection, treatment, finishing, packaging and delivery.';
$js    = ['process.js', 'promise.js'];
include __DIR__ . '/inc/header.php';
?>
<noscript><style>#promise-intro{display:none}html.pi-active{overflow:auto}html.pi-active #pi-target{visibility:visible}html.pi-active .page-hero .section-tag,html.pi-active .page-hero p{opacity:1!important}</style></noscript>

<div id="promise-intro" aria-hidden="true"><div class="pi-bg"></div><div class="pi-text" id="pi-text"></div></div>

<main id="main">

<section class="page-hero flow-sec" aria-labelledby="page-heading">
<div class="container">
<div class="section-tag">How It Works</div>
<h1 id="page-heading" class="section-title"><span id="pi-target" class="pi-target">Our 6-Stage <em>Promise</em></span></h1>
<p>From the moment we collect your laundry to the moment it is back on your shelves, every item follows the same six stages.</p>
</div>
<a href="#process" class="scroll-cue" aria-label="Scroll down"><span>Scroll</span><div class="scroll-line"></div></a>
</section>

<section id="process" class="process-section flow-sec" aria-label="Our process">
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

<section class="cta-band flow-sec" aria-labelledby="cta-heading">
<div class="container">
<h2 id="cta-heading" class="big-title" data-fx="up">Book your first <em>collection</em></h2>
<p class="lead" data-fx="up" style="--d:.15s;margin:0 auto 2.25rem">Get in touch and we will arrange a pickup that suits your schedule.</p>
<div class="cta-actions" data-fx="up" style="--d:.3s"><a href="contact.php" class="btn btn-gold">Get a Quote</a><a href="pricing.php" class="btn btn-ghost">View Pricing</a></div>
</div>
</section>

</main>
<?php include __DIR__ . '/inc/footer.php'; ?>

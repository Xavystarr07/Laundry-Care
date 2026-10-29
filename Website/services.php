<?php
$page  = 'services';
$snap  = true;
$title = 'Services | Laundry Care';
$desc  = 'Bed linen, towels, table linen, uniforms, stain removal and pickup & drop-off for hotels, lodges and guest houses in Durban.';
include __DIR__ . '/inc/header.php';
?>
<main id="main">

<section class="page-hero flow-sec big" aria-labelledby="page-heading">
<div class="container">
<div class="section-tag" data-fx="up">What We Offer</div>
<h1 id="page-heading" class="section-title" data-fx="up" style="--d:.1s">Comprehensive linen <em>solutions</em></h1>
<p data-fx="up" style="--d:.25s">Tailored packages for hotels, lodges and guest houses. Bulk loads, scheduled collection and a hotel-grade finish.</p>
<div class="hero-btns" data-fx="up" style="--d:.4s"><a href="#services" class="btn btn-gold">See Services</a><a href="clients.php" class="btn btn-ghost">I'm a Private Client</a></div>
</div>
<a href="#services" class="scroll-cue" aria-label="Scroll down"><span>Scroll</span><div class="scroll-line"></div></a>
</section>

<section id="services" class="services-section flow-sec alt" aria-labelledby="services-heading">
<div class="container">
<div class="sec-head"><div class="section-tag" data-fx="up">For Hospitality</div><h2 id="services-heading" class="big-title" data-fx="up" style="--d:.1s">Everything your property <em>needs</em></h2></div>
<div class="services-grid">
<div class="service-card"><img src="https://picsum.photos/seed/bedlinen/600/360" alt="" loading="lazy" /><div class="sc-body"><div class="service-icon"><i class="fa-solid fa-bed"></i></div><h3>Bed Linen</h3><p>Sheets, duvet covers and pillow cases, washed, treated and pressed to crisp hotel standards.</p></div></div>
<div class="service-card"><img src="https://picsum.photos/seed/towels/600/360" alt="" loading="lazy" /><div class="sc-body"><div class="service-icon"><i class="fa-solid fa-bath"></i></div><h3>Towels &amp; Bathrobes</h3><p>Deep-sanitised, fluffed and hygienically packaged for a premium guest experience.</p></div></div>
<div class="service-card"><img src="https://picsum.photos/seed/tablelinen/600/360" alt="" loading="lazy" /><div class="sc-body"><div class="service-icon"><i class="fa-solid fa-utensils"></i></div><h3>Table Linen</h3><p>Restaurant-quality tablecloths and napkins with dedicated stain treatment.</p></div></div>
<div class="service-card"><img src="https://picsum.photos/seed/uniforms/600/360" alt="" loading="lazy" /><div class="sc-body"><div class="service-icon"><i class="fa-solid fa-user-tie"></i></div><h3>Staff Uniforms</h3><p>Regular laundering and pressing of front-of-house and back-of-house uniforms.</p></div></div>
<div class="service-card"><img src="https://picsum.photos/seed/stainremoval/600/360" alt="" loading="lazy" /><div class="sc-body"><div class="service-icon"><i class="fa-solid fa-spray-can-sparkles"></i></div><h3>Stain Removal</h3><p>Enzyme and solvent treatment for stubborn stains, protecting your linen investment.</p></div></div>
<div class="service-card"><img src="https://picsum.photos/seed/vandelivery/600/360" alt="" loading="lazy" /><div class="sc-body"><div class="service-icon"><i class="fa-solid fa-truck-fast"></i></div><h3>Pickup &amp; Drop-off</h3><p>Reliable scheduled collection and delivery anywhere in Durban.</p></div></div>
</div>
</div>
</section>

<section class="cta-band flow-sec" aria-labelledby="cta-heading">
<div class="container">
<h2 id="cta-heading" class="big-title" data-fx="up">See what it <em>costs</em></h2>
<p class="lead" data-fx="up" style="--d:.15s;margin:0 auto 2.25rem">Our full 2026 price list is online, or ask us for a custom quote.</p>
<div class="cta-actions" data-fx="up" style="--d:.3s"><a href="pricing.php" class="btn btn-gold">View Pricing</a><a href="contact.php" class="btn btn-ghost">Get a Quote</a></div>
</div>
</section>

</main>
<?php include __DIR__ . '/inc/footer.php'; ?>

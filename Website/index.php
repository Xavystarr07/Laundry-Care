<?php
$page  = 'index';
$title = 'Laundry Care | A Fresh Start In Every Wash';
$desc  = 'Laundry Care - premium hospitality linen & garment maintenance in Durban. A Fresh Start In Every Wash.';
include __DIR__ . '/inc/header.php';
?>
<main id="main">

<section id="hero" class="hero-section" aria-label="Hero">
<div class="hero-bg-overlay"></div>
<div class="hero-content container">
<div class="hero-tabs reveal-up" data-delay="0"><button type="button" class="htab active">Hotels &amp; Lodges</button><button type="button" class="htab">Private Clients</button></div>
<div class="hero-slides reveal-up" data-delay="1">
<div class="hslide active">
<div>
<div class="hero-eyebrow"><span>Serving Durban</span></div>
<h1 class="hero-headline">Fresh Linen.<br /><em>Every Single Time.</em></h1>
<p class="hero-subheadline">Industrial-grade linen and garment care for Umhlanga's leading hotels. Collected, treated and delivered on your schedule.</p>
<div class="hero-actions"><a href="contact.php" class="btn btn-gold">Request a Quote</a><a href="process.php" class="btn btn-ghost">See Our Process</a></div>
<div class="hero-badges"><div class="badge"><i class="fa-solid fa-clock"></i> On-Time Delivery</div><div class="badge"><i class="fa-solid fa-hotel"></i> Hotel Grade</div></div>
</div>
<img class="hero-img" src="https://picsum.photos/seed/hotellinen/800/1000" alt="Fresh hotel linen" />
</div>
<div class="hslide">
<div>
<div class="hero-eyebrow"><span>Private Clients</span></div>
<h1 class="hero-headline">Everyday washing.<br /><em>Handled for you.</em></h1>
<p class="hero-subheadline">Weekly washing, school uniforms, suits and delicate garments. Pickup and drop-off anywhere in Durban, at the same hotel-grade standard.</p>
<div class="hero-actions"><a href="contact.php" class="btn btn-gold">Get a Quote</a><a href="pricing.php" class="btn btn-ghost">See Pricing</a></div>
<div class="hero-badges"><div class="badge"><i class="fa-solid fa-truck"></i> Pickup &amp; Drop-off</div><div class="badge"><i class="fa-solid fa-shirt"></i> Regular or Once-off</div></div>
</div>
<img class="hero-img" src="https://picsum.photos/seed/familywash/800/1000" alt="Folded clothes" />
</div>
</div>
</div>
<a href="#intro" class="scroll-cue" aria-label="Scroll down"><span>Scroll</span><div class="scroll-line"></div></a>
</section>

<section id="intro" class="page-section" aria-labelledby="intro-heading">
<div class="container text-center">
<div class="section-tag">Explore</div>
<h2 id="intro-heading" class="section-title">Everything you need to <em>know</em></h2>
<p class="section-intro">Pick a topic below to read the details.</p>
<div class="link-grid">
<a href="about.php" class="link-card"><i class="fa-solid fa-circle-info lc-ico"></i><h3>About Us</h3><p>Who we are and how we work.</p><span class="lc-more">Read more <i class="fa-solid fa-arrow-right"></i></span></a>
<a href="services.php" class="link-card"><i class="fa-solid fa-bed lc-ico"></i><h3>Services</h3><p>Linen, towels, uniforms and private clients.</p><span class="lc-more">Read more <i class="fa-solid fa-arrow-right"></i></span></a>
<a href="process.php" class="link-card"><i class="fa-solid fa-rotate lc-ico"></i><h3>Our Process</h3><p>Our 6-stage promise, from collection to delivery.</p><span class="lc-more">Read more <i class="fa-solid fa-arrow-right"></i></span></a>
<a href="pricing.php" class="link-card"><i class="fa-solid fa-tag lc-ico"></i><h3>Pricing</h3><p>Our full 2026 price list, searchable.</p><span class="lc-more">Read more <i class="fa-solid fa-arrow-right"></i></span></a>
</div>
</div>
</section>

<section class="clients-section" aria-labelledby="clients-heading">
<div class="container text-center">
<div class="section-tag">Trusted By</div>
<h2 id="clients-heading" class="section-title">20+ hotels and lodges across Durban</h2>
<p class="section-intro">Recent work includes Balito Utopia Holidays and Teremok Lodge. Large bulk loads are what we do best.</p>
<div class="client-logos">
<div class="client-chip"><i class="fa-solid fa-building"></i> Balito Utopia Holidays</div>
<div class="client-chip"><i class="fa-solid fa-building"></i> Teremok Lodge &amp; Spa</div>
<div class="client-chip"><i class="fa-solid fa-building"></i> Umhlanga Accommodation</div>
</div>
</div>
</section>

<section class="cta-band" aria-labelledby="cta-heading">
<div class="container">
<h2 id="cta-heading" class="section-title">Ready for a fresh start?</h2>
<p>Tell us what you need and we will send you a quote.</p>
<div class="cta-actions"><a href="contact.php" class="btn btn-gold">Get a Quote</a><a href="pricing.php" class="btn btn-ghost">View Pricing</a></div>
</div>
</section>

</main>
<?php include __DIR__ . '/inc/footer.php'; ?>

<?php
$page  = 'index';
$snap  = true;
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
</div>
<img class="hero-img" src="https://picsum.photos/seed/hotellinen/800/1000" alt="Fresh hotel linen" />
</div>
<div class="hslide">
<div>
<div class="hero-eyebrow"><span>Private Clients</span></div>
<h1 class="hero-headline">Everyday washing.<br /><em>Handled for you.</em></h1>
<p class="hero-subheadline">Weekly washing, school uniforms, suits and delicate garments. Pickup and drop-off anywhere in Durban, at the same hotel-grade standard.</p>
<div class="hero-actions"><a href="contact.php" class="btn btn-gold">Get a Quote</a><a href="clients.php" class="btn btn-ghost">Private Client Info</a></div>
</div>
<img class="hero-img" src="https://picsum.photos/seed/familywash/800/1000" alt="Folded clothes" />
</div>
</div>
</div>
<a href="#serve" class="scroll-cue" aria-label="Scroll down"><span>Scroll</span><div class="scroll-line"></div></a>
</section>

<section id="serve" class="flow-sec" data-no="01" aria-labelledby="serve-heading">
<div class="container">
<div class="sec-head">
<div class="section-tag" data-fx="up">Who We Serve</div>
<h2 id="serve-heading" class="big-title" data-fx="up" style="--d:.1s">Two ways we <em>care for your laundry</em></h2>
<p class="lead" data-fx="up" style="--d:.2s">Whether you run a lodge or run a household, you get the same inspected, treated, pressed and packaged result.</p>
</div>
<div class="split-panels">
<a href="services.php" class="sp-card" data-fx="left" style="--d:.2s">
<img class="sp-bg" src="https://picsum.photos/seed/hotelservice/900/1100" alt="" loading="lazy" />
<div class="sp-body"><span class="sp-tag">Hotels &amp; Lodges</span><h3>Linen for hospitality</h3><p>Bed linen, towels, table linen and uniforms in bulk, on a schedule that suits your property.</p><span class="sp-link">Explore services <i class="fa-solid fa-arrow-right"></i></span></div>
</a>
<a href="clients.php" class="sp-card" data-fx="right" style="--d:.3s">
<img class="sp-bg" src="https://picsum.photos/seed/privatewash/900/1100" alt="" loading="lazy" />
<div class="sp-body"><span class="sp-tag">Private Clients</span><h3>Everyday clothes, handled</h3><p>General washes, school uniforms, suits and delicates, collected and delivered to your door.</p><span class="sp-link">See how it works <i class="fa-solid fa-arrow-right"></i></span></div>
</a>
</div>
</div>
</section>

<section id="promise" class="flow-sec dark-sec" aria-labelledby="promise-heading">
<div class="container promise-grid">
<div class="promise-copy">
<div class="section-tag" data-fx="up">How It Works</div>
<h2 id="promise-heading" class="big-title" data-fx="up" style="--d:.1s">Our 6-stage <em>promise</em></h2>
<p class="lead" data-fx="up" style="--d:.2s">Every item follows the same path, from the moment we collect it to the moment it is back on your shelf.</p>
<a href="process.php" class="btn btn-gold" data-fx="up" style="--d:.35s">See the full process <i class="fa-solid fa-arrow-right"></i></a>
</div>
<div class="orbit" data-fx="zoom" style="--d:.2s" aria-label="Six stages: collection, inspection, treatment, finishing, packaging, delivery">
<div class="orbit-ring"></div>
<div class="orbit-core"><b>6</b><small>stages</small></div>
<div class="orbit-node" style="--a:-90deg;--i:0"><i class="fa-solid fa-truck-pickup"></i><span>Collection</span></div>
<div class="orbit-node" style="--a:-30deg;--i:1"><i class="fa-solid fa-magnifying-glass"></i><span>Inspection</span></div>
<div class="orbit-node" style="--a:30deg;--i:2"><i class="fa-solid fa-hands-bubbles"></i><span>Treatment</span></div>
<div class="orbit-node" style="--a:90deg;--i:3"><i class="fa-solid fa-iron"></i><span>Finishing</span></div>
<div class="orbit-node" style="--a:150deg;--i:4"><i class="fa-solid fa-box-open"></i><span>Packaging</span></div>
<div class="orbit-node" style="--a:210deg;--i:5"><i class="fa-solid fa-truck"></i><span>Delivery</span></div>
</div>
</div>
</section>

<section class="flow-sec alt" aria-labelledby="clients-heading">
<div class="container text-center">
<div class="section-tag" data-fx="up">Trusted By</div>
<h2 id="clients-heading" class="big-title" data-fx="up" style="--d:.1s">20+ hotels and lodges <em>across Durban</em></h2>
<p class="lead" data-fx="up" style="--d:.2s;margin-inline:auto">Recent work includes Balito Utopia Holidays and Teremok Lodge. Large bulk loads are what we do best.</p>
<div class="client-logos" data-stagger="zoom" style="margin-top:3rem">
<div class="client-chip"><i class="fa-solid fa-building"></i> Balito Utopia Holidays</div>
<div class="client-chip"><i class="fa-solid fa-building"></i> Teremok Lodge &amp; Spa</div>
<div class="client-chip"><i class="fa-solid fa-building"></i> Umhlanga Accommodation</div>
</div>
</div>
</section>

<section class="cta-band flow-sec" aria-labelledby="cta-heading">
<div class="container">
<h2 id="cta-heading" class="big-title" data-fx="up">Ready for a <em>fresh start?</em></h2>
<p class="lead" data-fx="up" style="--d:.15s;margin:0 auto 2.25rem">Call, WhatsApp or email us and we will sort out your quote.</p>
<div class="cta-actions" data-fx="up" style="--d:.3s"><a href="contact.php" class="btn btn-gold">Get a Quote</a><a href="pricing.php" class="btn btn-ghost">View Pricing</a></div>
</div>
</section>

</main>
<?php include __DIR__ . '/inc/footer.php'; ?>

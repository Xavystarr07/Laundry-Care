<?php
$page  = 'about';
$title = 'About Us | Laundry Care';
$desc  = 'Laundry Care is a specialist laundry service in Cornubia, Parkgate serving hotels, lodges and private clients across Durban.';
include __DIR__ . '/inc/header.php';
?>
<main id="main">

<section class="page-hero" aria-labelledby="page-heading">
<div class="container">
<div class="section-tag">Who We Are</div>
<h1 id="page-heading" class="section-title">About <em>Laundry Care</em></h1>
<p>A specialist laundry service built for the standards hotels expect, and available to families too.</p>
</div>
</section>

<section id="about" class="about-section" aria-labelledby="about-heading">
<div class="container about-grid">
<div class="about-visual"><img class="photo-img reveal-left" src="https://picsum.photos/seed/laundryfacility/900/700" alt="Our facility" /></div>
<div class="about-copy">
<div class="section-tag">Our Story</div>
<h2 id="about-heading" class="section-title">Where Precision Meets <em>Cleanliness</em></h2>
<p>Laundry Care is a specialist laundry service based in Cornubia, Parkgate. We serve hotels, lodges and guest houses across Durban, and families who want the same standard for their own clothes.</p>
<p>Every item is inspected on arrival, treated, washed, pressed and packaged before it comes back to you. Large bulk loads are what we do best.</p>
<ul class="about-pillars">
<li><i class="fa-solid fa-check-circle"></i> Industrial-grade equipment</li>
<li><i class="fa-solid fa-check-circle"></i> Hotels and private clients</li>
<li><i class="fa-solid fa-check-circle"></i> Pickup and drop-off</li>
<li><i class="fa-solid fa-check-circle"></i> Bulk loads welcome</li>
</ul>
</div>
</div>
</section>

<section class="page-section alt" aria-labelledby="why-heading">
<div class="container text-center">
<div class="section-tag">Why Choose Us</div>
<h2 id="why-heading" class="section-title">Built around <em>reliability</em></h2>
<div class="info-grid">
<div class="info-card"><i class="fa-solid fa-clock"></i><div><h3>On-time, every time</h3><p>Scheduled collection and delivery so your rooms are always ready.</p></div></div>
<div class="info-card"><i class="fa-solid fa-magnifying-glass"></i><div><h3>Inspected and logged</h3><p>Items are checked and logged on collection, so nothing goes missing.</p></div></div>
<div class="info-card"><i class="fa-solid fa-hotel"></i><div><h3>Hotel-grade finish</h3><p>Crisp pressing and careful folding to the standard your guests expect.</p></div></div>
<div class="info-card"><i class="fa-solid fa-people-group"></i><div><h3>Hotels and families</h3><p>The same care for a lodge's linen and your family's school uniforms.</p></div></div>
</div>
</div>
</section>

<section class="cta-band" aria-labelledby="cta-heading">
<div class="container">
<h2 id="cta-heading" class="section-title">Let's work together</h2>
<p>See what we offer, or get in touch for a quote.</p>
<div class="cta-actions"><a href="services.php" class="btn btn-gold">Our Services</a><a href="contact.php" class="btn btn-ghost">Contact Us</a></div>
</div>
</section>

</main>
<?php include __DIR__ . '/inc/footer.php'; ?>

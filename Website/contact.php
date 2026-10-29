<?php
$page  = 'contact';
$title = 'Contact & Quote | Laundry Care';
$desc  = 'Get a quote from Laundry Care. Call, WhatsApp or email us.';
include __DIR__ . '/inc/header.php';
?>
<main id="main">

<section class="page-hero flow-sec" aria-labelledby="page-heading">
<div class="container">
<div class="section-tag" data-fx="up">Let's Connect</div>
<h1 id="page-heading" class="section-title" data-fx="up" style="--d:.1s">Let's get your <em>laundry sorted</em></h1>
<p data-fx="up" style="--d:.25s">Call, WhatsApp or email us. Tell us what you need and we will send you a quote. Hotels, lodges and private clients are all welcome.</p>
</div>
</section>

<section id="contact" class="flow-sec alt" aria-labelledby="contact-heading">
<div class="container">
<div class="sec-head"><div class="section-tag" data-fx="up">Choose How</div><h2 id="contact-heading" class="big-title" data-fx="up" style="--d:.1s">Reach us the way <em>you prefer</em></h2></div>

<div class="contact-big" data-stagger="up">
<a href="tel:+27622839374" class="cc-card cc-call"><div class="cc-ico"><i class="fa-solid fa-phone"></i></div><h3>Phone call</h3><p>Speak to our team directly during office hours.</p><strong>062 283 9374</strong><span class="cc-go">Call now <i class="fa-solid fa-arrow-right"></i></span></a>
<a href="https://wa.me/27622839374?text=Hi%20Laundry%20Care%2C%20I'd%20like%20a%20quote." target="_blank" rel="noopener" class="cc-card cc-wa"><div class="cc-ico"><i class="fa-brands fa-whatsapp"></i></div><h3>WhatsApp</h3><p>Send us a message or photos of what you need washed.</p><strong>062 283 9374</strong><span class="cc-go">Open WhatsApp <i class="fa-solid fa-arrow-right"></i></span></a>
<a href="mailto:laundry_care@outlook.com?subject=Quote%20request" class="cc-card cc-mail"><div class="cc-ico"><i class="fa-solid fa-envelope"></i></div><h3>Email</h3><p>Prefer to write it all down? Send us the details.</p><strong>laundry_care@outlook.com</strong><span class="cc-go">Send an email <i class="fa-solid fa-arrow-right"></i></span></a>
</div>

<div class="info-strip" data-stagger="up">
<div class="info-card"><i class="fa-solid fa-clock"></i><div><h3>Office hours</h3><p>8am to 5pm</p></div></div>
<div class="info-card"><i class="fa-solid fa-envelope"></i><div><h3>Emails</h3><p>Answered until midnight</p></div></div>
<div class="info-card"><i class="fa-solid fa-location-dot"></i><div><h3>Based in</h3><p>Cornubia, Parkgate. Serving all of Durban.</p></div></div>
</div>
</div>
</section>

</main>
<?php include __DIR__ . '/inc/footer.php'; ?>

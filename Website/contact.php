<?php
$page  = 'contact';
$title = 'Contact & Quote | Laundry Care';
$desc  = 'Get a quote from Laundry Care. Call, WhatsApp or email us.';
$js    = ['contact.js'];
include __DIR__ . '/inc/header.php';
?>
<script>document.documentElement.classList.add('cx-on');setTimeout(function(){document.documentElement.classList.add('cx-go','cx-done')},8000)</script>
<div id="cx-intro" aria-hidden="true">
<div class="cx-in-ico"><i class="fa-solid fa-phone"></i><i class="fa-brands fa-whatsapp"></i><i class="fa-solid fa-envelope"></i></div>
<div class="cx-in-text"><span id="cx-type"></span></div>
<small>Laundry Care</small>
</div>

<main id="main">
<section id="cx" class="cx" aria-labelledby="cx-h">
<div class="cx-bg" aria-hidden="true"></div>
<div class="container cx-wrap">
<div class="cx-top">
<div><div class="section-tag">Let’s connect</div><h1 id="cx-h" class="cx-title">Choose how <em>you’d like to reach us</em></h1></div>
<div class="cx-status"><span class="cx-dot"></span><span id="cx-status-text">Office hours 8am to 5pm</span></div>
</div>

<div class="cx-cards">
<a href="tel:+27622839374" class="cx-card cx-call" style="--i:0"><i class="fa-solid fa-phone cx-wm" aria-hidden="true"></i>
<div class="cx-row"><div class="cx-ico"><i class="fa-solid fa-phone"></i></div><span class="cx-num">01</span></div>
<div class="cx-body"><h3>Phone call</h3><p>Speak to our team directly during office hours.</p><strong>062 283 9374</strong><span class="cx-go">Call now <i class="fa-solid fa-arrow-right"></i></span></div></a>

<a href="https://wa.me/27622839374?text=Hi%20Laundry%20Care%2C%20I'd%20like%20a%20quote." target="_blank" rel="noopener" class="cx-card cx-wa" style="--i:1"><i class="fa-brands fa-whatsapp cx-wm" aria-hidden="true"></i>
<div class="cx-row"><div class="cx-ico"><i class="fa-brands fa-whatsapp"></i></div><span class="cx-num">02</span></div>
<div class="cx-body"><h3>WhatsApp</h3><p>Send a message or photos of what you need washed.</p><strong>062 283 9374</strong><span class="cx-go">Open WhatsApp <i class="fa-solid fa-arrow-right"></i></span></div></a>

<a href="mailto:laundry_care@outlook.com?subject=Quote%20request" class="cx-card cx-mail" style="--i:2"><i class="fa-solid fa-envelope cx-wm" aria-hidden="true"></i>
<div class="cx-row"><div class="cx-ico"><i class="fa-solid fa-envelope"></i></div><span class="cx-num">03</span></div>
<div class="cx-body"><h3>Email</h3><p>Prefer to write it all down? Send us the details.</p><strong>laundry_care@outlook.com</strong><span class="cx-go">Send an email <i class="fa-solid fa-arrow-right"></i></span></div></a>
</div>

<p class="cx-foot"><span><i class="fa-solid fa-clock"></i> Office hours 8am to 5pm</span><span><i class="fa-solid fa-envelope"></i> Emails answered until midnight</span><span><i class="fa-solid fa-location-dot"></i> Cornubia, Parkgate · serving all of Durban</span></p>
</div>
</section>
</main>
<?php include __DIR__ . '/inc/footer.php'; ?>
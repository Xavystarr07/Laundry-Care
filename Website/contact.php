<?php
$page  = 'contact';
$title = 'Contact & Quote | Laundry Care';
$desc  = 'Get a quote from Laundry Care. Call, WhatsApp, email or send an enquiry.';
$js    = ['form.js'];
include __DIR__ . '/inc/header.php';
?>
<main id="main">

<section class="page-hero" aria-labelledby="page-heading">
<div class="container">
<div class="section-tag">Let's Connect</div>
<h1 id="page-heading" class="section-title">Let's get your <em>laundry sorted</em></h1>
<p>Tell us what you need and we will send you a quote. Hotels, lodges and private clients are all welcome.</p>
</div>
</section>

<section id="contact" class="form-section" aria-label="Contact">
<div class="container">

<div class="contact-channels-row">
<a href="tel:+27622839374" class="channel-card call-card"><div class="channel-icon"><i class="fa-solid fa-phone"></i></div><div class="channel-text"><strong>Call Us</strong><span>062 283 9374</span></div><i class="fa-solid fa-arrow-up-right-from-square channel-arrow"></i></a>
<a href="https://wa.me/27622839374?text=Hi%20Laundry%20Care%2C%20I'd%20like%20to%20enquire%20about%20your%20services." target="_blank" rel="noopener" class="channel-card whatsapp-card"><div class="channel-icon"><i class="fa-brands fa-whatsapp"></i></div><div class="channel-text"><strong>WhatsApp</strong><span>Chat directly with our team</span></div><i class="fa-solid fa-arrow-up-right-from-square channel-arrow"></i></a>
<a href="mailto:laundry_care@outlook.com" class="channel-card email-card"><div class="channel-icon"><i class="fa-solid fa-envelope"></i></div><div class="channel-text"><strong>Email Us</strong><span>laundry_care@outlook.com</span></div><i class="fa-solid fa-arrow-up-right-from-square channel-arrow"></i></a>
</div>

<address class="contact-address"><i class="fa-solid fa-location-dot"></i> Cornubia, Parkgate, Durban North, KwaZulu-Natal</address>

<div class="or-divider"><span class="or-line"></span><span class="or-text">OR</span><span class="or-line"></span></div>

<form id="enquiry-form" class="contact-form-wrap" novalidate>
<h2 class="form-title">Send an enquiry</h2>

<div class="form-group">
<label for="name">Full Name <span class="req-star">*</span></label>
<input type="text" id="name" name="name" required autocomplete="name" placeholder="Jane Smith" />
<span class="field-hint">Letters only.</span>
<span class="field-error"></span>
</div>

<div class="form-group">
<label for="email">Email <span class="req-star">*</span></label>
<input type="email" id="email" name="email" required autocomplete="email" placeholder="name@domain.com" />
<span class="field-hint">We will reply to this address.</span>
<span class="field-error"></span>
</div>

<div class="form-group">
<label for="phone">Phone <span class="req-star">*</span></label>
<input type="tel" id="phone" name="phone" required inputmode="numeric" maxlength="10" autocomplete="tel" placeholder="0622839374" />
<span class="field-hint">10 digits, no spaces.</span>
<span class="field-error"></span>
</div>

<div class="form-group">
<label for="service">I am interested in</label>
<select id="service" name="service">
<option value="">Select a service</option>
<option>Hotel / lodge linen</option>
<option>Towels &amp; bathrobes</option>
<option>Table linen</option>
<option>Staff uniforms</option>
<option>Stain removal</option>
<option>Private client laundry</option>
<option>Other</option>
</select>
</div>

<div class="form-group">
<span class="channel-label-heading">How should we reply?</span>
<div class="channel-toggles">
<label class="toggle-pill" data-channel="email"><input type="radio" name="preferred_channel" value="Email" checked /><span class="pill-inner"><i class="fa-solid fa-envelope pill-icon"></i> Email</span></label>
<label class="toggle-pill" data-channel="phone"><input type="radio" name="preferred_channel" value="Phone Call" /><span class="pill-inner"><i class="fa-solid fa-phone pill-icon"></i> Phone call</span></label>
<label class="toggle-pill" data-channel="whatsapp"><input type="radio" name="preferred_channel" value="WhatsApp" /><span class="pill-inner"><i class="fa-brands fa-whatsapp pill-icon"></i> WhatsApp</span></label>
</div>
</div>

<div class="form-group">
<label for="message">Message</label>
<textarea id="message" name="message" rows="5" maxlength="3000" placeholder="Tell us what you need washed, how often, and roughly how much."></textarea>
<div class="message-meta"><span class="field-error"></span><span class="char-count" id="message-count">0 / 3000</span></div>
</div>

<div id="form-global-error" class="form-global-message" role="alert"></div>

<button type="submit" id="submit-btn" class="btn btn-gold btn-full">
<span class="btn-text">Send Enquiry</span>
<i class="fa-solid fa-paper-plane btn-icon"></i>
<span class="btn-loader">Sending...</span>
</button>
<p class="form-privacy"><i class="fa-solid fa-lock"></i> Your details are only used to reply to your enquiry.</p>
</form>

<div class="contact-info">
<p><i class="fa-solid fa-clock"></i> Office hours: 8am to 5pm</p>
<p><i class="fa-solid fa-envelope"></i> Emails answered until midnight</p>
<p><i class="fa-solid fa-truck"></i> Pickup and drop-off available</p>
</div>

</div>
</section>

</main>
<?php include __DIR__ . '/inc/footer.php'; ?>

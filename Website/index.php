<?php
/**
 * Laundry Care — index.php  v2.0
 * Updated: real logo, correct stats, animated process icons,
 * real phone 062 283 9374, social brand colours, improved form UX,
 * channel selector with icons, animated story panel, private clients card,
 * improved footer, slogan fix, font match, OR divider in contact.
 */
session_start();

$flash_message = '';
$flash_type    = '';
if (isset($_SESSION['form_status'])) {
    $flash_message = htmlspecialchars($_SESSION['form_message'], ENT_QUOTES, 'UTF-8');
    $flash_type    = $_SESSION['form_status'] === 'success' ? 'success' : 'error';
    unset($_SESSION['form_status'], $_SESSION['form_message']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="Laundry Care — Premium hospitality linen &amp; garment maintenance. A Fresh Start In Every Wash." />
    <meta name="theme-color" content="#0B1E3D" />
    <title>Laundry Care | A Fresh Start In Every Wash</title>

    <!-- FONTS: Nunito matches logo's rounded humanist style; Raleway for display headings -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,300;0,400;0,600;0,700;1,400&family=Fraunces:opsz,wght@9..144,400;9..144,600&display=swap" rel="stylesheet" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <link rel="stylesheet" href="css/style.css" />
</head>
<body>

    <!-- PRELOADER -->
<div id="preloader" aria-hidden="true">
<div class="pl-wrap">
<div class="pl-door">
<div class="pl-ring"></div>
<div class="pl-lc"><span>L</span><span>C</span></div>
</div>
<p class="pl-name"><span>L</span><b>aundry</b> <span class="c">C</span><b>are</b></p>
<p class="pl-slogan"><span id="pl-t"></span><span id="pl-r" style="visibility:hidden"></span></p>
</div>
</div>

    <!-- FLASH BANNER -->
    <?php if ($flash_message): ?>
    <div id="flash-banner" class="flash-banner flash-<?= $flash_type ?>" role="alert">
        <i class="fa-solid <?= $flash_type === 'success' ? 'fa-circle-check' : 'fa-circle-exclamation' ?>"></i>
        <span><?= $flash_message ?></span>
        <button class="flash-close" aria-label="Dismiss" onclick="this.parentElement.remove()">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>
    <?php endif; ?>

    <!-- STICKY HEADER -->
    <header id="site-header" role="banner">
        <div class="header-inner container">

            <a href="#hero" class="brand" aria-label="Laundry Care Home">
                <img src="logo.png" alt="Laundry Care" class="brand-logo-img" />
                <span class="brand-wordmark">Laundry<em>Care</em></span>
            </a>

            <nav id="primary-nav" role="navigation" aria-label="Primary">
                <ul>
                    <li><a href="#about"    class="nav-link">About Us</a></li>
                    <li><a href="#services" class="nav-link">Services</a></li>
                    <li><a href="#process"  class="nav-link">Our Process</a></li>
                    <li><a href="#pricing" class="nav-link">Pricing</a></li><li><a href="#contact"  class="nav-link nav-cta">Get a Quote</a></li>
                </ul>
                <span class="nav-indicator" id="nav-indicator" aria-hidden="true"></span>
            </nav>

            <button id="nav-toggle" class="hamburger" aria-label="Toggle menu" aria-expanded="false">
                <span></span><span></span><span></span>
            </button>

        </div>
    </header>

    <!-- MOBILE DRAWER -->
    <aside id="mobile-drawer" aria-hidden="true" role="dialog" aria-modal="true" aria-label="Mobile menu">
        <div class="drawer-inner">
            <div class="drawer-logo">
                <img src="logo.png" alt="Laundry Care" />
            </div>
            <button id="drawer-close" aria-label="Close menu"><i class="fa-solid fa-xmark"></i></button>
            <ul class="drawer-nav">
                <li><a href="#about"    class="drawer-link"><i class="fa-solid fa-circle-info"></i> About Us</a></li>
                <li><a href="#services" class="drawer-link"><i class="fa-solid fa-list-check"></i> Services</a></li>
                <li><a href="#process"  class="drawer-link"><i class="fa-solid fa-rotate"></i> Our Process</a></li>
                <li><a href="#pricing" class="drawer-link"><i class="fa-solid fa-tag"></i> Pricing</a></li><li><a href="#contact"  class="drawer-link drawer-cta"><i class="fa-solid fa-comments"></i> Get a Quote</a></li>
            </ul>
            
            <p class="drawer-slogan">A Fresh Start In Every Wash</p>
        </div>
    </aside>
    <div id="drawer-overlay" aria-hidden="true"></div>

    <main id="main-content" role="main">

        <!-- ════════════════ HERO ════════════════ -->
        <section id="hero" class="hero-section" aria-label="Hero">
            <div class="hero-bg-overlay"></div>
            <div class="hero-grain"></div>

            <div class="hero-content container">
                <div class="hero-eyebrow reveal-up" data-delay="0">
                    <span class="eyebrow-line"></span>
                    <span>Serving Durban</span>
                    <span class="eyebrow-line"></span>
                </div>

                <h1 class="hero-headline reveal-up" data-delay="1">
                    Fresh Linen.<br />
                    <em>Every Single Time.</em>
                </h1>

                <p class="hero-subheadline reveal-up" data-delay="2">
                    Industrial-grade linen and garment care for Umhlanga's leading hotels. Collected, treated and delivered on your schedule.
                </p>

                <div class="hero-actions reveal-up" data-delay="3">
                    <a href="#contact" class="btn btn-gold">Request a Quote</a>
                    <a href="#process" class="btn btn-ghost">See Our Process</a>
                </div>

                <div class="hero-badges reveal-up" data-delay="4">
                    
                    <div class="badge"><i class="fa-solid fa-clock"></i> On-Time Delivery</div>
                    <div class="badge"><i class="fa-solid fa-hotel"></i> Hotel Grade</div>
                </div>
            </div>

            <div class="scroll-cue" aria-hidden="true">
                <span>Scroll</span>
                <div class="scroll-line"></div>
            </div>
        </section>

        <!-- ════════════════ ABOUT ════════════════ -->
        <section id="about" class="about-section" aria-labelledby="about-heading">
            <div class="container about-grid">

                <!-- Visual side: animated journey story cycling through all stages -->
                <div class="about-visual"><div class="photo-placeholder reveal-left"><i class="fa-solid fa-camera"></i><span>Photo: our facility</span></div></div>
                            <!-- Frame 2: Inspection — magnifying glass sweeps across linen -->
                            <div class="story-frame" data-stage="scan">
                                <div class="story-scene scene-scan">
                                    <div class="s-linen-sheet"><i class="fa-solid fa-sheet-plastic"></i></div>
                                    <div class="s-glass"><i class="fa-solid fa-magnifying-glass"></i></div>
                                    <div class="s-scan-beam"></div>
                                    <div class="s-scan-dots"><span></span><span></span><span></span></div>
                                </div>
                                <div class="story-label"><span class="sl-num">02</span> Inspection</div>
                            </div>

                            <!-- Frame 3: Treatment — flask pouring drops -->
                            <div class="story-frame" data-stage="treat">
                                <div class="story-scene scene-treat">
                                    <div class="s-flask"><i class="fa-solid fa-flask"></i></div>
                                    <div class="s-drops">
                                        <span class="s-drop d1"></span>
                                        <span class="s-drop d2"></span>
                                        <span class="s-drop d3"></span>
                                    </div>
                                    <div class="s-stain-shirt"><i class="fa-solid fa-shirt"></i></div>
                                    <div class="s-sparkles"><span>✦</span><span>✦</span><span>✦</span></div>
                                </div>
                                <div class="story-label"><span class="sl-num">03</span> Treatment</div>
                            </div>

                            <!-- Frame 4: Washing — drum spinning with bubbles -->
                            <div class="story-frame" data-stage="wash">
                                <div class="story-scene scene-wash">
                                    <div class="s-drum"><i class="fa-solid fa-rotate s-spin"></i></div>
                                    <div class="s-bubbles">
                                        <span class="b1"></span><span class="b2"></span>
                                        <span class="b3"></span><span class="b4"></span>
                                    </div>
                                    <div class="s-water-bar"></div>
                                </div>
                                <div class="story-label"><span class="sl-num">04</span> Washing</div>
                            </div>

                            <!-- Frame 5: Drying — wind wisps around shirt -->
                            <div class="story-frame" data-stage="dry">
                                <div class="story-scene scene-dry">
                                    <div class="s-drying-shirt"><i class="fa-solid fa-shirt"></i></div>
                                    <div class="s-wind-lines">
                                        <span></span><span></span><span></span>
                                    </div>
                                    <div class="s-heat-icon"><i class="fa-solid fa-wind"></i></div>
                                </div>
                                <div class="story-label"><span class="sl-num">04b</span> Drying</div>
                            </div>

                            <!-- Frame 6: Ironing — iron slides on board with steam -->
                            <div class="story-frame" data-stage="iron">
                                <div class="story-scene scene-iron">
                                    <div class="s-iron-board"></div>
                                    <div class="s-iron-icon"><i class="fa-solid fa-iron"></i></div>
                                    <div class="s-iron-steam">
                                        <span></span><span></span><span></span>
                                    </div>
                                </div>
                                <div class="story-label"><span class="sl-num">05</span> Finishing</div>
                            </div>

                            <!-- Frame 7: Packaging — box with label tag -->
                            <div class="story-frame" data-stage="pack">
                                <div class="story-scene scene-pack">
                                    <div class="s-box"><i class="fa-solid fa-box-open"></i></div>
                                    <div class="s-label-tag"><i class="fa-solid fa-tag"></i></div>
                                    <div class="s-fold-lines"><span></span><span></span></div>
                                </div>
                                <div class="story-label"><span class="sl-num">05b</span> Packaging</div>
                            </div>

                            <!-- Frame 8: Delivery — truck arrives at hotel -->
                            <div class="story-frame" data-stage="deliver">
                                <div class="story-scene scene-deliver">
                                    <div class="scene-road-strip"></div>
                                    <div class="scene-truck-group">
                                        <div class="s-truck deliver-truck"><i class="fa-solid fa-truck"></i></div>
                                        <div class="s-motion"><span></span><span></span><span></span></div>
                                    </div>
                                    <div class="s-hotel-dest"><i class="fa-solid fa-hotel"></i></div>
                                </div>
                                <div class="story-label"><span class="sl-num">06</span> Delivery</div>
                            </div>

                            <!-- Stage dot navigation -->
                            <div class="story-dots" aria-hidden="true" id="story-dots">
                                <span class="sdot active" data-target="0"></span>
                                <span class="sdot" data-target="1"></span>
                                <span class="sdot" data-target="2"></span>
                                <span class="sdot" data-target="3"></span>
                                <span class="sdot" data-target="4"></span>
                                <span class="sdot" data-target="5"></span>
                                <span class="sdot" data-target="6"></span>
                                <span class="sdot" data-target="7"></span>
                            </div>
                        </div><!-- /.story-panel -->

                        <div class="about-accent-card">
                            <span class="accent-number" data-count="250">0</span>
                            <span class="accent-label">Loads / Week</span>
                        </div>
                    </div>
                </div>

                <!-- Copy side -->
                <div class="about-copy">
                    <div class="section-tag reveal-up">Who We Are</div>
                    <h2 id="about-heading" class="section-title reveal-up" data-delay="1">
                        Where Precision Meets <em>Cleanliness</em>
                    </h2>
                    <p class="reveal-up" data-delay="2">
                        Laundry Care is a specialist hospitality laundry service headquartered in Cornubia, Parkgate. We were built from the ground up to serve the demanding operational needs of hotels, guest houses, and accommodation providers across the Umhlanga corridor and greater Durban North region.
                    </p>
                    <p class="reveal-up" data-delay="3">
                        Every item that passes through our facility is handled with clinical precision — from inspection on arrival to the moment it's delivered, pressed and packaged, back to your property. We don't just clean; we uphold the standard your guests expect.
                    </p>
                    <ul class="about-pillars reveal-up" data-delay="4">
                        <li><i class="fa-solid fa-check-circle"></i> Industrial-grade equipment</li>
                        <li><i class="fa-solid fa-check-circle"></i> Dedicated hospitality focus</li>
                        <li><i class="fa-solid fa-check-circle"></i> Flexible pickup &amp; delivery</li>
                        <li><i class="fa-solid fa-check-circle"></i> Transparent tracking</li>
                    </ul>
                </div>

            </div>
        </section>

        <!-- ════════════════ SERVICES ════════════════ -->
        <section id="services" class="services-section" aria-labelledby="services-heading">
            <div class="container">
                <div class="section-tag reveal-up text-center">What We Offer</div>
                <h2 id="services-heading" class="section-title text-center reveal-up" data-delay="1">
                    Comprehensive Linen <em>Solutions</em>
                </h2>
                <p class="section-intro text-center reveal-up" data-delay="2">
                    Tailored packages for hospitality operations — and private clients who demand the same standard.
                </p>

                <div class="services-grid">
                    <div class="service-card reveal-up" data-delay="0">
                        <div class="service-icon"><i class="fa-solid fa-bed"></i></div>
                        <h3>Bed Linen</h3>
                        <p>Sheets, duvet covers, pillow cases — washed, treated, and pressed to crisp hotel standards.</p>
                    </div>
                    <div class="service-card reveal-up" data-delay="1">
                        <div class="service-icon"><i class="fa-solid fa-bath"></i></div>
                        <h3>Towels &amp; Bathrobes</h3>
                        <p>Deep-sanitised, fluffed, and hygienically packaged for premium guest experience.</p>
                    </div>
                    <div class="service-card reveal-up" data-delay="2">
                        <div class="service-icon"><i class="fa-solid fa-utensils"></i></div>
                        <h3>Table Linen</h3>
                        <p>Restaurant-quality tablecloths and napkins handled with dedicated stain treatment protocols.</p>
                    </div>
                    <div class="service-card reveal-up" data-delay="3">
                        <div class="service-icon"><i class="fa-solid fa-user-tie"></i></div>
                        <h3>Staff Uniforms</h3>
                        <p>Regular laundering and pressing of all front-of-house and back-of-house uniforms.</p>
                    </div>
                    <div class="service-card reveal-up" data-delay="4">
                        <div class="service-icon"><i class="fa-solid fa-spray-can-sparkles"></i></div>
                        <h3>Specialist Stain Removal</h3>
                        <p>Advanced enzyme and solvent treatment for stubborn stains — protecting your linen investment.</p>
                    </div>
                    <div class="service-card reveal-up" data-delay="5">
                        <div class="service-icon"><i class="fa-solid fa-truck-fast"></i></div>
                        <h3>Scheduled Logistics</h3>
                        <p>Reliable collection and delivery routes across Umhlanga, La Lucia, and Durban North.</p>
                    </div>
                </div>

                <!-- Private clients — full-width feature card -->
                <div class="private-clients-card reveal-up" data-delay="1">
                    <div class="pc-badge">Private &amp; Personal</div>
                    <div class="pc-content">
                        <div class="pc-icons">
                            <i class="fa-solid fa-graduation-cap" title="School Uniforms"></i>
                            <i class="fa-solid fa-vest" title="Suits &amp; Formal Wear"></i>
                            <i class="fa-solid fa-shirt" title="Casual Garments"></i>
                            <i class="fa-solid fa-hat-cowboy" title="Accessories"></i>
                        </div>
                        <div class="pc-text">
                            <h3>Private Client Care</h3>
                            <p>Beyond hospitality — we also serve families and individuals who need hotel-grade care for school uniforms, formal suits, delicate garments, and everyday clothing. The same precision. The same standard. Just for you.</p>
                        </div>
                    </div>
                    <a href="#contact" class="btn btn-gold pc-cta">Enquire Now</a>
                </div>
            </div>
        </section>

        <!-- ════════════════ 6-STAGE PROCESS ════════════════ -->
        <section id="work" class="work-section" aria-labelledby="work-heading">
<div class="container text-center">
<div class="section-tag">Our Work</div>
<h2 id="work-heading" class="section-title">Pressed, folded and <em>ready</em></h2>
<div class="work-grid">
<div class="photo-placeholder">Photo: pressed linen</div>
<div class="photo-placeholder">Photo: our machines</div>
<div class="photo-placeholder">Photo: folded towels</div>
<div class="photo-placeholder">Photo: delivery van</div>
<div class="photo-placeholder">Photo: our team</div>
<div class="photo-placeholder">Photo: packaged orders</div>
</div>
</div>
</section>

<section id="process" class="process-section" aria-labelledby="process-heading">
            <div class="container">
                <div class="section-tag reveal-up text-center">How It Works</div>
                <h2 id="process-heading" class="section-title text-center reveal-up" data-delay="1">
                    Our 6-Stage <em>Promise</em>
                </h2>

                <div class="process-track" id="process-track">
                    <!-- Connector line — sits in a dedicated row, doesn't overlap cards -->
                    <div class="process-line-wrap" aria-hidden="true">
                        <div class="process-line" id="process-line"></div>
                    </div>

                   <div class="process-step"><div class="step-node"><div class="pa p1"><i class="fa-solid fa-truck-pickup"></i></div><span class="step-number">01</span></div><div class="step-content"><h3>Collection</h3><p>Standardised, scheduled pickup from your property. Items logged on collection.</p></div></div>
<div class="process-step"><div class="step-node"><div class="pa p2"><i class="fa-solid fa-shirt bg"></i><i class="fa-solid fa-magnifying-glass"></i></div><span class="step-number">02</span></div><div class="step-content"><h3>Inspection</h3><p>Every garment assessed for damage, staining and care requirements before processing.</p></div></div>
<div class="process-step"><div class="step-node"><div class="pa p3"><i class="fa-solid fa-hands-bubbles"></i><b></b><b></b><b></b></div><span class="step-number">03</span></div><div class="step-content"><h3>Treatment</h3><p>Specialist de-staining and industrial washing with hotel-approved detergents and sanitisers.</p></div></div>
<div class="process-step"><div class="step-node"><div class="pa p4"><i class="fa-solid fa-iron"></i><b></b><b></b><b></b><u></u></div><span class="step-number">04</span></div><div class="step-content"><h3>Finishing</h3><p>Professional tumble drying, then industrial pressing and ironing to a flawless finish.</p></div></div>
<div class="process-step"><div class="step-node"><div class="pa p5"><i class="fa-solid fa-box-open"></i><i class="fa-solid fa-circle-check ok"></i></div><span class="step-number">05</span></div><div class="step-content"><h3>Packaging</h3><p>Hotel-grade folding and protective packaging to preserve quality during transport.</p></div></div>
<div class="process-step"><div class="step-node"><div class="pa p6"><i class="fa-solid fa-truck"></i><i class="fa-solid fa-hotel dest"></i><u></u></div><span class="step-number">06</span></div><div class="step-content"><h3>Delivery</h3><p>On-time, scheduled delivery back to your property, ready for immediate use.</p></div></div>

        <!-- ════════════════ STATS ════════════════ -->
        </div></div></section><section class="clients-section" aria-labelledby="clients-heading">
<div class="container text-center">
<div class="section-tag">Trusted By</div>
<h2 id="clients-heading" class="section-title">20+ hotels and lodges across Durban</h2>
<p class="section-intro">Recent work includes Balito Utopia Holidays and Teremok Lodge. Large bulk loads are what we do best.</p>
<div class="client-logos">
<div class="photo-placeholder">Logo: Balito Utopia Holidays</div>
<div class="photo-placeholder">Logo: Teremok Lodge</div>
</div>
</div>
</section>

<section id="pricing" class="pricing-section" aria-labelledby="pricing-heading">
<div class="container text-center">
<div class="section-tag">Pricing</div>
<h2 id="pricing-heading" class="section-title">Our Price <em>List</em></h2>
<p class="section-intro">Clear pricing for hotels, lodges and private clients.</p>
<div class="photo-placeholder">Price list coming soon</div>
</div>
</section>

        <!-- ════════════════ CONTACT ════════════════ -->
        <section id="contact" class="contact-section" aria-labelledby="contact-heading">
            <div class="contact-bg-accent" aria-hidden="true"></div>
            <div class="container">

                <!-- TOP: Direct contact channels (prominent) -->
                <div class="contact-top">
                    <div class="section-tag reveal-up text-center">Let's Connect</div>
                    <h2 id="contact-heading" class="section-title text-center reveal-up" data-delay="1">
                        Ready to Elevate <em>Your Linen Standard?</em>
                    </h2>
                    <p class="section-intro text-center reveal-up" data-delay="2">
                        Reach out directly. Our team typically responds within the hour during business hours.
                    </p>

                    <div class="contact-channels-row">
                        <a href="tel:+27622839374" class="channel-card call-card reveal-up" data-delay="0">
                            <div class="channel-icon"><i class="fa-solid fa-phone"></i></div>
                            <div class="channel-text">
                                <strong>Call Us</strong>
                                <span>062 283 9374</span>
                            </div>
                            <i class="fa-solid fa-arrow-up-right-from-square channel-arrow"></i>
                        </a>

                        <a href="mailto:laundry_care@outlook.com" class="channel-card email-card reveal-up" data-delay="1">
                            <div class="channel-icon"><i class="fa-solid fa-envelope"></i></div>
                            <div class="channel-text">
                                <strong>Email Enquiry</strong>
                                <span>laundry_care@outlook.com</span>
                            </div>
                            <i class="fa-solid fa-arrow-up-right-from-square channel-arrow"></i>
                        </a>

                        <a href="https://wa.me/27622839374?text=Hi%20Laundry%20Care%2C%20I'd%20like%20to%20enquire%20about%20your%20services." target="_blank" rel="noopener" class="channel-card whatsapp-card reveal-up" data-delay="2">
                            <div class="channel-icon"><i class="fa-brands fa-whatsapp"></i></div>
                            <div class="channel-text">
                                <strong>WhatsApp Business</strong>
                                <span>Chat directly with our team</span>
                            </div>
                            <i class="fa-solid fa-arrow-up-right-from-square channel-arrow"></i>
                        </a>
                    </div>

                    <address class="contact-address text-center">
                        <i class="fa-solid fa-location-dot"></i>
                        Cornubia, Parkgate, Durban North, KwaZulu-Natal
                    </address>
                </div>

                <!-- OR DIVIDER -->
               </div><div class="contact-info reveal-up">
<p><i class="fa-solid fa-clock"></i> Office hours: 8am to 5pm</p>
<p><i class="fa-solid fa-envelope"></i> Emails answered until midnight</p>
<p><i class="fa-solid fa-truck"></i> Pickup and drop-off available</p>
</div>

        </section>

    </main>

    <!-- ════════════════ FOOTER ════════════════ -->
    <footer id="site-footer" role="contentinfo">
        <div class="container footer-inner">

            <div class="footer-brand">
                <a href="#hero" aria-label="Back to top">
                    <img src="logo.png" alt="Laundry Care" class="footer-logo" />
                </a>
                <p class="footer-slogan">"A Fresh Start In Every Wash"</p>
                <p class="footer-desc">Premium hospitality linen &amp; garment maintenance. Cornubia, Parkgate, KwaZulu-Natal.</p>
                
            </div>

            <div class="footer-col">
                <h4 class="footer-col-title">Services</h4>
                <ul class="footer-links">
                    <li><a href="#services">Bed Linen</a></li>
                    <li><a href="#services">Towels &amp; Bathrobes</a></li>
                    <li><a href="#services">Table Linen</a></li>
                    <li><a href="#services">Staff Uniforms</a></li>
                    <li><a href="#services">Stain Removal</a></li>
                    <li><a href="#services">Private Client Care</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h4 class="footer-col-title">Navigate</h4>
                <ul class="footer-links">
                    <li><a href="#about">About Us</a></li>
                    <li><a href="#services">Services</a></li>
                    <li><a href="#process">Our Process</a></li>
                    <li><a href="#contact">Get a Quote</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h4 class="footer-col-title">Contact Us</h4>
                <ul class="footer-contact-list">
                    <li><i class="fa-solid fa-phone"></i><a href="tel:+27622839374">062 283 9374</a></li>
                    <li><i class="fa-solid fa-envelope"></i><a href="mailto:laundry_care@outlook.com">laundry_care@outlook.com</a></li>
                    <li><i class="fa-brands fa-whatsapp"></i><a href="https://wa.me/27622839374" target="_blank" rel="noopener">WhatsApp Chat</a></li>
                    <li><i class="fa-solid fa-location-dot"></i><span>Cornubia, Parkgate, DBN</span></li>
                </ul>
            </div>

        </div>

        <div class="footer-bottom">
            <div class="container footer-bottom-inner">
                <p>&copy; <?= date('Y') ?> Laundry Care. All rights reserved.</p>
                <p class="footer-tagline">A Fresh Start In Every Wash · Cornubia, KwaZulu-Natal</p>
            </div>
        </div>
    </footer>

    <button id="back-to-top" aria-label="Back to top" title="Back to top">
        <i class="fa-solid fa-chevron-up"></i>
    </button>

    <!-- SCRIPTS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.4/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.4/ScrollTrigger.min.js"></script>
    <script src="js/preloader.js"  defer></script>
    <script src="js/navigation.js" defer></script>
    <script src="js/animations.js" defer></script>
    <script src="js/process.js"    defer></script>
    <script src="js/counter.js"    defer></script>
    <script src="js/form.js"       defer></script>
    <script src="js/scroll.js"     defer></script>
    <script src="js/story.js"      defer></script>
</body>
</html>

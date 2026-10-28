    <footer id="site-footer" role="contentinfo">
        <div class="container footer-inner">
            <div class="footer-brand">
                <a href="index.php" aria-label="Home"><img src="logo.png" alt="Laundry Care" class="footer-logo" /></a>
                <p class="footer-slogan">"A Fresh Start In Every Wash"</p>
                <p class="footer-desc">Premium hospitality linen &amp; garment maintenance. Cornubia, Parkgate, KwaZulu-Natal.</p>
            </div>
            <div class="footer-col">
                <h4 class="footer-col-title">Services</h4>
                <ul class="footer-links">
                    <li><a href="services.php">Bed Linen</a></li>
                    <li><a href="services.php">Towels &amp; Bathrobes</a></li>
                    <li><a href="services.php">Table Linen</a></li>
                    <li><a href="services.php">Staff Uniforms</a></li>
                    <li><a href="services.php">Stain Removal</a></li>
                    <li><a href="services.php">Private Client Care</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4 class="footer-col-title">Navigate</h4>
                <ul class="footer-links">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="about.php">About Us</a></li>
                    <li><a href="process.php">Our Process</a></li>
                    <li><a href="pricing.php">Pricing</a></li>
                    <li><a href="contact.php">Get a Quote</a></li>
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

    <button id="back-to-top" aria-label="Back to top" title="Back to top"><i class="fa-solid fa-chevron-up"></i></button>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.4/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.4/ScrollTrigger.min.js"></script>
    <script src="js/preloader.js"  defer></script>
    <script src="js/navigation.js" defer></script>
    <script src="js/animations.js" defer></script>
    <script src="js/scroll.js"     defer></script>
    <script src="js/theme.js"      defer></script>
    <?php foreach (($js ?? []) as $f): ?>
    <script src="js/<?= $f ?>" defer></script>
    <?php endforeach; ?>
</body>
</html>

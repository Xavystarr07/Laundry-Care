/**
 * js/animations.js
 * ─────────────────────────────────────────────────────────────────────────────
 * All GSAP + ScrollTrigger driven animations:
 *
 *   1. Hero staggered entrance (triggered after preloader dismisses)
 *   2. Scroll-reveal for .reveal-up / .reveal-left / .reveal-right elements
 *   3. Service card hover tilt parallax
 *   4. Hero background floating orb movement on mousemove
 *
 * Requires GSAP 3 and ScrollTrigger plugin (loaded in index.php)
 * ─────────────────────────────────────────────────────────────────────────────
 */

(function () {
    'use strict';

    /* Guard: GSAP must be available */
    if (typeof gsap === 'undefined') {
        console.warn('[animations.js] GSAP not found — animations disabled.');
        return;
    }

    // Register ScrollTrigger plugin
    gsap.registerPlugin(ScrollTrigger);

    /* ─────────────────────────────────────────────────────────────
       1. HERO ENTRANCE
       Triggered by the 'preloaderDone' custom event dispatched
       in preloader.js once the loader has faded out.
       ───────────────────────────────────────────────────────────── */

    function runHeroEntrance() {
        const tl = gsap.timeline({ defaults: { ease: 'expo.out' } });

        /* Stagger the hero content elements (tagged with data-delay) */
        const revealElements = document.querySelectorAll('.hero-content .reveal-up');

        tl
            /* Site header slides down */
            .fromTo(
                '#site-header',
                { y: -80, opacity: 0 },
                { y: 0, opacity: 1, duration: 1, ease: 'expo.out' }
            )
            /* Hero elements stagger in */
            .fromTo(
                revealElements,
                { y: 50, opacity: 0 },
                {
                    y: 0,
                    opacity: 1,
                    duration: 1.1,
                    stagger: 0.15,
                    ease: 'expo.out',
                    onComplete: function () {
                        // Mark elements as revealed so CSS transition state is correct
                        revealElements.forEach(el => el.classList.add('revealed'));
                    }
                },
                '-=0.5' // Overlap with header animation
            );
    }

    /* Listen for the preloader completion signal */
    document.addEventListener('preloaderDone', runHeroEntrance, { once: true });

    /* Fallback: if preloader was already dismissed (e.g. very fast load) */
    if (!document.getElementById('preloader')) {
        runHeroEntrance();
    }

    /* ─────────────────────────────────────────────────────────────
       2. SCROLL-REVEAL — .reveal-up / .reveal-left / .reveal-right
       IntersectionObserver picks up any element with these classes
       and adds .revealed, which CSS transitions handle the rest.
       ───────────────────────────────────────────────────────────── */

    const revealSelector = '.reveal-up:not(.hero-content .reveal-up), .reveal-left, .reveal-right';
    const revealElements = document.querySelectorAll(revealSelector);

    /* Map data-delay attribute to actual CSS transition delay */
    function applyDelay(el) {
        const delay = parseFloat(el.getAttribute('data-delay') || 0);
        el.style.transitionDelay = (delay * 0.15) + 's';
    }

    revealElements.forEach(applyDelay);

    const scrollRevealObserver = new IntersectionObserver(
        function (entries, observer) {
            entries.forEach(entry => {
                if (!entry.isIntersecting) return;

                entry.target.classList.add('revealed');
                observer.unobserve(entry.target); // Animate once only
            });
        },
        {
            threshold: 0.12,
            rootMargin: '0px 0px -60px 0px'
        }
    );

    revealElements.forEach(el => scrollRevealObserver.observe(el));

    /* ─────────────────────────────────────────────────────────────
       3. SERVICE CARD — 3D TILT on mousemove
       Applies a subtle perspective rotation when the user hovers
       over any .service-card element.
       ───────────────────────────────────────────────────────────── */

    const serviceCards = [];

    serviceCards.forEach(card => {
        card.addEventListener('mousemove', function (e) {
            const rect   = card.getBoundingClientRect();
            const centerX = rect.left + rect.width  / 2;
            const centerY = rect.top  + rect.height / 2;

            /* Normalise mouse position to -1 → +1 */
            const deltaX = (e.clientX - centerX) / (rect.width  / 2);
            const deltaY = (e.clientY - centerY) / (rect.height / 2);

            const MAX_ROTATE = 6; // degrees

            gsap.to(card, {
                rotationY:   deltaX * MAX_ROTATE,
                rotationX:  -deltaY * MAX_ROTATE,
                transformPerspective: 800,
                transformOrigin: 'center center',
                duration: 0.4,
                ease: 'power2.out'
            });
        });

        /* Reset on mouse leave */
        card.addEventListener('mouseleave', function () {
            gsap.to(card, {
                rotationX: 0,
                rotationY: 0,
                duration: 0.6,
                ease: 'elastic.out(1, 0.5)'
            });
        });
    });

    /* ─────────────────────────────────────────────────────────────
       4. HERO PARALLAX — background shift on mousemove
       Gives the hero a subtle depth effect by moving its overlay
       opposite to the cursor direction.
       ───────────────────────────────────────────────────────────── */

    const heroSection  = document.querySelector('.hero-section');
    const heroOverlay  = null;

    if (heroSection && heroOverlay) {
        heroSection.addEventListener('mousemove', function (e) {
            const w = window.innerWidth;
            const h = window.innerHeight;

            const xPct = (e.clientX / w - 0.5) * 2; // -1 → +1
            const yPct = (e.clientY / h - 0.5) * 2;

            gsap.to(heroOverlay, {
                x: xPct * -20,
                y: yPct * -15,
                duration: 1.2,
                ease: 'power2.out'
            });
        });
    }

    /* ─────────────────────────────────────────────────────────────
       5. GSAP SCROLLTRIGGER — section heading underline reveals
       Each .section-title gets a gold underline that grows from
       left to right as it enters the viewport.
       ───────────────────────────────────────────────────────────── */

    document.querySelectorAll('.section-title').forEach(title => {
        // Wrap in a span for the underline effect
        title.style.position = 'relative';
        title.style.display  = 'inline-block';

        const underline = document.createElement('span');
        underline.setAttribute('aria-hidden', 'true');
        Object.assign(underline.style, {
            position:   'absolute',
            bottom:     '-6px',
            left:       '0',
            height:     '2px',
            width:      '0%',
            background: 'linear-gradient(90deg, #C9A84C, #E2C97E)',
            borderRadius: '1px',
        });
        title.appendChild(underline);

        ScrollTrigger.create({
            trigger: title,
            start:   'top 85%',
            once:    true,
            onEnter: () => {
                gsap.to(underline, {
                    width:    '60%',
                    duration: 1,
                    ease:     'expo.out',
                    delay:    0.3
                });
            }
        });
    });

})();

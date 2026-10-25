/**
 * js/scroll.js
 * ─────────────────────────────────────────────────────────────────────────────
 * Handles scroll-driven UI updates:
 *
 *   1. Sticky header — adds .scrolled class when page is not at top
 *   2. Back-to-top button visibility toggle
 *   3. Back-to-top smooth scroll on click
 *   4. Passive event listener for maximum scroll performance
 * ─────────────────────────────────────────────────────────────────────────────
 */

(function () {
    'use strict';

    const header    = document.getElementById('site-header');
    const backToTop = document.getElementById('back-to-top');

    /* Minimum scroll distance (px) before triggering header/back-to-top changes */
    const SCROLL_THRESHOLD = 80;

    /* ─────────────────────────────────────────────────────────────
       1. SCROLL STATE HANDLER
       Uses requestAnimationFrame to throttle updates and prevent
       layout thrashing on high-frequency scroll events.
       ───────────────────────────────────────────────────────────── */

    let ticking = false; // RAF throttle flag

    function onScroll() {
        if (ticking) return;

        requestAnimationFrame(function () {
            const scrollY = window.scrollY || window.pageYOffset;

            /* ── Header ── */
            if (header) {
                if (scrollY > SCROLL_THRESHOLD) {
                    header.classList.add('scrolled');
                } else {
                    header.classList.remove('scrolled');
                }
            }

            /* ── Back-to-top button ── */
            if (backToTop) {
                if (scrollY > 400) {
                    backToTop.classList.add('visible');
                } else {
                    backToTop.classList.remove('visible');
                }
            }

            ticking = false;
        });

        ticking = true;
    }

    /* Listen passively — does not block the browser's native scroll */
    window.addEventListener('scroll', onScroll, { passive: true });

    /* Run once on load in case page is not at top (e.g. browser restores scroll) */
    onScroll();

    /* ─────────────────────────────────────────────────────────────
       2. BACK-TO-TOP — smooth scroll on click
       ───────────────────────────────────────────────────────────── */

    if (backToTop) {
        backToTop.addEventListener('click', function () {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    /* ─────────────────────────────────────────────────────────────
       3. HEADER TRANSPARENCY on hero overlap
       When the hero background is dark, the header doesn't need
       its scrolled style (it's already over a dark background).
       This ensures the header transitions naturally on scroll.
       ───────────────────────────────────────────────────────────── */

    const heroSection = document.getElementById('hero');

    if (heroSection && header) {
        const heroObserver = new IntersectionObserver(
            function (entries) {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        // Over the hero — allow CSS transparency to show through
                        header.dataset.overHero = 'true';
                    } else {
                        header.dataset.overHero = 'false';
                    }
                });
            },
            { threshold: 0.1 }
        );

        heroObserver.observe(heroSection);
    }

})();

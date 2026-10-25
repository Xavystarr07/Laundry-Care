/**
 * js/navigation.js
 * ─────────────────────────────────────────────────────────────────────────────
 * Handles all navigation interactions:
 *   - Mobile hamburger + drawer open/close
 *   - Smooth scroll anchor hijacking
 *   - Active nav-link highlighting based on scroll position
 * ─────────────────────────────────────────────────────────────────────────────
 */

(function () {
    'use strict';

    /* ── Element references ── */
    const navToggle     = document.getElementById('nav-toggle');
    const mobileDrawer  = document.getElementById('mobile-drawer');
    const drawerClose   = document.getElementById('drawer-close');
    const drawerOverlay = document.getElementById('drawer-overlay');
    const drawerLinks   = document.querySelectorAll('.drawer-link');
    const navLinks      = document.querySelectorAll('.nav-link');
    const allAnchors    = document.querySelectorAll('a[href^="#"]');

    // ─────────────────────────────────────────────────────────────
    //  1. MOBILE DRAWER — open / close
    // ─────────────────────────────────────────────────────────────

    /** Open the mobile navigation drawer */
    function openDrawer() {
        mobileDrawer.classList.add('open');
        mobileDrawer.setAttribute('aria-hidden', 'false');
        drawerOverlay.classList.add('visible');
        navToggle.classList.add('open');
        navToggle.setAttribute('aria-expanded', 'true');
        document.body.style.overflow = 'hidden';  // Prevent body scroll

        // Move focus into drawer for accessibility
        drawerClose && drawerClose.focus();
    }

    /** Close the mobile navigation drawer */
    function closeDrawer() {
        mobileDrawer.classList.remove('open');
        mobileDrawer.setAttribute('aria-hidden', 'true');
        drawerOverlay.classList.remove('visible');
        navToggle.classList.remove('open');
        navToggle.setAttribute('aria-expanded', 'false');
        document.body.style.overflow = '';

        // Return focus to hamburger button
        navToggle && navToggle.focus();
    }

    if (navToggle)     navToggle.addEventListener('click', openDrawer);
    if (drawerClose)   drawerClose.addEventListener('click', closeDrawer);
    if (drawerOverlay) drawerOverlay.addEventListener('click', closeDrawer);

    // Close drawer when a drawer link is clicked
    drawerLinks.forEach(link => link.addEventListener('click', closeDrawer));

    // Close drawer on Escape key
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && mobileDrawer.classList.contains('open')) {
            closeDrawer();
        }
    });

    // ─────────────────────────────────────────────────────────────
    //  2. SMOOTH SCROLL — for all internal anchor links
    //     Respects the CSS scroll-padding-top set on <html>
    // ─────────────────────────────────────────────────────────────

    allAnchors.forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const targetId = this.getAttribute('href');
            if (!targetId || targetId === '#') return;

            const targetEl = document.querySelector(targetId);
            if (!targetEl) return;

            e.preventDefault();
            targetEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    });

    // ─────────────────────────────────────────────────────────────
    //  3. ACTIVE LINK HIGHLIGHTING
    //     Uses IntersectionObserver to detect which section is
    //     currently most visible and marks the corresponding nav link.
    // ─────────────────────────────────────────────────────────────

    const sections = document.querySelectorAll('section[id]');

    const sectionObserver = new IntersectionObserver(
        function (entries) {
            entries.forEach(entry => {
                if (!entry.isIntersecting) return;

                const id = entry.target.getAttribute('id');

                navLinks.forEach(link => {
                    const href = link.getAttribute('href');
                    if (href === '#' + id) {
                        link.classList.add('active');
                    } else {
                        link.classList.remove('active');
                    }
                });
            });
        },
        {
            rootMargin: '-20% 0px -70% 0px', // Trigger when section is roughly centred
            threshold: 0
        }
    );

    sections.forEach(section => sectionObserver.observe(section));

})();

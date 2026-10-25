/**
 * js/counter.js  v2.0
 * Animates numeric counters. Supports:
 *   - Integer: [data-count="250"]
 *   - Decimal: [data-count-decimal="99.99"] — for satisfaction rate
 */
(function () {
    'use strict';

    const DURATION = 2000;

    function easeOutExpo(t) {
        return t === 1 ? 1 : 1 - Math.pow(2, -10 * t);
    }

    /* Integer counter */
    function animateInteger(el) {
        const target    = parseInt(el.getAttribute('data-count'), 10);
        const startTime = performance.now();
        function tick(now) {
            const progress = Math.min((now - startTime) / DURATION, 1);
            el.textContent = Math.round(easeOutExpo(progress) * target).toLocaleString();
            if (progress < 1) requestAnimationFrame(tick);
            else el.textContent = target.toLocaleString();
        }
        requestAnimationFrame(tick);
    }

    /* Decimal counter (e.g. 99.99) */
    function animateDecimal(el) {
        const target    = parseFloat(el.getAttribute('data-count-decimal'));
        const startTime = performance.now();
        function tick(now) {
            const progress = Math.min((now - startTime) / DURATION, 1);
            const value    = easeOutExpo(progress) * target;
            el.textContent = value.toFixed(2);
            if (progress < 1) requestAnimationFrame(tick);
            else el.textContent = target.toFixed(2);
        }
        requestAnimationFrame(tick);
    }

    const observer = new IntersectionObserver(
        (entries, obs) => {
            entries.forEach(entry => {
                if (!entry.isIntersecting) return;
                const el = entry.target;
                if (el.hasAttribute('data-count-decimal')) {
                    animateDecimal(el);
                } else {
                    animateInteger(el);
                }
                obs.unobserve(el);
            });
        },
        { threshold: 0.5 }
    );

    document.querySelectorAll('[data-count], [data-count-decimal]').forEach(el => observer.observe(el));

})();

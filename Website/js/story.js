/**
 * js/story.js
 * ─────────────────────────────────────────────────────────────────────────────
 * Drives the animated "journey story" panel in the About section.
 * Cycles through 8 frames (Collection → Inspection → Treatment → Washing →
 * Drying → Ironing → Packaging → Delivery) with auto-advance and dot nav.
 * ─────────────────────────────────────────────────────────────────────────────
 */
(function () {
    'use strict';

    const panel  = document.getElementById('story-panel');
    if (!panel) return;

    const frames = panel.querySelectorAll('.story-frame');
    const dots   = panel.querySelectorAll('.sdot');
    if (!frames.length) return;

    let current    = 0;
    let timer      = null;
    const INTERVAL = 2800; // ms between auto-advance

    /** showFrame — transitions to a specific frame index */
    function showFrame(index) {
        frames[current].classList.remove('active');
        dots[current] && dots[current].classList.remove('active');

        current = (index + frames.length) % frames.length;

        frames[current].classList.add('active');
        dots[current] && dots[current].classList.add('active');
    }

    /** Start auto-cycling */
    function startCycle() {
        timer = setInterval(() => showFrame(current + 1), INTERVAL);
    }

    /** Stop auto-cycling (when user manually navigates) */
    function stopCycle() {
        clearInterval(timer);
    }

    /** Dot navigation — click to jump to frame */
    dots.forEach(dot => {
        dot.addEventListener('click', function () {
            stopCycle();
            showFrame(parseInt(this.dataset.target, 10));
            // Resume auto-cycle after a pause
            setTimeout(startCycle, INTERVAL * 2);
        });
    });

    // Start cycling when the panel enters the viewport
    const panelObserver = new IntersectionObserver(
        (entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    startCycle();
                } else {
                    stopCycle();
                }
            });
        },
        { threshold: 0.3 }
    );

    panelObserver.observe(panel);

})();

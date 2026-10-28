/**
 * js/process.js
 * ─────────────────────────────────────────────────────────────────────────────
 * Animates the 6-stage process section:
 *   - Reveals each .process-step in sequence using IntersectionObserver
 *   - Animates the horizontal connector line to grow across revealed steps
 *   - Each step is delayed proportionally to create a "drawing" effect
 * ─────────────────────────────────────────────────────────────────────────────
 */

(function () {
    'use strict';

    const processTrack = document.getElementById('process-track');
    const processLine  = document.getElementById('process-line');
    const steps        = document.querySelectorAll('.process-step');

    if (!processTrack || steps.length === 0) return;

    /* Track how many steps have been revealed so far */
    let revealedCount = 0;
    const TOTAL_STEPS = steps.length;

    /**
     * updateLine
     * Expands the process connector line proportionally to the number
     * of steps that have been revealed. The line spans from the first
     * node centre to the last revealed node centre.
     *
     * Layout: 3-column grid, so step indices 0–2 are row 1, 3–5 are row 2.
     * We only animate the line for the first row (steps 1–3) visually.
     */
    function updateLine() {
        if (!processLine) return;

        // Line covers from left edge of step[0] to right edge of step[2]
        // At 3-column layout, 3 steps = line spans 2/3 of the track width
        // We animate based on ratio of revealed / total
        const ratio = Math.min(revealedCount / (TOTAL_STEPS - 1), 1);

        // Max width the line should reach (between node centres of step 1 → 3 in first row)
        // Approximated at 66% of container width for a 3-col layout
        const maxWidthPercent = 66;
        const newWidth = (ratio * maxWidthPercent).toFixed(1) + '%';

        processLine.style.width = newWidth;
    }

    /**
     * We use an IntersectionObserver that fires once the #process-track
     * container enters the viewport, then reveals steps sequentially.
     */
    const trackObserver = new IntersectionObserver(
        function (entries, observer) {
            entries.forEach(entry => {
                if (!entry.isIntersecting) { entry.target.classList.remove('revealed'); return; }

                // Reveal each step with a cascading delay
                steps.forEach(function (step, index) {
                    const delay = index * 200; // 200 ms between each step

                    setTimeout(function () {
                        step.classList.add('revealed');
                        revealedCount = index + 1;
                        updateLine();
                    }, delay);
                });

                
            });
        },
        { threshold: 0.15 }
    );

    trackObserver.observe(processTrack);

})();

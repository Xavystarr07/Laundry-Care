/**
 * js/preloader.js
 * ─────────────────────────────────────────────────────────────────────────────
 * Manages the full-screen preloader animation.
 * Hides the loader once the page's critical resources are loaded,
 * then triggers the GSAP hero entrance sequence (defined in animations.js).
 * ─────────────────────────────────────────────────────────────────────────────
 */

(function () {
    'use strict';

    const preloader = document.getElementById('preloader');
    if (!preloader) return;

    /**
     * dismissPreloader
     * Adds the `.hidden` class to fade out and collapse the preloader,
     * then removes it from the DOM after the CSS transition completes.
     */
    function dismissPreloader() {
        preloader.classList.add('hidden');

        // Clean up DOM after transition (600 ms matches CSS transition)
        preloader.addEventListener('transitionend', function onEnd() {
            preloader.removeEventListener('transitionend', onEnd);
            preloader.remove();

            // Signal to animations.js that the hero sequence can begin
            document.dispatchEvent(new CustomEvent('preloaderDone'));
        });
    }

    /**
     * We wait for both:
     *   1. The CSS bar animation to finish (~1.8s)
     *   2. The window 'load' event (all resources ready)
     *
     * Whichever finishes last triggers the dismiss.
     */
    const MIN_DISPLAY_MS = 1900; // Keep loader visible for at least this long
    const startTime = Date.now();

    function tryDismiss() {
        const elapsed = Date.now() - startTime;
        const remaining = Math.max(0, MIN_DISPLAY_MS - elapsed);
        setTimeout(dismissPreloader, remaining);
    }

    if (document.readyState === 'complete') {
        tryDismiss();
    } else {
        window.addEventListener('load', tryDismiss, { once: true });
    }

})();

(function(){var t=document.getElementById('pl-t'),r=document.getElementById('pl-r');if(!t||!r)return;var s='A Fresh Start In Every Wash',i=0;r.textContent=s;setTimeout(function k(){if(i<s.length){i++;t.textContent=s.slice(0,i);r.textContent=s.slice(i);setTimeout(k,55)}},1900)})();

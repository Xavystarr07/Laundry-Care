/** js/contact.js - contact page: typed intro, live status, spotlight/tilt cards, fade-out near footer */
(function () {
    'use strict';
    var root = document.documentElement;
    var rm   = matchMedia('(prefers-reduced-motion: reduce)').matches;
    var fine = matchMedia('(hover: hover) and (pointer: fine)').matches;
    var ov = document.getElementById('cx-intro'), ty = document.getElementById('cx-type');

    /* Intro */
    function done() { root.classList.add('cx-go', 'cx-done'); if (ov) ov.remove(); ov = null; }
    var out = false, t;
    function leave() {
        if (out) return;
        out = true; clearTimeout(t);
        if (!ov) { done(); return; }
        ov.classList.add('out');
        setTimeout(function () { root.classList.add('cx-go'); }, 350);
        setTimeout(done, 1000);
    }
    if (!ov || !ty || rm) { done(); }
    else {
        var s = 'Let’s get in touch', i = 0;
        ov.addEventListener('click', leave);
        (function type() {
            if (out) return;
            if (i < s.length) { i++; ty.textContent = s.slice(0, i); t = setTimeout(type, 75); }
            else { t = setTimeout(leave, 900); }
        })();
    }

    /* Live office status (South Africa time, 8am to 5pm) */
    var st = document.getElementById('cx-status-text');
    if (st) {
        try {
            var hr = parseInt(new Intl.DateTimeFormat('en-GB', { hour: 'numeric', hour12: false, timeZone: 'Africa/Johannesburg' }).format(new Date()), 10) % 24;
            var open = hr >= 8 && hr < 17;
            st.textContent = open ? 'We’re open now · until 5pm' : 'Office closed · email us, we reply until midnight';
            st.parentNode.classList.toggle('closed', !open);
        } catch (e) {}
    }

    /* Page fades away as the footer scrolls in, and back when you scroll up */
    var cx = document.getElementById('cx'), ft = document.getElementById('site-footer'), tick = false;
    function frame() {
        tick = false;
        if (!cx || !ft) return;
        var r = ft.getBoundingClientRect();
        var p = (innerHeight - r.top) / (r.height * 0.8);
        cx.style.setProperty('--s', Math.max(0, Math.min(1, p)).toFixed(3));
    }
    function req() { if (!tick) { tick = true; requestAnimationFrame(frame); } }
    addEventListener('scroll', req, { passive: true });
    addEventListener('resize', req);
    frame();

    /* Spotlight + 3D tilt */
    if (fine && !rm) {
        [].forEach.call(document.querySelectorAll('.cx-card'), function (c) {
            c.addEventListener('pointermove', function (e) {
                var r = c.getBoundingClientRect(), x = e.clientX - r.left, y = e.clientY - r.top;
                c.style.setProperty('--mx', x + 'px');
                c.style.setProperty('--my', y + 'px');
                c.style.setProperty('--ry', ((x / r.width - 0.5) * 8).toFixed(2) + 'deg');
                c.style.setProperty('--rx', ((0.5 - y / r.height) * 8).toFixed(2) + 'deg');
            });
            c.addEventListener('pointerleave', function () {
                c.style.removeProperty('--rx');
                c.style.removeProperty('--ry');
            });
        });
    }
})();
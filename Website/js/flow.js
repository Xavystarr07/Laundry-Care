/** js/flow.js - section-by-section scroll transitions, stagger, counters, section dots */
(function () {
    'use strict';
    var secs = [].slice.call(document.querySelectorAll('.flow-sec'));
    if (!secs.length) return;

    if (document.body.hasAttribute('data-snap')) document.documentElement.classList.add('snap');

    /* Auto-stagger children of [data-stagger] containers */
    [].forEach.call(document.querySelectorAll('[data-stagger]'), function (g) {
        var t = g.getAttribute('data-stagger') || 'up';
        [].forEach.call(g.children, function (c, i) {
            if (!c.hasAttribute('data-fx')) c.setAttribute('data-fx', t);
            c.style.setProperty('--d', (i * 0.12) + 's');
        });
    });

    /* Count-up numbers */
    function run(el) {
        var to = parseFloat(el.getAttribute('data-count')) || 0, suf = el.getAttribute('data-suffix') || '', t0 = null;
        cancelAnimationFrame(el._raf);
        (function step(ts) {
            if (!t0) t0 = ts;
            var p = Math.min((ts - t0) / 1600, 1), e = 1 - Math.pow(1 - p, 3);
            el.textContent = Math.round(to * e) + suf;
            if (p < 1) el._raf = requestAnimationFrame(step);
        })(performance.now());
    }
    function reset(el) { cancelAnimationFrame(el._raf); el.textContent = '0' + (el.getAttribute('data-suffix') || ''); }

    var io = new IntersectionObserver(function (es) {
        es.forEach(function (e) {
            var s = e.target, c = s.querySelectorAll('[data-count]');
            if (e.isIntersecting) { s.classList.add('is-in'); [].forEach.call(c, run); }
            else { s.classList.remove('is-in'); [].forEach.call(c, reset); }
        });
    }, { threshold: 0.12, rootMargin: '-6% 0px -6% 0px' });
    secs.forEach(function (s) { io.observe(s); });

    /* Side dots (snap pages only) */
    if (!document.body.hasAttribute('data-snap')) return;
    var all = [].slice.call(document.querySelectorAll('main > section'));
    if (all.length < 3) return;
    var nav = document.createElement('nav');
    nav.className = 'flow-dots';
    nav.setAttribute('aria-label', 'Page sections');
    var bs = all.map(function (s, i) {
        var b = document.createElement('button');
        b.type = 'button';
        b.setAttribute('aria-label', 'Go to section ' + (i + 1));
        b.addEventListener('click', function () { s.scrollIntoView({ behavior: 'smooth', block: 'start' }); });
        nav.appendChild(b);
        return b;
    });
    document.body.appendChild(nav);
    var io2 = new IntersectionObserver(function (es) {
        es.forEach(function (e) {
            if (!e.isIntersecting) return;
            var i = all.indexOf(e.target);
            bs.forEach(function (b, j) { b.classList.toggle('active', i === j); });
        });
    }, { threshold: 0.5 });
    all.forEach(function (s) { io2.observe(s); });
})();

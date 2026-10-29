/**
 * js/process.js - swipeable 6-stage carousel
 *   - touch swipe (native), mouse drag, arrows, dots, keyboard arrows
 *   - marks the stage in the middle as active
 *   - starts the icon animations when the section is on screen
 */
(function () {
    'use strict';
    var track = document.getElementById('process-track');
    var steps = [].slice.call(document.querySelectorAll('.process-step'));
    if (!track || !steps.length) return;

    var prev = document.getElementById('pr-prev'), next = document.getElementById('pr-next');
    var box = document.getElementById('pr-dots'), cnt = document.getElementById('pr-count');
    var cur = 0, dots = [];

    steps.forEach(function (s, i) {
        var b = document.createElement('button');
        b.type = 'button';
        b.setAttribute('aria-label', 'Go to stage ' + (i + 1));
        b.addEventListener('click', function () { go(i); });
        box.appendChild(b);
        dots.push(b);
    });

    function go(i) {
        i = Math.max(0, Math.min(steps.length - 1, i));
        steps[i].scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });
    }

    function mark(i) {
        cur = i;
        steps.forEach(function (s, j) { s.classList.toggle('is-active', j === i); });
        dots.forEach(function (d, j) { d.classList.toggle('active', j === i); });
        cnt.textContent = (i + 1) + ' / ' + steps.length;
        prev.disabled = i === 0;
        next.disabled = i === steps.length - 1;
    }

    function nearest() {
        var c = track.scrollLeft + track.clientWidth / 2, best = 0, d = 1e9;
        steps.forEach(function (s, j) {
            var m = Math.abs(s.offsetLeft + s.offsetWidth / 2 - c);
            if (m < d) { d = m; best = j; }
        });
        return best;
    }

    var tick = false;
    track.addEventListener('scroll', function () {
        if (tick) return;
        tick = true;
        requestAnimationFrame(function () { mark(nearest()); tick = false; });
    }, { passive: true });
    window.addEventListener('resize', function () { mark(nearest()); });

    prev.addEventListener('click', function () { go(cur - 1); });
    next.addEventListener('click', function () { go(cur + 1); });
    track.addEventListener('keydown', function (e) {
        if (e.key === 'ArrowRight') { e.preventDefault(); go(cur + 1); }
        if (e.key === 'ArrowLeft')  { e.preventDefault(); go(cur - 1); }
    });

    /* Mouse drag (touch uses native swipe) */
    var down = false, moved = false, sx = 0, sl = 0;
    track.addEventListener('pointerdown', function (e) {
        if (e.pointerType !== 'mouse') return;
        down = true; moved = false; sx = e.clientX; sl = track.scrollLeft;
    });
    window.addEventListener('pointermove', function (e) {
        if (!down) return;
        var dx = e.clientX - sx;
        if (Math.abs(dx) > 4) { moved = true; track.classList.add('drag'); }
        if (moved) track.scrollLeft = sl - dx;
    });
    window.addEventListener('pointerup', function () {
        if (!down) return;
        down = false;
        if (moved) { track.classList.remove('drag'); go(nearest()); }
    });

    /* Start icon animations when on screen */
    var io = new IntersectionObserver(function (es) {
        es.forEach(function (e) {
            steps.forEach(function (s, i) {
                if (e.isIntersecting) setTimeout(function () { s.classList.add('revealed'); }, i * 120);
                else s.classList.remove('revealed');
            });
        });
    }, { threshold: 0.15 });
    io.observe(track);

    mark(0);
})();

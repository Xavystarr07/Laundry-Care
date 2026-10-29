/** js/promise.js - types "Our 6-Stage Promise" big, then flies it into the page heading */
(function () {
    'use strict';
    var root = document.documentElement;
    var ov = document.getElementById('promise-intro');
    var tg = document.getElementById('pi-target');
    var tx = document.getElementById('pi-text');

    function finish() {
        if (finish.done) return;
        finish.done = true;
        clearTimeout(finish.t);
        if (ov) ov.remove();
        root.classList.remove('pi-active');
    }

    if (!ov || !tg || !tx || !root.classList.contains('pi-active')) { finish(); return; }

    /* Build one span per character, copying the heading's text and <em> */
    var chars = [];
    (function build(src, dst) {
        [].forEach.call(src.childNodes, function (n) {
            if (n.nodeType === 3) {
                n.textContent.split('').forEach(function (c) {
                    var s = document.createElement('span');
                    s.className = 'pi-c';
                    s.textContent = c;
                    dst.appendChild(s);
                    chars.push(s);
                });
            } else if (n.nodeType === 1) {
                var e = document.createElement(n.tagName);
                dst.appendChild(e);
                build(n, e);
            }
        });
    })(tg, tx);

    var i = 0;
    function type() {
        if (i > 0) chars[i - 1].classList.remove('last');
        if (i < chars.length) {
            chars[i].classList.add('on', 'last');
            i++;
            finish.t = setTimeout(type, i < 4 ? 170 : 75);
        } else {
            finish.t = setTimeout(move, 900);
        }
    }

    function move() {
        chars[chars.length - 1].classList.remove('last');
        var a = tx.getBoundingClientRect(), b = tg.getBoundingClientRect();
        var s = b.width / a.width;
        ov.classList.add('moving');
        tx.style.transformOrigin = '0 0';
        var anim = tx.animate(
            [{ transform: 'none' }, { transform: 'translate(' + (b.left - a.left) + 'px,' + (b.top - a.top) + 'px) scale(' + s + ')' }],
            { duration: 1200, easing: 'cubic-bezier(.76,0,.24,1)', fill: 'forwards' }
        );
        anim.onfinish = finish;
    }

    ov.addEventListener('click', finish);   /* click to skip */
    finish.t = setTimeout(type, 500);
})();

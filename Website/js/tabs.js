/** js/tabs.js - category tabs on the Private Clients page (supports clients.php#uniforms etc.) */
(function () {
    'use strict';
    var t = [].slice.call(document.querySelectorAll('.ctab'));
    var p = [].slice.call(document.querySelectorAll('.cpanel'));
    if (!t.length) return;

    function go(k) {
        t.forEach(function (b) { b.classList.toggle('active', b.dataset.t === k); });
        p.forEach(function (x) { x.classList.toggle('active', x.dataset.p === k); });
    }
    t.forEach(function (b) { b.addEventListener('click', function () { go(b.dataset.t); }); });

    var k = location.hash.slice(1);
    if (k && t.some(function (b) { return b.dataset.t === k; })) go(k);
})();

/** js/pricing.js - search + category filter for the price list */
(function () {
    'use strict';
    var q = document.getElementById('price-q');
    if (!q) return;

    var cats  = [].slice.call(document.querySelectorAll('.price-cat'));
    var chips = [].slice.call(document.querySelectorAll('.pchip'));
    var none  = document.getElementById('price-none');
    var cat   = 'all';

    function run() {
        var s = q.value.toLowerCase().trim(), shown = 0;
        cats.forEach(function (c) {
            var inCat = cat === 'all' || c.dataset.cat === cat, any = 0;
            [].forEach.call(c.querySelectorAll('tbody tr'), function (r) {
                var ok = inCat && (!s || r.textContent.toLowerCase().indexOf(s) > -1);
                r.classList.toggle('is-hidden', !ok);
                if (ok) any++;
            });
            c.classList.toggle('is-hidden', !any);
            shown += any;
        });
        if (none) none.classList.toggle('is-hidden', shown > 0);
    }

    q.addEventListener('input', run);
    chips.forEach(function (b) {
        b.addEventListener('click', function () {
            cat = b.dataset.cat;
            chips.forEach(function (x) { x.classList.toggle('active', x === b); });
            run();
        });
    });
})();

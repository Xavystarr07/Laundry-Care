// predict.js — Unit before Hotel + self-learning guesses (window.LCPredict)
(function () {
    const tb = document.getElementById('invoiceTable');
    if (!tb) return;
    function swap() {
        tb.querySelectorAll('tr').forEach(r => {
            const a = r.children[0], b = r.children[1];
            if (a && b && a.querySelector('.hotel') && b.querySelector('.unit-number')) r.insertBefore(b, a);
        });
    }
    const hr = document.querySelector('#invoiceForm thead tr');
    if (hr && /hotel/i.test(hr.children[0].textContent)) hr.insertBefore(hr.children[1], hr.children[0]);
    new MutationObserver(swap).observe(tb, { childList: true, subtree: true });
    swap();

    const q = c => tb.rows[0]?.querySelector(c);
    const inList = (h, n) => Array.from(h.options).some(o => o.value === n);
    const get = url => fetch(url).then(r => r.json()).catch(() => ({ cands: [], conf: 0 }));
    const invVal = () => (document.getElementById('invoice_number')?.value || '').replace(/\D/g, '');
    const L = window.LCPredict = {
        last: null,
        ask(inv, unit) {
            return get('php/predict.php?inv=' + (inv || '') + (unit ? '&unit=' + encodeURIComponent(unit) : '')).then(d => {
                const c = d.cands && d.cands[0];
                if (c && inv) L.last = { inv: String(inv), h: c.h, u: String(c.u), c: d.conf || 0 };
                return d;
            });
        }
    };
    let manual = false, last = { h: '', u: '' }, t1, t2;

    function setHotel(name) {
        const h = q('.hotel');
        if (!h || h.disabled || !inList(h, name)) return false;
        if (h.value !== name) {
            h.value = name;
            if (typeof updateUnitDatalist === 'function') updateUnitDatalist(h);
            if (typeof flashGreen === 'function') flashGreen(h);
        }
        return true;
    }
    document.addEventListener('change', e => {
        if (e.isTrusted && e.target.classList?.contains('hotel') && e.target.closest('tr') === tb.rows[0]) manual = true;
    });

    // unit typed -> guess hotel
    document.addEventListener('input', e => {
        const t = e.target;
        if (!e.isTrusted || !t.classList?.contains('unit-number') || t.closest('tr') !== tb.rows[0]) return;
        clearTimeout(t1);
        const v = t.value.trim();
        if (!v) { manual = false; return; }
        if (manual) return;
        t1 = setTimeout(async () => {
            const iv = invVal(), d = await L.ask(iv.length >= 4 ? iv : '', v);
            if (t.value.trim() !== v || manual) return;
            const h = q('.hotel'), c = h && (d.cands || []).find(x => inList(h, x.h));
            if (c && d.conf >= .5) setHotel(c.h);
        }, 250);
    });

    // invoice number typed -> guess hotel + unit
    document.addEventListener('input', e => {
        const t = e.target;
        if (!e.isTrusted || t.id !== 'invoice_number') return;
        clearTimeout(t2);
        const v = invVal();
        if (v.length < 4) return;
        t2 = setTimeout(async () => {
            const d = await L.ask(v);
            if (invVal() !== v) return;
            const u = q('.unit-number'), h = q('.hotel');
            if (!u || !h || u.readOnly) return;
            const c = (d.cands || []).find(x => inList(h, x.h));
            if (!c) return;
            const pct = Math.round((d.conf || 0) * 100);
            if (d.conf < .7) { if (typeof showPopup === 'function') showPopup('info', 'Best guess: ' + c.h + ', unit ' + c.u + ' (' + pct + '%).', 'Not sure. Please check.', 3500); return; }
            if (u.value && !(u.value === last.u && h.value === last.h)) return;
            setHotel(c.h); u.value = c.u; last = { h: c.h, u: String(c.u) }; manual = false;
            if (typeof showPopup === 'function') showPopup('info', 'Guessed ' + c.h + ', unit ' + c.u + ' (' + pct + '%).', 'Change it if it is wrong.', 3500);
        }, 250);
    });

    // send the guess with the save so the server can learn from hits/misses
    const f = document.getElementById('invoiceForm');
    f?.addEventListener('submit', () => {
        const l = L.last, v = invVal();
        [['pred_h', 'h'], ['pred_u', 'u'], ['pred_c', 'c']].forEach(([n, k]) => {
            let i = f.querySelector('[name=' + n + ']');
            if (!i) { i = document.createElement('input'); i.type = 'hidden'; i.name = n; f.appendChild(i); }
            i.value = l && l.inv === v ? l[k] : '';
        });
    }, true);
})();
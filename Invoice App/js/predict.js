// predict.js — Unit column before Hotel + hotel/unit guessing (regular invoices)
(function () {
    const tb = document.getElementById('invoiceTable');
    if (!tb) return;

    // Unit first, Hotel second. All other code finds cells by class, so the order is safe.
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
    const get = url => fetch(url).then(r => r.json()).catch(() => ({}));
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

    // If you pick the hotel yourself, stop guessing until the unit is cleared
    document.addEventListener('change', e => {
        if (e.isTrusted && e.target.classList?.contains('hotel') && e.target.closest('tr') === tb.rows[0]) manual = true;
    });

    // Typing the unit -> guess the hotel (most used hotel for that unit)
    document.addEventListener('input', e => {
        const t = e.target;
        if (!e.isTrusted || !t.classList?.contains('unit-number') || t.closest('tr') !== tb.rows[0]) return;
        clearTimeout(t1);
        const v = t.value.trim();
        if (!v) { manual = false; return; }
        if (manual) return;
        t1 = setTimeout(async () => {
            const d = await get('php/predict.php?unit=' + encodeURIComponent(v));
            if (t.value.trim() !== v || manual) return;
            const h = q('.hotel');
            const name = h && (d.hotels || []).find(n => inList(h, n));
            if (name && setHotel(name)) t.dispatchEvent(new Event('input', { bubbles: true }));
        }, 250);
    });

    // Typing the invoice number -> guess hotel + unit from the nearest saved invoice numbers
    document.addEventListener('input', e => {
        const t = e.target;
        if (!e.isTrusted || t.id !== 'invoice_number') return;
        clearTimeout(t2);
        const v = t.value.replace(/\D/g, '');
        if (v.length < 4) return;
        t2 = setTimeout(async () => {
            const d = await get('php/predict.php?inv=' + v);
            if (t.value.replace(/\D/g, '') !== v) return;
            const u = q('.unit-number'), h = q('.hotel');
            if (!u || !h || u.readOnly) return;
            if (u.value && !(u.value === last.u && h.value === last.h)) return;   // don't overwrite what you typed
            const c = (d.rows || []).find(x => inList(h, x.h));
            if (!c) return;
            setHotel(c.h);
            u.value = c.u;
            last = { h: c.h, u: String(c.u) };
            manual = false;
            u.dispatchEvent(new Event('input', { bubbles: true }));
            if (typeof showPopup === 'function')
                showPopup('info', 'Guessed ' + c.h + ', unit ' + c.u + ' (from invoice #' + c.n + ').', 'Change it if it is wrong.', 3500);
        }, 250);
    });
})();
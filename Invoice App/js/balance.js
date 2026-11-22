// balance.js — bottom-right balance peek (open statement period total)
(function () {
    const st = document.createElement('style');
    st.textContent = `
    #balPeek{position:fixed;right:24px;bottom:24px;z-index:9000;height:44px;min-width:44px;max-width:44px;padding:0 0 0 10px;display:flex;align-items:center;gap:0;overflow:hidden;white-space:nowrap;cursor:pointer;margin:0!important;border-radius:22px;font:700 13px 'Segoe UI',Arial,sans-serif;color:#0f766e;background:rgba(255,255,255,.6);-webkit-backdrop-filter:blur(8px);backdrop-filter:blur(8px);border:2px solid #14b8a6;box-shadow:0 4px 14px rgba(20,184,166,.28);transition:max-width .35s ease,background .25s,color .25s}
    #balPeek svg{width:20px;height:20px;flex-shrink:0}
    #balPeek span{max-width:0;opacity:0;margin-left:0;overflow:hidden;transition:max-width .35s ease,opacity .25s ease,margin .35s ease}
    #balPeek:hover,#balPeek:focus-visible{max-width:340px;background:#14b8a6;color:#fff;transform:none}
    #balPeek:hover span,#balPeek:focus-visible span{max-width:300px;opacity:1;margin-left:10px;padding-right:16px}
    body.dark-mode #balPeek{background:rgba(18,14,48,.65);color:#5eead4;border-color:#2dd4bf;box-shadow:0 0 12px rgba(45,212,191,.45)}
    body.dark-mode #balPeek:hover{background:#0d9488;color:#fff}
    @media print{#balPeek{display:none!important}}`;
    document.head.appendChild(st);

    const b = document.createElement('button');
    b.id = 'balPeek'; b.type = 'button'; b.title = 'Open statement balance';
    b.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="6" width="20" height="13" rx="2"/><path d="M2 10h20M6 15h4"/></svg><span>Balance …</span>';
    document.body.appendChild(b);
    bal = d.total.toFixed(2); lab.textContent = 'Balance R ' + d.total.toFixed(2)

    function load() {
        fetch('php/balance.php').then(r => r.json()).then(d => {
            if (!d.ok) { lab.textContent = 'Balance unavailable'; return; }
            lab.textContent = 'Balance R ' + d.total.toFixed(2) + ' · ' + d.count + ' invoice' + (d.count === 1 ? '' : 's');
        }).catch(() => { lab.textContent = 'Balance unavailable'; });
    }
    b.addEventListener('mouseenter', load);
    b.addEventListener('click', () => {
        if (bal === null) return;
        const done = ok => { lab.textContent = ok ? 'Copied R ' + bal : 'Could not copy'; setTimeout(load, 1800); };
        navigator.clipboard.writeText(bal).then(() => done(true)).catch(() => done(false));
    });
    load(); setInterval(load, 60000);

    // ── Save animation (runs instead of the black flash) ──
    const sv = document.createElement('style');
    sv.textContent = `
    @keyframes lcIn{from{opacity:0;transform:translateY(8px)}to{opacity:1;transform:none}}
    body{animation:lcIn .55s ease both}
    #lcSave{position:fixed;inset:0;z-index:100000;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:18px;background:radial-gradient(circle at 20% 20%,rgba(124,58,237,.55),transparent 55%),radial-gradient(circle at 80% 80%,rgba(6,182,212,.5),transparent 55%),#0b0d24;color:#fff;font:700 18px 'Segoe UI',Arial,sans-serif;letter-spacing:1px;opacity:0;animation:lcSv .35s ease forwards}
    html:not(.dk) #lcSave{background:radial-gradient(circle at 20% 20%,rgba(255,255,255,.9),transparent 55%),linear-gradient(180deg,#58a6f0,#b0d9fb);color:#0d2a4d}
    @keyframes lcSv{to{opacity:1}}
    #lcSave svg{width:84px;height:84px}
    #lcSave circle{fill:none;stroke:currentColor;stroke-width:4;stroke-linecap:round;stroke-dasharray:226;stroke-dashoffset:226;animation:lcDraw .9s ease forwards}
    #lcSave path{fill:none;stroke:currentColor;stroke-width:5;stroke-linecap:round;stroke-linejoin:round;stroke-dasharray:50;stroke-dashoffset:50;animation:lcDraw .5s ease .6s forwards}
    @keyframes lcDraw{to{stroke-dashoffset:0}}
    @media print{#lcSave{display:none!important}}`;
    document.head.appendChild(sv);
    function saving() {
        if (document.getElementById('lcSave')) return;
        const o = document.createElement('div'); o.id = 'lcSave';
        o.innerHTML = '<svg viewBox="0 0 84 84"><circle cx="42" cy="42" r="36"/><path d="M26 43l11 11 21-23"/></svg><div>Saving…</div>';
        document.body.appendChild(o);
    }
    const inv = document.getElementById('invoiceForm');
    if (inv) inv.addEventListener('submit', e => { if (!e.defaultPrevented) saving(); });
    const dep = document.getElementById('departureForm');
    if (dep) { const raw = dep.submit.bind(dep); dep.submit = () => { saving(); setTimeout(raw, 700); }; }
})();
// typeform.js — Laundry Care Quick Entry (opens on load)
(function () {
const S = p => `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">${p}</svg>`;
const IC = {
  inv:S('<path d="M4 9h16M4 15h16M10 3 8 21M16 3l-2 18"/>'), cal:S('<rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/>'),
  pin:S('<path d="M12 21s-7-6.2-7-11a7 7 0 0 1 14 0c0 4.8-7 11-7 11z"/><circle cx="12" cy="10" r="2.5"/>'),
  tag:S('<path d="M20.6 13.4l-7.2 7.2a2 2 0 0 1-2.8 0L2 12V2h10l8.6 8.6a2 2 0 0 1 0 2.8z"/><circle cx="7" cy="7" r="1.2"/>'),
  qty:S('<path d="M12 3 3 8l9 5 9-5z"/><path d="m3 13 9 5 9-5"/>'), plus:S('<path d="M12 5v14M5 12h14"/>'), check:S('<path d="M5 12.5l4.5 4.5L19 7"/>'),
  edit:S('<path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4z"/>'), trash:S('<path d="M3 6h18M8 6V4h8v2M6 6l1 14h10l1-14"/>'),
  arrow:S('<path d="M5 12h14M13 6l6 6-6 6"/>'), back:S('<path d="M19 12H5M11 6l-6 6 6 6"/>'), bolt:S('<path d="M13 2 4 14h7l-1 8 9-12h-7z"/>'), x:S('<path d="M18 6 6 18M6 6l12 12"/>')
};
const esc = s => String(s).replace(/[&<>"]/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c]));
const money = n => 'R ' + n.toFixed(2);
const today = () => new Date().toISOString().split('T')[0];
const minDate = () => { const d = new Date(); d.setMonth(d.getMonth() - 2); return d.toISOString().split('T')[0]; };
const LBL = ['Invoice number','Date received','Hotel & unit','Item code','Quantity','Add another?','Review'], PCT = [10,25,40,60,78,90,100];
const HS = document.createElement('select'); HS.innerHTML = getHotelOptionsHTML('x');
const okH = h => [...HS.options].some(o => o.value && o.value === h);

const style = document.createElement('style');
style.textContent = `
#tfFab{position:fixed;right:24px;bottom:84px;z-index:9000;height:46px;padding:0 16px;border:none;border-radius:23px;display:flex;align-items:center;gap:8px;cursor:pointer;color:#fff;font:700 14px 'Segoe UI',Arial,sans-serif;background:linear-gradient(135deg,#7c3aed,#06b6d4);box-shadow:0 6px 24px rgba(124,58,237,.5);margin:0!important;transition:transform .2s,filter .2s}
#tfFab:hover{transform:translateY(-2px) scale(1.04);filter:brightness(1.1)}#tfFab svg{width:18px;height:18px}
#tfOverlay{position:fixed;inset:0;z-index:99990;display:none;align-items:center;justify-content:center;padding:16px;background:radial-gradient(circle at 18% 20%,rgba(124,58,237,.55),transparent 50%),radial-gradient(circle at 82% 82%,rgba(6,182,212,.5),transparent 50%),rgba(8,10,25,.84);backdrop-filter:blur(8px)}
#tfOverlay.on{display:flex}
#tfCard{--bg:#fff;--tx:#1e1b4b;--mu:#6b7280;--ln:#e5e7eb;--so:#f5f3ff;--ac:#7c3aed;--ac2:#06b6d4;width:100%;max-width:560px;max-height:92vh;display:flex;flex-direction:column;overflow:hidden;background:var(--bg);color:var(--tx);border-radius:26px;box-shadow:0 30px 90px rgba(0,0,0,.5);font-family:'Segoe UI',Arial,sans-serif;text-align:left;animation:tfPop .35s cubic-bezier(.34,1.56,.64,1)}
body.dark-mode #tfCard{--bg:#14122b;--tx:#f1f0ff;--mu:#a5a3c9;--ln:#2d2a55;--so:#1d1a3d;--ac:#a78bfa;--ac2:#22d3ee}
@keyframes tfPop{from{opacity:0;transform:scale(.9) translateY(20px)}to{opacity:1;transform:none}}
@keyframes tfIn{from{opacity:0;transform:translateX(40px)}to{opacity:1;transform:none}}
#tfBar{height:5px;background:var(--so)}#tfBar i{display:block;height:100%;width:0;background:linear-gradient(90deg,var(--ac),var(--ac2));transition:width .45s ease}
#tfTop{display:flex;align-items:center;justify-content:space-between;padding:16px 22px 0;color:var(--mu);font-size:12px;font-weight:700;letter-spacing:1px;text-transform:uppercase}
#tfCard button{margin:0!important}
#tfX{width:32px;height:32px;border-radius:50%;border:none;background:var(--so);color:var(--mu);cursor:pointer;display:flex;align-items:center;justify-content:center;padding:0}#tfX svg{width:16px;height:16px}
#tfStage{padding:14px 26px 6px;overflow-y:auto;flex:1}.tf-step{animation:tfIn .35s ease}#tfStage.noanim *{animation:none!important}
.tf-ico{width:54px;height:54px;border-radius:18px;background:linear-gradient(135deg,var(--ac),var(--ac2));color:#fff;display:flex;align-items:center;justify-content:center;box-shadow:0 8px 22px rgba(124,58,237,.4)}.tf-ico svg{width:26px;height:26px}
.tf-q{font-size:25px;font-weight:800;margin:14px 0 4px}.tf-h{color:var(--mu);font-size:14px;margin-bottom:16px;line-height:1.5}.tf-h b{color:var(--ac)}
#tfOverlay .tf-in{width:100%!important;margin:0!important;padding:14px 16px!important;font-size:20px!important;font-weight:700;border:2px solid var(--ln)!important;border-radius:14px!important;background:var(--so)!important;color:var(--tx)!important;-webkit-text-fill-color:var(--tx);outline:none;box-sizing:border-box;display:block;color-scheme:light}
body.dark-mode #tfOverlay .tf-in{color-scheme:dark}#tfOverlay .tf-in:focus{border-color:var(--ac)!important}
#tfInv{letter-spacing:8px;text-align:center}
.tf-st{min-height:20px;margin-top:10px;font-size:13px;font-weight:700;color:var(--mu)}.tf-st.ok{color:#16a34a}.tf-st.bad{color:#dc2626}
.tf-chips{display:flex;flex-wrap:wrap;gap:8px;margin-top:12px}
.tf-chip{border:1.5px solid var(--ln);background:var(--so);color:var(--tx);border-radius:999px;padding:8px 14px;font:700 13px inherit;cursor:pointer;display:inline-flex;align-items:center;gap:6px}.tf-chip svg{width:14px;height:14px}.tf-chip:hover{border-color:var(--ac);color:var(--ac)}
.tf-sg{display:flex;width:100%;align-items:center;justify-content:space-between;border:1.5px solid var(--ln);background:var(--so);color:var(--tx);border-radius:14px;padding:12px 16px;margin-bottom:8px;font:600 15px inherit;cursor:pointer;transition:all .15s}
.tf-sg:first-child{border-color:var(--ac)}.tf-sg:hover{border-color:var(--ac);transform:translateX(4px)}.tf-sg em{font-style:normal;font-weight:800;color:#16a34a}
.tf-list{margin-top:10px;max-height:250px;overflow-y:auto;border:1.5px solid var(--ln);border-radius:14px}
.tf-opt{display:flex;gap:10px;align-items:center;padding:10px 14px;cursor:pointer;border-bottom:1px solid var(--ln)}.tf-opt:last-child{border:none}.tf-opt.hl,.tf-opt:hover{background:var(--so)}
.tf-opt b{font-family:Consolas,monospace;color:var(--ac);min-width:96px;font-size:13px}.tf-opt span{flex:1;font-size:13px}.tf-opt em{font-style:normal;font-weight:700;color:#16a34a;font-size:13px}
.tf-grid{display:grid;grid-template-columns:repeat(5,1fr);gap:8px;margin-bottom:12px}
.tf-num{border:1.5px solid var(--ln);background:var(--so);color:var(--ac);border-radius:12px;padding:13px 0;font:800 17px inherit;cursor:pointer}.tf-num:hover{background:linear-gradient(135deg,var(--ac),var(--ac2));color:#fff}
.tf-choice{display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-top:6px}
.tf-big{border:2px solid var(--ln);background:var(--so);color:var(--tx);border-radius:18px;padding:22px 10px;cursor:pointer;display:flex;flex-direction:column;align-items:center;gap:6px;font:inherit}
.tf-big svg{width:30px;height:30px;color:var(--ac)}.tf-big b{font-size:16px}.tf-big small{color:var(--mu);font-size:11px}.tf-big:hover{border-color:var(--ac);transform:translateY(-4px)}
.tf-big.fin{background:linear-gradient(135deg,#16a34a,#0d9488);color:#fff;border-color:transparent}.tf-big.fin svg,.tf-big.fin small{color:#fff}
.tf-row{display:flex;align-items:center;gap:8px;padding:10px 12px;border:1.5px solid var(--ln);border-radius:14px;margin-bottom:8px}
.tf-row .d{flex:1;min-width:0;display:flex;flex-direction:column}.tf-row .d b{font-family:Consolas,monospace;font-size:13px;color:var(--ac)}.tf-row .d small{color:var(--mu);font-size:12px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.tf-qty{display:flex;align-items:center;gap:6px;font-weight:800}.tf-sub{min-width:70px;text-align:right;font-size:13px}
.tf-mini{width:30px;height:30px;border:none;border-radius:9px;background:var(--so);color:var(--ac);cursor:pointer;display:flex;align-items:center;justify-content:center;padding:0;font:800 16px inherit}.tf-mini svg{width:15px;height:15px}
.tf-mini:hover{background:var(--ac);color:#fff}.tf-mini.del:hover{background:#dc2626}.tf-mini:disabled{opacity:.3;cursor:default}
.tf-total{display:flex;justify-content:space-between;padding:12px 4px;font-weight:800;font-size:18px}
#tfFoot{display:flex;gap:10px;align-items:center;justify-content:space-between;padding:12px 26px 22px}.tf-hint{color:var(--mu);font-size:12px}
.tf-btn{border:none;border-radius:14px;padding:13px 20px;font:700 15px 'Segoe UI',Arial,sans-serif;cursor:pointer;display:inline-flex;align-items:center;gap:8px}.tf-btn svg{width:18px;height:18px}
.tf-btn.pri{background:linear-gradient(135deg,var(--ac),var(--ac2));color:#fff;box-shadow:0 6px 18px rgba(124,58,237,.4);margin-left:auto!important}.tf-btn.ghost{background:var(--so);color:var(--tx)}.tf-btn:disabled{opacity:.4;cursor:default}
.tf-cal{max-width:340px;margin:0 auto 4px;background:var(--so);border:1.5px solid var(--ln);border-radius:20px;padding:14px}
.tf-calh{display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;font-size:16px}
.tf-wd,.tf-days{display:grid;grid-template-columns:repeat(7,1fr);gap:4px;text-align:center}.tf-wd span{font-size:11px;font-weight:700;color:var(--mu);padding:4px 0}
.tf-day{border:none;background:transparent;color:var(--tx);border-radius:12px;height:38px;padding:0;font-weight:700;font-size:14px;cursor:pointer}
.tf-day:hover:not(:disabled){background:var(--ac);color:#fff}.tf-day.now{box-shadow:inset 0 0 0 2px var(--ac2)}.tf-day.sel{background:linear-gradient(135deg,var(--ac),var(--ac2));color:#fff}.tf-day:disabled{opacity:.25;cursor:default}
@media print{#tfFab,#tfOverlay{display:none!important}}`;
document.head.appendChild(style);

const fab = document.createElement('button'); fab.id = 'tfFab'; fab.type = 'button'; fab.innerHTML = IC.bolt + '<span>Quick Entry</span>'; document.body.appendChild(fab);
const overlay = document.createElement('div'); overlay.id = 'tfOverlay';
overlay.innerHTML = `<div id="tfCard"><div id="tfBar"><i></i></div><div id="tfTop"><span id="tfLbl"></span><button id="tfX" type="button" data-a="close">${IC.x}</button></div><div id="tfStage"></div><div id="tfFoot"></div></div>`;
document.body.appendChild(overlay);
const card = overlay.firstElementChild, $ = s => card.querySelector(s);

let locked=false, calY=0, calM=0, step=0, rows=[], cur=null, inv='', date='', hotel='', unit='', pre=false, sug=[], conf=0, editIdx=-1, back7=false, dupe=false, hl=0, matches=[], tm;
const guess = (i, u) => LCPredict.ask(i, u).then(d => { conf = d.conf || 0; return (d.cands || []).filter(c => okH(c.h)); });
const sugHTML = () => sug.slice(0, 3).map((c, i) => `<button type="button" class="tf-sg" data-a="sug" data-i="${i}"><span><b>${esc(c.h)}</b> · Unit ${esc(c.u)}</span><em>${c.p}%</em></button>`).join('');
const unitChips = () => (hotelUnitMap[hotel] || []).map(u => `<button type="button" class="tf-chip" data-a="u" data-v="${u}">${u}</button>`).join('');
const w2 = () => { const n = $('#tfNext'); if (n) n.disabled = !(hotel && unit); };

function calHTML() {
    const mn = minDate(), mx = today(), MN = ['January','February','March','April','May','June','July','August','September','October','November','December'];
    const off = (new Date(calY, calM, 1).getDay() + 6) % 7, days = new Date(calY, calM + 1, 0).getDate(), idx = calY * 12 + calM;
    const a = new Date(mn + 'T00:00'), b = new Date(mx + 'T00:00');
    let cells = '<span></span>'.repeat(off);
    for (let d = 1; d <= days; d++) { const iso = `${calY}-${String(calM+1).padStart(2,'0')}-${String(d).padStart(2,'0')}`;
        cells += `<button type="button" class="tf-day${iso===date?' sel':''}${iso===mx?' now':''}" data-a="day" data-v="${iso}" ${iso<mn||iso>mx?'disabled':''}>${d}</button>`; }
    return `<div class="tf-cal"><div class="tf-calh"><button type="button" class="tf-mini" data-a="pm" ${idx<=a.getFullYear()*12+a.getMonth()?'disabled':''}>‹</button><b>${MN[calM]} ${calY}</b><button type="button" class="tf-mini" data-a="nm" ${idx>=b.getFullYear()*12+b.getMonth()?'disabled':''}>›</button></div>
    <div class="tf-wd">${['Mo','Tu','We','Th','Fr','Sa','Su'].map(x=>`<span>${x}</span>`).join('')}</div><div class="tf-days">${cells}</div></div>`;
}

function go(n) { step = n; if (n === 6) back7 = false; render(); }

function render(noAnim) {
    $('#tfBar i').style.width = PCT[step] + '%';
    $('#tfLbl').textContent = (locked ? '#' + inv + ' · ' : '') + LBL[step] + (rows.length ? ` · ${rows.length} line${rows.length>1?'s':''}` : '');
    const st = $('#tfStage'), ft = $('#tfFoot'); st.classList.toggle('noanim', !!noAnim);
    const back = `<button class="tf-btn ghost" data-a="back">${IC.back}Back</button>`;
    const next = d => `<button class="tf-btn pri" id="tfNext" data-a="next" ${d?'disabled':''}>Next ${IC.arrow}</button>`;
    if (step === 0) {
        st.innerHTML = `<div class="tf-step"><div class="tf-ico">${IC.inv}</div><div class="tf-q">What's the invoice number?</div><div class="tf-h">5 digits from the slip, e.g. 10042</div>
        <input id="tfInv" class="tf-in" inputmode="numeric" maxlength="5" placeholder="00000" autocomplete="off" value="${inv}"><div class="tf-st" id="tfSt"></div></div>`;
        ft.innerHTML = `<span class="tf-hint">Continues automatically at 5 digits</span>${next(true)}`; chk(false);
    } else if (step === 1) {
        date = date || today(); const dd = new Date(date + 'T00:00'); calY = dd.getFullYear(); calM = dd.getMonth();
        st.innerHTML = `<div class="tf-step" style="text-align:center"><div class="tf-ico" style="margin:0 auto">${IC.cal}</div><div class="tf-q">When was it received?</div><div class="tf-h">Pick a day from the last 2 months</div><div id="tfCal">${calHTML()}</div>
        <div class="tf-chips" style="justify-content:center"><button class="tf-chip" data-a="d0">${IC.cal}Today</button><button class="tf-chip" data-a="d1">${IC.back}Yesterday</button></div></div>`;
        ft.innerHTML = back + next(false);
    } else if (step === 2) {
        st.innerHTML = `<div class="tf-step"><div class="tf-ico">${IC.pin}</div><div class="tf-q">Which hotel & unit?</div>
        <div class="tf-h">${sug.length ? 'Learned from nearby invoices. Tap one, or choose below.' : 'Pick the hotel, then the unit.'}</div>
        <div id="tfSug">${sugHTML()}</div>
        <select id="tfHotel" class="tf-in">${getHotelOptionsHTML(hotel)}</select>
        <input id="tfUnit" class="tf-in" style="margin-top:10px!important" inputmode="numeric" placeholder="Unit number" value="${esc(unit)}" autocomplete="off">
        <div class="tf-chips" id="tfUnits">${unitChips()}</div></div>`;
        ft.innerHTML = back + next(!(hotel && unit));
    } else if (step === 3) {
        st.innerHTML = `<div class="tf-step"><div class="tf-ico">${IC.tag}</div><div class="tf-q">${editIdx>=0?'Change the item':'Which item?'}</div><div class="tf-h">Type a code or name · ↑↓ move · Enter picks</div>
        <input id="tfCode" class="tf-in" placeholder="e.g. TWL or towel" autocomplete="off"><div class="tf-list" id="tfList"></div></div>`;
        ft.innerHTML = back + `<button class="tf-btn pri" id="tfNext" data-a="pickhl" disabled>Next ${IC.arrow}</button>`; hl = 0; fillList();
    } else if (step === 4) {
        st.innerHTML = `<div class="tf-step"><div class="tf-ico">${IC.qty}</div><div class="tf-q">How many?</div><div class="tf-h"><b>${esc(cur.code)}</b> · ${esc(cur.description)} · ${money(cur.price)} each</div>
        <div class="tf-grid">${[1,2,3,4,5,6,7,8,9,10].map(n=>`<button class="tf-num" data-a="qty" data-v="${n}">${n}</button>`).join('')}</div>
        <input id="tfQty" type="number" min="1" max="99" class="tf-in" placeholder="Or type a number, then Enter"></div>`;
        ft.innerHTML = back + `<button class="tf-btn pri" id="tfNext" data-a="qtyin" disabled>Next ${IC.arrow}</button>`;
    } else if (step === 5) {
        const l = rows[rows.length - 1];
        st.innerHTML = `<div class="tf-step"><div class="tf-ico">${IC.check}</div><div class="tf-q">Line added</div><div class="tf-h"><b>${esc(l.code)}</b> · ${esc(l.description)} × ${l.qty} = <b>${money(l.price*l.qty)}</b></div>
        <div class="tf-choice"><button class="tf-big" data-a="more">${IC.plus}<b>Add next line</b><small>press N</small></button><button class="tf-big fin" data-a="review">${IC.check}<b>Complete</b><small>press Enter</small></button></div></div>`;
        ft.innerHTML = '<span class="tf-hint">Next line keeps the same invoice, hotel, unit and date</span>';
    } else {
        const tot = rows.reduce((s, r) => s + r.price * r.qty, 0);
        st.innerHTML = `<div class="tf-step"><div class="tf-q" style="margin-top:4px">Review your invoice</div><div class="tf-chips" style="margin:0 0 14px">${locked
          ? `<span class="tf-chip">#${esc(inv)}</span><span class="tf-chip">${esc(hotel)} · ${esc(unit)}</span><span class="tf-chip">${esc(date)}</span>`
          : `<button class="tf-chip" data-a="einv">${IC.edit}#${esc(inv)}</button><button class="tf-chip" data-a="ewhere">${IC.edit}${esc(hotel)} · ${esc(unit)}</button><button class="tf-chip" data-a="edate">${IC.edit}${esc(date)}</button>`}</div>
        ${rows.length ? rows.map((r,i)=>`<div class="tf-row"><div class="d"><b>${esc(r.code)}</b><small>${esc(r.description)} · ${money(r.price)}</small></div>
          <div class="tf-qty"><button class="tf-mini" data-a="dec" data-i="${i}">−</button><span>${r.qty}</span><button class="tf-mini" data-a="inc" data-i="${i}">+</button></div><b class="tf-sub">${money(r.price*r.qty)}</b>
          <button class="tf-mini" data-a="ecode" data-i="${i}">${IC.edit}</button><button class="tf-mini del" data-a="del" data-i="${i}">${IC.trash}</button></div>`).join('') : '<div class="tf-h">No lines yet. Add one.</div>'}
        <div class="tf-total"><span>Grand total</span><span>${money(tot)}</span></div></div>`;
        ft.innerHTML = `<button class="tf-btn ghost" data-a="more">${IC.plus}Add more lines</button><button class="tf-btn pri" data-a="fill" ${rows.length?'':'disabled'}>${IC.check}Fill invoice</button>`;
    }
    setTimeout(() => { const el = $(['#tfInv', '#tfUnit', '#tfCode', '#tfQty'][[0,2,3,4].indexOf(step)] || '#tfFoot'); if (el && el.focus) { el.focus(); if (step === 0 || step === 4) el.select?.(); } }, 60);
}

function chk(auto) {
    const my = inv, st = $('#tfSt'), nx = $('#tfNext'); if (!st || !nx) return;
    st.className = 'tf-st'; st.textContent = ''; nx.disabled = true; if (inv.length < 5) return;
    const n = parseInt(inv, 10); if (n < 1 || n > 50000) { st.className = 'tf-st bad'; st.textContent = 'Must be between 00001 and 50000'; return; }
    st.textContent = 'Checking…';
    guess(inv, '').then(c => { sug = c; if (c[0] && (!hotel || pre) && conf >= .75) { hotel = c[0].h; unit = String(c[0].u); pre = true; }
        if (step === 2 && !unit) render(true); });
    fetch('php/check_invoice.php?invoice_number=' + inv).then(r => r.json()).then(d => {
        if (my !== inv || step !== 0) return;
        if (d.exists) { dupe = true; st.className = 'tf-st bad'; st.textContent = `Invoice #${inv} already exists. Try another.`; }
        else { dupe = false; st.className = 'tf-st ok'; st.textContent = '✓ Number is free'; nx.disabled = false;
            if (auto) setTimeout(() => { if (step === 0 && my === inv && !dupe) act('next'); }, 450); }
    }).catch(() => { if (my === inv && step === 0) { st.textContent = ''; nx.disabled = false; } });
}

function fillList() {
    const q = ($('#tfCode').value || '').trim().toLowerCase();
    matches = priceList.filter(i => !q || i.code.toLowerCase().includes(q) || i.description.toLowerCase().includes(q)).slice(0, 50);
    if (hl >= matches.length) hl = 0;
    $('#tfList').innerHTML = matches.length ? matches.map((m, i) => `<div class="tf-opt${i===hl?' hl':''}" data-a="pick" data-c="${esc(m.code)}"><b>${esc(m.code)}</b><span>${esc(m.description)}</span><em>${money(m.price)}</em></div>`).join('') : '<div class="tf-opt">No match. Try fewer letters.</div>';
    $('#tfList .hl')?.scrollIntoView({ block: 'nearest' });
    const nx = $('#tfNext'); if (nx) nx.disabled = !(q && matches.length);
}
function pick(code) {
    const it = priceList.find(p => p.code === code); if (!it) return;
    if (editIdx >= 0) { rows[editIdx] = { ...it, qty: rows[editIdx].qty }; editIdx = -1; go(6); } else { cur = { ...it, qty: null }; go(4); }
}
function commit(v) { if (!cur || !(v > 0)) return; cur.qty = Math.min(v, 99); rows.push(cur); cur = null; go(5); }

function act(a, d = {}) {
    switch (a) {
        case 'close': close(); break;
        case 'back': if (back7) go(6); else if (step === 3 && rows.length) go(5); else if (step === 3 && locked) close(); else if (step === 3) go(2); else if (step === 0) close(); else go(step - 1); break;
        case 'next':
            if (step === 0) { if (!(inv.length === 5 && !dupe)) return; go(back7 ? 6 : 1); }
            else if (step === 1) go(back7 ? 6 : 2);
            else if (step === 2) { if (hotel && unit) go(back7 ? 6 : 3); }
            break;
        case 'd0': case 'd1': { const dt = new Date(); if (a === 'd1') dt.setDate(dt.getDate() - 1); date = dt.toISOString().split('T')[0]; calY = dt.getFullYear(); calM = dt.getMonth(); $('#tfCal').innerHTML = calHTML(); break; }
        case 'day': date = d.v; $('#tfCal').innerHTML = calHTML(); setTimeout(() => { if (step === 1 && date === d.v) act('next'); }, 350); break;
        case 'pm': case 'nm': calM += a === 'nm' ? 1 : -1; if (calM < 0) { calM = 11; calY--; } if (calM > 11) { calM = 0; calY++; } $('#tfCal').innerHTML = calHTML(); break;
        case 'sug': { const c = sug[+d.i]; hotel = c.h; unit = String(c.u); pre = false; go(back7 ? 6 : 3); break; }
        case 'u': unit = d.v; pre = false; $('#tfUnit').value = unit; w2(); if (hotel) act('next'); break;
        case 'pick': pick(d.c); break;
        case 'pickhl': { const q = ($('#tfCode').value || '').trim().toLowerCase(); const m = matches.find(x => x.code.toLowerCase() === q) || matches[hl]; if (m) pick(m.code); break; }
        case 'qty': commit(+d.v); break;
        case 'qtyin': commit(parseInt($('#tfQty').value)); break;
        case 'more': editIdx = -1; go(3); break;
        case 'review': go(6); break;
        case 'dec': if (rows[d.i].qty > 1) rows[d.i].qty--; render(true); break;
        case 'inc': if (rows[d.i].qty < 99) rows[d.i].qty++; render(true); break;
        case 'del': rows.splice(+d.i, 1); render(true); break;
        case 'ecode': editIdx = +d.i; back7 = true; go(3); break;
        case 'einv': back7 = true; go(0); break;
        case 'edate': back7 = true; go(1); break;
        case 'ewhere': back7 = true; go(2); break;
        case 'fill': if (rows.length) fill(); break;
    }
}

card.addEventListener('click', e => { const b = e.target.closest('[data-a]'); if (b && !b.disabled) act(b.dataset.a, b.dataset); });
card.addEventListener('input', e => {
    const id = e.target.id;
    if (id === 'tfInv') { e.target.value = e.target.value.replace(/\D/g, '').slice(0, 5); inv = e.target.value; dupe = false; pre = pre; chk(true); }
    if (id === 'tfCode') { hl = 0; fillList(); }
    if (id === 'tfQty') $('#tfNext').disabled = !(parseInt(e.target.value) > 0);
    if (id === 'tfUnit') { unit = e.target.value.trim(); pre = false; w2(); clearTimeout(tm);
        if (unit && !hotel) tm = setTimeout(() => guess(inv.length >= 4 ? inv : '', unit).then(c => { if (step === 2 && unit && !hotel) { sug = c; $('#tfSug').innerHTML = sugHTML(); } }), 200); }
});
card.addEventListener('change', e => { if (e.target.id === 'tfHotel') { hotel = e.target.value; pre = false; $('#tfUnits').innerHTML = unitChips(); w2(); } });
document.addEventListener('keydown', e => {
    if (!overlay.classList.contains('on')) return;
    const id = e.target.id, k = e.key;
    if (k === 'Escape') { close(); return; }
    if (id === 'tfCode') {
        if (k === 'ArrowDown') { e.preventDefault(); hl = Math.min(hl + 1, matches.length - 1); fillList(); }
        else if (k === 'ArrowUp') { e.preventDefault(); hl = Math.max(hl - 1, 0); fillList(); }
        else if (k === 'Enter' && matches[hl]) { e.preventDefault(); pick(matches[hl].code); }
        return;
    }
    if (k === 'Enter') {
        if (id === 'tfQty') act('qtyin');
        else if (id === 'tfInv' || id === 'tfUnit') { if (!$('#tfNext')?.disabled) act('next'); }
        else if (e.target.tagName !== 'BUTTON') { if (step === 5) act('review'); else if (step === 6) act('fill'); else if (step === 1) act('next'); }
    } else if ((k === 'n' || k === 'N') && step === 5) act('more');
});

function fill() {
    const table = document.getElementById('invoiceTable'), row1 = table.rows[0], invEl = document.getElementById('invoice_number');
    invEl.value = inv; invEl.classList.remove('invalid');
    if (!locked) {
        const h = row1.querySelector('.hotel'); h.value = hotel; updateUnitDatalist(h);
        row1.querySelector('.unit-number').value = unit; row1.querySelector('.date-received').value = date;
    }
    let first = !row1.querySelector('.code').value.trim() && !row1.querySelector('.quantity').value.trim();
    rows.forEach(r => {
        const tot = (r.price * r.qty).toFixed(2), pr = r.price.toFixed(2);
        if (first) {
            first = false;
            row1.querySelector('.code').value = r.code; row1.querySelector('.description').value = r.description;
            row1.querySelector('.price').value = pr; row1.querySelector('.quantity').value = r.qty; row1.querySelector('.total').value = tot;
        } else {
            const row = table.insertRow();
            row.innerHTML = `
            <td><select class="hotel" name="hotel" disabled>${getHotelOptionsHTML(hotel)}</select><input type="hidden" name="hotel" value="${esc(hotel)}"></td>
            <td><input type="number" name="unit-number" class="unit-number" value="${esc(unit)}" readonly></td>
            <td><input type="text" class="code" value="${esc(r.code)}"></td>
            <td><input type="text" class="description" readonly value="${esc(r.description)}"></td>
            <td><input type="number" class="price" readonly value="${pr}"></td>
            <td><input type="number" class="quantity" min="1" value="${r.qty}"></td>
            <td><input type="number" class="total" readonly value="${tot}"></td>
            <td><input type="date" class="date-received" name="date_received" value="${date}" readonly></td>`;
            const q = row.querySelector('.quantity'); q.addEventListener('input', () => calculateTotal(q));
            row.querySelectorAll('input, select').forEach(el => { el.addEventListener('focus', () => el.setAttribute('data-touched', 'true')); el.addEventListener('blur', checkInputs); });
        }
    });
    table.querySelectorAll('.date-received').forEach(d => d.value = date);
    updateGrandTotal(); checkInputs();
    const n = rows.length; close();
    showPopup('success', `${n} line${n > 1 ? 's' : ''} added to invoice #${inv}.`, 'Check the table, then press Save Invoice.', 5000);
}

function close() { overlay.classList.remove('on'); back7 = false; editIdx = -1; }
function open() {
    const t = document.getElementById('invoiceTable'), r1 = t.rows[0];
    const has = [...t.rows].some(r => r.querySelector('.code')?.value.trim() || r.querySelector('.quantity')?.value.trim());
    inv = (document.getElementById('invoice_number')?.value || '').replace(/\D/g, '');
    date = r1?.querySelector('.date-received')?.value || today();
    locked = has && inv.length === 5;
    hotel = locked ? (r1.querySelector('.hotel')?.value || '') : ''; unit = locked ? (r1.querySelector('.unit-number')?.value || '') : '';
    rows = []; cur = null; sug = []; pre = false; editIdx = -1; back7 = false; dupe = false;
    overlay.classList.add('on'); go(locked ? 3 : 0);
}
fab.addEventListener('click', open);
setTimeout(open, 300);
})();
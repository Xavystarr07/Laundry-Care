(function () {
const $ = s => document.querySelector(s);
const api = (a, d = {}) => fetch('php/prices_api.php', { method: 'POST', body: new URLSearchParams({ a, ...d }) }).then(r => r.json());
const esc = s => String(s ?? '').replace(/[&<>"]/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' }[c]));
const on = v => v === true || v === 't' || v === 'true' || v === 1 || v === '1';
let rows = [];

function toast(m, bad) {
    const t = document.createElement('div');
    t.className = 'toast' + (bad ? ' bad' : ''); t.textContent = m;
    document.body.appendChild(t); setTimeout(() => t.remove(), 3000);
}

function draw() {
    const q = $('#q').value.trim().toLowerCase();
    const list = rows.filter(r => !q || r.code.toLowerCase().includes(q) || r.description.toLowerCase().includes(q));
    $('#tb').innerHTML = list.length
        ? '<thead><tr><th>Code</th><th>Description</th><th>Price (R)</th><th></th></tr></thead><tbody>' +
          list.map(r => `<tr data-c="${esc(r.code)}" class="${on(r.active) ? '' : 'off'}"><td><b>${esc(r.code)}</b></td>
            <td><input class="in" data-f="description" value="${esc(r.description)}"></td>
            <td><input class="in" data-f="price" type="number" step="0.01" min="0" value="${Number(r.price).toFixed(2)}"></td>
            <td class="acts"><button type="button" class="ib" data-a="save" title="Save">✓</button><button type="button" class="ib" data-a="toggle" title="${on(r.active) ? 'Disable' : 'Enable'}">⏻</button><button type="button" class="ib" data-a="hist" title="Price history">🕘</button></td></tr>`).join('') + '</tbody>'
        : '<tbody><tr><td class="empty">No items found</td></tr></tbody>';
}

async function load() {
    try {
        const r = await api('list');
        if (!r.ok) return toast(r.msg, true);
        rows = r.rows; draw();
    } catch (e) { toast('Could not load prices.', true); }
}

$('#q').addEventListener('input', draw);
$('#tb').addEventListener('input', e => e.target.closest('tr')?.classList.add('dirty'));
$('#tb').addEventListener('click', async e => {
    const b = e.target.closest('[data-a]'); if (!b) return;
    const tr = b.closest('tr'), code = tr.dataset.c;
    if (b.dataset.a === 'save') {
        const r = await api('save', { code, description: tr.querySelector('[data-f=description]').value, price: tr.querySelector('[data-f=price]').value });
        if (!r.ok) return toast(r.msg, true);
        toast('Saved'); load();
    } else if (b.dataset.a === 'toggle') {
        const r = await api('toggle', { code });
        if (!r.ok) return toast(r.msg, true);
        load();
    } else {
        const r = await api('history', { code });
        if (!r.ok) return toast(r.msg, true);
        $('#hTitle').textContent = 'Price history: ' + code;
        $('#hBody').innerHTML = r.rows.length
            ? '<table class="mt"><thead><tr><th>Date</th><th class="n">Old</th><th class="n">New</th></tr></thead><tbody>' +
              r.rows.map(x => `<tr><td>${esc(String(x.changed_at).slice(0, 16))}</td><td class="n">${x.old_price == null ? '—' : Number(x.old_price).toFixed(2)}</td><td class="n">${Number(x.new_price).toFixed(2)}</td></tr>`).join('') + '</tbody></table>'
            : '<p class="note">No changes recorded yet.</p>';
        $('#hm').hidden = false;
    }
});

$('#addBtn').addEventListener('click', () => { $('#addBox').hidden = !$('#addBox').hidden; });
$('#aGo').addEventListener('click', async () => {
    const r = await api('add', { code: $('#aCode').value, description: $('#aDesc').value, price: $('#aPrice').value });
    if (!r.ok) return toast(r.msg, true);
    ['#aCode', '#aDesc', '#aPrice'].forEach(s => $(s).value = '');
    toast('Item added'); load();
});
$('#hClose').addEventListener('click', () => { $('#hm').hidden = true; });
$('#hm').addEventListener('click', e => { if (e.target.id === 'hm') $('#hm').hidden = true; });
document.addEventListener('keydown', e => { if (e.key === 'Escape') $('#hm').hidden = true; });
load();
})();

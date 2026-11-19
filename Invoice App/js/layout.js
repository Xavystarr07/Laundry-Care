// layout.js — top-right nav (Saved / Prices / Hotels / Statements) + tidy action row
(function () {
const I = p => `<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">${p}</svg>`;
const inv = !!document.getElementById('invoiceForm');
const go = u => (typeof guardGo === 'function' ? guardGo(u) : (location.href = u));
const items = [];
if (inv) items.push(
    ['Saved', '#f43f5e', '#fb7185', I('<path d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/>'), () => go('saved.php')],
    ['Prices', '#65a30d', '#a3e635', I('<path d="M20.6 13.4l-7.2 7.2a2 2 0 0 1-2.8 0L2 12V2h10l8.6 8.6a2 2 0 0 1 0 2.8z"/><circle cx="7" cy="7" r="1.2"/>'), () => go('prices.php')]);
items.push(
    ['Hotels', '#0ea5e9', '#38bdf8', I('<path d="M3 21h18M5 21V7l7-4 7 4v14M9 21v-6h6v6"/>'), () => window.open('hotels.php', '_blank')],
    ['Statements', '#0d9488', '#34d399', I('<path d="M5 12h14M13 6l6 6-6 6"/>'), () => (document.getElementById('monthlyStatsFab') || document.getElementById('statementsFab'))?.click()]);

function build() {
    if (document.querySelector('.lc-nav')) return;
    document.querySelectorAll('.btn-row .uv-btn[onclick], .dep-actions .uv-btn[onclick]').forEach(b => {
        if (/saved\.php|prices\.php|hotels\.php/.test(b.getAttribute('onclick'))) b.remove();
    });
    const row = document.querySelector('.btn-row');
    if (row) ['.add-item', '.custom-item', '#checklistBtn', '#insertImageBtn', '.print-btn'].forEach(s => { const e = row.querySelector(s); if (e) row.appendChild(e); });
    const tf = document.getElementById('tfFab');
    if (row && tf && !document.getElementById('tfBtn')) {
        const q = document.createElement('button'); q.type = 'button'; q.className = 'uv-btn'; q.id = 'tfBtn';
        q.innerHTML = I('<path d="M13 2 4 14h7l-1 8 9-12h-7z"/>').replace('width="16" height="16"', 'class="uv-icon"') + '<span>Quick Entry</span>';
        q.addEventListener('click', () => tf.click());
        row.insertBefore(q, row.firstChild);
    }
    const nav = document.createElement('nav'); nav.className = 'lc-nav';
    items.forEach(([t, c, d, ic, fn]) => {
        const b = document.createElement('button'); b.type = 'button';
        b.style.setProperty('--ac', c); b.style.setProperty('--acd', d);
        b.innerHTML = ic + '<span>' + t + '</span>'; b.addEventListener('click', fn); nav.appendChild(b);
    });
    document.body.appendChild(nav);
}
if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', build); else build();
})();

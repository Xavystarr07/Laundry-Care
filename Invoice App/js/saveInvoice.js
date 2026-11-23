// saveInvoice.js
// - Enforces exactly 5 digits on save attempt (popup if not)
// - Checks for duplicate invoice number live after 5 digits typed
// - On valid save: normal form submit → page refresh → popup shown from session

document.addEventListener('DOMContentLoaded', function () {

    const invoiceInput = document.getElementById('invoice_number');
    const form         = document.getElementById('invoiceForm');
    const saveBtn      = document.getElementById('saveInvoiceBtn');
    let lastCheckedVal = '';
    window.lcTest = false;
    document.getElementById('testModeBtn')?.addEventListener('click', function () {
        window.lcTest = !window.lcTest;
        this.style.background = window.lcTest ? '#f97316' : '';
        this.style.color = window.lcTest ? '#fff' : '';
        this.querySelector('span').textContent = window.lcTest ? 'Test mode ON' : 'Test mode';
        showPopup('info', window.lcTest ? 'Test mode ON. Save runs every check but adds nothing.' : 'Test mode OFF. Save adds invoices for real.', '', 4000);
    });
    let isDuplicate    = false;

    // ── Show any pending session popup (after page refresh) ───────────────────
    if (window._pendingPopup) {
        const p = window._pendingPopup;
        // Small delay so popup system is fully ready
        setTimeout(() => showPopup(p.type, p.msg, p.tip || '', p.type === 'success' ? 7000 : 0), 400);
    }

    // ── Live duplicate check: fires once after 5th digit typed ────────────────
    if (invoiceInput) {
        invoiceInput.addEventListener('input', function () {
            const val = this.value.replace(/\D/g, '');
            isDuplicate = false;
            invoiceInput.classList.remove('invalid');

            if (val.length === 5 && val !== lastCheckedVal) {
                lastCheckedVal = val;
                checkInvoiceExists(val);
            }
        });
    }

    function checkInvoiceExists(number) {
        fetch(`php/check_invoice.php?invoice_number=${encodeURIComponent(number)}`)
            .then(r => r.json())
            .then(data => {
                if (data.exists) {
                    isDuplicate = true;
                    invoiceInput.classList.add('invalid');
                    showPopup(
                        'dupe',
                        `Beep boop! Invoice #${number} already exists in the database 🤖`,
                        '💡 Try a different invoice number before saving.',
                        0
                    );
                }
            })
            .catch(() => { /* silent — don't block workflow */ });
    }

    // ── Intercept form submit for client-side validation ──────────────────────
    if (form) {
        form.addEventListener('submit', function (e) {
            const val    = (invoiceInput?.value || '').replace(/\D/g, '');
            const numVal = parseInt(val, 10);

            // Must be exactly 5 digits (or fewer but > 0 — up to 50000)
            if (val.length === 0) {
                e.preventDefault();
                showPopup('error',
                    'Invoice number is empty! 😟',
                    '💡 Enter a number between 1 and 50000 (up to 5 digits).',
                    0);
                invoiceInput?.focus();
                return;
            }

            if (val.length < 5) {
                e.preventDefault();
                showPopup('error',
                    `"${val}" is too short — invoice numbers must be exactly 5 digits.`,
                    `💡 For example, if your number is ${val}, enter it as ${val.padStart(5,'0')}.`,
                    0);
                invoiceInput?.classList.add('invalid');
                invoiceInput?.focus();
                return;
            }

            if (numVal < 1 || numVal > 50000) {
                e.preventDefault();
                showPopup('error',
                    `Invoice #${val} is out of range.`,
                    '💡 Invoice numbers must be between 00001 and 50000.',
                    0);
                invoiceInput?.classList.add('invalid');
                invoiceInput?.focus();
                return;
            }

            if (isDuplicate) {
                e.preventDefault();
                showPopup('dupe',
                    `Invoice #${val} already exists in the database.`,
                    '💡 Choose a different invoice number.',
                    0);
                invoiceInput?.focus();
                return;
            }

let bad = null;
            const r1 = document.querySelector('#invoiceTable tr');
            if (!r1?.querySelector('.hotel')?.value) bad = ['Pick a hotel.', r1?.querySelector('.hotel')];
            else if (!r1.querySelector('.unit-number')?.value.trim()) bad = ['Enter the unit number.', r1.querySelector('.unit-number')];
            else if (!r1.querySelector('.date-received')?.value) bad = ['Pick a date.', r1.querySelector('.date-received')];
            else for (const r of document.querySelectorAll('#invoiceTable tr')) {
                const c = r.querySelector('.code'), q = r.querySelector('.quantity');
                const custom = !r.querySelector('.price')?.hasAttribute('readonly');
                if (!c?.value.trim()) { bad = ['Enter an item code.', c]; break; }
                if (!custom && !isCodeInPriceList(c.value)) { bad = [`"${c.value.trim()}" is not on the price list.`, c]; break; }
                if (!(parseInt(q?.value) > 0)) { bad = ['Enter a quantity.', q]; break; }
                if (!(parseFloat(r.querySelector('.total')?.value) > 0)) { bad = ['This row has no price or total.', c]; break; }
            }
            if (bad) {
                e.preventDefault();
                showPopup('error', bad[0], 'Fix it, then save again.', 0);
                bad[1]?.focus();
                return;
            }

            const items = [];
document.querySelectorAll('#invoiceTable tr').forEach(r => {
    const code = r.querySelector('.code')?.value.trim();
    if (code) items.push({ code, description: r.querySelector('.description')?.value || '', price: r.querySelector('.price')?.value || 0, quantity: r.querySelector('.quantity')?.value || 0, total: r.querySelector('.total')?.value || 0 });
});
document.getElementById('itemsJson').value = JSON.stringify(items);
            if (window.lcTest) {
                e.preventDefault();
                const fd = new FormData(form);
                fd.append('save_invoice', '1');
                fd.append('test_mode', '1');
                fetch(form.action, { method: 'POST', body: fd }).then(r => r.json()).then(p => {
                    p = p || { type: 'warning', msg: 'Test finished but gave no message.', tip: '' };
                    showPopup(p.type, 'TEST: ' + p.msg.replace(/^TEST OK: /, ''), p.tip || '', 0);
                }).catch(() => showPopup('error', 'Test could not reach the server.', '', 5000));
            }
        });
    }

});

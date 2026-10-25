// departure.js — Departure billing page logic
// Every departure has its OWN invoice number.
// Rows are validated in the browser; departure_handler.php re-validates
// everything and looks up the real prices itself.

(function () {

    const $ = id => document.getElementById(id);

    const invInput = $('depInv');
    const hotelSel = $('depHotel');
    const unitSel  = $('depUnit');
    const codeSel  = $('depCode');
    const dateInp  = $('depDate');
    const hintEl   = $('depHint');
    const body     = $('depBody');
    const form     = $('departureForm');

    const rows = [];      // pending departures: {inv, hotel, unit, dep, date}
    const dbTaken = {};   // cache of database checks: '12345' -> true / false

    // ── Helpers ───────────────────────────────────────────────────────────────
    const money = n => Number(n).toFixed(2);
    const esc = s => String(s).replace(/[&<>"']/g, c =>
        ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));

    const fmt = d => d.getFullYear() + '-' +
        String(d.getMonth() + 1).padStart(2, '0') + '-' +
        String(d.getDate()).padStart(2, '0');

    function dateLimits() {
        const today = new Date();
        const min   = new Date();
        min.setMonth(min.getMonth() - 2);
        return { min: fmt(min), max: fmt(today), today: fmt(today) };
    }

    const digits = s => String(s || '').replace(/\D/g, '').slice(0, 5);

    // Asks the database if an invoice number is already used (cached)
    function checkTaken(v, cb) {
        if (v.length !== 5) return;
        if (Object.prototype.hasOwnProperty.call(dbTaken, v)) { if (cb) cb(dbTaken[v]); return; }
        fetch('php/check_invoice.php?invoice_number=' + encodeURIComponent(v))
            .then(r => r.json())
            .then(d => { dbTaken[v] = !!d.exists; if (cb) cb(dbTaken[v]); })
            .catch(() => {});
    }

    // ── Dropdown builders ─────────────────────────────────────────────────────
    const hotelOpts = sel =>
        '<option value="">-- Hotel --</option>' +
        getDepHotels().map(h => `<option value="${esc(h)}"${h === sel ? ' selected' : ''}>${esc(h)}</option>`).join('');

    function unitOpts(hotel, sel) {
        const units = getDepUnits(hotel);
        const head  = !hotel ? 'Pick hotel first' : (units.length ? '-- Unit --' : 'No units listed');
        return `<option value="">${head}</option>` +
            units.map(u => `<option value="${u}"${String(u) === String(sel) ? ' selected' : ''}>${u}</option>`).join('');
    }

    const codeOpts = sel =>
        '<option value="">-- Departure --</option>' +
        Object.entries(DEP_PRICES).map(([c, d]) =>
            `<option value="${c}"${c === sel ? ' selected' : ''}>${c} — ${d.beds} bed (R${money(d.price)})</option>`).join('');

    // ── Validation ────────────────────────────────────────────────────────────
    function validateRow(r) {
        const v = digits(r.inv);
        if (!v.length) return 'Enter an invoice number.';
        if (v.length < 5) return `Invoice "${v}" is too short — it must be exactly 5 digits.`;
        const n = parseInt(v, 10);
        if (n < 1 || n > 50000) return `Invoice #${v} is out of range (1–50000).`;
        if (dbTaken[v] === true) return `Invoice #${v} already exists in the database.`;

        if (!r.hotel || !Object.prototype.hasOwnProperty.call(DEP_BEDS, r.hotel)) return 'Pick a hotel.';
        if (!r.unit) return 'Pick a unit number.';
        if (!isValidDepUnit(r.hotel, r.unit)) return `Unit ${r.unit} isn't listed for ${r.hotel}.`;
        if (!DEP_PRICES[r.dep]) return 'Pick a departure type (DEP1–DEP4).';
        if (!r.date) return 'Pick a date.';
        const L = dateLimits();
        if (r.date < L.min || r.date > L.max) return 'Date must be within the last 2 months and not in the future.';
        return '';
    }

    // Row error, including "same invoice number used on another line"
    function rowError(i) {
        const r = rows[i];
        const err = validateRow(r);
        if (err) return err;
        if (rows.some((x, j) => j !== i && digits(x.inv) === digits(r.inv)))
            return `Invoice #${digits(r.inv)} is used on more than one line.`;
        return '';
    }

    // ── Entry bar ─────────────────────────────────────────────────────────────
    const readEntry = () => ({
        inv: digits(invInput.value), hotel: hotelSel.value,
        unit: unitSel.value, dep: codeSel.value, date: dateInp.value
    });
    const entryHasData = () => !!(invInput.value || unitSel.value || codeSel.value);

    function whenInvChanges() {
        const start = invInput.value;
        return () => invInput.value !== start;
    }

    function updateHint() {
        const { hotel, unit } = readEntry();
        if (!hotel || !unit) { hintEl.textContent = ''; return; }
        const beds = getDepBeds(hotel, unit);
        hintEl.textContent = beds
            ? `✅ ${hotel} ${unit} is a ${beds} bed unit — DEP${beds} auto-selected (you can change it).`
            : `⚠️ Bedroom count unknown for ${hotel} ${unit} — please pick DEP1–DEP4 yourself.`;
    }

    // Invoice number typed in the entry bar
    invInput.addEventListener('input', () => {
        formatInvoiceNumber(invInput);
        invInput.classList.remove('invalid');
        const v = invInput.value;
        if (v.length !== 5) return;

        if (rows.some(x => digits(x.inv) === v)) {
            invInput.classList.add('invalid');
            showPopup('warning', `Invoice #${v} is already on the list.`,
                '💡 Every departure needs its own invoice number.', 0, whenInvChanges());
            return;
        }
        checkTaken(v, taken => {
            if (invInput.value !== v) return;            // typed something else meanwhile
            if (taken) {
                invInput.classList.add('invalid');
                showPopup('dupe', `Beep boop! Invoice #${v} already exists in the database 🤖`,
                    '💡 Try a different invoice number before adding.', 0, whenInvChanges());
            } else {
                hotelSel.focus();
            }
        });
    });

    hotelSel.addEventListener('change', () => {
        unitSel.innerHTML = unitOpts(hotelSel.value, '');
        codeSel.value = '';
        updateHint();
        if (hotelSel.value) unitSel.focus();
    });

    unitSel.addEventListener('change', () => {
        const auto = getDepCodeForUnit(hotelSel.value, unitSel.value);
        if (auto) codeSel.value = auto;
        updateHint();
        if (unitSel.value) codeSel.focus();
    });

    // Enter in the entry bar = Add More
    [invInput, hotelSel, unitSel, codeSel, dateInp].forEach(el =>
        el.addEventListener('keydown', e => { if (e.key === 'Enter') { e.preventDefault(); addEntry(); } }));

    function focusEntry() {
        document.querySelector('.dep-entry')?.scrollIntoView({ behavior: 'smooth', block: 'center' });
        invInput.focus({ preventScroll: true });
    }

    function resetEntry() {
        setTimeout(focusEntry, 0);
        invInput.value = '';
        invInput.classList.remove('invalid');
        unitSel.innerHTML = unitOpts(hotelSel.value, '');   // keeps hotel + date
        codeSel.value = '';
        hintEl.textContent = '';
        invInput.focus();                                    // next slip
    }

    function addEntry() {
        const r = readEntry();
        const err = validateRow(r);
        if (err) { showPopup('error', err, '💡 Fill in invoice number, hotel, unit, type and date.', 0, () => !validateRow(readEntry())); return false; }

        if (rows.some(x => digits(x.inv) === r.inv)) {
            showPopup('warning', `Invoice #${r.inv} is already on the list.`,
                '💡 Every departure needs its own invoice number.', 0, whenInvChanges());
            return false;
        }
        if (rows.some(x => x.hotel === r.hotel && String(x.unit) === String(r.unit) && x.date === r.date)) {
            showPopup('warning', `${r.hotel} ${r.unit} is already listed for ${r.date}.`,
                '💡 Edit the existing line instead of adding it twice.', 0);
            return false;
        }
        rows.push({ inv: r.inv, hotel: r.hotel, unit: String(r.unit), dep: r.dep, date: r.date });
        render();
        resetEntry();
        return true;
    }

    // ── Table ─────────────────────────────────────────────────────────────────
    function bedsCell(r) {
        const beds = getDepBeds(r.hotel, r.unit);
        if (!beds) return '—';
        const mismatch = DEP_PRICES[r.dep] && r.dep !== 'DEP' + beds
            ? `<span class="dep-warn">⚠ ${r.dep} chosen</span>` : '';
        return `${beds} bed${mismatch}`;
    }

    function render() {
        const L = dateLimits();

        if (!rows.length) {
            body.innerHTML = '<tr><td colspan="9" class="dep-empty">No departures added yet</td></tr>';
        } else {
            body.innerHTML = rows.map((r, i) => {
                const err   = rowError(i);
                const bad   = err ? ` class="dep-bad" title="${esc(err)}"` : '';
                const price = DEP_PRICES[r.dep] ? 'R ' + money(DEP_PRICES[r.dep].price) : '—';
                return `
                <tr data-i="${i}"${bad}>
                    <td>${i + 1}</td>
                    <td><input type="text" data-f="inv" maxlength="5" inputmode="numeric" placeholder="00000" value="${esc(r.inv)}"></td>
                    <td><select data-f="hotel">${hotelOpts(r.hotel)}</select></td>
                    <td><select data-f="unit">${unitOpts(r.hotel, r.unit)}</select></td>
                    <td class="dep-beds">${bedsCell(r)}</td>
                    <td><select data-f="dep">${codeOpts(r.dep)}</select></td>
                    <td class="dep-price">${price}</td>
                    <td><input type="date" data-f="date" value="${esc(r.date)}" min="${L.min}" max="${L.max}"></td>
                    <td><button type="button" class="dep-remove" data-remove="${i}" title="Remove line">✕</button></td>
                </tr>`;
            }).join('');
        }

        const total = rows.reduce((s, r) => s + (DEP_PRICES[r.dep]?.price || 0), 0);
        $('depGrandTotal').textContent = money(total);
        $('depCount').textContent = rows.length;
    }

    // Keep the invoice box digits-only while typing (no re-render, so focus stays)
    body.addEventListener('input', e => {
        if (e.target.dataset.f === 'inv') e.target.value = digits(e.target.value);
    });

    // Edit an existing line — options always come from existing data
    body.addEventListener('change', e => {
        const f  = e.target.dataset.f;
        const tr = e.target.closest('tr');
        if (!f || !tr) return;
        const r = rows[Number(tr.dataset.i)];
        if (!r) return;

        if (f === 'inv') {
            r.inv = digits(e.target.value);
            checkTaken(r.inv, () => render());
        } else if (f === 'hotel') {
            r.hotel = e.target.value;
            // keep the unit number if the new hotel has it (Bay 2 → Beach 2), else clear it
            if (!isValidDepUnit(r.hotel, r.unit)) r.unit = '';
            const auto = getDepCodeForUnit(r.hotel, r.unit);
            if (auto) r.dep = auto;
        } else if (f === 'unit') {
            r.unit = e.target.value;
            const auto = getDepCodeForUnit(r.hotel, r.unit);
            if (auto) r.dep = auto;
        } else if (f === 'dep') {
            r.dep = e.target.value;
        } else if (f === 'date') {
            r.date = e.target.value;
        }
        render();
    });

    body.addEventListener('click', e => {
        const btn = e.target.closest('[data-remove]');
        if (!btn) return;
        const i = Number(btn.dataset.remove);
        const r = rows[i];
        rows.splice(i, 1);
        render();
        showPopup('info', `Line ${i + 1} removed (${r.hotel} ${r.unit}).`, '', 2500);
    });

    // ── Get everything checked (used by Save and Print) ───────────────────────
    function prepare() {
        // Something typed in the entry bar? Add it first (or stop if it's invalid)
        if (entryHasData() && !addEntry()) return false;

        if (!rows.length) {
            showPopup('warning', 'No departures added yet.',
                '💡 Enter an invoice number, hotel, unit and type, then click Add More.', 0);
            return false;
        }
        for (let i = 0; i < rows.length; i++) {
            const err = rowError(i);
            if (err) {
                showPopup('error', `Line ${i + 1}: ${err}`, '💡 Fix the highlighted line first.', 0);
                return false;
            }
        }
        return true;
    }

    // ── Complete & save ───────────────────────────────────────────────────────
    function complete() {
        if (!prepare()) return;
        // Only codes are sent — the server looks up the prices
        $('depPayload').value = JSON.stringify({
            rows: rows.map(r => ({
                inv: digits(r.inv), hotel: r.hotel, unit: Number(r.unit), dep: r.dep, date: r.date
            }))
        });
        form.submit();
    }

    // ── Print (clean white page: header + departures table only) ──────────────
       function printDepartures() {
        if (!prepare()) return;

        const total = rows.reduce((s, r) => s + DEP_PRICES[r.dep].price, 0);

        const tableRows = rows.map(r => {
            const beds = getDepBeds(r.hotel, r.unit);
            return `
            <tr>
                <td class="c-code">${esc(digits(r.inv).padStart(5, '0'))}</td>
                <td>${esc(r.hotel)}</td>
                <td>${esc(r.unit)}</td>
                <td>${beds ? beds + ' bed' : '—'}</td>
                <td>${esc(r.dep)} — ${esc(DEP_PRICES[r.dep].label)}</td>
                <td class="num strong">R ${money(DEP_PRICES[r.dep].price)}</td>
                <td>${esc(r.date)}</td>
            </tr>`;
        }).join('');

        const win = window.open('', '_blank', 'width=1000,height=800');
        if (!win) {
            showPopup('warning', 'The print window was blocked.', 'Allow pop-ups for this site and try again.', 0);
            return;
        }

        win.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="UTF-8">
                <title>Departures</title>
                <style>
                    @page { size: A4; margin: 12mm; }
                    * { margin: 0; padding: 0; box-sizing: border-box; color-scheme: light !important; }
                    :root { --blue: #1565C0; --blue-dark: #0D47A1; --line: #D9D9D9; --ink: #111; --muted: #444; }
                    body {
                        font-family: 'Segoe UI', Arial, sans-serif; background: #fff !important; color: var(--ink) !important;
                        padding: 8px 10px; font-size: 13px;
                        -webkit-print-color-adjust: exact; print-color-adjust: exact;
                    }
                    .bar { height: 8px; background: linear-gradient(90deg, var(--blue-dark), #2196F3); border-radius: 4px; margin-bottom: 22px; }
                    .header { display: flex; justify-content: space-between; align-items: flex-start; }
                    .brand h1 { font-size: 28px; letter-spacing: 3px; text-transform: uppercase; color: #111; font-weight: 800; }
                    .brand .tag { margin-top: 2px; font-size: 10.5px; letter-spacing: 2px; color: var(--blue); font-weight: 700; }
                    .brand p { margin-top: 10px; font-size: 12.5px; line-height: 1.55; color: #222; }
                    .contact { text-align: right; font-size: 12.5px; line-height: 1.7; color: #222; }
                    .contact .web { color: var(--blue); font-weight: 700; }

                    .title-row { display: flex; justify-content: space-between; align-items: flex-end; margin: 26px 0 16px; padding-bottom: 10px; border-bottom: 2px solid #111; }
                    .title-row h2 { font-size: 26px; letter-spacing: 6px; color: #111; font-weight: 300; text-transform: uppercase; }
                    .count { text-align: right; }
                    .count span { display: block; font-size: 10.5px; letter-spacing: 1.5px; color: var(--muted); text-transform: uppercase; }
                    .count strong { font-size: 24px; letter-spacing: 2px; color: var(--blue-dark); }

                    table { width: 100%; border-collapse: collapse; }
                    thead { display: table-header-group; }
                    th {
                        background: #0d0c0e !important; color: #fff !important; padding: 10px 12px; text-align: left;
                        font-size: 11.5px; letter-spacing: 1px; text-transform: uppercase; font-weight: 700;
                        -webkit-print-color-adjust: exact; print-color-adjust: exact;
                    }
                    th.num, td.num { text-align: right; }
                    td { padding: 9px 12px; border-bottom: 1px solid var(--line); font-size: 13px; }
                    tbody tr:nth-child(even) td { background: #F7F7F7 !important; }
                    tr { page-break-inside: avoid; }
                    td.c-code { font-family: Consolas, 'Courier New', monospace; font-size: 12.5px; font-weight: 700; }
                    td.strong { font-weight: 700; }

                    .totals { display: flex; justify-content: flex-end; margin-top: 18px; page-break-inside: avoid; }
                    .totals-box { min-width: 280px; border: 2px solid #111; border-radius: 10px; overflow: hidden; }
                    .totals-row { display: flex; justify-content: space-between; padding: 8px 16px; font-size: 13px; color: var(--muted); }
                    .totals-grand {
                        display: flex; justify-content: space-between; align-items: center; padding: 13px 16px;
                        background: var(--blue-dark) !important; color: #fff !important;
                        -webkit-print-color-adjust: exact; print-color-adjust: exact;
                    }
                    .totals-grand span { font-size: 12px; letter-spacing: 2px; text-transform: uppercase; font-weight: 700; }
                    .totals-grand strong { font-size: 22px; }

                    .footer { margin-top: 34px; padding-top: 12px; border-top: 1px solid var(--line); text-align: center; color: var(--muted); font-size: 12px; page-break-inside: avoid; }
                    .footer strong { display: block; color: #111; font-size: 13px; margin-bottom: 3px; }
                    .footer em { letter-spacing: 2px; font-size: 10.5px; color: var(--blue); font-style: normal; font-weight: 700; }
                </style>
            </head>
            <body>
                <div class="bar"></div>

                <div class="header">
                    <div class="brand">
                        <h1>Laundry Care</h1>
                        <div class="tag">A FRESH START IN EVERY WASH</div>
                        <p>2 Tottenham Road<br>Parkgate<br>Cornubia</p>
                    </div>
                    <div class="contact">
                        <div class="web">www.laundrycare.co.za</div>
                        <div>donovanmark14@gmail.com</div>
                        <div>laundry_care@outlook.com</div>
                        <div>062 283 9374</div>
                        <div>069 780 0340</div>
                    </div>
                </div>

                <div class="title-row">
                    <h2>Departures</h2>
                    <div class="count"><span>Departures</span><strong>${rows.length}</strong></div>
                </div>

                <table>
                    <thead>
                        <tr>
                            <th>Invoice No</th><th>Hotel</th><th>Unit</th><th>Bedrooms</th>
                            <th>Departure Type</th><th class="num">Price</th><th>Date</th>
                        </tr>
                    </thead>
                    <tbody>${tableRows}</tbody>
                </table>

                <div class="totals">
                    <div class="totals-box">
                        <div class="totals-row"><span>Total departures</span><span>${rows.length}</span></div>
                        <div class="totals-grand"><span>Grand Total</span><strong>R ${money(total)}</strong></div>
                    </div>
                </div>

                <div class="footer">
                    <strong>Thank you for your business</strong>
                    <em>LAUNDRY CARE</em>
                </div>

                <script>
                    window.addEventListener('load', function () {
                        setTimeout(function () {
                            window.onafterprint = function () { window.close(); };
                            window.print();
                        }, 500);
                    });
                <\/script>
            </body>
            </html>
        `);
        win.document.close();
    }

    // Enter key must never save by accident
    form.addEventListener('submit', e => e.preventDefault());
    $('depAddBtn').addEventListener('click', addEntry);
    $('depCompleteBtn').addEventListener('click', complete);
    $('depPrintBtn').addEventListener('click', printDepartures);

    // ── Navigation with unsaved-data guard ────────────────────────────────────
    function guardedGo(url) {
        const dirty = rows.length || entryHasData();
        if (!dirty) { window.location.href = url; return; }
        showConfirmDialog(
            '⚠️ Unsaved Departures',
            "These departures haven't been saved yet. If you leave now, all data will be lost.",
            'Yes, leave anyway',
            'Stay here',
            () => { window.location.href = url; }
        );
    }
    window.departureSwitchMode = () => guardedGo('welcome.php');
    $('statementsFab').addEventListener('click', () => guardedGo('monthly_statement/monthly_statement.php'));

    // ── Init ──────────────────────────────────────────────────────────────────
    const L = dateLimits();
    dateInp.min = L.min;
    dateInp.max = L.max;
    dateInp.value = L.today;

    hotelSel.innerHTML = hotelOpts('');
    unitSel.innerHTML  = unitOpts('', '');
    codeSel.innerHTML  = codeOpts('');

    // After a failed save, put the lines back so nothing has to be retyped
    if (window._restoreDep) {
        (window._restoreDep.rows || []).forEach(r => rows.push({
            inv: digits(r.inv), hotel: r.hotel, unit: String(r.unit), dep: r.dep, date: r.date
        }));
        rows.forEach(r => checkTaken(r.inv, () => render()));
    }
    render();

    if (window._pendingPopup) {
        const p = window._pendingPopup;
        setTimeout(() => showPopup(p.type, p.msg, p.tip || '', p.type === 'success' ? 7000 : 0), 400);
    }

    setTimeout(focusEntry, 150);

})();
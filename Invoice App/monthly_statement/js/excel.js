const MN = ['January','February','March','April','May','June','July','August','September','October','November','December'];
const S = window.STMT;
const D = s => new Date(s + 'T00:00');
const label = () => { const a = MN[D(S.start).getMonth()], b = MN[D(S.end).getMonth()]; return (a === b ? a : a + '-' + b) + ' ' + D(S.end).getFullYear(); };

async function buildExcel() {
    const wb = new ExcelJS.Workbook();
    const fill = c => ({ type: 'pattern', pattern: 'solid', fgColor: { argb: 'FF' + c } });
    const W = { bold: true, color: { argb: 'FFFFFFFF' } }, BL = { bold: true, color: { argb: 'FF0D47A1' } };
    const utc = s => { const [y, m, d] = s.split('-').map(Number); return new Date(Date.UTC(y, m - 1, d)); };
    const lg = s => D(s).toLocaleDateString('en-GB', { day: '2-digit', month: 'long', year: 'numeric' });
    const N = 4, R = '"R" #,##0.00';
    const ws = wb.addWorksheet('Excel Sheet', { views: [{ showGridLines: false }], pageSetup: { paperSize: 9, fitToPage: true, fitToWidth: 1, fitToHeight: 0 } });
    ws.columns = [{ width: 26 }, { width: 24 }, { width: 24 }, { width: 30 }];
    let r = 1;
    const style = (c, right) => { c.alignment = { horizontal: right ? 'right' : 'left', vertical: 'middle', indent: 2 }; };
    const line = (text, font, bg, h) => {
        ws.mergeCells(r, 1, r, N);
        const c = ws.getCell(r, 1); c.value = text; c.font = font; style(c);
        if (bg) for (let i = 1; i <= N; i++) ws.getCell(r, i).fill = fill(bg);
        ws.getRow(r).height = h || 30; r++;
    };
    const total = (text, val, bg, font) => {
        ws.mergeCells(r, 1, r, N - 1);
        const a = ws.getCell(r, 1), d = ws.getCell(r, N);
        a.value = text; d.value = val; d.numFmt = R; style(a); style(d, true);
        for (let i = 1; i <= N; i++) { ws.getCell(r, i).fill = fill(bg); ws.getCell(r, i).font = font; }
        ws.getRow(r).height = 30; r++;
        return N;
    };
    const group = (arr, k) => { const m = new Map(); arr.forEach(x => { const key = x[k]; if (!m.has(key)) m.set(key, []); m.get(key).push(x); }); return m; };

    line('Laundry Care Excel Sheet', { bold: true, size: 20, color: { argb: 'FF111111' } }, null, 34);
    line('"A fresh start in every wash"', { bold: true, size: 10.5, color: { argb: 'FF1565C0' } });
    r++;
    line('Excel sheet for period: ' + lg(S.start) + ' — ' + lg(S.end), { bold: true, size: 12 });
    line('Sent to: Umhlanga Accommodation', { size: 12 });
    r++;

    const secCells = [];
    [['REGULAR BILLING', x => x.BillingType !== 'departure'], ['DEPARTURES', x => x.BillingType === 'departure']].forEach(([title, f]) => {
        const rows = S.rows.filter(f);
        line(title, { bold: true, size: 13, color: { argb: 'FFFFFFFF' } }, '111111', 28);
        r++;
        let secSum = 0;
        if (!rows.length) line('No invoices in this period.', { italic: true, size: 11 });
        group(rows, 'HotelName').forEach((hRows, hotel) => {
            line(hotel, { bold: true, size: 14, color: { argb: 'FFFFFFFF' } }, '0D47A1', 28);
            let hSum = 0;
            group(hRows, 'HotelNumber').forEach((uRows, unit) => {
                line('Unit: ' + unit, { bold: true, size: 12, color: { argb: 'FF0D47A1' } }, 'E8F1FB');
                ['Date', 'Invoice No', 'Type', 'Amount'].forEach((h, i) => {
                    const c = ws.getCell(r, i + 1); c.value = h; c.fill = fill('111111'); c.font = { bold: true, size: 10, color: { argb: 'FFFFFFFF' } }; style(c, i === 3);
                });
                ws.getRow(r).height = 30; r++;
                let uSum = 0;
                uRows.forEach(x => {
                    const t = Number(x.Total); uSum += t;
                    [utc(x.DateReceived), Number(x.InvoiceNumber), x.DepCode || 'Regular', t].forEach((v, k) => {
                        const c = ws.getCell(r, k + 1); c.value = v; c.font = { size: 11, bold: k === 3 }; style(c, k === 3);
                        c.border = { bottom: { style: 'thin', color: { argb: 'FFC9D6E6' } } };
                    });
                    ws.getCell(r, 1).numFmt = 'dd/mm/yyyy'; ws.getCell(r, 2).numFmt = '0'; ws.getCell(r, 4).numFmt = '#,##0.00';
                    ws.getRow(r).height = 30; r++;
                });
                total('Unit ' + unit + ' total', uSum, 'F3F3F3', { bold: true, size: 11 });
                r++;
                hSum += uSum;
            });
            total(hotel + ' total', hSum, 'E8F1FB', { bold: true, size: 12, color: { argb: 'FF0D47A1' } });
            r++;
            secSum += hSum;
        });
        secCells.push('D' + r);
        total(title + ' TOTAL', secSum, '0D47A1', { bold: true, size: 13, color: { argb: 'FFFFFFFF' } });
        r += 2;
    });
    total('GRAND TOTAL', Number(S.total), '111111', { bold: true, size: 14, color: { argb: 'FFFFFFFF' } });
    ws.getCell(r - 1, N).value = { formula: secCells.join('+'), result: Number(S.total) };
    return wb.xlsx.writeBuffer();
}

async function downloadExcel() {
    const buf = await buildExcel();
    const a = document.createElement('a');
    a.href = URL.createObjectURL(new Blob([buf], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' }));
    a.download = 'Laundry_Care_Statement_' + label().replace(/ /g, '_') + '.xlsx';
    a.click();
    setTimeout(() => URL.revokeObjectURL(a.href), 2000);
}

// ── Send Email dialog ────────────────────────────────────────────────────────
const st = document.createElement('style');
st.textContent = `#emOv{position:fixed;inset:0;z-index:99;display:none;align-items:center;justify-content:center;padding:16px;background:rgba(8,10,25,.8);backdrop-filter:blur(6px)}
#emOv.on{display:flex}
#emBox{width:100%;max-width:620px;max-height:94vh;overflow:auto;background:#14122b;border:1px solid rgba(255,255,255,.14);border-radius:22px;padding:22px;color:#f1f0ff}
#emBox h2{font-size:20px;margin-bottom:14px}
#emBox label{display:block;font-size:11px;letter-spacing:1.2px;text-transform:uppercase;color:#a5a3c9;font-weight:700;margin-top:12px}
#emBox input,#emBox textarea{width:100%;margin-top:6px;background:rgba(255,255,255,.08);border:1.5px solid rgba(255,255,255,.14);border-radius:12px;color:#f1f0ff;padding:10px 12px;font:600 14px 'Segoe UI',Arial,sans-serif;outline:none;box-sizing:border-box}
#emBox textarea{height:230px;resize:vertical;font-family:'Century Gothic','Segoe UI',sans-serif;font-weight:400;line-height:1.5}
#emBox input:focus,#emBox textarea:focus{border-color:#a78bfa}
#emBox small{display:block;margin-top:12px;color:#a5a3c9;font-size:12px}
.emF{display:flex;gap:10px;justify-content:flex-end;margin-top:16px}`;
document.head.appendChild(st);

const ov = document.createElement('div');
ov.id = 'emOv';
ov.innerHTML = `<div id="emBox"><h2>Send statement by email</h2>
<label>To<input id="emTo"></label><label>CC<input id="emCc"></label><label>Subject<input id="emSub"></label>
<label>Message<textarea id="emMsg"></textarea></label>
<small>The Excel downloads automatically. Attach it and your saved PDF in the email. Click the email body and press Ctrl+V to paste the blue Century Gothic message.</small>
<div class="emF"><button type="button" class="btn ghost" id="emCancel">Cancel</button><button type="button" class="btn" id="emSend">Send Email</button></div></div>`;
document.body.appendChild(ov);
const $ = id => document.getElementById(id);

function openEmail() {
    const L = label();
    $('emTo').value = 'carmen@umhacc.co.za';
    $('emCc').value = 'donovanmark14@gmail.com';
    $('emSub').value = 'Laundry Care Monthly Statement - ' + L;
    $('emMsg').value = 'Good evening Carmen, hope you are well.\n\nPlease review the attached documents, including the Excel sheet and the monthly statement for ' + L + ' from Laundry Care.\n\nKindly let me know if you require any additional information or clarification.\n\nThank you.\n\nWarm regards,\nXavier\nLaundry Care\nwww.laundrycare.co.za';
    ov.classList.add('on');
}

async function sendEmail() {
    const txt = $('emMsg').value;
    const html = `<div style="font-family:'Century Gothic',sans-serif;color:#1565C0;font-size:11pt">` +
        txt.split(/\n\s*\n/).map(p => '<p>' + p.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/\n/g, '<br>').replace(/(www\.[\w.-]+)/g, '<a href="https://$1" style="color:#1565C0">$1</a>') + '</p>').join('') + '</div>';
    let copied = false;
    try {
        await navigator.clipboard.write([new ClipboardItem({ 'text/html': new Blob([html], { type: 'text/html' }), 'text/plain': new Blob([txt], { type: 'text/plain' }) })]);
        copied = true;
    } catch (e) {}
    await downloadExcel();
    const cc = $('emCc').value.trim();
    location.href = 'mailto:' + encodeURIComponent($('emTo').value.trim()).replace(/%40/g, '@').replace(/%2C/g, ',') +
        '?subject=' + encodeURIComponent($('emSub').value) + (cc ? '&cc=' + cc : '') + (copied ? '' : '&body=' + encodeURIComponent(txt));
    ov.classList.remove('on');
}

$('emCancel').onclick = () => ov.classList.remove('on');
$('emSend').onclick = sendEmail;
ov.addEventListener('click', e => { if (e.target === ov) ov.classList.remove('on'); });
document.getElementById('xl')?.addEventListener('click', downloadExcel);
document.getElementById('xlMail')?.addEventListener('click', openEmail);
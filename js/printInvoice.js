// ============================================================
//  js/printInvoice.js — Validates then opens clean print window
//  Uses popup window approach to bypass Brave/Chrome dark mode
//  Edit the HTML template at the bottom to change print layout
// ============================================================

function printInvoice() {
    event.preventDefault();

    // ── 1. Check invoice number ───────────────────────────────────────────────
    const invoiceInput = document.getElementById('invoice_number');
    const invoiceVal   = (invoiceInput?.value || '').replace(/\D/g, '');

    if (invoiceVal.length === 0) {
        showPopup('error', 'No invoice number entered! 😟', '💡 Enter a 5-digit invoice number (e.g. 10042) before printing.', 0);
        invoiceInput?.classList.add('invalid');
        invoiceInput?.focus();
        return;
    }

    if (invoiceVal.length < 5) {
        showPopup('error', `Invoice number "${invoiceVal}" is too short — needs to be exactly 5 digits.`, `💡 For example: ${invoiceVal.padStart(5, '0')}`, 0);
        invoiceInput?.classList.add('invalid');
        invoiceInput?.focus();
        return;
    }

    const numVal = parseInt(invoiceVal, 10);
    if (numVal < 1 || numVal > 50000) {
        showPopup('error', `Invoice #${invoiceVal} is out of range.`, '💡 Invoice numbers must be between 00001 and 50000.', 0);
        invoiceInput?.classList.add('invalid');
        invoiceInput?.focus();
        return;
    }

    // ── 2. Check all rows are filled ─────────────────────────────────────────
    const rows = document.querySelectorAll('#invoiceTable tr');
    let allFilled = true;

    rows.forEach(row => {
        if (
            !row.querySelector('.hotel')?.value.trim()       ||
            !row.querySelector('.unit-number')?.value.trim() ||
            !row.querySelector('.code')?.value.trim()        ||
            !row.querySelector('.description')?.value.trim() ||
            !row.querySelector('.price')?.value.trim()       ||
            !row.querySelector('.quantity')?.value.trim()    ||
            !row.querySelector('.total')?.value.trim()       ||
            !row.querySelector('.date-received')?.value.trim()
        ) {
            allFilled = false;
        }
    });

    if (!allFilled) {
        showPopup('error', 'Some row fields are still empty.', '💡 Fill in all items completely before printing.', 0);
        return;
    }

    // ── 3. Collect all row data ───────────────────────────────────────────────
    const rowsData = [];
    rows.forEach(row => {
        rowsData.push({
            hotel:       row.querySelector('.hotel')?.value        || '',
            unit:        row.querySelector('.unit-number')?.value  || '',
            code:        row.querySelector('.code')?.value         || '',
            description: row.querySelector('.description')?.value  || '',
            price:       row.querySelector('.price')?.value        || '',
            quantity:    row.querySelector('.quantity')?.value     || '',
            total:       row.querySelector('.total')?.value        || '',
            date:        row.querySelector('.date-received')?.value|| ''
        });
    });

    const grandTotal = document.getElementById('grandTotal')?.innerText || '0.00';

    // ── 4. Build table rows HTML ──────────────────────────────────────────────
    const tableRows = rowsData.map(r => `
        <tr>
            <td>${r.hotel}</td>
            <td>${r.unit}</td>
            <td>${r.code}</td>
            <td>${r.description}</td>
            <td>R ${parseFloat(r.price).toFixed(2)}</td>
            <td>${r.quantity}</td>
            <td>R ${parseFloat(r.total).toFixed(2)}</td>
            <td>${r.date}</td>
        </tr>
    `).join('');

    // ── 5. Open clean white print window ─────────────────────────────────────
    const win = window.open('', '_blank', 'width=1000,height=750');
    win.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>${invoiceVal}</title>
            <style>
                * { margin: 0; padding: 0; box-sizing: border-box; color-scheme: light !important; }

                body {
                    font-family: Arial, sans-serif;
                    background: white !important;
                    color: black !important;
                    padding: 20px 30px;
                    -webkit-print-color-adjust: exact;
                    print-color-adjust: exact;
                }

                /* ── Header ── */
                .header {
                    display: flex;
                    justify-content: space-between;
                    align-items: flex-start;
                    margin-bottom: 16px;
                    background: white !important;
                }

                .header-left h1 {
                    font-size: 1.5em;
                    text-transform: uppercase;
                    letter-spacing: 2px;
                    color: black !important;
                    margin-bottom: 4px;
                }

                .header-left p,
                .header-right p {
                    font-size: 13px;
                    color: black !important;
                    line-height: 1.6;
                }

                .header-right { text-align: right; }

                /* ── Invoice number ── */
                .invoice-number {
                    text-align: center;
                    font-size: 1.1em;
                    font-weight: bold;
                    color: black !important;
                    margin: 12px 0;
                    letter-spacing: 3px;
                }

                /* ── Table ── */
                table {
                    width: 100%;
                    border-collapse: collapse;
                    background: white !important;
                    margin-top: 10px;
                }

                thead tr {
                    background-color: #2196F3 !important;
                    -webkit-print-color-adjust: exact !important;
                    print-color-adjust: exact !important;
                }

                th {
                    background-color: #2196F3 !important;
                    color: black !important;
                    padding: 10px 12px;
                    text-align: left;
                    border: 1px solid black;
                    font-size: 13px;
                    -webkit-print-color-adjust: exact !important;
                    print-color-adjust: exact !important;
                }

                td {
                    border: 1px solid black;
                    padding: 8px 10px;
                    font-size: 13px;
                    color: black !important;
                    background: white !important;
                    text-align: left;
                }

                tbody tr:nth-child(even) td {
                    background: #f5f5f5 !important;
                }

                /* ── Grand Total ── */
                .grand-total {
                    text-align: center;
                    font-size: 1.2em;
                    font-weight: bold;
                    color: black !important;
                    margin-top: 16px;
                }

                @media print {
                    * { color-scheme: light !important; }
                    body { background: white !important; color: black !important; }
                    th {
                        background-color: #2196F3 !important;
                        -webkit-print-color-adjust: exact !important;
                        print-color-adjust: exact !important;
                    }
                    tbody tr:nth-child(even) td {
                        background: #f5f5f5 !important;
                        -webkit-print-color-adjust: exact !important;
                        print-color-adjust: exact !important;
                    }
                }
            </style>
        </head>
        <body>

            <div class="header">
                <div class="header-left">
                    <h1>Laundry Care</h1>
                    <p>2 Tottenham Road</p>
                    <p>Parkgate</p>
                    <p>Cornubia</p>
                </div>
                <div class="header-right">
                    <p>Email: donovanmark14@gmail.com</p>
                    <p>Cell: 062 283 9374</p>
                    <p>Cell: 069 780 0340</p>
                </div>
            </div>

            <div class="invoice-number">Invoice no: ${invoiceVal}</div>

            <table>
                <thead>
                    <tr>
                        <th>Hotel</th>
                        <th>Unit Number</th>
                        <th>Code</th>
                        <th>Description</th>
                        <th>Unit Price (R)</th>
                        <th>Quantity</th>
                        <th>Total Price (R)</th>
                        <th>Date Received</th>
                    </tr>
                </thead>
                <tbody>
                    ${tableRows}
                </tbody>
            </table>

            <div class="grand-total">Grand Total: R${parseFloat(grandTotal.replace(/[^0-9.]/g, '')).toFixed(2)}</div>

            <script>
                window.addEventListener('load', function() {
                    setTimeout(function() {
                        window.print();
                        window.onafterprint = function() {
                        window.close();
                    };
                    // Fallback — force close after 1s if onafterprint doesn't fire
                    setTimeout(function() { window.close(); }, 0000);
                    }, 500);
                });
            </script>
        </body>
        </html>
    `);
    win.document.close();
}

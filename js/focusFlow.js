// focusFlow.js — focus flow, date limits, validation guards, green flash
// isCustomRow and isCodeInPriceList are global so fieldGuide.js can use them

// ── Global helpers used by both focusFlow and fieldGuide ──────────────────────
function isCustomRow(tr) {
    const price = tr?.querySelector('.price');
    return price && !price.hasAttribute('readonly');
}

function isCodeInPriceList(code) {
    if (typeof priceList === 'undefined') return false;
    return priceList.some(item => item.code === (code || '').trim().toUpperCase());
}

// cached-hotel datalist fix, custom row flow, and combo walkthrough

let isComboNavigating = false;

function flashGreen(el) {
    if (!el) return;
    el.classList.remove('invalid');
    el.classList.add('field-ok');
    setTimeout(() => el.classList.remove('field-ok'), 2000);
}

// ── Utility: block advancement — show popup and refocus ───────────────────────
function blockAndWarn(msg, solution, el) {
    showPopup('warning', msg, solution, 0);
    setTimeout(() => el?.focus(), 80);
}

document.addEventListener('DOMContentLoaded', () => {

    const invoiceInput = document.getElementById('invoice_number');
    const table        = document.getElementById('invoiceTable');
    const row1         = table?.querySelector('tr');
    if (!invoiceInput || !row1) return;

    const getR1 = cls => row1.querySelector(cls);

    // ═══════════════════════════════════════════════════
    // DATE LIMITS — today back to 2 months ago
    // ═══════════════════════════════════════════════════
    function applyDateLimits(input) {
        const today        = new Date();
        const twoMonthsAgo = new Date();
        twoMonthsAgo.setMonth(today.getMonth() - 2);
        const fmt = d => d.toISOString().split('T')[0];
        input.max = fmt(today);
        input.min = fmt(twoMonthsAgo);
        if (!input.value) input.value = fmt(today);
    }

    document.querySelectorAll('.date-received').forEach(applyDateLimits);
    new MutationObserver(() => {
        document.querySelectorAll('.date-received:not([data-limited])').forEach(inp => {
            applyDateLimits(inp);
            inp.setAttribute('data-limited', '1');
        });
    }).observe(table, { childList: true, subtree: true });

    // ═══════════════════════════════════════════════════
    // STEP 1 — Invoice number on load
    // ═══════════════════════════════════════════════════
    setTimeout(() => invoiceInput.focus(), 100);

    invoiceInput.addEventListener('input', () => {
        const val = invoiceInput.value.replace(/\D/g, '');
        if (val.length === 5) {
            flashGreen(invoiceInput);
            setTimeout(() => openCalendar(getR1('.date-received')), 80);
        }
    });

    // ═══════════════════════════════════════════════════
    // STEP 2 — Date → Hotel
    // ═══════════════════════════════════════════════════
    document.addEventListener('change', e => {
        if (!e.target.classList.contains('date-received')) return;
        if (e.target.closest('tr') !== row1) return;

        // Block if invoice not filled
        if (invoiceInput.value.replace(/\D/g,'').length < 5) {
            blockAndWarn(
                'Invoice number needed first!',
                '💡 Fill in all 5 digits of the invoice number before picking a date.',
                invoiceInput
            );
            return;
        }
        flashGreen(e.target);
        setTimeout(() => getR1('.hotel')?.focus(), 80);
    });

    // ═══════════════════════════════════════════════════
    // STEP 3 — Hotel → Unit# (always show datalist, even cached hotel)
    // ═══════════════════════════════════════════════════

    // When hotel SELECT gains focus, always rebuild & show its datalist
    document.addEventListener('focus', e => {
        if (!e.target.classList.contains('hotel')) return;
        const tr = e.target.closest('tr');
        if (tr !== row1) return;
        // Always refresh datalist so cached hotel still shows its list
        updateUnitDatalist(e.target);
    }, true);

    document.addEventListener('change', e => {
        if (!e.target.classList.contains('hotel')) return;
        const tr      = e.target.closest('tr');
        const unitInp = tr?.querySelector('.unit-number');
        if (!unitInp) return;

        // Block if date not filled
        if (tr === row1 && !getR1('.date-received')?.value) {
            blockAndWarn(
                'Date needed first!',
                '💡 Pick a date before choosing the hotel.',
                getR1('.date-received')
            );
            return;
        }

        updateUnitDatalist(e.target);
        flashGreen(e.target);

        if (tr === row1) {
            setTimeout(() => {
                unitInp.focus();
                // Force-open the datalist dropdown by triggering input event
                unitInp.dispatchEvent(new Event('input', { bubbles: true }));
                unitInp.dispatchEvent(new KeyboardEvent('keydown', { key: 'ArrowDown', bubbles: true }));
            }, 100);
        }
    });

    // Also show datalist when unit input is focused (handles cached hotel case)
    document.addEventListener('focus', e => {
        if (!e.target.classList.contains('unit-number')) return;
        const tr = e.target.closest('tr');
        if (tr !== row1) return;
        const hotelSel = getR1('.hotel');
        if (hotelSel) {
            updateUnitDatalist(hotelSel);
            // Trigger input to make browser show the datalist
            setTimeout(() => {
                e.target.dispatchEvent(new Event('input', { bubbles: true }));
            }, 50);
        }
    }, true);

    // ═══════════════════════════════════════════════════
    // STEP 4 — Unit# → Code
    // ═══════════════════════════════════════════════════
    document.addEventListener('blur', e => {
        if (!e.target.classList.contains('unit-number')) return;
        if (isComboNavigating) return;
        const tr  = e.target.closest('tr');
        const val = e.target.value.trim();

        if (!val) {
            if (tr === row1) {
                blockAndWarn(
                    'Unit number is empty!',
                    '💡 Enter the unit number for this hotel before moving on.',
                    e.target
                );
            }
            return;
        }
        flashGreen(e.target);
        const codeInp = tr?.querySelector('.code');
        if (codeInp) setTimeout(() => codeInp.focus(), 60);
    }, true);

    // ═══════════════════════════════════════════════════
    // STEP 5 — Code → next field
    // Standard rows: only advance if code is valid in priceList
    // Custom rows: code→description→price→quantity (all manual)
    // ═══════════════════════════════════════════════════
    document.addEventListener('blur', e => {
        if (!e.target.classList.contains('code')) return;
        if (isComboNavigating) return;
        const tr  = e.target.closest('tr');
        const val = e.target.value.trim();

        if (!val) return; // empty — just leave it, don't advance

        if (isCustomRow(tr)) {
            // Custom: code can be anything, move to description
            flashGreen(e.target);
            const desc = tr.querySelector('.description');
            if (desc) setTimeout(() => desc.focus(), 60);
            return;
        }

        // Standard row: must be a valid priceList code
        if (!isCodeInPriceList(val)) {
            blockAndWarn(
                `"${val}" is not a valid item code.`,
                '💡 Type a few letters to see suggestions, then pick one from the list.',
                e.target
            );
            e.target.classList.add('invalid');
            return;
        }

        flashGreen(e.target);
        const qty = tr?.querySelector('.quantity');
        if (qty) setTimeout(() => qty.focus(), 60);
    }, true);

    // Also catch datalist selection (fires 'change' not 'blur')
    document.addEventListener('change', e => {
        if (!e.target.classList.contains('code')) return;
        if (isComboNavigating) return;
        const tr  = e.target.closest('tr');
        const val = e.target.value.trim();
        if (!val) return;

        if (isCustomRow(tr)) {
            flashGreen(e.target);
            setTimeout(() => tr.querySelector('.description')?.focus(), 60);
            return;
        }

        if (!isCodeInPriceList(val)) {
            blockAndWarn(
                `"${val}" is not a valid item code.`,
                '💡 Pick a code from the suggestions list.',
                e.target
            );
            e.target.classList.add('invalid');
            return;
        }
        flashGreen(e.target);
        setTimeout(() => tr.querySelector('.quantity')?.focus(), 60);
    });

    // ═══════════════════════════════════════════════════
    // CUSTOM ROW: Description → Price → Quantity
    // ═══════════════════════════════════════════════════
    document.addEventListener('blur', e => {
        if (!e.target.classList.contains('description')) return;
        if (isComboNavigating) return;
        const tr  = e.target.closest('tr');
        if (!isCustomRow(tr)) return;
        const val = e.target.value.trim();
        if (!val) return;
        flashGreen(e.target);
        setTimeout(() => tr.querySelector('.price')?.focus(), 60);
    }, true);

    document.addEventListener('blur', e => {
        if (!e.target.classList.contains('price')) return;
        if (isComboNavigating) return;
        const tr  = e.target.closest('tr');
        if (!isCustomRow(tr)) return;
        const val = e.target.value.trim();
        if (!val || parseFloat(val) <= 0) {
            if (val !== '') blockAndWarn(
                'Price must be greater than zero.',
                '💡 Enter the unit price for this custom item.',
                e.target
            );
            return;
        }
        flashGreen(e.target);
        setTimeout(() => tr.querySelector('.quantity')?.focus(), 60);
    }, true);

    // ═══════════════════════════════════════════════════
    // Quantity flash green when filled
    // ═══════════════════════════════════════════════════
    document.addEventListener('input', e => {
        if (!e.target.classList.contains('quantity')) return;
        const val = e.target.value.trim();
        if (val && parseInt(val) > 0) {
            setTimeout(() => flashGreen(e.target), 300);
        }
    });

    // ═══════════════════════════════════════════════════
    // Add Item button — focus code on new last row
    // ═══════════════════════════════════════════════════
    document.querySelector('.add-item')?.addEventListener('click', () => {
        setTimeout(() => {
            const codes = document.querySelectorAll('.code');
            codes[codes.length - 1]?.focus();
        }, 60);
    });

});

// ═══════════════════════════════════════════════════
// COMBO QUANTITY WALKTHROUGH — called by comboModal.js
// ═══════════════════════════════════════════════════
function walkComboQuantities(quantityInputs) {
    isComboNavigating = true;

    function focusNext(index) {
        if (index >= quantityInputs.length) {
            isComboNavigating = false;
            showPopup('success', 'All combo quantities filled! 🎉', '✅ Looking good — ready to save or add more.', 4000);
            return;
        }

        const qty = quantityInputs[index];
        qty.focus();
        let moved = false;

        function advance() {
            if (moved) return;
            moved = true;
            qty.removeEventListener('change', onChange);
            qty.removeEventListener('blur',   onBlur);
            flashGreen(qty);
            focusNext(index + 1);
        }

        function onChange() { if (qty.value.trim() !== '') advance(); }
        function onBlur()   { if (qty.value.trim() !== '') advance(); }

        qty.addEventListener('change', onChange);
        qty.addEventListener('blur',   onBlur);
    }

    focusNext(0);
}

// Opens browser date picker
function openCalendar(input) {
    if (!input) return;
    input.focus();
    try { input.showPicker(); } catch(e) {}
}

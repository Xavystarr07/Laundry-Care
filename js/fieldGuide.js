// fieldGuide.js — Top-right assistant popup
// Fixed:
//  1. Never shows on page load — only after user has touched a field
//  2. Auto-hides immediately when all errors are fixed
//  3. Bottom popup also auto-hides when error is resolved
//  4. Passive scan re-arms correctly after errors are fixed

(function () {

// ── Styles ─────────────────────────────────────────────────────────────────────
const style = document.createElement('style');
style.textContent = `
  @import url('https://fonts.googleapis.com/css2?family=Nunito:wght@600;700;800&display=swap');

  #fieldGuide {
    position: fixed;
    top: 24px;
    right: 24px;
    width: 310px;
    background: linear-gradient(145deg, #0f0c29, #302b63, #24243e);
    border-radius: 20px;
    box-shadow: 0 12px 48px rgba(0,0,0,0.35), 0 0 0 1px rgba(255,255,255,0.07);
    font-family: 'Nunito', 'Segoe UI', Arial, sans-serif;
    font-size: 13px;
    color: #e0e0f5;
    z-index: 99998;
    opacity: 0;
    transform: translateY(-16px) scale(0.96);
    transition: opacity 0.35s ease, transform 0.35s ease;
    pointer-events: none;
    overflow: hidden;
    border-top: 4px solid #7c3aed;
  }
  #fieldGuide::before {
    content: '· · · · · · · · · · · · · · · · · ·';
    position: absolute; top: 5px; left: 0; right: 0;
    font-size: 8px; color: rgba(255,255,255,0.08);
    letter-spacing: 4px; text-align: center; pointer-events: none;
    animation: fgDots 8s linear infinite;
  }
  @keyframes fgDots { 0%{transform:translateX(0)} 100%{transform:translateX(-30px)} }

  #fieldGuide.fg-show {
    opacity: 1;
    transform: translateY(0) scale(1);
    pointer-events: all;
  }
  #fieldGuide.fg-error   { border-top-color: #ef4444; background: linear-gradient(145deg, #1a0505, #3d0a0a, #1f0000); }
  #fieldGuide.fg-success { border-top-color: #22c55e; background: linear-gradient(145deg, #031a08, #065f20, #022910); }
  #fieldGuide.fg-guide   { border-top-color: #f59e0b; background: linear-gradient(145deg, #1a1000, #4a2e00, #1f1500); }

  #fgHeader {
    display: flex; align-items: center; gap: 10px;
    padding: 12px 14px 10px;
    background: rgba(255,255,255,0.05);
    border-bottom: 1px solid rgba(255,255,255,0.08);
  }
  #fgIcon { font-size: 24px; flex-shrink: 0; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.5)); }
  #fgHeaderText { flex: 1; font-weight: 800; font-size: 13.5px; color: #fff; line-height: 1.3; text-shadow: 0 1px 4px rgba(0,0,0,0.5); }
  #fgClose {
    background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.15);
    color: rgba(255,255,255,0.6); font-size: 13px;
    cursor: pointer; padding: 3px 7px; margin: 0 !important; box-shadow: none !important;
    flex-shrink: 0; line-height: 1; border-radius: 50%; transition: all 0.2s;
  }
  #fgClose:hover { background: rgba(255,80,80,0.4); color: white; transform: rotate(90deg); }
  #fgBody { padding: 12px 14px; line-height: 1.55; color: rgba(255,255,255,0.82); font-size: 13px; }
  #fgStep {
    margin-top: 9px; padding: 8px 11px;
    background: rgba(255,255,255,0.08);
    border-left: 3px solid rgba(245,158,11,0.7);
    border-radius: 8px; font-size: 12px; color: rgba(255,220,120,0.95);
    backdrop-filter: blur(4px);
    display: none;
  }
  #fgStep.visible { display: block; }
  #fgActions { display: flex; gap: 8px; padding: 0 14px 14px; flex-wrap: wrap; }
  #fgAssistBtn {
    background: linear-gradient(135deg, #22c55e, #16a34a);
    color: white; border: none; border-radius: 20px; padding: 7px 16px;
    font-size: 13px; font-weight: 700; cursor: pointer;
    font-family: 'Nunito','Segoe UI',Arial,sans-serif;
    transition: all 0.2s; box-shadow: 0 2px 12px rgba(34,197,94,0.4); margin: 0 !important;
    letter-spacing: 0.2px;
  }
  #fgAssistBtn:hover { transform:scale(1.06); box-shadow: 0 4px 16px rgba(34,197,94,0.5); }
  #fgDismissBtn {
    background: rgba(255,255,255,0.1); color: rgba(255,255,255,0.7);
    border: 1px solid rgba(255,255,255,0.15); border-radius: 20px;
    padding: 7px 14px; font-size: 12px; cursor: pointer;
    font-family: 'Nunito','Segoe UI',Arial,sans-serif;
    transition: all 0.2s; margin: 0 !important; box-shadow: none !important;
  }
  #fgDismissBtn:hover { background: rgba(255,255,255,0.18); color: white; }
  #fgProgress { height: 3px; background: rgba(255,255,255,0.08); overflow: hidden; }
  #fgProgressBar {
    height: 100%;
    background: linear-gradient(90deg, #7c3aed, #22c55e);
    width: 0%; transition: width 0.4s ease;
  }

  /* Tinkering magic icon shown after dismiss */
  #fgTinker {
    position: fixed;
    top: 24px; right: 24px;
    width: 44px; height: 44px;
    background: linear-gradient(135deg, #f39c12, #e67e22);
    border-radius: 50%;
    display: none;
    align-items: center; justify-content: center;
    font-size: 22px;
    cursor: pointer;
    box-shadow: 0 4px 16px rgba(243,156,18,0.4);
    z-index: 99998;
    transition: transform 0.2s;
    animation: tinkerSpin 2s ease-in-out infinite;
  }
  #fgTinker.visible { display: flex; }
  #fgTinker:hover { transform: scale(1.15); }
  @keyframes tinkerSpin {
    0%, 100% { transform: rotate(-8deg) scale(1); }
    50%       { transform: rotate(8deg) scale(1.08); }
  }

  .field-ok {
    border-color: #2ecc71 !important;
    box-shadow: 0 0 0 2px rgba(46,204,113,0.25) !important;
    transition: border-color 0.3s, box-shadow 0.3s;
  }
`;
document.head.appendChild(style);

// ── DOM ────────────────────────────────────────────────────────────────────────
const guide = document.createElement('div');
guide.id = 'fieldGuide';
guide.innerHTML = `
  <div id="fgProgress"><div id="fgProgressBar"></div></div>
  <div id="fgHeader">
    <span id="fgIcon">🔍</span>
    <span id="fgHeaderText">Invoice Assistant</span>
    <button id="fgClose" title="Dismiss">✕</button>
  </div>
  <div id="fgBody">
    <span id="fgMsg"></span>
    <div id="fgStep"></div>
  </div>
  <div id="fgActions">
    <button id="fgAssistBtn">✨ Use Assistance</button>
    <button id="fgDismissBtn">Dismiss</button>
  </div>
`;
document.body.appendChild(guide);

// Tinkering icon
const tinker = document.createElement('div');
tinker.id = 'fgTinker';
tinker.title = 'Click to reopen assistant';
tinker.textContent = '🔧';
document.body.appendChild(tinker);

// ── Refs ───────────────────────────────────────────────────────────────────────
const fgEl         = guide;
const fgIcon       = guide.querySelector('#fgIcon');
const fgTitle      = guide.querySelector('#fgHeaderText');
const fgMsg        = guide.querySelector('#fgMsg');
const fgStep       = guide.querySelector('#fgStep');
const fgBar        = guide.querySelector('#fgProgressBar');
const fgAssist     = guide.querySelector('#fgAssistBtn');
const fgDismissBtn = guide.querySelector('#fgDismissBtn');
const fgClose      = guide.querySelector('#fgClose');

// ── State ──────────────────────────────────────────────────────────────────────
let guidanceActive     = false;
let userHasInteracted  = false; // ← NEVER show anything until user touches a field
let dismissed          = false;
let dismissReopenTimer = null;
let idleTimer          = null;
let cleanCheckTimer    = null; // continuously checks if errors resolved → auto-hide

// ── Show / hide ────────────────────────────────────────────────────────────────
function showGuide(type, icon, title, msg, step, showAssist, autoDismiss) {
    dismissed = false;
    tinker.classList.remove('visible');
    if (dismissReopenTimer) { clearTimeout(dismissReopenTimer); dismissReopenTimer = null; }

    fgEl.className = 'fg-show fg-' + type;
    fgIcon.textContent  = icon;
    fgTitle.textContent = title;
    fgMsg.textContent   = msg;

    if (step) { fgStep.textContent = step; fgStep.classList.add('visible'); }
    else      { fgStep.textContent = ''; fgStep.classList.remove('visible'); }

    fgAssist.style.display = (showAssist !== false) ? 'inline-block' : 'none';

    if (autoDismiss > 0) setTimeout(hideGuide, autoDismiss);

    // Start watching for errors to be resolved so we can auto-hide
    startCleanCheck();
}

function hideGuide() {
    fgEl.classList.remove('fg-show');
    guidanceActive = false;
    stopCleanCheck();
}

function dismissGuide() {
    hideGuide();
    dismissed  = true;
    guidanceActive = false;
    stopCleanCheck();

    const err = findFirstError();
    if (err) {
        tinker.classList.add('visible');
        if (dismissReopenTimer) clearTimeout(dismissReopenTimer);
        dismissReopenTimer = setTimeout(() => {
            dismissed = false;
            tinker.classList.remove('visible');
            const error = findFirstError();
            if (error) {
                const g = GUIDANCE[error.field] || fallbackGuidance(error.label);
                showGuide('error', g.icon, 'Still need your attention!',
                    `${error.label} still needs fixing. Click "Use Assistance" to get help.`,
                    '', true, 0);
            }
        }, 60000);
    } else {
        dismissed = false;
    }
}

fgClose.addEventListener('click', dismissGuide);
fgDismissBtn.addEventListener('click', dismissGuide);

// Tinkering icon re-opens guide
tinker.addEventListener('click', () => {
    tinker.classList.remove('visible');
    dismissed = false;
    const error = findFirstError();
    if (error) {
        const g = GUIDANCE[error.field] || fallbackGuidance(error.label);
        showGuide('error', g.icon, 'I found something to fix!',
            `${error.label} needs attention. Click "Use Assistance" to go there.`,
            '', true, 0);
    }
});

// ── Auto-hide when errors resolved ────────────────────────────────────────────
// Polls every 500ms while guide is visible — hides immediately when no errors
function startCleanCheck() {
    stopCleanCheck();
    cleanCheckTimer = setInterval(() => {
        if (!fgEl.classList.contains('fg-show')) { stopCleanCheck(); return; }
        if (guidanceActive) return; // don't interrupt active guidance mid-walk
        const error = findFirstError();
        if (!error) {
            hideGuide();
            tinker.classList.remove('visible');
            updateProgress();
            // Kill bottom popup too
            const rp = document.getElementById('robotPopup');
            if (rp) rp.classList.remove('show');
        }
    }, 500);
}

function stopCleanCheck() {
    if (cleanCheckTimer) { clearInterval(cleanCheckTimer); cleanCheckTimer = null; }
}

// ── Progress ───────────────────────────────────────────────────────────────────
function updateProgress() {
    const { total, filled } = countFields();
    const pct = total > 0 ? Math.round((filled / total) * 100) : 0;
    fgBar.style.width = pct + '%';
}

// ── Find first error ───────────────────────────────────────────────────────────
function findFirstError() {
    const inv = document.getElementById('invoice_number');
    if (!inv?.value || inv.value.replace(/\D/g,'').length < 5)
        return { el: inv, field: 'invoice', label: 'Invoice Number' };

    const row1   = document.querySelector('#invoiceTable tr');
    const dateEl = row1?.querySelector('.date-received');
    if (dateEl && !dateEl.value)
        return { el: dateEl, field: 'date', label: 'Date Received' };

    for (const row of document.querySelectorAll('#invoiceTable tr')) {
        const hotel = row.querySelector('.hotel');
        if (hotel && !hotel.disabled && !hotel.value)
            return { el: hotel, field: 'hotel', label: 'Hotel Name' };

        const unit = row.querySelector('.unit-number');
        if (unit && !unit.hasAttribute('readonly') && !unit.value.trim())
            return { el: unit, field: 'unit', label: 'Unit Number' };

        const code = row.querySelector('.code');
        if (code && !code.value.trim())
            return { el: code, field: 'code', label: 'Item Code' };

        if (code?.value.trim() && !isCustomRow(row) && !isCodeInPriceList(code.value))
            return { el: code, field: 'code-invalid', label: 'Invalid Item Code' };

        if (isCustomRow(row)) {
            const desc = row.querySelector('.description');
            if (desc && !desc.value.trim())
                return { el: desc, field: 'description', label: 'Description' };

            const price = row.querySelector('.price');
            if (price && (!price.value || parseFloat(price.value) <= 0))
                return { el: price, field: 'price', label: 'Unit Price' };
        }

        const qty = row.querySelector('.quantity');
        if (qty && !qty.value.trim())
            return { el: qty, field: 'quantity', label: 'Quantity' };
    }
    return null;
}

// ── Count fields ───────────────────────────────────────────────────────────────
function countFields() {
    let total = 0, filled = 0;
    const inv = document.getElementById('invoice_number');
    total++;
    if (inv?.value.replace(/\D/g,'').length === 5) filled++;

    document.querySelectorAll('#invoiceTable tr').forEach(row => {
        ['.hotel', '.unit-number', '.code', '.quantity', '.date-received'].forEach(cls => {
            const el = row.querySelector(cls);
            if (!el || el.hasAttribute('readonly') || el.disabled) return;
            total++;
            if (el.value.trim()) filled++;
        });
        if (isCustomRow(row)) {
            ['.description', '.price'].forEach(cls => {
                const el = row.querySelector(cls);
                if (!el) return;
                total++;
                if (el.value.trim() && parseFloat(el.value) > 0) filled++;
            });
        }
    });
    return { total, filled };
}

// ── Guidance content ───────────────────────────────────────────────────────────
const GUIDANCE = {
    invoice:      { icon:'🔢', title:'Invoice Number',  msg:'A 5-digit invoice number is needed.',            step:'Type exactly 5 digits (e.g. 10042). Max value is 50000.' },
    date:         { icon:'📅', title:'Date Received',   msg:'Please pick a date.',                           step:'Click the date field — only dates within the last 2 months are allowed.' },
    hotel:        { icon:'🏨', title:'Hotel Name',      msg:'Choose a hotel from the dropdown.',             step:'Click the Hotel dropdown and select the correct hotel.' },
    unit:         { icon:'🔑', title:'Unit Number',     msg:'Unit number is missing.',                       step:'Click Unit Number — a suggestion list will appear. Pick or type a valid number.' },
    code:         { icon:'🏷️', title:'Item Code',       msg:'An item code is needed.',                       step:'Type a few letters (e.g. TWL) and pick a matching code from the dropdown.' },
    'code-invalid':{ icon:'⚠️', title:'Invalid Code',   msg:"This code isn't in the price list.",            step:'Clear it, type a few letters, and choose a valid code from the suggestions.' },
    description:  { icon:'📝', title:'Description',    msg:'Custom item needs a description.',              step:'Type a short description, then press Tab or click the next field when done.' },
    price:        { icon:'💰', title:'Unit Price',      msg:'Price is missing or zero.',                     step:'Enter the price in Rands (e.g. 25.50), then press Tab or click next when done.' },
    quantity:     { icon:'🔢', title:'Quantity',        msg:"Quantity hasn't been filled in.",               step:'Enter how many (e.g. 2), then press Tab or click the next field when done.' },
};

function fallbackGuidance(label) {
    return { icon:'❓', title: label, msg:'This field needs attention.', step:'Fill in this field correctly.' };
}

// ── Use Assistance click ───────────────────────────────────────────────────────
fgAssist.addEventListener('click', () => {
    const error = findFirstError();
    if (!error) {
        showGuide('success', '🎉', 'All looks great!', 'Every field is correctly filled. Ready to save or print!', '', false, 5000);
        return;
    }
    guidanceActive = true;
    startGuidingField(error);
});

// ── Guide a specific field ─────────────────────────────────────────────────────
function startGuidingField(error) {
    const g = GUIDANCE[error.field] || fallbackGuidance(error.label);
    showGuide('guide', g.icon, g.title, g.msg, g.step, false, 0);
    error.el.scrollIntoView({ behavior: 'smooth', block: 'center' });
    setTimeout(() => error.el.focus(), 300);
    watchForFix(error.el, error.field);
}

// ── Watch field for correction ─────────────────────────────────────────────────
function watchForFix(el, field) {
    let moved = false;

    function isFixed() {
        if (field === 'invoice')      return el.value.replace(/\D/g,'').length === 5;
        if (field === 'date')         return !!el.value;
        if (field === 'hotel')        return !!el.value;
        if (field === 'unit')         return !!el.value.trim();
        if (field === 'code')         return !!el.value.trim();
        if (field === 'code-invalid') return isCodeInPriceList(el.value);
        if (field === 'description')  return !!el.value.trim();
        if (field === 'price')        return parseFloat(el.value) > 0;
        if (field === 'quantity')     return parseInt(el.value) > 0;
        return false;
    }

    function onDone() {
        if (moved) return;
        if (!isFixed()) return;
        moved = true;
        cleanup();
        flashGreen(el);
        updateProgress();

        setTimeout(() => {
            const next = findFirstError();
            if (!next) {
                guidanceActive = false;
                dismissed = false;
                showGuide('success', '🎉', 'Well done!', 'All fields are correctly filled. Ready to save or print!', '', false, 5000);
            } else {
                startGuidingField(next);
            }
        }, 500);
    }

    const watchEvent = (field === 'date' || field === 'hotel') ? 'change' : 'blur';

    function onBlur()   { onDone(); }
    function onChange() { onDone(); }

    function cleanup() {
        el.removeEventListener('blur',   onBlur);
        el.removeEventListener('change', onChange);
    }

    if (watchEvent === 'blur') {
        el.addEventListener('blur',   onBlur);
        el.addEventListener('change', onChange);
    } else {
        el.addEventListener('change', onChange);
    }
}

// ── Passive scan — only after user has interacted ─────────────────────────────
function schedulePassiveScan() {
    if (!userHasInteracted) return;  // ← hard gate — nothing shows on page load
    if (guidanceActive) return;
    if (dismissed) return;
    if (idleTimer) clearTimeout(idleTimer);

    idleTimer = setTimeout(() => {
        if (guidanceActive || dismissed) return;

        updateProgress();
        const error = findFirstError();

        if (error) {
            const g = GUIDANCE[error.field] || fallbackGuidance(error.label);
            showGuide('error', g.icon, 'I found something to fix!',
                `${error.label} needs attention. Click "Use Assistance" and I'll take you there.`,
                '', true, 0);
        } else {
            hideGuide();
            tinker.classList.remove('visible');
            // Also hide the bottom popup if errors are gone
            if (typeof hideFieldGuide === 'function') hideFieldGuide();
            const rp = document.getElementById('robotPopup');
            if (rp) rp.classList.remove('show');
        }
    }, 4000);
}

function markInteraction() {
    userHasInteracted = true;

    if (dismissed) {
        dismissed = false;
        if (dismissReopenTimer) { clearTimeout(dismissReopenTimer); dismissReopenTimer = null; }
        tinker.classList.remove('visible');
    }

    schedulePassiveScan();
}

document.addEventListener('input',  markInteraction);
document.addEventListener('change', markInteraction);
document.addEventListener('blur',   markInteraction, true);

// ── Helpers expected by other JS files ────────────────────────────────────────
function flashGreen(el) {
    el.classList.add('field-ok');
    setTimeout(() => el.classList.remove('field-ok'), 2000);
}

function isCustomRow(row) {
    return row.querySelector('.description:not([readonly])') !== null;
}

function isCodeInPriceList(code) {
    if (typeof priceList === 'undefined') return true;
    const trimmed = code.trim().toUpperCase();
    // priceList is an array of {code, description, price}
    return priceList.some(item => item.code.toUpperCase() === trimmed);
}

window.showFieldGuide = function(type, icon, title, msg, step, showAssist, autoDismiss) {
    userHasInteracted = true;
    showGuide(type, icon, title, msg, step || '', showAssist !== false, autoDismiss || 0);
};
window.hideFieldGuide = hideGuide;

// ── Global always-on watcher — kills BOTH popups the instant errors clear ──
setInterval(() => {
    if (!userHasInteracted) return;
    if (guidanceActive) return;
    const error = findFirstError();
    if (!error) {
        // No errors — kill top guide
        if (fgEl.classList.contains('fg-show')) {
            hideGuide();
            tinker.classList.remove('visible');
        }
        // Kill bottom popup
        const rp = document.getElementById('robotPopup');
        if (rp && rp.classList.contains('show')) {
            rp.classList.remove('show');
        }
    }
}, 300);

})();

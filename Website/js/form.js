/**
 * js/form.js  v3.0 — Web3Forms
 * Sends enquiry emails directly to laundry_care@outlook.com via Web3Forms API.
 * No server-side PHP required. Works on InfinityFree free plan.
 *
 * Validation:
 *   - Name:    letters + spaces only, no numbers/symbols
 *   - Email:   format guidance, live validation
 *   - Phone:   exactly 10 digits, blocks letters/symbols in real-time
 *   - Message: 3000 char limit, profanity filter
 *   - Channel: icon pill selector
 */
(function () {
    'use strict';

    const WEB3FORMS_KEY = 'd854d9fe-0932-405e-af60-0df5f0618527';
    const WEB3FORMS_URL = 'https://api.web3forms.com/submit';

    /* ── Element references ── */
    const form      = document.getElementById('enquiry-form');
    const submitBtn = document.getElementById('submit-btn');
    const globalMsg = document.getElementById('form-global-error');
    const msgArea   = document.getElementById('message');
    const charCount = document.getElementById('message-count');

    if (!form) return;

    /* ─────────────────────────────────────────────
       PROFANITY FILTER
       ───────────────────────────────────────────── */
    const PROFANITY = [
        'fuck','shit','bitch','asshole','bastard','cunt','dick','cock',
        'pussy','nigger','kaffir','slut','whore','motherfucker','bullshit',
        'damn','crap','piss','arse','bollocks','wanker','twat'
    ];
    const LAUNDRY_EXEMPT = ['strip','soil','stain','dirty','smear'];

    function containsProfanity(text) {
        return PROFANITY.some(word => {
            if (LAUNDRY_EXEMPT.includes(word)) return false;
            return new RegExp('\\b' + word + '\\b', 'i').test(text);
        });
    }

    /* ─────────────────────────────────────────────
       VALIDATION RULES
       ───────────────────────────────────────────── */
    const RULES = {
        name:  {
            validate: v => /^[A-Za-z\s\-']{2,80}$/.test(v.trim()),
            message:  'Full name must contain letters only — no numbers or symbols.'
        },
        email: {
            validate: v => /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(v.trim()),
            message:  'Please enter a valid email — format: name@domain.com'
        },
        phone: {
            validate: v => /^[0-9]{10}$/.test(v.trim()),
            message:  'Phone must be exactly 10 digits — no spaces or symbols.'
        }
    };

    /* ─────────────────────────────────────────────
       REAL-TIME INPUT BLOCKING
       ───────────────────────────────────────────── */
    const nameField = document.getElementById('name');
    if (nameField) {
        nameField.addEventListener('keypress', function (e) {
            if (!/^[A-Za-z\s\-']$/.test(e.key) && !isNavKey(e.key)) {
                e.preventDefault();
                flashHint(nameField, 'Numbers and symbols are not allowed in your name.');
            }
        });
        nameField.addEventListener('paste', function (e) {
            e.preventDefault();
            const cleaned = (e.clipboardData || window.clipboardData)
                .getData('text').replace(/[^A-Za-z\s\-']/g, '');
            document.execCommand('insertText', false, cleaned);
        });
    }

    const phoneField = document.getElementById('phone');
    if (phoneField) {
        phoneField.addEventListener('keypress', function (e) {
            if (!/[0-9]/.test(e.key) && !isNavKey(e.key)) {
                e.preventDefault();
                flashHint(phoneField, 'Only digits (0–9) allowed.');
            }
            if (this.value.replace(/\D/g, '').length >= 10 && /[0-9]/.test(e.key)) {
                e.preventDefault();
                flashHint(phoneField, 'Maximum 10 digits reached.');
            }
        });
        phoneField.addEventListener('paste', function (e) {
            e.preventDefault();
            const digits = (e.clipboardData || window.clipboardData)
                .getData('text').replace(/\D/g, '').slice(0, 10);
            document.execCommand('insertText', false, digits);
        });
        phoneField.addEventListener('input', function () {
            const clean = this.value.replace(/\D/g, '').slice(0, 10);
            if (this.value !== clean) this.value = clean;
        });
    }

    function isNavKey(key) {
        return ['Backspace','Delete','Tab','Enter','ArrowLeft','ArrowRight','Home','End'].includes(key);
    }

    function flashHint(field, msg) {
        const hint = field.closest('.form-group') && field.closest('.form-group').querySelector('.field-hint');
        if (!hint) return;
        const orig = hint.textContent;
        hint.style.color = '#EF4444';
        hint.textContent  = msg;
        setTimeout(() => { hint.style.color = ''; hint.textContent = orig; }, 2500);
    }

    /* ─────────────────────────────────────────────
       CHARACTER COUNTER
       ───────────────────────────────────────────── */
    if (msgArea && charCount) {
        msgArea.addEventListener('input', function () {
            const len = this.value.length;
            charCount.textContent = len + ' / 3000';
            charCount.style.color = len > 2700 ? '#EF4444' : len > 2400 ? '#F59E0B' : '';
        });
    }

    /* ─────────────────────────────────────────────
       FIELD VALIDATION
       ───────────────────────────────────────────── */
    function validateField(field) {
        const name  = field.getAttribute('name');
        const group = field.closest('.form-group');
        const errEl = group && group.querySelector('.field-error');
        if (!RULES[name]) return true;
        const valid = RULES[name].validate(field.value);
        field.setAttribute('aria-invalid', String(!valid));
        group && group.classList.toggle('has-error', !valid);
        group && group.classList.toggle('is-valid', valid);
        if (errEl) errEl.textContent = valid ? '' : RULES[name].message;
        return valid;
    }

    function validateAll() {
        let ok = true;
        form.querySelectorAll('[required]').forEach(f => { if (!validateField(f)) ok = false; });
        return ok;
    }

    form.querySelectorAll('input, textarea, select').forEach(field => {
        field.addEventListener('blur',  () => validateField(field));
        field.addEventListener('input', () => {
            if (field.closest('.form-group') && field.closest('.form-group').classList.contains('has-error')) {
                validateField(field);
            }
        });
    });

    /* ─────────────────────────────────────────────
       GLOBAL MESSAGE
       ───────────────────────────────────────────── */
    function showMsg(text, type) {
        if (!globalMsg) return;
        globalMsg.textContent = text;
        globalMsg.className   = 'form-global-message visible msg-' + type;
        if (type === 'success') setTimeout(() => globalMsg.classList.remove('visible'), 8000);
    }

    function setLoading(on) {
        if (!submitBtn) return;
        submitBtn.classList.toggle('loading', on);
        submitBtn.disabled = on;
        submitBtn.setAttribute('aria-busy', String(on));
    }

    /* ─────────────────────────────────────────────
       FORM SUBMIT — Web3Forms API
       ───────────────────────────────────────────── */
    form.addEventListener('submit', async function (e) {
        e.preventDefault();

        if (!validateAll()) {
            showMsg('Please fix the highlighted fields before submitting.', 'error');
            const first = form.querySelector('.has-error input, .has-error textarea');
            first && first.scrollIntoView({ behavior: 'smooth', block: 'center' });
            return;
        }

        if (msgArea && containsProfanity(msgArea.value)) {
            const g = msgArea.closest('.form-group');
            if (g) { g.classList.add('has-error'); g.querySelector('.field-error').textContent = 'Please keep your message professional.'; }
            msgArea.scrollIntoView({ behavior: 'smooth', block: 'center' });
            return;
        }

        setLoading(true);

        /* Build the data payload for Web3Forms */
        const name     = document.getElementById('name').value.trim();
        const email    = document.getElementById('email').value.trim();
        const phone    = document.getElementById('phone').value.trim();
        const service  = document.getElementById('service').value;
        const message  = msgArea ? msgArea.value.trim() : '';
        const channel  = form.querySelector('input[name="preferred_channel"]:checked');
        const chanVal  = channel ? channel.value : 'Email';

        const payload = {
            access_key:   WEB3FORMS_KEY,
            subject:      'New Enquiry — Laundry Care Website',
            from_name:    'Laundry Care Website',
            replyto:      email,

            /* These become the body of the email you receive */
            Name:                name,
            Email:               email,
            Phone:               phone,
            'Service Interest':  service || 'Not specified',
            'Preferred Channel': chanVal,
            Message:             message || 'No message provided.',

            /* Redirect behaviour — keep on same page */
            redirect: 'false',
            botcheck: ''   /* honeypot — must be empty; bots fill this in */
        };

        try {
            const res  = await fetch(WEB3FORMS_URL, {
                method:  'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body:    JSON.stringify(payload)
            });
            const data = await res.json();

            if (data.success) {
                showMsg('✅ Thank you ' + name.split(' ')[0] + '! Your enquiry has been received. We\'ll be in touch soon via ' + chanVal + '.', 'success');
                form.reset();
                if (charCount) charCount.textContent = '0 / 3000';
                form.querySelectorAll('.form-group').forEach(g => g.classList.remove('has-error', 'is-valid'));

                /* If WhatsApp was preferred, open chat after a short delay */
                if (chanVal === 'WhatsApp') {
                    setTimeout(() => {
                        const waText = encodeURIComponent("Hi Laundry Care! I'm " + name + " and I've just submitted an enquiry about " + (service || 'your services') + ". Looking forward to hearing from you!");
                        window.open('https://wa.me/27622839374?text=' + waText, '_blank', 'noopener,noreferrer');
                    }, 1500);
                }
            } else {
                showMsg(data.message || 'Something went wrong. Please try again or call us on 062 283 9374.', 'error');
            }

        } catch (err) {
            console.error('[form.js] Web3Forms error:', err);
            showMsg('Connection error. Please call us on 062 283 9374 or WhatsApp directly.', 'error');
        } finally {
            setLoading(false);
        }
    });

})();

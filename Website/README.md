# Laundry Care — Website

Premium hospitality linen & garment maintenance service website.
Cornubia, Parkgate · Serving Umhlanga & Durban North.

---

## Project Structure

```
laundry-care/
├── index.php               ← Main entry point (serves all HTML)
├── .htaccess               ← Apache security & routing rules
├── css/
│   └── style.css           ← Full design system stylesheet
├── js/
│   ├── preloader.js        ← Preloader dismiss + hero trigger
│   ├── navigation.js       ← Sticky nav, mobile drawer, active links
│   ├── animations.js       ← GSAP hero entrance + scroll reveals + tilt
│   ├── process.js          ← 6-stage process timeline animation
│   ├── counter.js          ← Animated number counters
│   ├── form.js             ← Form validation + AJAX submission
│   └── scroll.js           ← Header scroll state + back-to-top
└── php/
    └── contact.php         ← Form handler: validation, rate limit, email
```

---

## Setup

### 1. PHP & Web Server
- Requires **PHP 7.4+**
- Apache with `mod_rewrite` enabled (`.htaccess` configured)
- Or Nginx with equivalent `try_files` config

### 2. PHPMailer (Email Backend)

**Option A — Composer (recommended):**
```bash
cd /path/to/laundry-care
composer require phpmailer/phpmailer
```

**Option B — Manual:**
1. Download PHPMailer from https://github.com/PHPMailer/PHPMailer
2. Place `src/` folder at `/php/PHPMailer/src/`

### 3. SMTP Configuration
Edit `php/contact.php` and update the **CONFIG** section:

```php
define('SMTP_HOST', 'smtp.office365.com');      // Outlook SMTP
define('SMTP_PORT', 587);
define('SMTP_USER', 'laundry_care@outlook.com');
define('SMTP_PASS', 'YOUR_PASSWORD_HERE');       // ← Update this
```

> **Note:** For Outlook/Microsoft 365, you may need to enable SMTP AUTH
> in your Microsoft 365 Admin Center. Consider using an App Password.

### 4. Update Contact Details
Replace placeholder values throughout `index.php`:
- Phone number: `+27000000000` → actual number
- WhatsApp link: `https://wa.me/27000000000` → actual number
- Social media links → actual profile URLs
- Site URL in `contact.php`

### 5. Images
The about section uses a CSS placeholder. To add a real image:
- Replace `.about-img-placeholder` div with `<img src="..." alt="..." />`
- Hero background: Add a `background-image` to `.hero-section` in CSS

---

## Deployment Checklist
- [ ] Update SMTP credentials in `php/contact.php`
- [ ] Update phone numbers and social links in `index.php`
- [ ] Install PHPMailer (Composer or manual)
- [ ] Enable HTTPS redirect in `.htaccess`
- [ ] Test form submission end-to-end
- [ ] Replace image placeholders with real photography
- [ ] Update `SITE_URL` constant in `contact.php`
- [ ] Point DNS to server

---

## Browser Support
Modern browsers (Chrome, Firefox, Safari, Edge) — last 2 major versions.
Progressive enhancement used throughout; core content accessible without JS.

---

## Dependencies (CDN — no installation required)
| Library         | Version | Purpose                         |
|-----------------|---------|---------------------------------|
| GSAP            | 3.12.4  | Hero + scroll animations        |
| ScrollTrigger   | 3.12.4  | GSAP scroll-driven animations   |
| Animate.css     | 4.1.1   | CSS animation utility           |
| Font Awesome    | 6.5.0   | Icons                           |
| Google Fonts    | —       | Cormorant Garamond + DM Sans    |

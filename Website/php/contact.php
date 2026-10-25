<?php
/**
 * php/contact.php
 * ─────────────────────────────────────────────────────────────────────────────
 * Laundry Care — Enquiry Form Handler
 *
 * Responsibilities:
 *   1. CSRF token validation
 *   2. Server-side sanitisation and validation of all fields
 *   3. Rate limiting per IP (1 submission per 60 seconds)
 *   4. Dual email dispatch via PHPMailer SMTP:
 *        a) Notification to laundry_care@outlook.com
 *        b) Auto-confirmation to the enquirer
 *   5. JSON response for AJAX requests
 *   6. Session-based redirect with flash message for non-AJAX fallback
 *
 * Dependencies:
 *   - PHPMailer (installed via Composer or manual include)
 *   - PHP 7.4+
 *   - An SMTP-capable mail server / Outlook account credentials
 *
 * ─────────────────────────────────────────────────────────────────────────────
 * SETUP INSTRUCTIONS:
 *   1. Install PHPMailer:
 *        composer require phpmailer/phpmailer
 *      OR download and place in /php/PHPMailer/
 *
 *   2. Fill in SMTP credentials in the CONFIG section below.
 *
 *   3. Ensure /php/ is not directly browsable (add .htaccess deny if needed).
 * ─────────────────────────────────────────────────────────────────────────────
 */

declare(strict_types=1);

/* ══════════════════════════════════════════════════════
   BOOTSTRAP
   ══════════════════════════════════════════════════════ */

session_start();

// Detect AJAX request (set by form.js X-Requested-With header)
$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH'])
    && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respondAndExit(false, 'Invalid request method.', $isAjax);
}

/* ══════════════════════════════════════════════════════
   CONFIGURATION  ← Edit these values before deployment
   ══════════════════════════════════════════════════════ */

define('SMTP_HOST',     'smtp.office365.com');       // Outlook SMTP host
define('SMTP_PORT',     587);                         // TLS port
define('SMTP_USER',     'laundry_care@outlook.com'); // Your Outlook address
define('SMTP_PASS',     'YOUR_SMTP_PASSWORD_HERE');  // App password / SMTP password
define('SMTP_FROM_NAME','Laundry Care');

define('RECIPIENT_EMAIL', 'laundry_care@outlook.com');
define('RECIPIENT_NAME',  'Laundry Care Team');

define('SITE_NAME', 'Laundry Care');
define('SITE_URL',  'https://www.laundrcare.co.za');  // Update with live URL

// Rate limit: seconds between submissions per IP
define('RATE_LIMIT_SECONDS', 60);

/* ══════════════════════════════════════════════════════
   1. CSRF VALIDATION
   ══════════════════════════════════════════════════════ */

$submittedToken = $_POST['csrf_token'] ?? '';
$sessionToken   = $_SESSION['csrf_token'] ?? '';

if (empty($submittedToken) || !hash_equals($sessionToken, $submittedToken)) {
    respondAndExit(false, 'Security token mismatch. Please refresh the page and try again.', $isAjax);
}

// Regenerate CSRF token after successful validation
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

/* ══════════════════════════════════════════════════════
   2. RATE LIMITING (per IP, stored in session)
   ══════════════════════════════════════════════════════ */

$userIp      = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$rateLimitKey = 'last_submit_' . md5($userIp);
$lastSubmit   = $_SESSION[$rateLimitKey] ?? 0;
$now          = time();

if (($now - $lastSubmit) < RATE_LIMIT_SECONDS) {
    $waitSeconds = RATE_LIMIT_SECONDS - ($now - $lastSubmit);
    respondAndExit(
        false,
        "Please wait {$waitSeconds} second(s) before submitting another enquiry.",
        $isAjax
    );
}

/* ══════════════════════════════════════════════════════
   3. SANITISE & VALIDATE INPUT
   ══════════════════════════════════════════════════════ */

/**
 * sanitiseString
 * Trims whitespace and strips HTML/PHP tags from a string.
 *
 * @param  string|null $input
 * @return string
 */
function sanitiseString(?string $input): string {
    return htmlspecialchars(strip_tags(trim($input ?? '')), ENT_QUOTES, 'UTF-8');
}

// Collect and sanitise all fields
$name             = sanitiseString($_POST['name']              ?? '');
$email            = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
$phone            = sanitiseString($_POST['phone']             ?? '');
$service          = sanitiseString($_POST['service']           ?? '');
$message          = sanitiseString($_POST['message']           ?? '');
$preferredChannel = sanitiseString($_POST['preferred_channel'] ?? 'Email');

// Field validation errors array
$errors = [];

// Name: required, minimum 2 characters
if (mb_strlen($name) < 2) {
    $errors['name'] = 'A valid name is required.';
}

// Email: required, valid format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = 'A valid email address is required.';
}

// Phone: required, basic format check (7–20 chars, digits/spaces/+-())
if (!preg_match('/^[+\d][\d\s\-().]{6,19}$/', $phone)) {
    $errors['phone'] = 'A valid phone number is required.';
}

// Message: optional but cap at 2000 characters
if (mb_strlen($message) > 2000) {
    $errors['message'] = 'Message must be under 2000 characters.';
}

// If any validation errors, return them
if (!empty($errors)) {
    $firstError = reset($errors);
    respondAndExit(false, $firstError, $isAjax, $errors);
}

/* ══════════════════════════════════════════════════════
   4. LOAD PHPMAILER
   Try Composer autoload first, fall back to manual include.
   ══════════════════════════════════════════════════════ */

$composerAutoload = __DIR__ . '/../vendor/autoload.php';
$manualSrc        = __DIR__ . '/PHPMailer/src/';

if (file_exists($composerAutoload)) {
    require_once $composerAutoload;
} elseif (is_dir($manualSrc)) {
    require_once $manualSrc . 'Exception.php';
    require_once $manualSrc . 'PHPMailer.php';
    require_once $manualSrc . 'SMTP.php';
} else {
    // PHPMailer not available — use PHP's native mail() as fallback
    $emailSent = sendFallbackMail($name, $email, $phone, $service, $message, $preferredChannel);

    if ($emailSent) {
        $_SESSION[$rateLimitKey] = $now;
        respondAndExit(true, 'Enquiry received successfully!', $isAjax);
    } else {
        respondAndExit(false, 'Failed to send your enquiry. Please contact us directly.', $isAjax);
    }
}

/* ══════════════════════════════════════════════════════
   5. SEND EMAILS VIA PHPMAILER
   ══════════════════════════════════════════════════════ */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception as MailException;

/**
 * createMailer
 * Instantiates and configures a PHPMailer object with SMTP credentials.
 *
 * @return PHPMailer
 * @throws MailException
 */
function createMailer(): PHPMailer {
    $mail = new PHPMailer(true); // true = throw exceptions

    $mail->isSMTP();
    $mail->Host       = SMTP_HOST;
    $mail->Port       = SMTP_PORT;
    $mail->SMTPAuth   = true;
    $mail->Username   = SMTP_USER;
    $mail->Password   = SMTP_PASS;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->CharSet    = 'UTF-8';

    $mail->setFrom(SMTP_USER, SMTP_FROM_NAME);

    return $mail;
}

$notificationSent    = false;
$confirmationSent    = false;
$currentDate         = date('d F Y, H:i');
$serviceLabel        = $service ?: 'Not specified';
$messageBlock        = $message ?: '(No additional message provided)';

/* ── 5a. NOTIFICATION email to the Laundry Care team ── */
try {
    $mail = createMailer();
    $mail->addAddress(RECIPIENT_EMAIL, RECIPIENT_NAME);
    $mail->isHTML(true);
    $mail->Subject = "New Enquiry from {$name} — " . SITE_NAME;
    $mail->Body    = buildNotificationEmail($name, $email, $phone, $serviceLabel, $messageBlock, $preferredChannel, $currentDate);
    $mail->AltBody = buildNotificationEmailPlain($name, $email, $phone, $serviceLabel, $messageBlock, $preferredChannel, $currentDate);

    $mail->send();
    $notificationSent = true;
} catch (MailException $e) {
    error_log('[LaundryCare] Notification email failed: ' . $e->getMessage());
}

/* ── 5b. AUTO-CONFIRMATION email to the enquirer ── */
try {
    $mail = createMailer();
    $mail->addAddress($email, $name);
    $mail->isHTML(true);
    $mail->Subject = "We've received your enquiry — " . SITE_NAME;
    $mail->Body    = buildConfirmationEmail($name, $serviceLabel, $preferredChannel);
    $mail->AltBody = buildConfirmationEmailPlain($name, $serviceLabel, $preferredChannel);

    $mail->send();
    $confirmationSent = true;
} catch (MailException $e) {
    error_log('[LaundryCare] Confirmation email failed: ' . $e->getMessage());
}

/* ── Record rate limit timestamp only on successful dispatch ── */
if ($notificationSent || $confirmationSent) {
    $_SESSION[$rateLimitKey] = $now;
    respondAndExit(true, 'Your enquiry has been received! Check your inbox for a confirmation.', $isAjax);
} else {
    respondAndExit(false, 'We could not process your enquiry at this time. Please email laundry_care@outlook.com directly.', $isAjax);
}


/* ══════════════════════════════════════════════════════
   RESPONSE HELPER
   ══════════════════════════════════════════════════════ */

/**
 * respondAndExit
 * Sends a JSON response for AJAX calls, or redirects with a session
 * flash message for traditional (non-AJAX) form submissions.
 *
 * @param bool         $success
 * @param string       $message
 * @param bool         $isAjax
 * @param array        $fieldErrors  Optional per-field error map
 * @return never
 */
function respondAndExit(bool $success, string $message, bool $isAjax, array $fieldErrors = []): never {
    if ($isAjax) {
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode([
            'success'     => $success,
            'message'     => $message,
            'fieldErrors' => $fieldErrors,
        ]);
    } else {
        // Traditional form fallback — redirect back with flash
        $_SESSION['form_status']  = $success ? 'success' : 'error';
        $_SESSION['form_message'] = $message;
        $redirectUrl = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '../index.php';
        header('Location: ' . $redirectUrl);
    }
    exit;
}


/* ══════════════════════════════════════════════════════
   FALLBACK MAIL — php mail() when PHPMailer is absent
   ══════════════════════════════════════════════════════ */

/**
 * sendFallbackMail
 * Uses PHP's built-in mail() function as a last resort.
 * Not recommended for production — configure PHPMailer instead.
 */
function sendFallbackMail(
    string $name, string $email, string $phone,
    string $service, string $message, string $channel
): bool {
    $to      = RECIPIENT_EMAIL;
    $subject = "New Enquiry from {$name}";
    $body    = "Name: {$name}\nEmail: {$email}\nPhone: {$phone}\n"
             . "Service: {$service}\nPreferred Channel: {$channel}\n\nMessage:\n{$message}";
    $headers = "From: {$email}\r\nReply-To: {$email}\r\nContent-Type: text/plain; charset=UTF-8";

    return mail($to, $subject, $body, $headers);
}


/* ══════════════════════════════════════════════════════
   EMAIL TEMPLATES
   ══════════════════════════════════════════════════════ */

/**
 * buildNotificationEmail
 * HTML email sent to the Laundry Care team with full enquiry details.
 */
function buildNotificationEmail(
    string $name, string $email, string $phone,
    string $service, string $message, string $channel, string $date
): string {
    $whatsappLink = 'https://wa.me/' . preg_replace('/\D/', '', $phone);

    return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>New Enquiry</title></head>
<body style="margin:0;padding:0;font-family:'DM Sans',Helvetica,Arial,sans-serif;background:#F5F7FA;">
  <table width="100%" cellpadding="0" cellspacing="0" style="background:#F5F7FA;padding:40px 0;">
    <tr><td align="center">
      <table width="600" cellpadding="0" cellspacing="0" style="background:#FFFFFF;border-radius:12px;overflow:hidden;box-shadow:0 4px 24px rgba(11,30,61,0.1);">

        <!-- Header -->
        <tr><td style="background:#0B1E3D;padding:32px 40px;text-align:center;">
          <p style="color:#C9A84C;font-size:11px;letter-spacing:3px;text-transform:uppercase;margin:0 0 8px 0;">Incoming Enquiry</p>
          <h1 style="color:#FFFFFF;font-size:28px;font-weight:300;margin:0;font-family:Georgia,serif;">Laundry<em style="color:#C9A84C;">Care</em></h1>
        </td></tr>

        <!-- Body -->
        <tr><td style="padding:40px;">
          <p style="color:#4A5568;margin:0 0 24px 0;font-size:15px;">A new enquiry has been submitted via the website on <strong>{$date}</strong>.</p>

          <!-- Detail table -->
          <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #E8ECF0;border-radius:8px;overflow:hidden;margin-bottom:24px;">
            <tr style="background:#F5F7FA;">
              <td style="padding:12px 16px;font-size:12px;font-weight:600;letter-spacing:0.1em;text-transform:uppercase;color:#8A9BB0;width:40%;">Name</td>
              <td style="padding:12px 16px;font-size:15px;color:#0B1E3D;">{$name}</td>
            </tr>
            <tr>
              <td style="padding:12px 16px;font-size:12px;font-weight:600;letter-spacing:0.1em;text-transform:uppercase;color:#8A9BB0;">Email</td>
              <td style="padding:12px 16px;"><a href="mailto:{$email}" style="color:#0B1E3D;text-decoration:underline;font-size:15px;">{$email}</a></td>
            </tr>
            <tr style="background:#F5F7FA;">
              <td style="padding:12px 16px;font-size:12px;font-weight:600;letter-spacing:0.1em;text-transform:uppercase;color:#8A9BB0;">Phone</td>
              <td style="padding:12px 16px;font-size:15px;color:#0B1E3D;">{$phone}</td>
            </tr>
            <tr>
              <td style="padding:12px 16px;font-size:12px;font-weight:600;letter-spacing:0.1em;text-transform:uppercase;color:#8A9BB0;">Service</td>
              <td style="padding:12px 16px;font-size:15px;color:#0B1E3D;">{$service}</td>
            </tr>
            <tr style="background:#F5F7FA;">
              <td style="padding:12px 16px;font-size:12px;font-weight:600;letter-spacing:0.1em;text-transform:uppercase;color:#8A9BB0;">Preferred Channel</td>
              <td style="padding:12px 16px;font-size:15px;color:#0B1E3D;">{$channel}</td>
            </tr>
          </table>

          <!-- Message block -->
          <p style="font-size:12px;font-weight:600;letter-spacing:0.1em;text-transform:uppercase;color:#8A9BB0;margin:0 0 8px 0;">Message</p>
          <div style="background:#F5F7FA;border-radius:8px;padding:16px;color:#4A5568;font-size:14px;line-height:1.7;margin-bottom:32px;">
            {$message}
          </div>

          <!-- Quick action buttons -->
          <table cellpadding="0" cellspacing="0">
            <tr>
              <td style="padding-right:12px;">
                <a href="mailto:{$email}" style="display:inline-block;padding:12px 24px;background:#0B1E3D;color:#FFFFFF;text-decoration:none;border-radius:4px;font-size:13px;font-weight:600;letter-spacing:0.1em;">Reply via Email</a>
              </td>
              <td style="padding-right:12px;">
                <a href="{$whatsappLink}" style="display:inline-block;padding:12px 24px;background:#25D366;color:#FFFFFF;text-decoration:none;border-radius:4px;font-size:13px;font-weight:600;letter-spacing:0.1em;">Open WhatsApp</a>
              </td>
              <td>
                <a href="tel:{$phone}" style="display:inline-block;padding:12px 24px;background:#C9A84C;color:#0B1E3D;text-decoration:none;border-radius:4px;font-size:13px;font-weight:600;letter-spacing:0.1em;">Call Now</a>
              </td>
            </tr>
          </table>
        </td></tr>

        <!-- Footer -->
        <tr><td style="padding:20px 40px;border-top:1px solid #E8ECF0;text-align:center;">
          <p style="color:#8A9BB0;font-size:12px;margin:0;">This notification was generated by the Laundry Care website contact form.</p>
        </td></tr>

      </table>
    </td></tr>
  </table>
</body>
</html>
HTML;
}

/**
 * buildNotificationEmailPlain
 * Plain-text alternative for the notification email.
 */
function buildNotificationEmailPlain(
    string $name, string $email, string $phone,
    string $service, string $message, string $channel, string $date
): string {
    return <<<TEXT
LAUNDRY CARE — NEW WEBSITE ENQUIRY
Received: {$date}

---
Name:               {$name}
Email:              {$email}
Phone:              {$phone}
Service of Interest:{$service}
Preferred Response: {$channel}

MESSAGE:
{$message}
---

Reply: {$email} | WhatsApp: {$phone}
TEXT;
}

/**
 * buildConfirmationEmail
 * HTML auto-reply sent to the enquirer acknowledging receipt.
 */
function buildConfirmationEmail(string $name, string $service, string $channel): string {
    $firstName = explode(' ', $name)[0]; // Use first name only for personal touch

    return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>Enquiry Confirmed</title></head>
<body style="margin:0;padding:0;font-family:'DM Sans',Helvetica,Arial,sans-serif;background:#F5F7FA;">
  <table width="100%" cellpadding="0" cellspacing="0" style="background:#F5F7FA;padding:40px 0;">
    <tr><td align="center">
      <table width="600" cellpadding="0" cellspacing="0" style="background:#FFFFFF;border-radius:12px;overflow:hidden;box-shadow:0 4px 24px rgba(11,30,61,0.1);">

        <!-- Header -->
        <tr><td style="background:#0B1E3D;padding:32px 40px;text-align:center;">
          <h1 style="color:#FFFFFF;font-size:28px;font-weight:300;margin:0;font-family:Georgia,serif;">Laundry<em style="color:#C9A84C;">Care</em></h1>
          <p style="color:rgba(255,255,255,0.6);font-size:13px;margin:8px 0 0 0;">Premium Hospitality Linen Service · Cornubia, Parkgate</p>
        </td></tr>

        <!-- Body -->
        <tr><td style="padding:40px;">
          <p style="color:#C9A84C;font-size:11px;letter-spacing:3px;text-transform:uppercase;margin:0 0 16px 0;">Enquiry Confirmed</p>
          <h2 style="color:#0B1E3D;font-size:24px;font-weight:400;font-family:Georgia,serif;margin:0 0 20px 0;">Thank you, {$firstName}.</h2>
          <p style="color:#4A5568;font-size:15px;line-height:1.8;margin:0 0 16px 0;">
            We've received your enquiry regarding <strong>{$service}</strong> and our team will be in touch with you via <strong>{$channel}</strong> shortly.
          </p>
          <p style="color:#4A5568;font-size:15px;line-height:1.8;margin:0 0 32px 0;">
            During business hours (Mon–Fri, 7am–6pm), we typically respond within the hour. For urgent matters, feel free to reach us directly via the channels below.
          </p>

          <!-- Contact options -->
          <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #E8ECF0;border-radius:8px;overflow:hidden;margin-bottom:32px;">
            <tr style="background:#F5F7FA;">
              <td style="padding:14px 16px;font-size:13px;color:#4A5568;">📞 <strong>Phone:</strong> +27 (0)00 000 0000</td>
            </tr>
            <tr>
              <td style="padding:14px 16px;font-size:13px;color:#4A5568;">✉️ <strong>Email:</strong> laundry_care@outlook.com</td>
            </tr>
            <tr style="background:#F5F7FA;">
              <td style="padding:14px 16px;font-size:13px;color:#4A5568;">💬 <strong>WhatsApp:</strong> +27 (0)00 000 0000</td>
            </tr>
            <tr>
              <td style="padding:14px 16px;font-size:13px;color:#4A5568;">📍 <strong>Location:</strong> Cornubia, Parkgate, Durban North</td>
            </tr>
          </table>

          <p style="color:#8A9BB0;font-size:13px;margin:0;">We look forward to discussing how Laundry Care can elevate your linen standards.</p>
          <p style="color:#0B1E3D;font-size:14px;margin:16px 0 0 0;font-weight:600;">— The Laundry Care Team</p>
        </td></tr>

        <!-- Footer -->
        <tr><td style="padding:20px 40px;background:#0B1E3D;text-align:center;">
          <p style="color:rgba(255,255,255,0.4);font-size:11px;margin:0;">
            © <?php echo date('Y'); ?> Laundry Care · Cornubia, Parkgate · KwaZulu-Natal, South Africa
          </p>
        </td></tr>

      </table>
    </td></tr>
  </table>
</body>
</html>
HTML;
}

/**
 * buildConfirmationEmailPlain
 * Plain-text alternative for the confirmation email.
 */
function buildConfirmationEmailPlain(string $name, string $service, string $channel): string {
    $firstName = explode(' ', $name)[0];
    return <<<TEXT
LAUNDRY CARE — ENQUIRY CONFIRMATION

Hi {$firstName},

Thank you for reaching out! We've received your enquiry regarding {$service}.

Our team will contact you via {$channel} shortly. During business hours (Mon–Fri, 7am–6pm) we typically respond within the hour.

For urgent matters:
  Phone:    +27 (0)00 000 0000
  Email:    laundry_care@outlook.com
  WhatsApp: +27 (0)00 000 0000
  Address:  Cornubia, Parkgate, Durban North, KwaZulu-Natal

— The Laundry Care Team
TEXT;
}

# ══════════════════════════════════════════════════════════════════════════════
# Laundry Care — CLOUDFLARE_SECURITY.md
# Complete Cloudflare security configuration guide for your stack:
# Cloudflare (CDN/WAF) + InfinityFree (hosting) + GoDaddy (domain)
# ══════════════════════════════════════════════════════════════════════════════

## STEP 1 — DNS & SSL IN CLOUDFLARE DASHBOARD

1. Log in to dash.cloudflare.com
2. Select your domain
3. Go to SSL/TLS → set mode to "Full (Strict)"
   - NOT "Flexible" — that sends unencrypted traffic to InfinityFree
4. Go to SSL/TLS → Edge Certificates:
   - Enable "Always Use HTTPS" ✅
   - Enable "Automatic HTTPS Rewrites" ✅
   - Enable "HSTS" (min-fresh-age 6 months once tested) ✅
5. Go to SSL/TLS → Edge Certificates → Minimum TLS Version:
   - Set to TLS 1.2 (blocks old vulnerable connections)


## STEP 2 — CLOUDFLARE WAF (Web Application Firewall)

Go to Security → WAF:

### Managed Rules (Free plan)
- Enable "Cloudflare Managed Ruleset" ✅
- Enable "Cloudflare OWASP Core Ruleset" ✅
  This blocks the OWASP Top 10 attacks:
  SQL injection, XSS, path traversal, RFI, CSRF etc.

### Custom Rules — create these manually:

RULE 1: Block access to sensitive file extensions
  Expression:
    (http.request.uri.path matches "\.(env|sql|bak|log|git|json|md|swp|tmp)$")
  Action: Block

RULE 2: Block non-SA countries (optional — blocks most bot traffic)
  Expression:
    (not ip.geoip.country in {"ZA"} and not cf.client.bot)
  Action: Challenge (not Block — so real international clients can still pass)

RULE 3: Rate limit the contact form endpoint
  Expression:
    (http.request.uri.path eq "/php/contact.php" and http.request.method eq "POST")
  Action: Rate Limit — 5 requests per minute per IP
  This prevents spam bots hammering your enquiry form.

RULE 4: Block known bad IPs hitting your origin
  Expression:
    (cf.threat_score gt 14)
  Action: Challenge
  (Threat score > 14 catches most scanners without blocking real users)


## STEP 3 — CLOUDFLARE BOT FIGHT MODE

Go to Security → Bots:
- Enable "Bot Fight Mode" ✅ (Free plan)
  Intercepts and challenges automated traffic before it reaches your server.


## STEP 4 — CLOUDFLARE SECURITY LEVEL & CHALLENGE PASSAGE

Go to Security → Settings:
- Security Level: Medium (auto-challenges suspicious IPs)
- Challenge Passage: 30 minutes
- Browser Integrity Check: On ✅
- Privacy Pass Support: On ✅


## STEP 5 — CLOUDFLARE SCRAPE SHIELD

Go to Security → Scrape Shield:
- Email Address Obfuscation: On ✅
  (Hides laundry_care@outlook.com from email harvesting bots)
- Server-side Excludes: On ✅
- Hotlink Protection: On ✅


## STEP 6 — CLOUDFLARE PAGE RULES (or Rules → Redirects)

Rule 1: Force HTTPS everywhere
  URL pattern: http://*yourdomain.co.za/*
  Setting: Always Use HTTPS

Rule 2: Cache static assets at edge
  URL pattern: *yourdomain.co.za/css/*
  Setting: Cache Level = Cache Everything, Edge Cache TTL = 1 month
  (Repeat for /js/* and image files)

Rule 3: Bypass cache for PHP
  URL pattern: *yourdomain.co.za/*.php*
  Setting: Cache Level = Bypass


## STEP 7 — INFINITYFREE HOSTING SETTINGS

In your InfinityFree control panel:
1. Ensure PHP version is 8.0+ (go to PHP Config)
2. Your .htaccess file handles the rest at the Apache level
3. Note: InfinityFree does NOT support mod_php directives for php_value/php_flag
   Those lines are safe to leave in but will be silently ignored — no error.
   To set PHP settings on InfinityFree, use a php.ini file instead (see below).


## INFINITYFREE php.ini (create this file in your Website/ root folder)

Create a file named php.ini with these contents:

    display_errors = Off
    log_errors = On
    expose_php = Off
    max_execution_time = 30
    memory_limit = 64M
    post_max_size = 10M
    upload_max_filesize = 2M


## STEP 8 — GODADDY DNS SETTINGS

In GoDaddy DNS management, ensure:
- A record for @ points to Cloudflare's IP (Cloudflare sets this automatically)
- CNAME for www points to your root domain
- Do NOT expose your InfinityFree origin IP directly — always route through Cloudflare
  (Orange cloud icon must be ON for all records in Cloudflare DNS tab)

Add these DNS records in Cloudflare (not GoDaddy) for email security:
- SPF record (TXT): v=spf1 include:outlook.com -all
  (Prevents spammers spoofing your laundry_care@outlook.com address)
- DMARC record (TXT on _dmarc subdomain): v=DMARC1; p=quarantine; rua=mailto:laundry_care@outlook.com


## STEP 9 — WHAT TO UPDATE IN .htaccess BEFORE GOING LIVE

Search for "yourdomain.co.za" in .htaccess and replace with your real domain.
Then uncomment the HTTPS redirect block (lines starting with #).


## SECURITY SUMMARY — WHAT PROTECTS YOU

| Threat                    | Protected By                              |
|---------------------------|-------------------------------------------|
| SQL Injection             | .htaccess rules + Cloudflare OWASP WAF    |
| XSS (Cross-Site Scripting)| CSP header + .htaccess + Cloudflare WAF   |
| Clickjacking              | X-Frame-Options header                    |
| CSRF                      | CSRF token in contact.php                 |
| Form spam / bots          | Cloudflare Bot Fight + rate limit rule    |
| Brute force               | Cloudflare rate limiting                  |
| Path traversal            | .htaccess rewrite rules                   |
| Bad bots / scanners       | .htaccess user-agent block + Bot Fight    |
| Sensitive file exposure   | .htaccess FilesMatch deny                 |
| Email harvesting          | Cloudflare Email Obfuscation              |
| DDoS                      | Cloudflare network-level mitigation       |
| Man-in-the-middle         | HTTPS enforcement + HSTS                  |
| Hotlinking                | .htaccess + Cloudflare Hotlink Protection |
| Data interception         | TLS 1.2+ enforced                         |
| PHP error leaks           | display_errors Off in php.ini             |
| Server info leaks         | X-Powered-By removed, custom error pages  |

══════════════════════════════════════════════════════════════════════════════

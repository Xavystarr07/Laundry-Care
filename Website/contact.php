<?php
$page  = 'contact';
$title = 'Contact | Laundry Care';
$desc  = 'Get a quote from Laundry Care. Call, WhatsApp or email us.';
include __DIR__ . '/inc/header.php';
?>
<style>
.ct{position:relative;min-height:100vh;min-height:100svh;display:flex;align-items:center;padding:7rem 0 4rem;overflow:hidden;background:linear-gradient(135deg,#08162e,#0B1E3D 60%,#12305c);--t:#fff;--t2:rgba(255,255,255,.75);--g:rgba(255,255,255,.08);--gb:rgba(255,255,255,.22)}
[data-theme="light"] .ct{background:linear-gradient(135deg,#f2fafa,#e8f3f8 55%,#d9eaf4);--t:#0B1E3D;--t2:#4A5568;--g:rgba(255,255,255,.6);--gb:rgba(255,255,255,.9)}
.ct::before,.ct::after{content:'';position:absolute;border-radius:50%;pointer-events:none}
.ct::before{width:50vw;height:50vw;right:-12vw;top:-18vw;background:radial-gradient(circle,rgba(74,191,191,.35),transparent 65%)}
.ct::after{width:42vw;height:42vw;left:-12vw;bottom:-18vw;background:radial-gradient(circle,rgba(37,211,102,.2),transparent 65%)}
.ct .container{position:relative;z-index:1;width:100%}
.ct-head{text-align:center;margin-bottom:2.5rem}
.ct-head h1{color:var(--t);font-size:clamp(2rem,5vw,3.6rem)}
.ct-head p{color:var(--t2);margin-top:.5rem}
.ct-cards{display:grid;grid-template-columns:repeat(3,1fr);gap:1.5rem}
.ct-card{display:flex;flex-direction:column;justify-content:space-between;min-height:300px;padding:2rem;border-radius:24px;color:var(--t);background:var(--g);border:1px solid var(--gb);-webkit-backdrop-filter:blur(16px);backdrop-filter:blur(16px);box-shadow:0 10px 40px rgba(0,0,0,.18);transition:transform .4s,border-color .4s,box-shadow .4s}
.ct-card:hover{transform:translateY(-10px);border-color:var(--c);box-shadow:0 30px 70px rgba(0,0,0,.3),0 0 40px var(--c)}
.ct-call{--c:#4ABFBF}.ct-wa{--c:#25D366}.ct-mail{--c:#6DCFCF}
.ct-ico{width:68px;height:68px;border-radius:20px;display:grid;place-items:center;font-size:1.7rem;color:var(--c);background:rgba(255,255,255,.1);border:1px solid var(--gb)}
.ct-card h2{font-size:1.6rem;margin:1.5rem 0 .4rem;color:var(--t)}
.ct-card p{color:var(--t2)}
.ct-card strong{display:block;margin-top:.8rem;font-size:1.15rem;word-break:break-word}
.ct-go{margin-top:1.25rem;color:var(--c);font-weight:700}
.ct-go i{transition:transform .3s}
.ct-card:hover .ct-go i{transform:translateX(6px)}
.ct-foot{text-align:center;margin-top:2rem;color:var(--t2);font-size:.9rem}
.ct-foot i{color:var(--teal);margin-right:.4rem}

/* Page content rises in after the intro */
.ct-head,.ct-card,.ct-foot{opacity:0;animation:ct-in 1s cubic-bezier(.22,1,.36,1) forwards}
.ct-head{animation-delay:3.9s}
.ct-card:nth-child(1){animation-delay:4.1s}.ct-card:nth-child(2){animation-delay:4.3s}.ct-card:nth-child(3){animation-delay:4.5s}
.ct-foot{animation-delay:4.8s}
@keyframes ct-in{from{opacity:0;transform:translateY(60px) scale(.94)}to{opacity:1;transform:none}}

/* ===== INTRO: connect network, then split open ===== */
.cx{position:fixed;inset:0;z-index:600;pointer-events:none;animation:cx-gone 0s 4.4s forwards}
.cx-a,.cx-b{position:absolute;left:0;right:0;height:50.5%;background:#08162e}
.cx-a{top:0;animation:cx-up 1s cubic-bezier(.76,0,.24,1) 3.5s forwards}
.cx-b{bottom:0;animation:cx-down 1s cubic-bezier(.76,0,.24,1) 3.5s forwards}
[data-theme="light"] .cx-a,[data-theme="light"] .cx-b{background:#e8f3f8}
.cx-stage{position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:1.5rem;animation:cx-fade .5s ease 3.2s forwards}
.cx-net{position:relative;width:min(300px,78vw);height:min(260px,68vw)}
.cx-net svg{position:absolute;inset:0;width:100%;height:100%;overflow:visible}
.cx-net line{stroke:#4ABFBF;stroke-width:2;stroke-linecap:round;stroke-dasharray:1;stroke-dashoffset:1;filter:drop-shadow(0 0 6px #4ABFBF);animation:cx-draw .7s ease forwards}
.cx-net line:nth-child(1){animation-delay:1s}.cx-net line:nth-child(2){animation-delay:1.25s}.cx-net line:nth-child(3){animation-delay:1.5s}
.cx-n{position:absolute;width:68px;height:68px;border-radius:50%;display:grid;place-items:center;font-size:1.6rem;color:var(--c);background:rgba(74,191,191,.12);border:1.5px solid var(--c);box-shadow:0 0 30px var(--c);transform:translate(-50%,-50%) scale(0);animation:cx-pop .7s cubic-bezier(.34,1.56,.64,1) forwards}
.cx-n1{left:50%;top:15%;--c:#4ABFBF;animation-delay:.3s}
.cx-n2{left:16%;top:82%;--c:#25D366;animation-delay:.55s}
.cx-n3{left:84%;top:82%;--c:#6DCFCF;animation-delay:.8s}
.cx-ring{position:absolute;left:50%;top:60%;width:20px;height:20px;margin:-10px;border-radius:50%;border:2px solid #4ABFBF;opacity:0;animation:cx-ripple 2.2s ease-out infinite}
.cx-ring:nth-of-type(2){animation-delay:.55s}.cx-ring:nth-of-type(3){animation-delay:1.1s}
.cx-type{display:inline-block;overflow:hidden;white-space:nowrap;max-width:0;font:600 clamp(1.5rem,5.5vw,3rem)/1.2 var(--font-display);color:#fff;border-right:3px solid #4ABFBF;animation:cx-typing 1.1s steps(18) 1.7s forwards,cx-caret .6s step-end infinite}
[data-theme="light"] .cx-type{color:#0B1E3D}
@keyframes cx-pop{to{transform:translate(-50%,-50%) scale(1)}}
@keyframes cx-draw{to{stroke-dashoffset:0}}
@keyframes cx-ripple{0%{opacity:.7;transform:scale(1)}100%{opacity:0;transform:scale(14)}}
@keyframes cx-typing{to{max-width:14em}}
@keyframes cx-caret{50%{border-color:transparent}}
@keyframes cx-fade{to{opacity:0;transform:scale(1.08)}}
@keyframes cx-up{to{transform:translateY(-101%)}}
@keyframes cx-down{to{transform:translateY(101%)}}
@keyframes cx-gone{to{visibility:hidden}}

@media(max-width:860px){.ct-cards{grid-template-columns:1fr}.ct-card{min-height:0}.ct{padding-top:6rem}}
@media(prefers-reduced-motion:reduce){.cx{display:none}.ct-head,.ct-card,.ct-foot{animation:none;opacity:1}}
</style>

<div class="cx" aria-hidden="true">
<div class="cx-a"></div><div class="cx-b"></div>
<div class="cx-stage">
<div class="cx-net">
<i class="cx-ring"></i><i class="cx-ring"></i><i class="cx-ring"></i>
<svg viewBox="0 0 300 260"><line x1="150" y1="39" x2="48" y2="213" pathLength="1"/><line x1="48" y1="213" x2="252" y2="213" pathLength="1"/><line x1="252" y1="213" x2="150" y2="39" pathLength="1"/></svg>
<div class="cx-n cx-n1"><i class="fa-solid fa-phone"></i></div>
<div class="cx-n cx-n2"><i class="fa-brands fa-whatsapp"></i></div>
<div class="cx-n cx-n3"><i class="fa-solid fa-envelope"></i></div>
</div>
<div class="cx-type">Let&rsquo;s get in touch</div>
</div>
</div>

<main id="main">
<section class="ct">
<div class="container">
<div class="ct-head"><h1>Choose how <em>you&rsquo;d like to reach us</em></h1><p>We&rsquo;ll get back to you with a quote.</p></div>
<div class="ct-cards">
<a href="tel:+27622839374" class="ct-card ct-call"><div><div class="ct-ico"><i class="fa-solid fa-phone"></i></div><h2>Call</h2><p>Speak to our team during office hours.</p><strong>062 283 9374</strong></div><span class="ct-go">Call now <i class="fa-solid fa-arrow-right"></i></span></a>
<a href="https://wa.me/27622839374?text=Hi%20Laundry%20Care%2C%20I'd%20like%20a%20quote." target="_blank" rel="noopener" class="ct-card ct-wa"><div><div class="ct-ico"><i class="fa-brands fa-whatsapp"></i></div><h2>WhatsApp</h2><p>Send a message or photos of what you need washed.</p><strong>062 283 9374</strong></div><span class="ct-go">Open WhatsApp <i class="fa-solid fa-arrow-right"></i></span></a>
<a href="mailto:laundry_care@outlook.com?subject=Quote%20request" class="ct-card ct-mail"><div><div class="ct-ico"><i class="fa-solid fa-envelope"></i></div><h2>Email</h2><p>Prefer to write it down? Send us the details.</p><strong>laundry_care@outlook.com</strong></div><span class="ct-go">Send an email <i class="fa-solid fa-arrow-right"></i></span></a>
</div>
<p class="ct-foot"><i class="fa-solid fa-clock"></i> Office hours 8am to 5pm &nbsp;&middot;&nbsp; <i class="fa-solid fa-location-dot"></i> Cornubia, Parkgate, serving all of Durban</p>
</div>
</section>
</main>
<script>setTimeout(function(){var c=document.querySelector('.cx');c&&c.remove()},4400);</script>
<?php include __DIR__ . '/inc/footer.php'; ?>
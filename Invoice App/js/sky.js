document.head.appendChild(Object.assign(document.createElement('script'), { src: 'js/layout.js' }));
// sky.js — light = soft blue sky, dark = rotating purple particle galaxy. Animated switch.
(function () {
if (matchMedia('(prefers-reduced-motion: reduce)').matches) return;
const st = document.createElement('style');
st.textContent = '@media print{canvas.lc-sky{display:none!important}}';
document.head.appendChild(st);
const mk = z => { const c = document.createElement('canvas'); c.className = 'lc-sky'; c.style.cssText = `position:fixed;inset:0;width:100%;height:100%;pointer-events:none;z-index:${z}`; document.body.appendChild(c); return c; };
const bg = mk(-1), fx = mk(99996), bx = bg.getContext('2d'), fc = fx.getContext('2d');
fx.style.display = 'none';
let W, H, D = Math.min(devicePixelRatio || 1, 2);
function size() { W = innerWidth; H = innerHeight; [bg, fx].forEach(c => { c.width = W * D; c.height = H * D; }); bx.setTransform(D, 0, 0, D, 0, 0); fc.setTransform(D, 0, 0, D, 0, 0); }
addEventListener('resize', size); size();
const R = (a, b) => a + Math.random() * (b - a);
const dark = () => document.body.classList.contains('dark-mode');

function cloud(c, x, y, s, a) {
    c.fillStyle = `rgba(255,255,255,${a})`; c.beginPath();
    [[0, 0, 26], [28, -10, 32], [60, 0, 26], [30, 8, 28]].forEach(([dx, dy, r]) => { c.moveTo(x + (dx + r) * s, y + dy * s); c.arc(x + dx * s, y + dy * s, r * s, 0, 6.29); });
    c.fill();
}
// soft shaded cumulus sprites (white tops, blue-grey bellies)
function sprite() {
    const w = 460, h = 200, c = document.createElement('canvas'); c.width = w; c.height = h; const x = c.getContext('2d'), n = 22 + (R(0, 10) | 0);
    for (let i = 0; i < n; i++) {
        const t = i / n, px = w * (.14 + .72 * t) + R(-20, 20), r = R(30, 62) * (1 - Math.abs(t - .5) * .9) + 22, py = h * .62 - Math.sin(t * 3.14) * R(20, 55) + R(-10, 10);
        const g = x.createRadialGradient(px, py, 0, px, py, r);
        g.addColorStop(0, 'rgba(255,255,255,.95)'); g.addColorStop(.6, 'rgba(255,255,255,.55)'); g.addColorStop(1, 'rgba(255,255,255,0)');
        x.fillStyle = g; x.beginPath(); x.arc(px, py, r, 0, 6.29); x.fill();
    }
    x.globalCompositeOperation = 'source-atop';
    const s = x.createLinearGradient(0, h * .35, 0, h * .85); s.addColorStop(0, 'rgba(255,255,255,0)'); s.addColorStop(1, 'rgba(140,170,210,.55)');
    x.fillStyle = s; x.fillRect(0, 0, w, h);
    return c;
}
const SP = [...Array(5)].map(sprite);
const clouds = [...Array(11)].map((_, i) => { const l = i % 3; return { spr: SP[i % 5], x: R(-200, W), y: [R(.04, .28), R(.2, .5), R(.4, .72)][l] * H, s: [.55, .9, 1.4][l] * R(.8, 1.2), v: [.06, .14, .28][l], a: [.55, .8, .95][l] }; }).sort((a, b) => a.s - b.s);
const birds = [...Array(6)].map(() => ({ x: R(0, W), y: R(.12, .4) * H, s: R(.6, 1.3), v: R(.25, .5), ph: R(0, 6) }));
const stars = [...Array(140)].map(() => ({ x: R(0, 1), y: R(0, 1), r: R(.3, 1.3), p: R(0, 6.3), v: R(.8, 2.5) }));

// ── Galaxy: 3 spiral arms + bulge, colour by radius ──
const COL = ['255,236,222', '255,170,215', '196,132,252', '150,100,240', '99,130,246', '255,255,255'];
const B = COL.map(() => []);
for (let i = 0; i < 8000; i++) {
    let r, th;
    if (i % 6 === 0) { r = Math.pow(Math.random(), 3) * .22; th = R(0, 6.28); }                 // bulge
    else {
        r = Math.pow(Math.random(), 1.45);
        th = (i % 3) * 2.0944 + r * 5.4 + (Math.random() + Math.random() - 1) * (.7 - r * .35);  // arm + scatter
    }
    const h = (Math.random() - .5) * .05 * (1 - r * .6);
    const c = Math.random() < .03 ? 5 : r < .1 ? 0 : r < .28 ? 1 : r < .55 ? 2 : r < .8 ? 3 : 4;
    B[c].push({ r, th, h, z: Math.random() < .12 ? 2 : 1 });
}
const TILT = 1.12, ROT = -.35, cT = Math.cos(TILT), sT = Math.sin(TILT), cR = Math.cos(ROT), sR = Math.sin(ROT);
let ang = 0, boost = 1, last = 0;

function galaxy(K) {
    const S = Math.min(W * .62, H * 1.05), cx = W * .5, cy = H * .52;
    const g = bx.createRadialGradient(cx, cy, 0, cx, cy, S * .35);
    g.addColorStop(0, `rgba(255,205,235,${.45 * K})`); g.addColorStop(.35, `rgba(168,85,247,${.2 * K})`); g.addColorStop(1, 'rgba(60,20,120,0)');
    bx.fillStyle = g; bx.fillRect(0, 0, W, H);
    bx.globalCompositeOperation = 'lighter';
    B.forEach((arr, ci) => {
        bx.fillStyle = `rgba(${COL[ci]},${.8 * K})`;
        for (const p of arr) {
            const t = p.th + ang * (.3 / (.18 + p.r));
            const x0 = p.r * Math.cos(t), z0 = p.r * Math.sin(t);
            const yy = z0 * sT + p.h * cT;
            bx.fillRect(cx + (x0 * cR - yy * sR) * S, cy + (x0 * sR + yy * cR) * S, p.z, p.z);
        }
    });
    bx.globalCompositeOperation = 'source-over';
}

let m = dark() ? 1 : 0, shoot = null, t0 = 0, dir = 0, puffs = [], prev = dark();
function go(d) {
    dir = d; t0 = performance.now();
    puffs = [...Array(18)].map(() => ({ x: R(-200, W), y: R(-.2, 1.1) * H, s: R(.6, 2.2), k: R(0, 5) | 0 }));
    if (d) boost = 14;
    fx.style.display = 'block';
}

function frame(now) {
    requestAnimationFrame(frame);
    if (document.hidden) { last = now; return; }
    const dt = Math.min(now - last, 50); last = now;
    boost += (1 - boost) * .018;
    ang += dt * .00012 * boost;
    m += ((dark() ? 1 : 0) - m) * .04;
    const L = 1 - m, K = m;
    bx.clearRect(0, 0, W, H);

    if (L > .01) {
        const sg = bx.createLinearGradient(0, 0, 0, H);
        sg.addColorStop(0, `rgba(88,166,240,${L})`); sg.addColorStop(.55, `rgba(176,217,251,${L})`); sg.addColorStop(1, `rgba(244,251,255,${L})`);
        bx.fillStyle = sg; bx.fillRect(0, 0, W, H);
        const sx = W * .86, sy = H * .12;
        bx.save(); bx.translate(sx, sy); bx.rotate(now / 40000);           // slow light rays
        for (let i = 0; i < 10; i++) {
            bx.rotate(.628);
            const rg = bx.createLinearGradient(0, 0, H * 1.3, 0); rg.addColorStop(0, `rgba(255,250,220,${.16 * L})`); rg.addColorStop(1, 'rgba(255,250,220,0)');
            bx.fillStyle = rg; bx.beginPath(); bx.moveTo(0, 0); bx.lineTo(H * 1.3, -H * .09); bx.lineTo(H * 1.3, H * .09); bx.fill();
        }
        bx.restore();
        const g = bx.createRadialGradient(sx, sy, 0, sx, sy, H * .6);
        g.addColorStop(0, `rgba(255,255,255,${.95 * L})`); g.addColorStop(.08, `rgba(255,246,200,${.8 * L})`); g.addColorStop(.3, `rgba(255,236,170,${.25 * L})`); g.addColorStop(1, 'rgba(255,236,170,0)');
        bx.fillStyle = g; bx.fillRect(0, 0, W, H);
        clouds.forEach(c => {
            c.x += c.v; if (c.x > W + 50) c.x = -460 * c.s;
            bx.globalAlpha = c.a * L; bx.drawImage(c.spr, c.x, c.y, 460 * c.s, 200 * c.s);
        });
        bx.globalAlpha = 1;
        birds.forEach(b => {
            b.x += b.v; if (b.x > W + 30) { b.x = -30; b.y = R(.12, .4) * H; }
            const f = Math.sin(now / 180 + b.ph) * 5 * b.s, w = 11 * b.s;
            bx.strokeStyle = `rgba(50,70,100,${.55 * L})`; bx.lineWidth = 1.4; bx.beginPath();
            bx.moveTo(b.x - w, b.y - f); bx.quadraticCurveTo(b.x - w / 2, b.y - f * 1.2 - 3, b.x, b.y);
            bx.quadraticCurveTo(b.x + w / 2, b.y - f * 1.2 - 3, b.x + w, b.y - f); bx.stroke();
        });
        const hz = bx.createLinearGradient(0, H * .75, 0, H); hz.addColorStop(0, 'rgba(255,255,255,0)'); hz.addColorStop(1, `rgba(255,255,255,${.5 * L})`);
        bx.fillStyle = hz; bx.fillRect(0, H * .75, W, H * .25);
    }
    if (K > .01) {
        stars.forEach(s => { bx.fillStyle = `rgba(255,255,255,${(.3 + .7 * Math.abs(Math.sin(now / 1000 * s.v + s.p))) * K * .8})`; bx.beginPath(); bx.arc(s.x * W, s.y * H, s.r, 0, 6.29); bx.fill(); });
        galaxy(K);
        if (!shoot && Math.random() < .002 * K) shoot = { x: R(.3, 1) * W, y: R(0, .4) * H };
        if (shoot) {
            shoot.x -= 11; shoot.y += 5.5;
            const g2 = bx.createLinearGradient(shoot.x, shoot.y, shoot.x + 100, shoot.y - 50);
            g2.addColorStop(0, `rgba(255,255,255,${K})`); g2.addColorStop(1, 'rgba(255,255,255,0)');
            bx.strokeStyle = g2; bx.lineWidth = 1.6; bx.beginPath(); bx.moveTo(shoot.x, shoot.y); bx.lineTo(shoot.x + 100, shoot.y - 50); bx.stroke();
            if (shoot.x < -120 || shoot.y > H + 50) shoot = null;
        }
    }

    if (t0) {
        const p = (now - t0) / 3200;
        fc.clearRect(0, 0, W, H);
        if (p >= 1) { t0 = 0; fx.style.display = 'none'; return; }
        // a = altitude 0 (ground) .. 1 (space). Going dark we climb, going light we descend.
        const a = dir ? p : 1 - p, al = Math.min(1, a / .06, (1 - a) / .25);
        const stops = [[0, 88, 166, 240, 244, 251, 255], [.3, 30, 90, 200, 120, 190, 250], [.55, 60, 30, 130, 60, 90, 190], [.8, 12, 8, 35, 50, 20, 100], [1, 5, 6, 15, 11, 13, 36]];
        let i = 0; while (i < 3 && a > stops[i + 1][0]) i++;
        const s0 = stops[i], s1 = stops[i + 1], f = Math.min(1, Math.max(0, (a - s0[0]) / (s1[0] - s0[0])));
        const mx = k => Math.round(s0[k] + (s1[k] - s0[k]) * f);
        const sg = fc.createLinearGradient(0, 0, 0, H);
        sg.addColorStop(0, `rgba(${mx(1)},${mx(2)},${mx(3)},${al})`); sg.addColorStop(1, `rgba(${mx(4)},${mx(5)},${mx(6)},${al})`);
        fc.fillStyle = sg; fc.fillRect(0, 0, W, H);
        const sa = Math.min(1, Math.max(0, (a - .45) / .35)) * al;
        if (sa > 0) stars.forEach(s => { fc.fillStyle = `rgba(255,255,255,${sa * (.3 + .7 * Math.abs(Math.sin(now / 1000 * s.v + s.p)))})`; fc.beginPath(); fc.arc(s.x * W, s.y * H, s.r, 0, 6.29); fc.fill(); });
        const ca = Math.max(0, 1 - a * 1.6) * al;
        if (ca > 0) puffs.forEach(u => { const y = u.y + (dir ? p : 1 - p) * 1.6 * H * (u.s / 1.2); fc.globalAlpha = ca * .95; fc.drawImage(SP[u.k], u.x, y, 460 * u.s, 200 * u.s); });
        fc.globalAlpha = 1;
    }
}
requestAnimationFrame(frame);

new MutationObserver(() => { const d = dark(); if (d !== prev) { prev = d; go(d ? 1 : 0); } })
    .observe(document.body, { attributes: true, attributeFilter: ['class'] });
})();

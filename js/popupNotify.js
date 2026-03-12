// popupNotify.js — Robot popup with creative gradient backgrounds per type

(function () {

const style = document.createElement('style');
style.textContent = `
  @import url('https://fonts.googleapis.com/css2?family=Nunito:wght@600;700;800&display=swap');

  #robotPopup {
    position: fixed;
    bottom: 30px;
    right: 30px;
    max-width: 330px;
    min-width: 270px;
    border-radius: 22px;
    box-shadow: 0 12px 40px rgba(0,0,0,0.22);
    display: flex;
    align-items: flex-start;
    gap: 14px;
    padding: 18px 20px 22px 18px;
    z-index: 99999;
    font-family: 'Nunito', 'Segoe UI', Arial, sans-serif;
    font-size: 14px;
    opacity: 0;
    transform: translateY(30px) scale(0.92);
    transition: opacity 0.35s ease, transform 0.35s ease;
    pointer-events: none;
    overflow: hidden;
    /* Default: info */
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
    color: #e0e0f0;
    border: 1px solid rgba(255,255,255,0.1);
  }

  /* Animated starfield background layer */
  #robotPopup::before {
    content: '✦ ✧ ✦ ✧ ✦ ✧ ✦ ✧ ✦ ✧ ✦ ✧';
    position: absolute;
    top: 4px; left: 0; right: 0;
    font-size: 9px;
    color: rgba(255,255,255,0.12);
    letter-spacing: 6px;
    text-align: center;
    pointer-events: none;
    animation: starsFloat 6s linear infinite;
  }
  @keyframes starsFloat {
    0%   { transform: translateX(0); }
    100% { transform: translateX(-40px); }
  }

  #robotPopup.show {
    opacity: 1;
    transform: translateY(0) scale(1);
    pointer-events: all;
  }

  /* ── Type themes ── */
  #robotPopup.type-error {
    background: linear-gradient(135deg, #2d0a0a 0%, #5c1010 50%, #8b0000 100%);
    border-color: rgba(255,100,100,0.25);
  }
  #robotPopup.type-success {
    background: linear-gradient(135deg, #0a2d12 0%, #0f5c22 50%, #157a30 100%);
    border-color: rgba(100,255,140,0.25);
  }
  #robotPopup.type-warning {
    background: linear-gradient(135deg, #2d1f00 0%, #5c3d00 50%, #8b6000 100%);
    border-color: rgba(255,200,80,0.25);
  }
  #robotPopup.type-info {
    background: linear-gradient(135deg, #0a1a2d 0%, #0f3060 50%, #1a4a8b 100%);
    border-color: rgba(100,180,255,0.25);
  }
  #robotPopup.type-dupe {
    background: linear-gradient(135deg, #1e0a2d 0%, #3d0f5c 50%, #5a0f8b 100%);
    border-color: rgba(180,100,255,0.25);
  }

  /* Decorative corner accent */
  #robotPopup::after {
    content: '';
    position: absolute;
    top: -30px; right: -30px;
    width: 80px; height: 80px;
    border-radius: 50%;
    background: rgba(255,255,255,0.05);
    pointer-events: none;
  }

  #robotPopupFace {
    font-size: 38px;
    line-height: 1;
    flex-shrink: 0;
    animation: robotBob 1.2s ease-in-out infinite;
    filter: drop-shadow(0 2px 6px rgba(0,0,0,0.4));
    position: relative; z-index: 1;
  }
  @keyframes robotBob {
    0%, 100% { transform: translateY(0) rotate(-3deg); }
    50%       { transform: translateY(-5px) rotate(3deg); }
  }

  #robotPopupBody { flex: 1; position: relative; z-index: 1; }

  #robotPopupTitle {
    font-weight: 800;
    font-size: 14.5px;
    margin-bottom: 5px;
    color: #ffffff;
    text-shadow: 0 1px 4px rgba(0,0,0,0.5);
    letter-spacing: 0.2px;
  }

  #robotPopupMsg {
    color: rgba(255,255,255,0.85);
    line-height: 1.5;
    font-size: 13.5px;
  }

  #robotPopupSolution {
    margin-top: 8px;
    padding: 6px 10px;
    background: rgba(255,255,255,0.1);
    border-radius: 8px;
    border-left: 3px solid rgba(255,255,255,0.4);
    color: rgba(255,255,255,0.95);
    font-weight: 700;
    font-size: 12.5px;
    line-height: 1.4;
    backdrop-filter: blur(4px);
  }

  #robotPopupClose {
    background: rgba(255,255,255,0.12);
    border: 1px solid rgba(255,255,255,0.18);
    color: rgba(255,255,255,0.7);
    font-size: 14px;
    cursor: pointer;
    padding: 3px 7px;
    flex-shrink: 0;
    margin: 0 !important;
    box-shadow: none !important;
    border-radius: 50%;
    line-height: 1;
    transition: all 0.2s;
    align-self: flex-start;
    position: relative; z-index: 1;
  }
  #robotPopupClose:hover {
    background: rgba(255,80,80,0.4);
    color: white;
    transform: rotate(90deg);
  }

  #robotPopupProgress {
    position: absolute;
    bottom: 0; left: 0;
    height: 4px;
    width: 100%;
    overflow: hidden;
    border-radius: 0 0 22px 22px;
    background: rgba(255,255,255,0.08);
  }
  #robotPopupProgressBar {
    height: 100%;
    width: 100%;
    background: linear-gradient(90deg, rgba(255,255,255,0.3), rgba(255,255,255,0.7));
    border-radius: 0 0 22px 22px;
    transition: width linear;
  }
`;
document.head.appendChild(style);

const popup = document.createElement('div');
popup.id = 'robotPopup';
popup.innerHTML = `
  <div id="robotPopupFace">🤖</div>
  <div id="robotPopupBody">
    <div id="robotPopupTitle"></div>
    <div id="robotPopupMsg"></div>
    <div id="robotPopupSolution"></div>
  </div>
  <button id="robotPopupClose" title="Close">✕</button>
  <div id="robotPopupProgress"><div id="robotPopupProgressBar"></div></div>
`;
document.body.appendChild(popup);
document.getElementById('robotPopupClose').addEventListener('click', hidePopup);

let autoTimer  = null;
let watchTimer = null;

const FACES  = { error:'😟', success:'🎉', warning:'😬', info:'🤖', dupe:'🤔' };
const TITLES = { error:'Oops! Something went wrong', success:'Woohoo! All done!', warning:'Hey, heads up!', info:'Just so you know...', dupe:'Hold on a second!' };

window.showPopup = function(type, msg, solution = '', duration = 6000, watchField = null) {
  const el = document.getElementById('robotPopup');
  el.className = 'show type-' + type;
  document.getElementById('robotPopupFace').textContent     = FACES[type]  || '🤖';
  document.getElementById('robotPopupTitle').textContent    = TITLES[type] || '';
  document.getElementById('robotPopupMsg').textContent      = msg;
  document.getElementById('robotPopupSolution').textContent = solution;
  document.getElementById('robotPopupSolution').style.display = solution ? 'block' : 'none';

  const bar = document.getElementById('robotPopupProgressBar');
  bar.style.transition = 'none';
  bar.style.width = '100%';
  if (autoTimer) clearTimeout(autoTimer);
  if (watchTimer) { clearInterval(watchTimer); watchTimer = null; }

  if (duration > 0) {
    requestAnimationFrame(() => requestAnimationFrame(() => {
      bar.style.transition = `width ${duration}ms linear`;
      bar.style.width = '0%';
    }));
    autoTimer = setTimeout(hidePopup, duration);
  }

  // If a field is passed, watch it and hide popup when it's fixed
  if (watchField) {
    watchTimer = setInterval(() => {
      const el = document.querySelector(watchField);
      if (!el) { clearInterval(watchTimer); watchTimer = null; return; }
      // Hide if field now has a valid value
      if (el.value && el.value.trim() !== '' && !el.classList.contains('invalid')) {
        hidePopup();
        clearInterval(watchTimer);
        watchTimer = null;
      }
    }, 300);
  }
};

function hidePopup() {
  document.getElementById('robotPopup').classList.remove('show');
  if (autoTimer)  { clearTimeout(autoTimer);   autoTimer  = null; }
  if (watchTimer) { clearInterval(watchTimer);  watchTimer = null; }
}

})();

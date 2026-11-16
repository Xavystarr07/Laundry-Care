<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Hotels — Laundry Care</title>
<link rel="stylesheet" href="monthly_statement/css/statement.css"><link rel="stylesheet" href="html_css/manage.css">
<style>
.modes{display:flex;gap:10px;flex-wrap:wrap;margin:18px 0}.modes[hidden]{display:none}
.modes .btn{opacity:.55}.modes .btn.on{opacity:1}
.btn.green{background:linear-gradient(135deg,#16a34a,#0d9488);box-shadow:0 6px 18px rgba(22,163,74,.35)}
.btn.sm{padding:8px 14px;font-size:13px;border-radius:10px;box-shadow:none}
.btn.busy{opacity:.6;pointer-events:none}
.panel h2{font-size:17px;margin-bottom:12px}
.panel+.panel{margin-top:16px}
.pick{display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-top:6px}
.pick button{all:unset;box-sizing:border-box;cursor:pointer;text-align:center;padding:20px 10px;border-radius:16px;border:1.5px solid rgba(255,255,255,.14);background:rgba(255,255,255,.06);transition:all .2s}
.pick button:hover{transform:translateY(-4px);border-color:#fdba74;background:rgba(251,146,60,.13)}
.pick b{display:block;font-size:17px;margin:6px 0 2px}.pick small{color:#cdb4a6;font-size:12px}.pick span{font-size:28px}
.hint{color:#cdb4a6;font-size:13px;margin:0 0 10px}
.mt td .in{min-width:70px}
@media(max-width:640px){.pick{grid-template-columns:1fr}}
</style></head>
<body class="dark-mode"><main class="wide">
<nav class="nav2"><a class="back" href="javascript:window.close()">✕ Close</a></nav>
<h1>Hotels, units &amp; departure types</h1>

<div class="modes" hidden>
  <button type="button" class="btn" data-m="add">＋ Add</button>
  <button type="button" class="btn" data-m="edit">✎ Edit</button>
  <button type="button" class="btn red" data-m="remove">🗑 Remove</button>
</div>

<!-- ADD -->
<div class="mode" id="m-add" hidden>
<section class="panel"><h2>New hotel</h2><div class="tools">
<input class="in" id="aName" placeholder="Hotel name" maxlength="60" autocomplete="off"><button type="button" class="btn green" data-act="add-hotel">Add hotel</button></div></section>
<section class="panel"><h2>New unit</h2><div class="tools">
<select class="in" id="uH"></select>
<input class="in" id="uNum" type="number" min="1" placeholder="Unit number">
<select class="in" id="uBeds"></select>
<button type="button" class="btn green" data-act="add-unit">Add unit</button></div></section>
<section class="panel"><h2>Departure type (code = DEP + bedrooms)</h2><div class="tools">
<input class="in" id="dBeds" type="number" min="1" max="10" placeholder="Bedrooms">
<input class="in" id="dLabel" placeholder="e.g. 5 full bedrooms + towels" maxlength="80">
<input class="in" id="dPrice" type="number" step="0.01" min="0" placeholder="Price">
<button type="button" class="btn green" data-act="add-dep">Add / update</button></div></section>
</div>

<!-- EDIT -->
<div class="mode" id="m-edit" hidden>
<section class="panel"><h2>Edit hotel</h2>
<div class="tools"><select class="in" id="eH"></select></div>
<div class="tools"><input class="in" id="eName" placeholder="Hotel name" maxlength="60" autocomplete="off"><button type="button" class="btn green" data-act="rename">Rename</button></div>
<p class="hint">Saved invoices keep the old name. Only new invoices use the new one.</p>
<p class="hint">Bedrooms save as soon as you pick one.</p>
<table class="mt" id="eUnits"></table></section>
<section class="panel"><h2>Edit departure types</h2><table class="mt" id="eDeps"></table></section>
</div>

<!-- REMOVE -->
<div class="mode" id="m-remove" hidden>
<section class="panel"><h2>Remove hotel or unit</h2>
<div class="tools"><select class="in" id="rH"></select><button type="button" class="btn red" data-act="del-hotel">Remove whole hotel</button></div>
<table class="mt" id="rUnits"></table></section>
<section class="panel"><h2>Remove departure type</h2><table class="mt" id="rDeps"></table></section>
</div>

<div class="modal" id="chooser"><div class="mcard">
<h2>What would you like to do?</h2><p class="hint">Pick one. You can switch any time with the buttons at the top.</p>
<div class="pick">
<button type="button" data-m="add"><span>＋</span><b>Add</b><small>New hotel, unit or type</small></button>
<button type="button" data-m="edit"><span>✎</span><b>Edit</b><small>Rename or change details</small></button>
<button type="button" data-m="remove"><span>🗑</span><b>Remove</b><small>Hotel, unit or type</small></button>
</div></div></div>

<div class="modal" id="ask" hidden><div class="mcard" style="max-width:420px">
<h2 id="aT"></h2><p class="hint" id="aM"></p>
<div class="mfoot"><button type="button" class="btn ghost" id="aNo">Cancel</button><button type="button" class="btn red" id="aYes">Remove</button></div></div></div>
</main>
<script src="js/popupNotify.js"></script><script>
(function(){
const $=s=>document.querySelector(s), $$=s=>[...document.querySelectorAll(s)];
const esc=s=>String(s??'').replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
let D={hotels:[],deps:[]}, mode='';

const api=async d=>{try{const t=await fetch('php/hotels_api.php',{method:'POST',body:new URLSearchParams(d)}).then(r=>r.text());
  try{return JSON.parse(t.slice(t.indexOf('{')))}catch(e){return{ok:false,msg:'Server error: '+t.slice(0,150)}}}catch(e){return{ok:false,msg:'Could not reach the server. Check your connection.'}}};
function toast(m,bad){showPopup(bad?'error':'success',m,'',bad?6000:3000)}
function ask(title,msg,ok){return new Promise(res=>{$('#aT').textContent=title;$('#aM').textContent=msg;$('#aYes').textContent=ok||'Remove';$('#ask').hidden=false;
  const done=v=>{$('#ask').hidden=true;$('#aYes').onclick=$('#aNo').onclick=null;res(v)};$('#aYes').onclick=()=>done(true);$('#aNo').onclick=()=>done(false)})}

function setMode(m){mode=m;$('#chooser').hidden=true;$('.modes').hidden=false;
  $$('.mode').forEach(x=>x.hidden=x.id!=='m-'+m);$$('.modes .btn').forEach(b=>b.classList.toggle('on',b.dataset.m===m));render()}

const selOpts=(sel,keep)=>{const v=keep??sel.value;sel.innerHTML=D.hotels.length?D.hotels.map(h=>`<option>${esc(h.name)}</option>`).join(''):'<option value="">No hotels yet</option>';
  if(D.hotels.some(h=>h.name===v))sel.value=v};
const bedsOpts=(v,first)=>`<option value="">${first||'Unknown'}</option>`+[1,2,3,4,5,6,7,8,9,10].map(n=>`<option value="${n}"${n===v?' selected':''}>${n}</option>`).join('');
const hotel=n=>D.hotels.find(h=>h.name===n);

function render(){
  selOpts($('#uH'));selOpts($('#eH'));selOpts($('#rH'));
  $('#uBeds').innerHTML=bedsOpts(null,'Bedrooms unknown');
  if(mode==='edit'){const H=hotel($('#eH').value);$('#eName').value=H?H.name:'';
    $('#eUnits').innerHTML=H&&H.units.length?'<thead><tr><th>Unit</th><th>Bedrooms</th></tr></thead><tbody>'+H.units.map(u=>`<tr><td><b>${u.unit}</b></td><td><select class="in" data-beds="${u.unit}">${bedsOpts(u.beds)}</select></td></tr>`).join('')+'</tbody>':'<tbody><tr><td class="empty">No units for this hotel</td></tr></tbody>';
    $('#eDeps').innerHTML=D.deps.length?'<thead><tr><th>Code</th><th>Label</th><th>Price</th><th></th></tr></thead><tbody>'+D.deps.map(d=>`<tr data-c="${esc(d.code)}"><td><b>${esc(d.code)}</b></td><td><input class="in" data-l value="${esc(d.label)}" maxlength="80"></td><td><input class="in" data-p type="number" step="0.01" min="0" value="${d.price}"></td><td class="acts"><button type="button" class="btn sm green" data-act="save-dep">Save</button></td></tr>`).join('')+'</tbody>':'<tbody><tr><td class="empty">No departure types</td></tr></tbody>'}
  if(mode==='remove'){const H=hotel($('#rH').value);
    $('#rUnits').innerHTML=H&&H.units.length?'<thead><tr><th>Unit</th><th>Bedrooms</th><th></th></tr></thead><tbody>'+H.units.map(u=>`<tr><td><b>${u.unit}</b></td><td>${u.beds??'Unknown'}</td><td class="acts"><button type="button" class="btn sm red" data-act="del-unit" data-u="${u.unit}">Remove</button></td></tr>`).join('')+'</tbody>':'<tbody><tr><td class="empty">No units for this hotel</td></tr></tbody>';
    $('#rDeps').innerHTML=D.deps.length?'<thead><tr><th>Code</th><th>Label</th><th>Price</th><th></th></tr></thead><tbody>'+D.deps.map(d=>`<tr><td><b>${esc(d.code)}</b></td><td>${esc(d.label)}</td><td>R ${d.price.toFixed(2)}</td><td class="acts"><button type="button" class="btn sm red" data-act="del-dep" data-c="${esc(d.code)}">Remove</button></td></tr>`).join('')+'</tbody>':'<tbody><tr><td class="empty">No departure types</td></tr></tbody>'}
}
async function load(){const r=await api({a:'list'});if(!r.ok){toast('Could not load hotels: '+r.msg,true);return}D=r;render()}

// run an action: disables the button, shows result, reloads data
async function run(btn,data,okMsg,after){
  btn&&btn.classList.add('busy');const r=await api(data);btn&&btn.classList.remove('busy');
  if(!r.ok){toast(r.msg,true);return false}
  toast(okMsg);await load();after&&after();return true}

document.addEventListener('click',async e=>{
  const mb=e.target.closest('[data-m]');if(mb)return setMode(mb.dataset.m);
  const b=e.target.closest('[data-act]');if(!b)return;
  const act=b.dataset.act, bad=m=>{toast(m,true);return};
  if(act==='add-hotel'){const n=$('#aName').value.trim();
    if(n.length<2)return bad('Enter a hotel name (at least 2 characters).');
    if(D.hotels.some(h=>h.name.toLowerCase()===n.toLowerCase()))return bad('That hotel already exists.');
    run(b,{a:'hotel',name:n},'Hotel added ✓',()=>{$('#aName').value='';$('#aName').focus()})}
  else if(act==='add-unit'){const h=$('#uH').value,u=parseInt($('#uNum').value);
    if(!h)return bad('Add a hotel first.');if(!(u>0))return bad('Enter a unit number.');
    if(hotel(h).units.some(x=>x.unit===u))return bad(`Unit ${u} already exists in ${h}.`);
    run(b,{a:'unit',hotel:h,unit:u,beds:$('#uBeds').value},`Unit ${u} added ✓`,()=>{$('#uNum').value='';$('#uNum').focus()})}
  else if(act==='add-dep'){const be=parseInt($('#dBeds').value),l=$('#dLabel').value.trim(),p=$('#dPrice').value;
    if(!(be>=1&&be<=10))return bad('Bedrooms must be 1-10.');if(!l)return bad('Enter a label.');if(p===''||+p<0)return bad('Enter a valid price.');
    const ex=D.deps.some(d=>d.beds===be);
    run(b,{a:'dep',beds:be,label:l,price:p},ex?`DEP${be} updated ✓`:`DEP${be} added ✓`,()=>['#dBeds','#dLabel','#dPrice'].forEach(s=>$(s).value=''))}
  else if(act==='rename'){const o=$('#eH').value,n=$('#eName').value.trim();
    if(!o)return bad('Pick a hotel.');if(n.length<2)return bad('Enter a hotel name (at least 2 characters).');if(n===o)return bad('Change the name first.');
    if(await ask('Rename hotel?',`"${o}" will become "${n}" for new invoices. Saved invoices keep the old name.`,'Rename'))
      run(b,{a:'rename_hotel',old:o,name:n},'Hotel renamed ✓',()=>{$('#eH').value=n;render()})}
  else if(act==='save-dep'){const tr=b.closest('tr'),c=tr.dataset.c,l=tr.querySelector('[data-l]').value.trim(),p=tr.querySelector('[data-p]').value;
    if(!l)return bad('Enter a label.');if(p===''||+p<0)return bad('Enter a valid price.');
    run(b,{a:'dep',beds:c.replace('DEP',''),label:l,price:p},c+' saved ✓')}
  else if(act==='del-hotel'){const h=$('#rH').value;if(!h)return bad('Pick a hotel.');
    if(await ask('Remove hotel?',`"${h}" and its ${hotel(h).units.length} unit(s) will be removed from the lists. Saved invoices are not touched.`))run(b,{a:'del_hotel',name:h},'Hotel removed ✓')}
  else if(act==='del-unit'){const h=$('#rH').value,u=b.dataset.u;
    if(await ask('Remove unit?',`Unit ${u} will be removed from ${h}.`))run(b,{a:'del_unit',hotel:h,unit:u},`Unit ${u} removed ✓`)}
  else if(act==='del-dep'){const c=b.dataset.c;
    if(await ask('Remove departure type?',`${c} will no longer be available when capturing departures.`))run(b,{a:'del_dep',code:c},c+' removed ✓')}
});

document.addEventListener('change',e=>{
  if(e.target.id==='eH'||e.target.id==='rH')return render();
  const u=e.target.dataset.beds;
  if(u)run(null,{a:'set_beds',hotel:$('#eH').value,unit:u,beds:e.target.value},`Unit ${u} saved ✓`)});

// Enter key submits the panel's button
document.addEventListener('keydown',e=>{
  if(e.key==='Escape'){if(!$('#ask').hidden)$('#aNo').click();else if(!$('#chooser').hidden)setMode('add');return}
  if(e.key!=='Enter'||!e.target.classList.contains('in'))return;
  const btn=e.target.closest('.tools')?.querySelector('[data-act]');if(btn){e.preventDefault();btn.click()}});
$('#chooser').addEventListener('click',e=>{if(e.target.id==='chooser')setMode('add')});

load();
})();
</script></body></html>
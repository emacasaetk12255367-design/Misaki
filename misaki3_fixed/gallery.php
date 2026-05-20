<?php
$page        = 'gallery';
$title       = 'Gallery — Misaki Handcrafted';
$description = 'A visual diary of seasonal arrangements.';
require __DIR__ . '/includes/settings.php';
require __DIR__ . '/includes/header.php';

// CMS-controlled text for gallery page
$gallery_eyebrow = setting('gallery_eyebrow', 'Misaki Atelier');
$gallery_tagline = setting('gallery_tagline', '— A quiet study of bloom, thread, and patience.');
?>
<style>
  * { margin: 0; padding: 0; box-sizing: border-box }
  :root { --ink: #e8e2d6; --ink-dim: #a89f8e; --accent: #c9a36b; --bg: #0e0d0c; }
  html, body { height: 100%; background: var(--bg); color: var(--ink); font-family: 'Cormorant Garamond', serif; overflow: hidden }
  .hero { position: fixed; inset: 0; overflow: hidden }
  .hero::before {
    content: ""; position: absolute; inset: 0;
    background: radial-gradient(ellipse at 70% 50%, rgba(0,0,0,0) 0%, rgba(0,0,0,.55) 60%, rgba(0,0,0,.9) 100%),
                linear-gradient(90deg, rgba(0,0,0,.85) 0%, rgba(0,0,0,.4) 45%, rgba(0,0,0,0) 70%),
                url('https://images.unsplash.com/photo-1490750967868-88aa4486c946?w=1920&q=80') center/cover no-repeat;
    filter: saturate(.85) contrast(1.05); transform: scale(1.05);
    animation: slowZoom 30s ease-in-out infinite alternate;
  }
  @keyframes slowZoom { to { transform: scale(1.15) translateX(-2%) } }
  .grain { position: absolute; inset: 0; pointer-events: none; opacity: .08; mix-blend-mode: overlay;
    background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='200' height='200'><filter id='n'><feTurbulence baseFrequency='0.9'/></filter><rect width='100%25' height='100%25' filter='url(%23n)'/></svg>") }
  .vignette { position: absolute; inset: 0; box-shadow: inset 0 0 240px 60px #000; pointer-events: none }
  .top-bar, .bottom-bar { position: absolute; left: 0; right: 0; display: flex; justify-content: space-between;
    padding: 28px 48px; font-family: 'Cinzel', serif; font-size: 11px; letter-spacing: .4em;
    color: var(--ink-dim); text-transform: uppercase; z-index: 5 }
  .top-bar { top: 34px }
  .bottom-bar { bottom: 0; font-size: 10px }
  .bar-mark { color: var(--accent) }
  .menu-wrap { position: absolute; left: 96px; top: 120px; z-index: 4; height: auto; display: flex;
    flex-direction: column; align-items: flex-start; width: min(720px, calc(100% - 192px)); max-height: calc(100vh - 160px); }
  .eyebrow { font-family: 'Cinzel', serif; font-size: 11px; letter-spacing: .6em; color: var(--accent);
    margin-bottom: 18px; display: flex; align-items: center; gap: 14px }
  .eyebrow::before { content: ""; width: 36px; height: 1px; background: var(--accent) }
  h1 { font-family: 'Cinzel', serif; font-weight: 400; font-size: clamp(40px, 6vw, 82px);
    letter-spacing: .18em; line-height: 1; color: #f3ecdd; text-shadow: 0 2px 30px rgba(0,0,0,.6) }
  .tagline { font-style: italic; font-size: 18px; color: var(--ink-dim); margin-top: 14px; letter-spacing: .08em }
  nav#nav { margin-top: 54px; display: flex; flex-direction: column; gap: 6px }
  .item { background: none; border: 0; color: var(--ink); font-family: 'Cinzel', serif; font-size: 18px;
    letter-spacing: .32em; padding: 10px 0; display: flex; align-items: center; gap: 18px; cursor: pointer;
    text-transform: uppercase; transition: transform .35s ease, color .25s ease, letter-spacing .35s ease; text-align: left; }
  .box { width: 14px; height: 14px; border: 1px solid var(--ink-dim); display: inline-block; flex-shrink: 0; transition: all .25s ease }
  .item:hover { color: #fff; transform: translateX(14px); letter-spacing: .42em }
  .item:hover .box { background: var(--accent); border-color: var(--accent); box-shadow: 0 0 18px rgba(201,163,107,.7) }
  .item.active .box { background: var(--ink); border-color: var(--ink) }
  .overlay { position: fixed; inset: 0; background: #000; z-index: 50; opacity: 0; pointer-events: none; transition: opacity .8s ease }
  .overlay.open { opacity: 1; pointer-events: auto }
  .slides { position: absolute; inset: 0 }
  .slide { position: absolute; inset: 0; opacity: 0; transform: scale(1.08);
    transition: opacity 1.4s ease, transform 6s ease; background-size: cover; background-position: center }
  .slide.on { opacity: 1; transform: scale(1) }
  .slide::after { content: ""; position: absolute; inset: 0;
    background: linear-gradient(180deg, rgba(0,0,0,.5) 0%, rgba(0,0,0,0) 30%, rgba(0,0,0,0) 60%, rgba(0,0,0,.85) 100%) }
  .ov-top { position: absolute; top: 34px; left: 0; right: 0; display: flex; justify-content: space-between;
    align-items: center; padding: 30px 48px; z-index: 6; font-family: 'Cinzel', serif;
    letter-spacing: .4em; font-size: 11px; color: var(--ink-dim); text-transform: uppercase }
  .ov-title { color: #f3ecdd; font-size: 14px; letter-spacing: .5em }
  .close { background: none; border: 1px solid var(--ink-dim); color: var(--ink); width: 38px; height: 38px;
    cursor: pointer; font-family: inherit; transition: all .25s; position: relative; z-index: 7 }
  .close:hover { background: var(--accent); border-color: var(--accent); color: #000 }
  .ov-bottom { position: absolute; bottom: 0; left: 0; right: 0; padding: 36px 48px; z-index: 3;
    display: flex; justify-content: space-between; align-items: flex-end; gap: 32px }
  .caption { max-width: 520px }
  .caption h2 { font-family: 'Cinzel', serif; font-weight: 400; font-size: 38px; letter-spacing: .18em;
    color: #f3ecdd; text-transform: uppercase }
  .caption p { font-style: italic; color: var(--ink-dim); margin-top: 8px; font-size: 17px }
  .controls { display: flex; align-items: center; gap: 18px; font-family: 'Cinzel', serif;
    font-size: 11px; letter-spacing: .3em; color: var(--ink-dim) }
  .ctrl { background: none; border: 1px solid var(--ink-dim); color: var(--ink); width: 44px; height: 44px;
    cursor: pointer; font-size: 16px; transition: all .25s; font-family: inherit }
  .ctrl:hover { background: var(--accent); border-color: var(--accent); color: #000 }
  .count { min-width: 60px; text-align: center }
  .progress { position: absolute; top: 0; left: 0; height: 2px; background: var(--accent); width: 0; z-index: 4; transition: width .15s linear }
  body[data-page="gallery"] .menu-btn, body[data-page="gallery"] .mobile-nav { display: none !important; }
  .gallery-loading { color: var(--ink-dim); font-family: 'Cinzel', serif; font-size: 12px; letter-spacing: .4em; text-transform: uppercase; margin-top: 12px; }
</style>

<section class="hero">
  <div class="grain"></div>
  <div class="vignette"></div>
  <div class="top-bar">
    <span><span class="bar-mark">—</span>&nbsp; Handcrafted in silence, given with intent</span>
    <span>EST · MMXXIV &nbsp;<span class="bar-mark">+</span></span>
  </div>
  <div class="menu-wrap">
    <div class="eyebrow"><?= htmlspecialchars($gallery_eyebrow) ?></div>
    <h1>MISAKI</h1>
    <div class="tagline"><?= htmlspecialchars($gallery_tagline) ?></div>
    <nav id="nav"><div class="gallery-loading">Loading collections…</div></nav>
  </div>
  <div class="bottom-bar">
    <span><span class="bar-mark">+</span>&nbsp; © MISAKI HANDICRAFT</span>
    <span>Each stem folded by hand. Each gift, a memory kept. &nbsp;<span class="bar-mark">+</span></span>
  </div>
</section>

<div class="overlay" id="overlay">
  <div class="progress" id="progress"></div>
  <div class="slides"  id="slides"></div>
  <div class="ov-top">
    <span id="ovEyebrow">Collection</span>
    <button class="close" id="closeBtn" aria-label="Close">×</button>
  </div>
  <div class="ov-bottom">
    <div class="caption">
      <h2 id="capTitle">—</h2>
      <p  id="capText">—</p>
    </div>
    <div class="controls">
      <button class="ctrl" id="prevBtn" aria-label="Previous">‹</button>
      <button class="ctrl" id="playBtn" aria-label="Play/Pause">❚❚</button>
      <span class="count"  id="count">01 / 01</span>
      <button class="ctrl" id="nextBtn" aria-label="Next">›</button>
    </div>
  </div>
</div>

<script>
(function () {
  'use strict';
  let COLLECTIONS = [];
  let cur = 0, timer = null, playing = true, active = null, progT = 0;
  const DUR = 5000;

  const nav      = document.getElementById('nav');
  const overlay  = document.getElementById('overlay');
  const slidesEl = document.getElementById('slides');
  const progress = document.getElementById('progress');
  const nextBtn  = document.getElementById('nextBtn');
  const prevBtn  = document.getElementById('prevBtn');
  const playBtn  = document.getElementById('playBtn');
  const closeBtn = document.getElementById('closeBtn');

  fetch('api/gallery.php')
    .then(r => r.json())
    .then(data => { COLLECTIONS = data || []; buildNav(); })
    .catch(() => { nav.innerHTML = '<div class="gallery-loading" style="color:#c9a36b">Could not load collections.</div>'; });

  function buildNav() {
    nav.innerHTML = '';
    if (!COLLECTIONS.length) {
      nav.innerHTML = '<div class="gallery-loading">No collections yet.</div>';
      return;
    }
    COLLECTIONS.forEach((c, i) => {
      const b = document.createElement('button');
      b.className = 'item';
      b.innerHTML = '<span class="box"></span>' + c.name;
      b.addEventListener('mouseenter', () => document.querySelectorAll('.item').forEach(x => x.classList.remove('active')));
      b.addEventListener('click', () => openCollection(i));
      nav.appendChild(b);
    });
  }

  function openCollection(i) {
    active = COLLECTIONS[i];
    document.getElementById('ovEyebrow').textContent = 'Collection · ' + active.tag;
    slidesEl.innerHTML = '';
    (active.slides || []).forEach(s => {
      const d = document.createElement('div');
      d.className = 'slide';
      d.style.backgroundImage = "url('" + s.img + "')";
      slidesEl.appendChild(d);
    });
    cur = 0; playing = true; playBtn.innerHTML = '❚❚';
    overlay.classList.add('open');
    show(0); startTimer();
  }

  function show(i) {
    const slides = slidesEl.children;
    if (!slides.length) return;
    cur = ((i % slides.length) + slides.length) % slides.length;
    Array.from(slides).forEach((s, idx) => s.classList.toggle('on', idx === cur));
    document.getElementById('capTitle').textContent = active.name;
    const sl = (active.slides || [])[cur] || {};
    document.getElementById('capText').textContent  = (sl.cap || '') + (active.desc ? ' — ' + active.desc : '');
    document.getElementById('count').textContent    = String(cur + 1).padStart(2,'0') + ' / ' + String(slides.length).padStart(2,'0');
    progT = 0; progress.style.width = '0%';
  }

  function startTimer() {
    clearInterval(timer);
    timer = setInterval(() => {
      if (!playing) return;
      progT += 100;
      progress.style.width = (progT / DUR * 100) + '%';
      if (progT >= DUR) show(cur + 1);
    }, 100);
  }

  nextBtn  && nextBtn.addEventListener('click',  () => show(cur + 1));
  prevBtn  && prevBtn.addEventListener('click',  () => show(cur - 1));
  playBtn  && playBtn.addEventListener('click',  function () { playing = !playing; this.innerHTML = playing ? '❚❚' : '▶'; });
  closeBtn && closeBtn.addEventListener('click', () => { overlay.classList.remove('open'); clearInterval(timer); timer = null; });

  document.addEventListener('keydown', e => {
    if (!overlay.classList.contains('open')) return;
    if (e.key === 'Escape')     { closeBtn && closeBtn.click(); }
    if (e.key === 'ArrowRight') show(cur + 1);
    if (e.key === 'ArrowLeft')  show(cur - 1);
  });
})();
</script>

<?php require __DIR__ . '/includes/footer.php'; ?>

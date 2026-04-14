<?php
/**
 * Template Name: 音乐布道会
 */
get_header();
?>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+SC:wght@300;400;500;700;900&family=Noto+Serif+SC:wght@400;700&family=Cormorant+Garamond:wght@300;400&display=swap" rel="stylesheet">

<style>
/* ── Concert page variables ── */
.cw {
  --cw-bg0: #111111;
  --cw-bg1: #181818;
  --cw-gold: #c9a050;
  --cw-gold-light: #dfc07a;
  --cw-gold-line: rgba(201,160,80,0.25);
  --cw-text1: #f0ebe0;
  --cw-text2: #9a9080;
  --cw-text3: #5a5248;
  --cw-white: #ffffff;
  font-family: 'Noto Sans SC', 'PingFang SC', 'Hiragino Sans GB', 'Microsoft YaHei', sans-serif;
  -webkit-font-smoothing: antialiased;
}

/* ── Hero ── */
.cw .cw-hero {
  position: relative;
  min-height: 100svh;
  display: flex;
  align-items: flex-end;
  overflow: hidden;
  background: #0d0d0d;
  margin-top: -80px; /* pull up under WP fixed header */
}
.cw .cw-hero-img {
  position: absolute;
  top: 80px; left: 0; right: 0; bottom: 0;
  width: 100%; height: calc(100% - 80px);
  object-fit: cover; object-position: top center;
  opacity: .7;
}
.cw .cw-hero-overlay {
  position: absolute; inset: 0;
  background:
    linear-gradient(to bottom, rgba(13,13,13,.25) 0%, rgba(13,13,13,.05) 25%, rgba(13,13,13,.55) 60%, rgba(13,13,13,.97) 100%),
    linear-gradient(to right, rgba(13,13,13,.7) 0%, transparent 55%);
}
.cw .ring {
  position: absolute; border-radius: 50%;
  border: 1px solid rgba(201,160,80,.1);
  pointer-events: none;
}
.cw .ring-1 { width: 560px; height: 560px; top: -140px; right: -140px; animation: cw-spin 50s linear infinite; }
.cw .ring-2 { width: 340px; height: 340px; top: -20px;  right: 20px;  border-color: rgba(201,160,80,.06); animation: cw-spin 35s linear infinite reverse; }
.cw .ring-3 { width: 160px; height: 160px; top: 120px;  right: 120px; border-color: rgba(201,160,80,.16); }
@keyframes cw-spin { to { transform: rotate(360deg); } }

.cw .cw-hero-body {
  position: relative; z-index: 2;
  padding: 0 44px 68px; max-width: 640px;
  padding-top: 80px; /* compensate for header */
}
.cw .cw-eyebrow {
  font-size: 11px; letter-spacing: .35em; text-transform: uppercase;
  color: var(--cw-gold); margin-bottom: 18px; font-weight: 500;
}
.cw .cw-hero-title {
  font-family: 'Noto Serif SC', serif;
  font-size: clamp(48px, 9vw, 88px);
  font-weight: 700; line-height: 1.05;
  color: var(--cw-white); margin-bottom: 14px;
  text-shadow: 0 4px 40px rgba(0,0,0,.5);
}
.cw .cw-hero-title em { color: var(--cw-gold); font-style: normal; }
.cw .cw-hero-sub {
  font-size: clamp(15px, 2.2vw, 20px);
  color: var(--cw-text2); font-weight: 300;
  letter-spacing: .18em; margin-bottom: 36px;
}
.cw .cw-badge {
  display: inline-flex; align-items: center; gap: 14px;
  border: 1px solid var(--cw-gold); padding: 11px 22px;
  font-size: 13px; color: var(--cw-gold-light); letter-spacing: .1em;
}
.cw .cw-badge .dot {
  width: 7px; height: 7px;
  background: var(--cw-gold); border-radius: 50%;
  animation: cw-blink 2.2s ease-in-out infinite;
}
@keyframes cw-blink { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:.4;transform:scale(.65)} }

.cw .cw-scroll-hint {
  position: absolute; bottom: 28px; left: 50%; transform: translateX(-50%);
  z-index: 2; display: flex; flex-direction: column; align-items: center; gap: 8px;
  font-size: 10px; letter-spacing: .25em; text-transform: uppercase; color: var(--cw-text3);
}
.cw .cw-scroll-line {
  width: 1px; height: 44px;
  background: linear-gradient(to bottom, var(--cw-gold), transparent);
  animation: cw-drip 2s ease-in-out infinite;
}
@keyframes cw-drip {
  0%   { transform: scaleY(0); transform-origin: top; opacity: 1; }
  49%  { transform: scaleY(1); transform-origin: top; opacity: 1; }
  50%  { transform: scaleY(1); transform-origin: bottom; opacity: 1; }
  100% { transform: scaleY(0); transform-origin: bottom; opacity: 0; }
}

/* ── Divider ── */
.cw .cw-divider {
  width: 100%; height: 1px;
  background: linear-gradient(to right, transparent, var(--cw-gold), transparent);
}

/* ── Section common ── */
.cw .cw-wrap { max-width: 1080px; margin: 0 auto; padding: 88px 28px; }
.cw .cw-sec-label {
  font-size: 11px; letter-spacing: .35em; text-transform: uppercase;
  color: var(--cw-gold); margin-bottom: 10px; font-weight: 500;
}
.cw .cw-sec-title {
  font-family: 'Noto Serif SC', serif;
  font-size: clamp(26px, 4vw, 40px);
  font-weight: 700; color: var(--cw-white); line-height: 1.2; margin-bottom: 52px;
}
.cw .fi { opacity: 0; transform: translateY(22px); transition: opacity .65s ease, transform .65s ease; }
.cw .fi.on { opacity: 1; transform: none; }

/* ── Info cards ── */
.cw .cw-info-bg { background: var(--cw-bg1); }
.cw .cw-info-grid { display: grid; grid-template-columns: repeat(4,1fr); gap: 14px; }
.cw .icard {
  border: 1px solid var(--cw-gold-line); padding: 26px 22px;
  background: rgba(201,160,80,.03);
  text-decoration: none; color: inherit; display: block;
  transition: border-color .3s, background .3s;
}
.cw .icard:hover { border-color: var(--cw-gold); background: rgba(201,160,80,.07); }
.cw .icard-lbl { font-size: 11px; letter-spacing: .18em; text-transform: uppercase; color: var(--cw-text3); margin-bottom: 8px; }
.cw .icard-val { font-size: 15px; font-weight: 500; color: var(--cw-text1); line-height: 1.6; }
.cw .icard-val.gold { color: var(--cw-gold-light); }
.cw .icard-small { font-size: 12px; color: var(--cw-text2); display: block; margin-top: 4px; }

/* ── Artist cards ── */
.cw .cw-artist-bg { background: var(--cw-bg0); }
.cw .cw-artist-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; }
.cw .acard {
  border: 1px solid var(--cw-gold-line); padding: 44px 36px;
  background: linear-gradient(140deg, rgba(201,160,80,.05) 0%, rgba(201,160,80,.01) 100%);
  position: relative; overflow: hidden;
}
.cw .acard::before {
  content: ''; position: absolute; top: 0; left: 0; right: 0; height: 2px;
  background: linear-gradient(to right, var(--cw-gold), transparent);
}
.cw .acard-num {
  position: absolute; top: 16px; right: 20px;
  font-family: 'Cormorant Garamond', serif;
  font-size: 80px; font-weight: 300; line-height: 1;
  color: rgba(201,160,80,.07); letter-spacing: -.04em;
  user-select: none;
}
.cw .acard-name {
  font-family: 'Noto Serif SC', serif;
  font-size: 30px; font-weight: 700; color: var(--cw-white); margin-bottom: 6px;
}
.cw .acard-en { font-size: 11px; letter-spacing: .22em; color: var(--cw-gold); text-transform: uppercase; margin-bottom: 22px; }
.cw .tags { display: flex; flex-wrap: wrap; gap: 7px; margin-bottom: 22px; }
.cw .tag {
  font-size: 11px; padding: 4px 10px;
  border: 1px solid rgba(201,160,80,.28); color: var(--cw-gold-light);
  letter-spacing: .04em;
}
.cw .acard-bio { font-size: 14px; color: var(--cw-text2); line-height: 1.85; }

/* ── Timeline ── */
.cw .cw-program-bg { background: var(--cw-bg1); }
.cw .timeline { position: relative; padding-left: 28px; }
.cw .timeline::before {
  content: ''; position: absolute; left: 0; top: 10px; bottom: 10px;
  width: 1px;
  background: linear-gradient(to bottom, var(--cw-gold) 0%, rgba(201,160,80,.15) 100%);
}
.cw .titem {
  position: relative; padding: 0 0 34px 30px;
  opacity: 0; transform: translateX(-12px);
  transition: opacity .5s ease, transform .5s ease;
}
.cw .titem.on { opacity: 1; transform: none; }
.cw .titem::before {
  content: ''; position: absolute;
  left: -5px; top: 7px;
  width: 10px; height: 10px; border-radius: 50%;
  background: var(--cw-bg0); border: 2px solid var(--cw-gold);
}
.cw .titem.hl::before {
  background: var(--cw-gold);
  width: 14px; height: 14px; left: -7px; top: 5px;
  box-shadow: 0 0 14px rgba(201,160,80,.55);
}
.cw .ttime {
  font-size: 12px; font-weight: 700; color: var(--cw-gold);
  letter-spacing: .08em; margin-bottom: 4px;
}
.cw .tcontent { font-size: 15px; color: var(--cw-text1); line-height: 1.6; }
.cw .tcontent strong { color: var(--cw-white); font-weight: 700; }
.cw .titem.hl .tcontent { font-size: 16px; font-weight: 500; }

/* ── Decision ── */
.cw .cw-decision-bg {
  background: linear-gradient(160deg, #160f04 0%, #1a1a1a 45%, #100c02 100%);
  position: relative; overflow: hidden;
}
.cw .cw-decision-bg::before {
  content: ''; position: absolute; inset: 0; pointer-events: none;
  background:
    radial-gradient(ellipse at 25% 35%, rgba(201,160,80,.09) 0%, transparent 55%),
    radial-gradient(ellipse at 75% 65%, rgba(201,160,80,.06) 0%, transparent 45%);
}
.cw .cw-decision-inner { position: relative; z-index: 1; text-align: center; }
.cw .cw-decision-inner .cw-sec-label { display: block; }
.cw .cw-decision-inner .cw-sec-title { text-align: center; }
.cw .d-sub {
  max-width: 580px; margin: -36px auto 52px;
  font-size: 15px; color: var(--cw-text2); line-height: 1.9; text-align: center;
}
.cw .form-box {
  max-width: 680px; margin: 0 auto;
  border: 1px solid rgba(201,160,80,.22);
  background: rgba(201,160,80,.03); overflow: hidden;
  transition: border-color .35s, background .35s;
}
.cw .form-box iframe { display: block; width: 100%; height: 620px; border: none; }

/* CTA 引导态 */
.cw .form-cta {
  padding: 64px 28px 56px;
  display: flex; flex-direction: column; align-items: center;
  gap: 20px; text-align: center;
}
.cw .form-cta-icon {
  color: var(--cw-gold); opacity: .88;
  animation: cw-heart 2.6s ease-in-out infinite;
}
@keyframes cw-heart {
  0%,100% { transform: scale(1); }
  50% { transform: scale(1.08); }
}
.cw .form-cta-lead {
  font-family: 'Noto Serif SC', serif;
  font-size: 18px; color: var(--cw-text1);
  letter-spacing: .1em; margin: 0;
}
.cw .form-cta-btn {
  display: inline-flex; align-items: center; gap: 12px;
  padding: 16px 38px;
  background: var(--cw-gold); color: #0d0d0d;
  border: none; cursor: pointer;
  font-family: inherit; font-size: 15px; font-weight: 700;
  letter-spacing: .12em;
  box-shadow: 0 8px 28px rgba(201,160,80,.22);
  transition: background .25s, transform .25s, box-shadow .25s;
}
.cw .form-cta-btn:hover {
  background: var(--cw-gold-light);
  transform: translateY(-2px);
  box-shadow: 0 14px 40px rgba(201,160,80,.38);
}
.cw .form-cta-btn svg { transition: transform .25s; }
.cw .form-cta-btn:hover svg { transform: translateX(4px); }
.cw .form-cta-alt {
  font-size: 12px; color: var(--cw-text3);
  text-decoration: none; letter-spacing: .08em;
  margin-top: 2px; transition: color .25s;
}
.cw .form-cta-alt:hover { color: var(--cw-gold); }

/* 展开态 */
.cw .form-box.is-open { background: rgba(201,160,80,.04); }
.cw .form-box.is-open .form-cta { display: none; }

/* ── Churches ── */
.cw .cw-churches-bg { background: var(--cw-bg1); }
.cw .church-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 16px; }
.cw .ccard {
  border: 1px solid var(--cw-gold-line); padding: 24px 22px 20px;
  background: rgba(201,160,80,.03);
  transition: border-color .3s, background .3s;
}
.cw .ccard:hover { border-color: var(--cw-gold); background: rgba(201,160,80,.07); }
.cw .ccard-dot-row { display: flex; align-items: center; gap: 8px; margin-bottom: 10px; }
.cw .ccard-dot { width: 6px; height: 6px; border-radius: 50%; background: var(--cw-gold); flex-shrink: 0; }
.cw .ccard-name {
  font-family: 'Noto Serif SC', serif;
  font-size: 15px; font-weight: 700; color: var(--cw-white); letter-spacing: .02em;
}
.cw .ccard-time { font-size: 12px; color: var(--cw-gold-light); font-weight: 500; letter-spacing: .06em; margin-bottom: 8px; padding-left: 14px; }
.cw .ccard-addr { font-size: 12px; color: var(--cw-text2); line-height: 1.6; margin-bottom: 12px; padding-left: 14px; }
.cw .ccard-divider { height: 1px; background: rgba(201,160,80,.12); margin-bottom: 12px; }
.cw .ccard-contact { display: flex; justify-content: space-between; align-items: center; padding-left: 14px; }
.cw .ccard-person { font-size: 13px; font-weight: 500; color: var(--cw-text1); }
.cw .ccard-phone { font-size: 12px; color: var(--cw-text2); letter-spacing: .04em; text-decoration: none; transition: color .25s; }
.cw .ccard-phone:hover { color: var(--cw-gold); }
.cw .ccard-map { display: inline-block; margin-top: 12px; padding-left: 14px; font-size: 11px; color: var(--cw-text3); text-decoration: none; letter-spacing: .08em; transition: color .25s; }
.cw .ccard-map:hover { color: var(--cw-gold); }

/* ── Back link bar ── */
.cw .cw-back-bar {
  background: #0a0a0a;
  border-top: 1px solid rgba(201,160,80,.14);
  padding: 32px 28px;
  display: flex; align-items: center; gap: 12px;
}
.cw .cw-back-bar a {
  display: inline-flex; align-items: center; gap: 8px;
  font-size: 13px; color: var(--cw-text2); text-decoration: none;
  letter-spacing: .08em; transition: color .25s;
}
.cw .cw-back-bar a:hover { color: var(--cw-gold); }

/* ── Responsive ── */
@media (max-width: 900px) {
  .cw .cw-info-grid { grid-template-columns: 1fr 1fr; }
  .cw .church-grid { grid-template-columns: 1fr 1fr; }
}
@media (max-width: 720px) {
  .cw .cw-hero-body { padding: 80px 24px 60px; }
  .cw .cw-artist-grid { grid-template-columns: 1fr; }
  .cw .cw-wrap { padding: 60px 20px; }
  .cw .acard { padding: 32px 24px; }
  .cw .form-box iframe { height: 760px; }

  /* Mobile font boost — 提升手机阅读体验 */
  .cw .cw-hero-sub { font-size: 17px; line-height: 1.7; }
  .cw .cw-hero-meta { font-size: 14px; }
  .cw .icard-lbl { font-size: 12px; }
  .cw .icard-val { font-size: 17px; line-height: 1.7; }
  .cw .icard-small { font-size: 13px; }
  .cw .acard-bio { font-size: 16px; line-height: 1.95; }
  .cw .acard-en { font-size: 12px; }
  .cw .ttime { font-size: 13px; }
  .cw .tcontent { font-size: 17px; line-height: 1.75; }
  .cw .titem.hl .tcontent { font-size: 18px; }
  .cw .d-sub { font-size: 17px; line-height: 2; }
  .cw .ccard-name { font-size: 17px; }
  .cw .ccard-time { font-size: 14px; }
  .cw .ccard-addr { font-size: 14px; line-height: 1.7; }
  .cw .ccard-person { font-size: 15px; }
  .cw .ccard-phone { font-size: 14px; }
  .cw .ccard-map { font-size: 12px; }
  .cw .form-cta { padding: 48px 22px 44px; gap: 18px; }
  .cw .form-cta-lead { font-size: 17px; }
  .cw .form-cta-btn { padding: 15px 30px; font-size: 15px; width: 100%; max-width: 300px; justify-content: center; }
  .cw .form-cta-alt { font-size: 13px; }
  .cw .cw-back-bar a { font-size: 14px; }
}
@media (max-width: 560px) {
  .cw .church-grid { grid-template-columns: 1fr; }
}
@media (max-width: 480px) {
  .cw .cw-info-grid { grid-template-columns: 1fr 1fr; gap: 10px; }
  .cw .icard { padding: 18px 14px; }
  .cw .icard-val { font-size: 16px; }
  .cw .timeline { padding-left: 20px; }
  .cw .titem { padding-left: 22px; }
  .cw .tcontent { font-size: 16px; }
}
</style>

<div class="cw">

  <!-- Hero -->
  <header class="cw-hero">
    <img class="cw-hero-img"
         src="<?php echo get_template_directory_uri(); ?>/images/concert-poster.png"
         alt="没有不可能 音乐布道会海报"
         onerror="this.style.opacity='0'">
    <div class="cw-hero-overlay"></div>
    <div class="ring ring-1"></div>
    <div class="ring ring-2"></div>
    <div class="ring ring-3"></div>
    <div class="cw-hero-body">
      <p class="cw-eyebrow">意大利华人复兴教会北部牧区 呈献</p>
      <h1 class="cw-hero-title">没有<em>不可能</em></h1>
      <p class="cw-hero-sub">黄国伦 &nbsp;·&nbsp; 寇乃馨 &nbsp;&nbsp;音乐布道会</p>
      <div class="cw-badge">
        <span class="dot"></span>
        2026.04.16 &nbsp;周四 &nbsp;·&nbsp; 20:30 &nbsp;·&nbsp; 免费入场
      </div>
    </div>
    <div class="cw-scroll-hint">
      <span>向下滚动</span>
      <div class="cw-scroll-line"></div>
    </div>
  </header>

  <div class="cw-divider"></div>

  <!-- 活动资讯 -->
  <div class="cw-info-bg">
    <div class="cw-wrap fi">
      <p class="cw-sec-label">活动资讯</p>
      <h2 class="cw-sec-title">关于这场布道会</h2>
      <div class="cw-info-grid">
        <div class="icard">
          <div class="icard-lbl">日期与时间</div>
          <div class="icard-val">2026年4月16日<br>周四 20:30 — 22:30</div>
        </div>
        <a class="icard" href="https://maps.google.com/?q=Via+Camillo+Ugoni+20,+20158+Milano" target="_blank" rel="noopener">
          <div class="icard-lbl">活动地点</div>
          <div class="icard-val">米兰华人复兴教会
            <span class="icard-small">Via Camillo Ugoni, 20<br>20158 Milano &nbsp;↗</span>
          </div>
        </a>
        <div class="icard">
          <div class="icard-lbl">主办单位</div>
          <div class="icard-val">意大利华人复兴教会<br>北部牧区</div>
        </div>
        <div class="icard">
          <div class="icard-lbl">入场方式</div>
          <div class="icard-val gold">免费入场
            <span class="icard-small" style="color:#7a7068">先到先得 · 座位有限<br>全场容量 400 人</span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="cw-divider"></div>

  <!-- 嘉宾介绍 -->
  <div class="cw-artist-bg">
    <div class="cw-wrap fi">
      <p class="cw-sec-label">嘉宾介绍</p>
      <h2 class="cw-sec-title">认识他们</h2>
      <div class="cw-artist-grid">
        <div class="acard">
          <div class="acard-num">01</div>
          <div class="acard-name">黄国伦</div>
          <div class="acard-en">Huang Guo Lun</div>
          <div class="tags">
            <span class="tag">流行音乐家</span>
            <span class="tag">选秀评审</span>
            <span class="tag">主持人</span>
            <span class="tag">梦想家</span>
            <span class="tag">福音布道家</span>
          </div>
          <p class="acard-bio">知名流行音乐家、选秀评审、主持人，他也是一位勇敢的梦想家，更是在全世界巡回的福音布道家。</p>
        </div>
        <div class="acard">
          <div class="acard-num">02</div>
          <div class="acard-name">寇乃馨</div>
          <div class="acard-en">Kou Nai Xin</div>
          <div class="tags">
            <span class="tag">主持人</span>
            <span class="tag">情感导师</span>
            <span class="tag">超级演说家</span>
            <span class="tag">福音布道家</span>
          </div>
          <p class="acard-bio">知名主持人、情感导师、超级演说家，也是在全世界巡回的福音布道家。</p>
        </div>
      </div>
    </div>
  </div>

  <div class="cw-divider"></div>

  <!-- 音乐会流程 -->
  <div class="cw-program-bg">
    <div class="cw-wrap fi">
      <p class="cw-sec-label">音乐会流程</p>
      <h2 class="cw-sec-title">今晚的安排</h2>
      <div class="timeline">
        <div class="titem"><div class="ttime">20:00</div><div class="tcontent">开始入场</div></div>
        <div class="titem"><div class="ttime">20:30 — 20:40</div><div class="tcontent"><strong>开场敬拜</strong></div></div>
        <div class="titem"><div class="ttime">20:40 — 20:43</div><div class="tcontent">缪献光长老 · 开幕祷告</div></div>
        <div class="titem"><div class="ttime">20:43 — 20:48</div><div class="tcontent">介绍讲员</div></div>
        <div class="titem hl">
          <div class="ttime">20:48 — 22:18</div>
          <div class="tcontent"><strong>黄国伦 · 寇乃馨</strong><br>音乐 &nbsp;·&nbsp; 见证 &nbsp;·&nbsp; 福音信息</div>
        </div>
        <div class="titem"><div class="ttime">约 22:10</div><div class="tcontent"><strong>福音呼召</strong></div></div>
        <div class="titem"><div class="ttime">22:18 — 22:23</div><div class="tcontent">回应诗歌 · <strong>《我信》</strong></div></div>
        <div class="titem"><div class="ttime">22:23 — 22:26</div><div class="tcontent">黄爱敏牧师 · 祝福祷告</div></div>
        <div class="titem"><div class="ttime">22:26 — 22:30</div><div class="tcontent">结尾 &nbsp;·&nbsp; 全场大合照</div></div>
        <div class="titem"><div class="ttime">22:30</div><div class="tcontent">散场</div></div>
      </div>
    </div>
  </div>

  <div class="cw-divider"></div>

  <!-- 决志卡 -->
  <div class="cw-decision-bg">
    <div class="cw-wrap cw-decision-inner fi">
      <p class="cw-sec-label">决志卡</p>
      <h2 class="cw-sec-title">我愿意接受耶稣</h2>
      <p class="d-sub">
        如果今晚您愿意做出生命中最重要的决定，<br>
        请填写以下表单，我们的同工将与您联系、<br>陪伴您继续这段信仰旅程。
      </p>
      <div class="form-box" id="decisionFormBox">
        <div class="form-cta" id="decisionFormCta">
          <div class="form-cta-icon" aria-hidden="true">
            <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.3">
              <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
            </svg>
          </div>
          <p class="form-cta-lead">准备好了吗？</p>
          <button type="button" class="form-cta-btn" id="openFormBtn" aria-controls="decisionFormIframe">
            <span>我愿意填写决志卡</span>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              <line x1="5" y1="12" x2="19" y2="12"/>
              <polyline points="12 5 19 12 12 19"/>
            </svg>
          </button>
          <a href="https://docs.google.com/forms/d/e/1FAIpQLSe5oeB0LYRdtTcBojnjQIvHeD4hCF07N6n0wgd2F6RXsKmwKw/viewform" target="_blank" rel="noopener" class="form-cta-alt">或在新窗口打开填写</a>
        </div>
        <iframe id="decisionFormIframe" title="决志卡表单" hidden></iframe>
      </div>
    </div>
  </div>

  <div class="cw-divider"></div>

  <!-- 各地教会 -->
  <div class="cw-churches-bg">
    <div class="cw-wrap fi">
      <p class="cw-sec-label">各地教会</p>
      <h2 class="cw-sec-title">找到你附近的教会</h2>
      <div class="church-grid">

        <div class="ccard">
          <div class="ccard-dot-row"><div class="ccard-dot"></div><div class="ccard-name">米兰华人复兴教会</div></div>
          <div class="ccard-time">主日 15:30 – 17:30</div>
          <div class="ccard-addr">Via Camillo Ugoni 20<br>20158 Milano</div>
          <div class="ccard-divider"></div>
          <div class="ccard-contact">
            <span class="ccard-person">黄爱敏牧师</span>
            <a class="ccard-phone" href="tel:+393394295778">339 4295778</a>
          </div>
          <a class="ccard-map" href="https://maps.google.com/?q=Via+Camillo+Ugoni+20,+20158+Milano" target="_blank" rel="noopener">查看地图 ↗</a>
        </div>

        <div class="ccard">
          <div class="ccard-dot-row"><div class="ccard-dot"></div><div class="ccard-name">Legnano华人复兴教会</div></div>
          <div class="ccard-time">主日 15:30 – 17:30</div>
          <div class="ccard-addr">Via Marzabotto 3<br>20025 Legnano</div>
          <div class="ccard-divider"></div>
          <div class="ccard-contact">
            <span class="ccard-person">缪献光长老</span>
            <a class="ccard-phone" href="tel:+393299294342">329 9294342</a>
          </div>
          <a class="ccard-map" href="https://maps.google.com/?q=Via+Marzabotto+3,+20025+Legnano" target="_blank" rel="noopener">查看地图 ↗</a>
        </div>

        <div class="ccard">
          <div class="ccard-dot-row"><div class="ccard-dot"></div><div class="ccard-name">Bergamo华人复兴教会</div></div>
          <div class="ccard-time">主日 15:00 – 17:00</div>
          <div class="ccard-addr">Via Andrea Gritti 11<br>24125 Bergamo</div>
          <div class="ccard-divider"></div>
          <div class="ccard-contact">
            <span class="ccard-person">张建峰弟兄</span>
            <a class="ccard-phone" href="tel:+393313486021">331 3486021</a>
          </div>
          <a class="ccard-map" href="https://maps.google.com/?q=Via+Andrea+Gritti+11,+24125+Bergamo" target="_blank" rel="noopener">查看地图 ↗</a>
        </div>

        <div class="ccard">
          <div class="ccard-dot-row"><div class="ccard-dot"></div><div class="ccard-name">Brescia华人复兴教会</div></div>
          <div class="ccard-time">主日 15:30 – 17:30</div>
          <div class="ccard-addr">Via Belvedere 20<br>25124 Brescia</div>
          <div class="ccard-divider"></div>
          <div class="ccard-contact">
            <span class="ccard-person">周小丽姊妹</span>
            <a class="ccard-phone" href="tel:+393395908417">339 5908417</a>
          </div>
          <a class="ccard-map" href="https://maps.google.com/?q=Via+Belvedere+20,+25124+Brescia" target="_blank" rel="noopener">查看地图 ↗</a>
        </div>

        <div class="ccard">
          <div class="ccard-dot-row"><div class="ccard-dot"></div><div class="ccard-name">Verona华人复兴教会</div></div>
          <div class="ccard-time">主日 15:30 – 17:30</div>
          <div class="ccard-addr">Via Fiumicello 15<br>37131 Verona</div>
          <div class="ccard-divider"></div>
          <div class="ccard-contact">
            <span class="ccard-person">张丽丽姊妹</span>
            <a class="ccard-phone" href="tel:+393882563902">388 2563902</a>
          </div>
          <a class="ccard-map" href="https://maps.google.com/?q=Via+Fiumicello+15,+37131+Verona" target="_blank" rel="noopener">查看地图 ↗</a>
        </div>

        <div class="ccard">
          <div class="ccard-dot-row"><div class="ccard-dot"></div><div class="ccard-name">Trento华人复兴教会</div></div>
          <div class="ccard-time">主日 15:30 – 17:30</div>
          <div class="ccard-addr">Via Valentina Zambra 18<br>38121 Trento</div>
          <div class="ccard-divider"></div>
          <div class="ccard-contact">
            <span class="ccard-person">林爱道弟兄</span>
            <a class="ccard-phone" href="tel:+393394155132">339 4155132</a>
          </div>
          <a class="ccard-map" href="https://maps.google.com/?q=Via+Valentina+Zambra+18,+38121+Trento" target="_blank" rel="noopener">查看地图 ↗</a>
        </div>

      </div>
    </div>
  </div>

  <!-- 返回 -->
  <div class="cw-back-bar">
    <?php
      $news_page = get_page_by_path('news');
      $news_url  = $news_page ? get_permalink($news_page->ID) : home_url('/news/');
    ?>
    <a href="<?php echo esc_url($news_url); ?>">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
      返回最新消息
    </a>
  </div>

</div><!-- .cw -->

<script>
(function(){
  // 滚动淡入
  var fiObs = new IntersectionObserver(function(entries){
    entries.forEach(function(e){
      if(e.isIntersecting){ e.target.classList.add('on'); fiObs.unobserve(e.target); }
    });
  }, { threshold: 0.08 });
  document.querySelectorAll('.cw .fi').forEach(function(el){ fiObs.observe(el); });

  // 时间轴逐项
  var tlObs = new IntersectionObserver(function(entries){
    entries.forEach(function(e){
      if(e.isIntersecting){
        e.target.querySelectorAll('.titem').forEach(function(item, i){
          setTimeout(function(){ item.classList.add('on'); }, i * 90);
        });
        tlObs.unobserve(e.target);
      }
    });
  }, { threshold: 0.05 });
  document.querySelectorAll('.cw .timeline').forEach(function(el){ tlObs.observe(el); });

  // 决志卡 —— 点击后才加载表单
  var openBtn = document.getElementById('openFormBtn');
  if (openBtn) {
    openBtn.addEventListener('click', function(){
      var box = document.getElementById('decisionFormBox');
      var iframe = document.getElementById('decisionFormIframe');
      if (!iframe.src) {
        iframe.src = 'https://docs.google.com/forms/d/e/1FAIpQLSe5oeB0LYRdtTcBojnjQIvHeD4hCF07N6n0wgd2F6RXsKmwKw/viewform?embedded=true';
      }
      iframe.removeAttribute('hidden');
      box.classList.add('is-open');
      setTimeout(function(){
        iframe.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }, 120);
    });
  }
})();
</script>

<?php get_footer(); ?>

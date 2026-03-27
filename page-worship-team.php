<?php
/**
 * Template Name: 敬拜团内部页面
 * 内部专用页面 - 不出现在导航菜单中
 */
get_header();
?>

<style>
/* ===== 敬拜团页面专属样式 ===== */
.worship-team-page {
    padding-top: 80px;
    min-height: 100vh;
    background: var(--bg-primary, #fff);
}

/* 页面标题区 */
.worship-team-header {
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
    padding: 60px 0 50px;
    position: relative;
    overflow: hidden;
}

.worship-team-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="20" cy="20" r="40" fill="none" stroke="rgba(255,255,255,0.03)" stroke-width="1"/><circle cx="80" cy="80" r="40" fill="none" stroke="rgba(255,255,255,0.03)" stroke-width="1"/></svg>') center/cover;
    pointer-events: none;
}

.worship-team-header .container {
    position: relative;
    z-index: 1;
}

.worship-team-badge {
    display: inline-block;
    background: rgba(255,255,255,0.12);
    border: 1px solid rgba(255,255,255,0.2);
    color: rgba(255,255,255,0.85);
    font-size: 0.72rem;
    font-weight: 600;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    padding: 5px 14px;
    border-radius: 20px;
    margin-bottom: 18px;
}

.worship-team-header h1 {
    color: #fff;
    font-size: clamp(1.8rem, 4vw, 2.8rem);
    font-weight: 700;
    margin: 0 0 12px;
    letter-spacing: 0.02em;
}

.worship-team-header .header-sub {
    color: rgba(255,255,255,0.65);
    font-size: 0.95rem;
    margin: 0;
}

.worship-team-header .header-divider {
    width: 40px;
    height: 2px;
    background: rgba(255,255,255,0.4);
    margin: 18px 0;
}

/* 主内容区 */
.worship-team-body {
    padding: 48px 0 72px;
}

.worship-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 24px;
    margin-bottom: 24px;
}

@media (max-width: 768px) {
    .worship-grid {
        grid-template-columns: 1fr;
    }
}

/* 卡片通用 */
.worship-card {
    background: var(--bg-card, #f9f9f9);
    border: 1px solid var(--border-color, #e8e8e8);
    border-radius: 12px;
    padding: 28px;
    transition: box-shadow 0.2s ease;
}

[data-theme="dark"] .worship-card {
    background: rgba(255,255,255,0.04);
    border-color: rgba(255,255,255,0.08);
}

.worship-card:hover {
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
}

[data-theme="dark"] .worship-card:hover {
    box-shadow: 0 4px 20px rgba(0,0,0,0.3);
}

.worship-card-header {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 20px;
    padding-bottom: 16px;
    border-bottom: 1px solid var(--border-color, #e8e8e8);
}

[data-theme="dark"] .worship-card-header {
    border-color: rgba(255,255,255,0.08);
}

.worship-card-icon {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    background: var(--accent, #c8a96e);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.worship-card-icon svg {
    width: 18px;
    height: 18px;
    stroke: #fff;
}

.worship-card-title {
    font-size: 1rem;
    font-weight: 700;
    color: var(--text-primary, #1a1a1a);
    margin: 0;
    letter-spacing: 0.02em;
}

[data-theme="dark"] .worship-card-title {
    color: rgba(255,255,255,0.92);
}

/* 歌单样式 */
.setlist-card {
    grid-column: 1 / -1;
}

.setlist-meta {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 0.8rem;
    color: var(--text-secondary, #888);
    margin-bottom: 20px;
}

.setlist-meta svg {
    width: 14px;
    height: 14px;
    stroke: var(--text-secondary, #888);
}

.song-list {
    list-style: none;
    padding: 0;
    margin: 0;
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
    gap: 10px;
}

.song-item {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 12px 14px;
    background: var(--bg-primary, #fff);
    border: 1px solid var(--border-color, #e8e8e8);
    border-radius: 8px;
}

[data-theme="dark"] .song-item {
    background: rgba(255,255,255,0.03);
    border-color: rgba(255,255,255,0.06);
}

.song-number {
    font-size: 0.7rem;
    font-weight: 700;
    color: var(--accent, #c8a96e);
    min-width: 20px;
    margin-top: 2px;
    letter-spacing: 0.05em;
}

.song-info {
    flex: 1;
}

.song-name {
    font-size: 0.9rem;
    font-weight: 600;
    color: var(--text-primary, #1a1a1a);
    margin: 0 0 3px;
    display: block;
}

[data-theme="dark"] .song-name {
    color: rgba(255,255,255,0.9);
}

.song-detail {
    font-size: 0.75rem;
    color: var(--text-secondary, #888);
    margin: 0;
}

.song-key-badge {
    font-size: 0.68rem;
    font-weight: 700;
    background: rgba(200, 169, 110, 0.12);
    color: var(--accent, #c8a96e);
    border: 1px solid rgba(200, 169, 110, 0.25);
    padding: 2px 8px;
    border-radius: 4px;
    white-space: nowrap;
    align-self: flex-start;
    margin-top: 2px;
}

.setlist-note {
    margin-top: 16px;
    padding: 12px 16px;
    background: rgba(200, 169, 110, 0.08);
    border-left: 3px solid var(--accent, #c8a96e);
    border-radius: 0 6px 6px 0;
    font-size: 0.82rem;
    color: var(--text-secondary, #666);
    line-height: 1.6;
}

[data-theme="dark"] .setlist-note {
    color: rgba(255,255,255,0.55);
}

/* 排练安排 */
.schedule-list {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.schedule-item {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 12px 0;
    border-bottom: 1px solid var(--border-color, #eee);
}

[data-theme="dark"] .schedule-item {
    border-color: rgba(255,255,255,0.06);
}

.schedule-item:last-child {
    border-bottom: none;
    padding-bottom: 0;
}

.schedule-day {
    font-size: 0.75rem;
    font-weight: 700;
    color: var(--accent, #c8a96e);
    min-width: 48px;
    text-align: center;
    background: rgba(200, 169, 110, 0.1);
    border-radius: 6px;
    padding: 4px 6px;
    letter-spacing: 0.03em;
}

.schedule-info {
    flex: 1;
}

.schedule-event {
    font-size: 0.88rem;
    font-weight: 600;
    color: var(--text-primary, #1a1a1a);
    margin: 0 0 2px;
    display: block;
}

[data-theme="dark"] .schedule-event {
    color: rgba(255,255,255,0.9);
}

.schedule-time {
    font-size: 0.76rem;
    color: var(--text-secondary, #888);
    margin: 0;
}

.schedule-location {
    font-size: 0.75rem;
    color: var(--text-secondary, #999);
    white-space: nowrap;
}

/* 通知区 */
.notice-list {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.notice-item {
    padding: 12px 14px;
    background: var(--bg-primary, #fff);
    border: 1px solid var(--border-color, #e8e8e8);
    border-left: 3px solid #e74c3c;
    border-radius: 0 8px 8px 0;
}

[data-theme="dark"] .notice-item {
    background: rgba(255,255,255,0.03);
    border-color: rgba(255,255,255,0.06);
    border-left-color: #e74c3c;
}

.notice-item.notice-info {
    border-left-color: #3498db;
}

.notice-item.notice-ok {
    border-left-color: #27ae60;
}

.notice-date {
    font-size: 0.7rem;
    color: var(--text-secondary, #999);
    margin-bottom: 4px;
    display: block;
}

.notice-text {
    font-size: 0.85rem;
    color: var(--text-primary, #333);
    margin: 0;
    line-height: 1.5;
}

[data-theme="dark"] .notice-text {
    color: rgba(255,255,255,0.78);
}

/* 资源链接 */
.resource-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
}

@media (max-width: 480px) {
    .resource-grid {
        grid-template-columns: 1fr;
    }
}

.resource-link {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 14px;
    background: var(--bg-primary, #fff);
    border: 1px solid var(--border-color, #e8e8e8);
    border-radius: 8px;
    text-decoration: none;
    color: var(--text-primary, #333);
    font-size: 0.85rem;
    font-weight: 500;
    transition: border-color 0.2s, background 0.2s;
}

[data-theme="dark"] .resource-link {
    background: rgba(255,255,255,0.03);
    border-color: rgba(255,255,255,0.06);
    color: rgba(255,255,255,0.78);
}

.resource-link:hover {
    border-color: var(--accent, #c8a96e);
    background: rgba(200, 169, 110, 0.05);
    color: var(--accent, #c8a96e);
}

.resource-link svg {
    width: 16px;
    height: 16px;
    stroke: var(--accent, #c8a96e);
    flex-shrink: 0;
}

.resource-ext {
    margin-left: auto;
    font-size: 0.65rem;
    color: var(--text-secondary, #bbb);
}

/* WordPress 编辑器内容区 */
.worship-editor-content {
    grid-column: 1 / -1;
}

.worship-editor-content .entry-content {
    font-size: 0.9rem;
    line-height: 1.8;
    color: var(--text-secondary, #555);
}

[data-theme="dark"] .worship-editor-content .entry-content {
    color: rgba(255,255,255,0.6);
}

/* 底部提示 */
.worship-footer-note {
    text-align: center;
    padding: 24px 0 0;
    border-top: 1px solid var(--border-color, #eee);
    margin-top: 12px;
}

[data-theme="dark"] .worship-footer-note {
    border-color: rgba(255,255,255,0.07);
}

.worship-footer-note p {
    font-size: 0.78rem;
    color: var(--text-secondary, #aaa);
    margin: 0;
}

.worship-footer-note a {
    color: var(--accent, #c8a96e);
    text-decoration: none;
}

.worship-footer-note a:hover {
    text-decoration: underline;
}
</style>

<main class="worship-team-page">

    <!-- 页面标题 -->
    <section class="worship-team-header">
        <div class="container">
            <div class="worship-team-badge">Internal · 内部专用</div>
            <h1>敬拜团工作页面</h1>
            <div class="header-divider"></div>
            <p class="header-sub">米兰华人复兴教会 &mdash; 敬拜团成员专用，请勿外传链接</p>
        </div>
    </section>

    <!-- 主内容 -->
    <section class="worship-team-body">
        <div class="container">

            <!-- 第一行：本周歌单（全宽） -->
            <div class="worship-grid">
                <div class="worship-card setlist-card fade-in">
                    <div class="worship-card-header">
                        <div class="worship-card-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke-width="2"><path d="M9 18V5l12-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="18" cy="16" r="3"/></svg>
                        </div>
                        <h2 class="worship-card-title">本周歌单</h2>
                    </div>

                    <div class="setlist-meta">
                        <svg viewBox="0 0 24 24" fill="none" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        <span>
                            <?php
                            // 自动显示下一个主日日期
                            $today = new DateTime('now', new DateTimeZone('Europe/Rome'));
                            $dow = (int) $today->format('N'); // 1=Mon ... 7=Sun
                            $days_until_sunday = $dow === 7 ? 7 : 7 - $dow;
                            $next_sunday = clone $today;
                            $next_sunday->modify("+{$days_until_sunday} days");
                            echo '下次主日：' . $next_sunday->format('Y年m月d日') . '（主日崇拜 15:30）';
                            ?>
                        </span>
                    </div>

                    <ul class="song-list">
                        <li class="song-item">
                            <span class="song-number">01</span>
                            <div class="song-info">
                                <span class="song-name">在主里面</span>
                                <p class="song-detail">敬拜开始曲 · 会众一起</p>
                            </div>
                            <span class="song-key-badge">D</span>
                        </li>
                        <li class="song-item">
                            <span class="song-number">02</span>
                            <div class="song-info">
                                <span class="song-name">何等恩典</span>
                                <p class="song-detail">快歌 · 全场站立</p>
                            </div>
                            <span class="song-key-badge">G</span>
                        </li>
                        <li class="song-item">
                            <span class="song-number">03</span>
                            <div class="song-info">
                                <span class="song-name">我心所愿</span>
                                <p class="song-detail">慢歌 · 敬拜进入</p>
                            </div>
                            <span class="song-key-badge">E</span>
                        </li>
                        <li class="song-item">
                            <span class="song-number">04</span>
                            <div class="song-info">
                                <span class="song-name">在圣殿里</span>
                                <p class="song-detail">敬拜高潮</p>
                            </div>
                            <span class="song-key-badge">A</span>
                        </li>
                        <li class="song-item">
                            <span class="song-number">05</span>
                            <div class="song-info">
                                <span class="song-name">感谢主</span>
                                <p class="song-detail">回应诗歌 · 安静</p>
                            </div>
                            <span class="song-key-badge">C</span>
                        </li>
                    </ul>

                    <div class="setlist-note">
                        <strong>领唱负责人备注：</strong>请各位团员提前熟悉歌词，排练时间为周六下午。如有调号需要调整，请提前联系负责人。
                    </div>
                </div>
            </div>

            <!-- 第二行：排练安排 + 重要通知 -->
            <div class="worship-grid">

                <!-- 排练安排 -->
                <div class="worship-card fade-in">
                    <div class="worship-card-header">
                        <div class="worship-card-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        </div>
                        <h2 class="worship-card-title">排练安排</h2>
                    </div>

                    <ul class="schedule-list">
                        <li class="schedule-item">
                            <span class="schedule-day">周六</span>
                            <div class="schedule-info">
                                <span class="schedule-event">主日敬拜排练</span>
                                <p class="schedule-time">下午 14:00 &ndash; 16:00</p>
                            </div>
                            <span class="schedule-location">教会礼堂</span>
                        </li>
                        <li class="schedule-item">
                            <span class="schedule-day">周日</span>
                            <div class="schedule-info">
                                <span class="schedule-event">主日前彩排</span>
                                <p class="schedule-time">下午 14:30 &ndash; 15:00</p>
                            </div>
                            <span class="schedule-location">教会礼堂</span>
                        </li>
                        <li class="schedule-item">
                            <span class="schedule-day">不定期</span>
                            <div class="schedule-info">
                                <span class="schedule-event">团队聚会 / 培训</span>
                                <p class="schedule-time">时间另行通知</p>
                            </div>
                            <span class="schedule-location">待定</span>
                        </li>
                    </ul>
                </div>

                <!-- 重要通知 -->
                <div class="worship-card fade-in">
                    <div class="worship-card-header">
                        <div class="worship-card-icon" style="background: #e74c3c;">
                            <svg viewBox="0 0 24 24" fill="none" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                        </div>
                        <h2 class="worship-card-title">重要通知</h2>
                    </div>

                    <ul class="notice-list">
                        <li class="notice-item">
                            <span class="notice-date">2026-03-27</span>
                            <p class="notice-text">本周歌单已更新，请所有成员确认调号，如有问题请联系带领人。</p>
                        </li>
                        <li class="notice-item notice-info">
                            <span class="notice-date">2026-03-20</span>
                            <p class="notice-text">复活节特别敬拜排练安排将另行通知，请提前预留时间。</p>
                        </li>
                        <li class="notice-item notice-ok">
                            <span class="notice-date">2026-03-15</span>
                            <p class="notice-text">新麦克风系统已安装完毕，周六排练时请留意监听音量调试。</p>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- 第三行：团队资源 + 联系方式 -->
            <div class="worship-grid">

                <!-- 团队资源 -->
                <div class="worship-card fade-in">
                    <div class="worship-card-header">
                        <div class="worship-card-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        </div>
                        <h2 class="worship-card-title">团队资源</h2>
                    </div>

                    <div class="resource-grid">
                        <a href="https://www.youtube.com/@%E5%9F%BA%E7%9D%A3%E6%95%99%E7%B1%B3%E5%85%B0%E5%8D%8E%E4%BA%BA%E5%A4%8D%E5%85%B4%E6%95%99%E4%BC%9A" class="resource-link" target="_blank" rel="noopener noreferrer">
                            <svg viewBox="0 0 24 24" fill="none" stroke-width="2"><polygon points="23 7 16 12 23 17 23 7"/><rect x="1" y="5" width="15" height="14" rx="2" ry="2"/></svg>
                            教会 YouTube 频道
                            <span class="resource-ext">外链</span>
                        </a>
                        <a href="https://docs.google.com" class="resource-link" target="_blank" rel="noopener noreferrer">
                            <svg viewBox="0 0 24 24" fill="none" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                            歌单文档
                            <span class="resource-ext">外链</span>
                        </a>
                        <a href="https://drive.google.com" class="resource-link" target="_blank" rel="noopener noreferrer">
                            <svg viewBox="0 0 24 24" fill="none" stroke-width="2"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>
                            共享文件夹
                            <span class="resource-ext">外链</span>
                        </a>
                        <a href="https://www.ultimate-guitar.com" class="resource-link" target="_blank" rel="noopener noreferrer">
                            <svg viewBox="0 0 24 24" fill="none" stroke-width="2"><path d="M9 18V5l12-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="18" cy="16" r="3"/></svg>
                            和弦谱查询
                            <span class="resource-ext">外链</span>
                        </a>
                    </div>
                </div>

                <!-- 联系方式 -->
                <div class="worship-card fade-in">
                    <div class="worship-card-header">
                        <div class="worship-card-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                        </div>
                        <h2 class="worship-card-title">团队联系</h2>
                    </div>

                    <ul class="schedule-list">
                        <li class="schedule-item">
                            <span class="schedule-day" style="font-size: 0.65rem; padding: 4px 4px;">带领</span>
                            <div class="schedule-info">
                                <span class="schedule-event">敬拜带领人</span>
                                <p class="schedule-time">负责整体敬拜安排与带领</p>
                            </div>
                        </li>
                        <li class="schedule-item">
                            <span class="schedule-day" style="font-size: 0.65rem; padding: 4px 4px;">音响</span>
                            <div class="schedule-info">
                                <span class="schedule-event">音响负责人</span>
                                <p class="schedule-time">现场音响、直播技术支持</p>
                            </div>
                        </li>
                        <li class="schedule-item">
                            <span class="schedule-day" style="font-size: 0.65rem; padding: 4px 4px;">投影</span>
                            <div class="schedule-info">
                                <span class="schedule-event">投影字幕负责人</span>
                                <p class="schedule-time">歌词投影与 ProPresenter 操作</p>
                            </div>
                        </li>
                        <li class="schedule-item">
                            <span class="schedule-day" style="font-size: 0.65rem; padding: 4px 4px;">牧师</span>
                            <div class="schedule-info">
                                <span class="schedule-event">张牧师</span>
                                <p class="schedule-time">教会整体事工指导</p>
                            </div>
                        </li>
                    </ul>
                </div>

            </div>

            <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
                <?php if (get_the_content()) : ?>
                    <!-- WordPress 编辑器额外内容（可选） -->
                    <div class="worship-grid">
                        <div class="worship-card worship-editor-content fade-in">
                            <div class="worship-card-header">
                                <div class="worship-card-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke-width="2"><line x1="17" y1="10" x2="3" y2="10"/><line x1="21" y1="6" x2="3" y2="6"/><line x1="21" y1="14" x2="3" y2="14"/><line x1="17" y1="18" x2="3" y2="18"/></svg>
                                </div>
                                <h2 class="worship-card-title">附加内容</h2>
                            </div>
                            <div class="entry-content">
                                <?php the_content(); ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endwhile; endif; ?>

            <!-- 底部提示 -->
            <div class="worship-footer-note">
                <p>本页面为内部专用，请勿转发链接 &nbsp;&middot;&nbsp; 如需更新内容请联系 <a href="<?php echo esc_url(home_url('/')); ?>">教会管理员</a></p>
            </div>

        </div>
    </section>

</main>

<?php get_footer(); ?>

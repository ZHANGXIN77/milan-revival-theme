<?php
/**
 * Template Name: 最新消息
 * 独立页面 - 教会活动与消息列表
 */
get_header();

$easter_page = get_page_by_path('easter-2026');
$easter_url = $easter_page ? get_permalink($easter_page->ID) : '#';

$concert_page = get_page_by_path('concert-2026');
$concert_url = $concert_page ? get_permalink($concert_page->ID) : '#';
?>

<main class="news-page">
    <!-- Page Header -->
    <section class="news-page-header">
        <div class="container">
            <div class="section-header fade-in">
                <span class="section-label">Latest News</span>
                <h2>最新消息</h2>
                <div class="section-divider"></div>
                <p class="news-page-desc">了解教会近期的活动、见证与最新动态</p>
            </div>
        </div>
    </section>

    <!-- News List -->
    <section class="news-list-section">
        <div class="container">
            <div class="news-list">

                <!-- Event 1: Easter -->
                <article class="news-card fade-in">
                    <a href="<?php echo esc_url($easter_url); ?>" class="news-card-inner">
                        <div class="news-card-image">
                            <div class="news-card-placeholder news-card-placeholder-styled news-card-placeholder-easter">
                                <div class="placeholder-overlay"></div>
                                <div class="placeholder-content">
                                    <span class="placeholder-tag">EASTER WORSHIP</span>
                                    <span class="placeholder-title-text">复活节</span>
                                    <span class="placeholder-subtitle">北部联合崇拜</span>
                                </div>
                            </div>
                        </div>
                        <div class="news-card-content">
                            <time class="news-card-date">2026年4月6日 周一</time>
                            <h3 class="news-card-title">北部复活节联合崇拜</h3>
                            <p class="news-card-excerpt">欢迎您与家人朋友一同参加 2026 北部复活节联合崇拜，在敬拜与真理中纪念主耶稣的复活，一起经历生命的更新与盼望。</p>
                            <span class="news-card-link">了解详情 <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg></span>
                        </div>
                    </a>
                </article>

                <!-- Event 2: Concert -->
                <article class="news-card fade-in">
                    <a href="<?php echo esc_url($concert_url); ?>" class="news-card-inner">
                        <div class="news-card-image">
                            <div class="news-card-placeholder news-card-placeholder-styled">
                                <div class="placeholder-overlay"></div>
                                <div class="placeholder-content">
                                    <span class="placeholder-tag">CONCERT + TESTIMONY</span>
                                    <span class="placeholder-title-text">没有不可能</span>
                                    <span class="placeholder-subtitle">黄国伦 x 寇乃馨</span>
                                </div>
                            </div>
                        </div>
                        <div class="news-card-content">
                            <time class="news-card-date">2026年4月16日 周四 20:30-22:30</time>
                            <h3 class="news-card-title">没有不可能！黄国伦 x 寇乃馨 音乐布道会</h3>
                            <p class="news-card-excerpt">华语基督徒艺人黄国伦与寇乃馨，跨越舞台与信仰，带着音乐与生命见证来到米兰！这不只是一场音乐会，更是一个被触动、被改变的夜晚。免费入场。</p>
                            <span class="news-card-link">了解详情 <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg></span>
                        </div>
                    </a>
                </article>

            </div>
        </div>
    </section>
</main>

<?php get_footer(); ?>

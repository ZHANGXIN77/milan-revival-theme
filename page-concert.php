<?php
/**
 * Template Name: 音乐布道会
 */
get_header();
?>

<main class="event-page">
    <div class="event-page-spacer"></div>
    <article class="event-article">
        <div class="container">
            <div class="event-header fade-in">
                <time class="event-date">2026年4月16日 周四 20:30-22:30</time>
                <h1 class="event-title">没有不可能！黄国伦 x 寇乃馨 音乐布道会</h1>
                <div class="section-divider"></div>
            </div>
            <div class="event-content fade-in">
                <p>华语基督徒艺人黄国伦与寇乃馨，跨越舞台与信仰，带着音乐与生命见证来到米兰！这不只是一场音乐会，更是一个被触动、被改变的夜晚。</p>

                <h2>活动详情</h2>
                <ul>
                    <li><strong>日期：</strong>2026年4月16日（周四）</li>
                    <li><strong>时间：</strong>20:30 - 22:30</li>
                    <li><strong>地点：</strong>Via Camillo Ugoni 20, 20158 Milano</li>
                    <li><strong>费用：</strong>免费入场</li>
                </ul>

                <h2>聚会流程</h2>
                <p>（详细流程待更新）</p>

                <p>无论你是否有信仰，欢迎带上朋友，一起来听一个关于"不可能"变成可能的故事！</p>

                <p><strong>主办：</strong>意大利华人复兴教会北部牧区</p>
            </div>
            <div class="event-footer fade-in">
                <?php $news_page = get_page_by_path('news'); $news_url = $news_page ? get_permalink($news_page->ID) : home_url('/news/'); ?>
                <a href="<?php echo esc_url($news_url); ?>" class="news-back-link">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
                    返回最新消息
                </a>
            </div>
        </div>
    </article>
</main>

<?php get_footer(); ?>

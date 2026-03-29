<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="基督教米兰华人复兴教会 - 建造活出爱的健康基督教会">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<script>
(function(){var t=localStorage.getItem('theme');if(t)document.documentElement.setAttribute('data-theme',t);})();
</script>

<!-- Mobile Menu Overlay -->
<div class="mobile-menu" id="mobileMenu">
    <ul>
        <li><a href="<?php echo esc_url(home_url('/#pastor')); ?>" data-scroll>牧师欢迎</a></li>
        <?php $news_page = get_page_by_path('news'); $news_url = $news_page ? get_permalink($news_page->ID) : home_url('/news/'); ?>
                <li><a href="<?php echo esc_url($news_url); ?>">最新消息</a></li>
        <?php $about_page = get_page_by_path('about'); $about_url = $about_page ? get_permalink($about_page->ID) : home_url('/about/'); ?>
                <li><a href="<?php echo esc_url($about_url); ?>">关于我们</a></li>
        <?php $sunday_page = get_page_by_path('sunday'); $sunday_url = $sunday_page ? get_permalink($sunday_page->ID) : home_url('/sunday/'); ?>
                <li><a href="<?php echo esc_url($sunday_url); ?>">主日崇拜</a></li>
        <?php $visit_page = get_page_by_path('visit'); $visit_url = $visit_page ? get_permalink($visit_page->ID) : home_url('/?page_id=visit'); ?>
                <li><a href="<?php echo esc_url($visit_url); ?>">初次来访</a></li>
        <?php $giving_page = get_page_by_path('giving'); $giving_url = $giving_page ? get_permalink($giving_page->ID) : home_url('/giving/'); ?>
                <li><a href="<?php echo esc_url($giving_url); ?>">线上奉献</a></li>
        <li><a href="<?php echo esc_url(get_template_directory_uri()); ?>/pastoral-system/dist/index.html">牧养系统</a></li>
    </ul>
</div>

<!-- Header -->
<header class="site-header" id="siteHeader">
    <div class="header-inner">
        <a href="<?php echo esc_url(home_url('/')); ?>" class="site-logo">
            <?php if (has_custom_logo()) : ?>
                <?php the_custom_logo(); ?>
            <?php else : ?>
                <span class="logo-zh">米兰华人<span>复兴</span>教会</span>
                <span class="logo-en">Milano Revival Church</span>
            <?php endif; ?>
        </a>

        <nav class="main-nav">
            <ul>
                <li><a href="<?php echo esc_url(home_url('/#pastor')); ?>" data-scroll>牧师欢迎</a></li>
                <?php $news_page = get_page_by_path('news'); $news_url = $news_page ? get_permalink($news_page->ID) : home_url('/news/'); ?>
                <li><a href="<?php echo esc_url($news_url); ?>">最新消息</a></li>
                <?php $about_page = get_page_by_path('about'); $about_url = $about_page ? get_permalink($about_page->ID) : home_url('/about/'); ?>
                <li><a href="<?php echo esc_url($about_url); ?>">关于我们</a></li>
                <?php $sunday_page = get_page_by_path('sunday'); $sunday_url = $sunday_page ? get_permalink($sunday_page->ID) : home_url('/sunday/'); ?>
                <li><a href="<?php echo esc_url($sunday_url); ?>">主日崇拜</a></li>
                <?php $visit_page = get_page_by_path('visit'); $visit_url = $visit_page ? get_permalink($visit_page->ID) : home_url('/?page_id=visit'); ?>
                <li><a href="<?php echo esc_url($visit_url); ?>">初次来访</a></li>
                <?php $giving_page = get_page_by_path('giving'); $giving_url = $giving_page ? get_permalink($giving_page->ID) : home_url('/giving/'); ?>
                <li><a href="<?php echo esc_url($giving_url); ?>">线上奉献</a></li>
                <li><a href="<?php echo esc_url(get_template_directory_uri()); ?>/pastoral-system/dist/index.html">牧养系统</a></li>
            </ul>
        </nav>

        <div class="header-actions">
            <button class="theme-toggle" id="themeToggle" aria-label="切换亮色/暗色模式">
                <svg class="icon-moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
                <svg class="icon-sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg>
            </button>
            <button class="menu-toggle" id="menuToggle" aria-label="打开菜单">
            <span></span>
            <span></span>
            <span></span>
        </button>
        </div>
    </div>
</header>

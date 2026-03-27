<?php
/**
 * 消息详情页 (Single Post)
 */
get_header();
?>

<main class="news-single">
    <?php while (have_posts()) : the_post(); ?>

        <!-- Featured Image -->
        <?php if (has_post_thumbnail()) : ?>
            <div class="news-single-hero">
                <div class="news-single-hero-image">
                    <?php the_post_thumbnail('full', array('alt' => get_the_title())); ?>
                </div>
                <div class="news-single-hero-overlay"></div>
            </div>
        <?php else : ?>
            <div class="news-single-spacer"></div>
        <?php endif; ?>

        <!-- Article Content -->
        <article class="news-single-article">
            <div class="container">
                <div class="news-single-header fade-in">
                    <time class="news-single-date" datetime="<?php echo get_the_date('Y-m-d'); ?>">
                        <?php echo get_the_date('Y年n月j日'); ?>
                    </time>
                    <h1 class="news-single-title"><?php the_title(); ?></h1>
                    <div class="section-divider"></div>
                </div>
                <div class="news-single-content fade-in">
                    <?php the_content(); ?>
                </div>
                <div class="news-single-footer fade-in">
                    <a href="<?php echo esc_url(get_permalink(get_option('page_for_posts'))); ?>" class="news-back-link">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
                        返回最新消息
                    </a>
                </div>
            </div>
        </article>

    <?php endwhile; ?>
</main>

<?php get_footer(); ?>

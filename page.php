<?php
/**
 * 通用页面模板
 */
get_header();
?>

<main class="section section-dark" style="padding-top: 120px;">
    <div class="container">
        <?php while (have_posts()) : the_post(); ?>
            <article>
                <div class="section-header">
                    <h2><?php the_title(); ?></h2>
                    <div class="section-divider"></div>
                </div>
                <div class="page-content" style="color: var(--color-text-secondary); line-height: 1.9;">
                    <?php the_content(); ?>
                </div>
            </article>
        <?php endwhile; ?>
    </div>
</main>

<?php get_footer(); ?>

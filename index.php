<?php
/**
 * 默认模板 - 重定向到首页
 */
get_header();
?>

<main class="section section-dark">
    <div class="container">
        <?php if (have_posts()) : ?>
            <?php while (have_posts()) : the_post(); ?>
                <article>
                    <h2><?php the_title(); ?></h2>
                    <div><?php the_content(); ?></div>
                </article>
            <?php endwhile; ?>
        <?php else : ?>
            <p>暂无内容。</p>
        <?php endif; ?>
    </div>
</main>

<?php get_footer(); ?>

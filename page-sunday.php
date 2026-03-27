<?php
/**
 * Template Name: 主日崇拜
 * 独立页面 - 直播 + 往期主日回顾
 */
get_header();
?>

<main class="sunday-page">
    <!-- Page Header -->
    <section class="sunday-page-header">
        <div class="container">
            <div class="section-header fade-in">
                <span class="section-label">Sunday Worship</span>
                <h2>主日崇拜</h2>
                <div class="section-divider"></div>
                <p class="sunday-page-desc">每周日与我们一起敬拜，无论您身在何处</p>
            </div>
        </div>
    </section>

    <!-- Live / Latest Video -->
    <section class="sunday-live-section">
        <div class="container">
            <div class="sunday-live-player fade-in">
                <div class="video-wrapper" id="youtubePlayerPage">
                    <iframe
                        id="ytFramePage"
                        src="https://www.youtube.com/embed/live_stream?channel=UCSNVRXPho2A_xXp-hB3_iig"
                        title="主日崇拜直播"
                        frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen>
                    </iframe>
                </div>
            </div>
            <div class="sunday-live-meta fade-in">
                <div class="sunday-meta-item">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    <span>每周主日 15:30</span>
                </div>
                <div class="sunday-meta-item">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    <span>Via Camillo Ugoni 20, 20158 Milano</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Past Sermons -->
    <section class="sunday-archive-section">
        <div class="container">
            <div class="section-header fade-in">
                <span class="section-label">Past Sermons</span>
                <h3 class="sunday-archive-title">往期主日回顾</h3>
                <div class="section-divider"></div>
            </div>

            <?php $videos = milan_revival_get_recent_videos(6); ?>
            <?php if (!empty($videos)) : ?>
                <div class="sunday-archive-grid">
                    <?php
                    // Skip the first video (it's the one already showing in the player)
                    $archive_videos = array_slice($videos, 1);
                    foreach ($archive_videos as $index => $video) :
                        $delay_class = $index > 0 ? ' fade-in-delay-' . min($index, 5) : '';
                    ?>
                        <a href="<?php echo esc_url($video['url']); ?>" class="sunday-archive-card fade-in<?php echo $delay_class; ?>" target="_blank" rel="noopener noreferrer">
                            <div class="sunday-archive-thumb">
                                <img src="<?php echo esc_url($video['thumbnail']); ?>" alt="<?php echo esc_attr($video['title']); ?>" loading="lazy">
                                <div class="sunday-archive-play">
                                    <svg width="36" height="36" viewBox="0 0 24 24" fill="currentColor"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                                </div>
                            </div>
                            <div class="sunday-archive-info">
                                <time class="sunday-archive-date"><?php echo esc_html($video['date']); ?></time>
                                <h4 class="sunday-archive-name"><?php echo esc_html($video['title']); ?></h4>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>

                <div class="sunday-archive-more fade-in">
                    <a href="https://www.youtube.com/@%E5%9F%BA%E7%9D%A3%E6%95%99%E7%B1%B3%E5%85%B0%E5%8D%8E%E4%BA%BA%E5%A4%8D%E5%85%B4%E6%95%99%E4%BC%9A" class="btn btn-outline" target="_blank" rel="noopener noreferrer">
                        在 YouTube 观看更多
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align: middle; margin-left: 6px;"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                    </a>
                </div>
            <?php else : ?>
                <div class="sunday-archive-empty fade-in">
                    <p>暂无往期视频，请稍后再来。</p>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php get_footer(); ?>

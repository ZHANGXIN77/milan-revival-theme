<?php
/**
 * Template Name: 首页
 * 首页模板 - 单页长滚动设计
 */
get_header();

// Get customizer images
$hero_bg = get_theme_mod('hero_background', '');
$pastor_photo = get_theme_mod('pastor_photo', '');
?>

<!-- ==================== HERO ==================== -->
<section class="hero" id="home">
    <div class="hero-image" <?php if ($hero_bg) : ?>style="background-image: url('<?php echo esc_url($hero_bg); ?>')"<?php endif; ?>></div>
    <div class="hero-bg"></div>
    <div class="hero-content">
        <p class="hero-subtitle fade-in">Milano Revival Church</p>
        <h1 class="fade-in fade-in-delay-1">建造活出爱的<br>健康基督教会</h1>
        <p class="fade-in fade-in-delay-2">无论你带着怎样的故事来到这里，<br>我们想对你说：欢迎回家！</p>
        <div class="hero-buttons fade-in fade-in-delay-3">
            <a href="#sunday" class="btn btn-primary" data-scroll>了解主日聚会</a>
            <a href="#contact" class="btn btn-outline" data-scroll>联系我们</a>
        </div>
    </div>
</section>

<!-- ==================== PASTOR WELCOME ==================== -->
<section class="section section-darker" id="pastor">
    <div class="container">
        <div class="section-header fade-in">
            <span class="section-label">牧师的话</span>
            <h2>欢迎回家</h2>
            <div class="section-divider"></div>
        </div>
        <div class="pastor-section">
            <div class="pastor-image fade-in">
                <?php if ($pastor_photo) : ?>
                    <img src="<?php echo esc_url($pastor_photo); ?>" alt="主任牧师">
                <?php else : ?>
                    <div class="placeholder-text">牧师照片<br><small>在「外观 → 自定义 → 牧师欢迎」中上传</small></div>
                <?php endif; ?>
            </div>
            <div class="pastor-content fade-in fade-in-delay-1">
                <p class="greeting">亲爱的朋友，平安！很高兴你来了。</p>
                <p>我知道，生活在米兰这座时尚与忙碌并存的城市，我们常会被两种感觉拉扯：一种是追逐梦想的兴奋，另一种是异乡漂泊的孤单。也许你在这里已经很久，习惯了独自应对风雨；也许你刚刚落地，对未来充满憧憬却又有些迷茫。</p>
                <p>米兰华人复兴教会不只是一个周末聚会的场所，更是一个可以卸下面具、真实呼吸的家。在这个充斥着快餐文化的时代，我们固执地扎根真理，因为我们深信，唯有神的话语能给人心灵最深层的安稳。</p>
                <p>在这里，没有完美的圣人，只有被爱接纳的家人。若你正在寻找一个可以安放灵魂、可以并肩作战的属灵港湾，请给我们、也给自己一个机会。</p>
                <p class="pastor-name">— 黄爱敏 主任牧师</p>
            </div>
        </div>
    </div>
</section>

<!-- ==================== SUNDAY EXPERIENCE ==================== -->
<section class="section section-dark" id="sunday">
    <div class="container">
        <div class="section-header fade-in">
            <span class="section-label">主日体验</span>
            <h2>与我们一起敬拜</h2>
            <div class="section-divider"></div>
        </div>
        <div class="sunday-live fade-in">
            <div class="sunday-live-info">
                <div class="highlight-day">每周主日</div>
                <div class="highlight-time">15:30</div>
                <p>每周主日下午，我们在此同步直播崇拜。无论您身在何处，都欢迎与我们一起敬拜。</p>
            </div>
            <div class="sunday-live-video">
                <div class="video-wrapper" id="youtubePlayer">
                    <iframe
                        id="ytFrame"
                        src="https://www.youtube.com/embed/live_stream?channel=UCSNVRXPho2A_xXp-hB3_iig"
                        title="主日崇拜直播"
                        frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen>
                    </iframe>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ==================== NEW VISITOR STEPS ==================== -->
<section class="section section-darker" id="visit">
    <div class="container">
        <div class="section-header fade-in">
            <span class="section-label">初次来访</span>
            <h2>新朋友三步曲</h2>
            <div class="section-divider"></div>
        </div>
        <div class="steps-grid">
            <div class="step-card fade-in">
                <div class="step-number">01</div>
                <h3>茶水间见</h3>
                <p>主日崇拜结束后，请移步到「茶水间」。这里有茶点，还有牧者同工期待认识您。</p>
            </div>
            <div class="step-card fade-in fade-in-delay-1">
                <div class="step-number">02</div>
                <h3>保持联系</h3>
                <p>扫描周报上的二维码，填写简单的联络卡，让我们能更好地服务您。</p>
            </div>
            <div class="step-card fade-in fade-in-delay-2">
                <div class="step-number">03</div>
                <h3>探索信仰</h3>
                <p>欢迎留下参加崇拜后的《启发》课程，轻松地边吃边聊吧！</p>
            </div>
        </div>

        <!-- CTA: go to visitor page -->
        <div class="visit-cta fade-in">
            <?php
            $visit_page = get_page_by_path('visit');
            $visit_url = $visit_page ? get_permalink($visit_page->ID) : home_url('/visit/');
            ?>
            <a href="<?php echo esc_url($visit_url); ?>" class="btn btn-primary">填写来访卡</a>
        </div>
    </div>
</section>

<!-- ==================== CONTACT ==================== -->
<section class="section section-dark" id="contact">
    <div class="container">
        <div class="section-header fade-in">
            <span class="section-label">与我们联系</span>
            <h2>联系我们</h2>
            <div class="section-divider"></div>
        </div>
        <div class="contact-grid">
            <div class="fade-in">
                <ul class="contact-info-list">
                    <li>
                        <span class="contact-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg></span>
                        <div class="contact-detail">
                            <h4>地址</h4>
                            <p>Via Camillo Ugoni 20, 20158 Milano</p>
                        </div>
                    </li>
                    <li>
                        <span class="contact-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></span>
                        <div class="contact-detail">
                            <h4>主日聚会</h4>
                            <p>每周日 15:30 - 17:00</p>
                        </div>
                    </li>
                    <li>
                        <span class="contact-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg></span>
                        <div class="contact-detail">
                            <h4>邮箱</h4>
                            <p><a href="mailto:italiachiesa@gmail.com">italiachiesa@gmail.com</a></p>
                        </div>
                    </li>
                    <li>
                        <span class="contact-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg></span>
                        <div class="contact-detail">
                            <h4>微信</h4>
                            <p>CCRCMIT</p>
                        </div>
                    </li>
                </ul>
                <div class="social-links">
                    <a href="https://www.instagram.com/revivalchurch_of_milan" class="social-link" target="_blank" rel="noopener noreferrer" aria-label="Instagram">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
                    </a>
                    <a href="https://www.youtube.com/results?search_query=%E5%9F%BA%E7%9D%A3%E6%95%99%E7%B1%B3%E5%85%B0%E5%8D%8E%E4%BA%BA%E5%A4%8D%E5%85%B4%E6%95%99%E4%BC%9A" class="social-link" target="_blank" rel="noopener noreferrer" aria-label="YouTube">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M22.54 6.42a2.78 2.78 0 0 0-1.94-2C18.88 4 12 4 12 4s-6.88 0-8.6.46a2.78 2.78 0 0 0-1.94 2A29 29 0 0 0 1 11.75a29 29 0 0 0 .46 5.33A2.78 2.78 0 0 0 3.4 19.1c1.72.46 8.6.46 8.6.46s6.88 0 8.6-.46a2.78 2.78 0 0 0 1.94-2 29 29 0 0 0 .46-5.25 29 29 0 0 0-.46-5.43z"/><polygon points="9.75 15.02 15.5 11.75 9.75 8.48 9.75 15.02"/></svg>
                    </a>
                    <a href="https://podcasts.apple.com/podcast/%E7%B1%B3%E5%85%B0%E5%A4%8D%E5%85%B4%E6%95%99%E4%BC%9A" class="social-link" target="_blank" rel="noopener noreferrer" aria-label="Podcast">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 1a3 3 0 0 0-3 3v8a3 3 0 0 0 6 0V4a3 3 0 0 0-3-3z"/><path d="M19 10v2a7 7 0 0 1-14 0v-2"/><line x1="12" y1="19" x2="12" y2="23"/><line x1="8" y1="23" x2="16" y2="23"/></svg>
                    </a>
                </div>
            </div>
            <div class="contact-right fade-in fade-in-delay-1">
                <div class="contact-map">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2796.0!2d9.17!3d45.50!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zVmlhIENhbWlsbG8gVWdvbmkgMjAsIDIwMTU4IE1pbGFubw!5e0!3m2!1sit!2sit!4v1700000000000!5m2!1sit!2sit"
                        allowfullscreen=""
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                        title="教会位置">
                    </iframe>
                </div>

                <!-- Transport Info -->
                <div class="contact-transport">
                    <h4 class="transport-title">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="14" rx="2"/><path d="M3 10h18"/><path d="M8 17v4"/><path d="M16 17v4"/></svg>
                        公共交通指南
                    </h4>
                    <div class="transport-list">
                        <div class="transport-item">
                            <span class="transport-badge transport-metro">M3</span>
                            <div class="transport-detail">
                                <strong>Dergano 站 / Maciachini 站</strong>
                                <p>地铁 M3 (黄线)，出站步行约 8-10 分钟</p>
                            </div>
                        </div>
                        <div class="transport-item">
                            <span class="transport-badge transport-tram">Tram</span>
                            <div class="transport-detail">
                                <strong>2 路电车</strong>
                                <p>Via Imbriani-Via Scalvini 站下车，步行约 4 分钟</p>
                            </div>
                        </div>
                        <div class="transport-item">
                            <span class="transport-badge transport-bus">Bus</span>
                            <div class="transport-detail">
                                <strong>90 / 91 / 92 路</strong>
                                <p>Via Imbriani-Via Scalvini 站下车，步行约 4 分钟</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>

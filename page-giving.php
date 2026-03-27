<?php
/**
 * Template Name: 线上奉献
 * 独立页面 - 奉献方式详情
 */
get_header();
?>

<main class="giving-page">
    <!-- Page Header -->
    <section class="giving-page-header">
        <div class="container">
            <div class="section-header fade-in">
                <span class="section-label">Online Giving</span>
                <h2>线上奉献</h2>
                <div class="section-divider"></div>
                <p class="giving-page-desc">感谢您愿意透过奉献支持教会的事工与使命。<br>您的每一份奉献都在参与建造神的国度，将祝福带给更多人。</p>
            </div>
        </div>
    </section>

    <!-- Giving Methods -->
    <section class="giving-methods-section">
        <div class="container">
            <div class="giving-methods">

                <!-- Bank Transfer -->
                <div class="giving-method-card fade-in">
                    <div class="giving-method-icon">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <rect x="1" y="4" width="22" height="16" rx="2" ry="2"/>
                            <line x1="1" y1="10" x2="23" y2="10"/>
                        </svg>
                    </div>
                    <h3>银行转账</h3>
                    <p class="giving-method-desc">您可以通过银行转账的方式进行奉献，以下是教会的银行账户信息：</p>
                    <div class="giving-info-table">
                        <div class="giving-info-row">
                            <span class="giving-info-label">收款人</span>
                            <span class="giving-info-value">Aps Cristiana Revival In Milano Italia</span>
                        </div>
                        <div class="giving-info-row">
                            <span class="giving-info-label">IBAN</span>
                            <span class="giving-info-value">IT72 C030 6909 6061 0000 0409 499</span>
                        </div>
                        <div class="giving-info-row">
                            <span class="giving-info-label">BIC</span>
                            <span class="giving-info-value">BCITITMM</span>
                        </div>
                        <div class="giving-info-row">
                            <span class="giving-info-label">附言 / Causale</span>
                            <span class="giving-info-value">Offerta</span>
                        </div>
                    </div>
                </div>

                <!-- WeChat Pay -->
                <div class="giving-method-card fade-in">
                    <div class="giving-method-icon">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                        </svg>
                    </div>
                    <h3>微信扫码付款</h3>
                    <p class="giving-method-desc">使用微信扫描下方二维码，即可进行奉献。</p>
                    <div class="giving-qr-placeholder">
                        <div class="qr-placeholder-box">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
                                <rect x="3" y="3" width="7" height="7"/>
                                <rect x="14" y="3" width="7" height="7"/>
                                <rect x="3" y="14" width="7" height="7"/>
                                <rect x="14" y="14" width="3" height="3"/>
                                <rect x="18" y="14" width="3" height="3"/>
                                <rect x="14" y="18" width="3" height="3"/>
                                <rect x="18" y="18" width="3" height="3"/>
                            </svg>
                            <span>微信收款码</span>
                            <small>请在「外观 → 自定义 → 线上奉献」中上传</small>
                        </div>
                    </div>
                </div>

                <!-- On-site -->
                <div class="giving-method-card fade-in">
                    <div class="giving-method-icon">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                            <polyline points="9 22 9 12 15 12 15 22"/>
                        </svg>
                    </div>
                    <h3>现场奉献</h3>
                    <p class="giving-method-desc">您也可以在每周主日崇拜时，通过现场奉献箱参与奉献。欢迎每周日下午 15:30 来到教会。</p>
                    <div class="giving-info-table">
                        <div class="giving-info-row">
                            <span class="giving-info-label">地址</span>
                            <span class="giving-info-value">Via Camillo Ugoni 20, 20158 Milano</span>
                        </div>
                        <div class="giving-info-row">
                            <span class="giving-info-label">时间</span>
                            <span class="giving-info-value">每周日 15:30 - 17:00</span>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Verse -->
            <div class="giving-verse-section fade-in">
                <blockquote>
                    <p>"各人要随本心所酌定的，不要作难，不要勉强，因为捐得乐意的人是神所喜爱的。"</p>
                    <cite>— 哥林多后书 9:7</cite>
                </blockquote>
            </div>

            <!-- Contact -->
            <div class="giving-contact fade-in">
                <p>如有任何奉献相关的疑问，请随时与我们联系：<a href="mailto:italiachiesa@gmail.com">italiachiesa@gmail.com</a></p>
            </div>
        </div>
    </section>
</main>

<?php get_footer(); ?>

<?php
/**
 * Template Name: 初次来访
 * 独立页面 - 新朋友三步曲 + 来访卡表单
 */
get_header();
?>

<main class="visit-page">
    <!-- Page Header -->
    <section class="visit-page-header">
        <div class="container">
            <div class="section-header fade-in">
                <span class="section-label">First Visit</span>
                <h2>初次来访</h2>
                <div class="section-divider"></div>
                <p class="visit-page-desc">无论你带着怎样的故事来到这里，我们都想对你说：欢迎回家！</p>
            </div>
        </div>
    </section>

    <!-- Steps -->
    <section class="visit-steps-section">
        <div class="container">
            <div class="steps-grid">
                <div class="step-card fade-in">
                    <div class="step-number">01</div>
                    <h3>茶水间见</h3>
                    <p>主日崇拜结束后，请移步到「茶水间」。这里有茶点，还有牧者同工期待认识您。</p>
                </div>
                <div class="step-card fade-in fade-in-delay-1">
                    <div class="step-number">02</div>
                    <h3>保持联系</h3>
                    <p>填写下方的来访卡，让我们能更好地服务您、与您保持联系。</p>
                </div>
                <div class="step-card fade-in fade-in-delay-2">
                    <div class="step-number">03</div>
                    <h3>探索信仰</h3>
                    <p>欢迎留下参加崇拜后的《启发》课程，轻松地边吃边聊吧！</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Visitor Form -->
    <section class="visit-form-section">
        <div class="container">
            <div class="visitor-form-wrapper fade-in">
                <div class="visitor-form-header">
                    <h3>新朋友来访卡</h3>
                    <p>欢迎填写以下信息，让我们更好地认识您、服务您。</p>
                </div>
                <form class="visitor-form" id="visitorForm">
                    <?php wp_nonce_field('milan_visitor_submit', 'milan_visitor_nonce'); ?>
                    <input type="hidden" name="action" value="visitor_form">

                    <div class="form-row">
                        <div class="form-group form-group-full">
                            <label for="visitor_name">姓名 <span class="required">*</span></label>
                            <input type="text" id="visitor_name" name="visitor_name" required placeholder="您的姓名">
                        </div>
                    </div>

                    <div class="form-row form-row-2col">
                        <div class="form-group">
                            <label for="visitor_phone">电话 / WhatsApp</label>
                            <input type="tel" id="visitor_phone" name="visitor_phone" placeholder="您的电话号码">
                        </div>
                        <div class="form-group">
                            <label for="visitor_wechat">微信号</label>
                            <input type="text" id="visitor_wechat" name="visitor_wechat" placeholder="您的微信号">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group form-group-full">
                            <label for="visitor_email">邮箱</label>
                            <input type="email" id="visitor_email" name="visitor_email" placeholder="您的邮箱地址">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group form-group-full">
                            <label for="visitor_source">您是如何得知我们的？</label>
                            <select id="visitor_source" name="visitor_source">
                                <option value="">请选择</option>
                                <option value="朋友介绍">朋友介绍</option>
                                <option value="网上搜索">网上搜索</option>
                                <option value="社交媒体">社交媒体 (Instagram / YouTube)</option>
                                <option value="路过教会">路过教会</option>
                                <option value="其他">其他</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group form-group-full">
                            <label>想了解的内容</label>
                            <div class="checkbox-group">
                                <label class="checkbox-label">
                                    <input type="checkbox" name="visitor_interests[]" value="主日崇拜">
                                    <span>主日崇拜</span>
                                </label>
                                <label class="checkbox-label">
                                    <input type="checkbox" name="visitor_interests[]" value="团契小组">
                                    <span>团契小组</span>
                                </label>
                                <label class="checkbox-label">
                                    <input type="checkbox" name="visitor_interests[]" value="启发课程">
                                    <span>启发课程</span>
                                </label>
                                <label class="checkbox-label">
                                    <input type="checkbox" name="visitor_interests[]" value="儿童主日学">
                                    <span>儿童主日学</span>
                                </label>
                                <label class="checkbox-label">
                                    <input type="checkbox" name="visitor_interests[]" value="诗班">
                                    <span>诗班</span>
                                </label>
                                <label class="checkbox-label">
                                    <input type="checkbox" name="visitor_interests[]" value="祷告会">
                                    <span>祷告会</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group form-group-full">
                            <label for="visitor_message">留言</label>
                            <textarea id="visitor_message" name="visitor_message" rows="3" placeholder="有什么想对我们说的？"></textarea>
                        </div>
                    </div>

                    <div class="form-row">
                        <button type="submit" class="btn btn-primary visitor-submit-btn" id="visitorSubmitBtn">提交来访卡</button>
                    </div>

                    <div class="form-message" id="formMessage"></div>
                </form>
            </div>
        </div>
    </section>
</main>

<?php get_footer(); ?>

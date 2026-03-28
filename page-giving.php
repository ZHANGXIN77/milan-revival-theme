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
                    <h3>微信扫码奉献</h3>
                    <p class="giving-method-desc">使用微信扫描下方二维码，即可进行奉献。感谢您的慷慨心意。</p>

                    <!-- 二维码容器 -->
                    <div class="giving-wechat-qr-wrap">
                        <div class="giving-wechat-qr-frame">
                            <div class="wechat-qr-label">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="#c8a96e"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                                微信收款码
                            </div>
                            <div id="wechatQrCode"></div>
                            <p class="wechat-qr-hint">微信扫一扫 即可奉献</p>
                        </div>

                        <!-- 一键保存按钮 -->
                        <button class="giving-save-btn" id="saveQrBtn" onclick="saveWechatQrWithVerse()">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                            保存收款码
                        </button>
                    </div>

                    <!-- 隐藏 Canvas，用于合成图片 -->
                    <canvas id="qrCanvas" style="display:none;"></canvas>
                </div>

<style>
.giving-wechat-qr-wrap {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 16px;
    margin-top: 16px;
}
.giving-wechat-qr-frame {
    background: #fff;
    border: 1px solid #e0e0e0;
    border-radius: 16px;
    padding: 20px 24px 16px;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 12px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.06);
}
[data-theme="dark"] .giving-wechat-qr-frame {
    background: #fff;
    border-color: #ddd;
}
.wechat-qr-label {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 0.82rem;
    font-weight: 600;
    color: #c8a96e;
    letter-spacing: 0.03em;
}
#wechatQrCode canvas,
#wechatQrCode img {
    display: block;
    border-radius: 6px;
}
.wechat-qr-hint {
    font-size: 0.75rem;
    color: #999;
    margin: 0;
    letter-spacing: 0.02em;
}
.giving-save-btn {
    display: flex;
    align-items: center;
    gap: 8px;
    background: #c8a96e;
    color: #fff;
    border: none;
    border-radius: 10px;
    padding: 11px 24px;
    font-size: 0.88rem;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.2s, transform 0.1s;
    letter-spacing: 0.02em;
}
.giving-save-btn:hover {
    background: #b8975c;
    transform: translateY(-1px);
}
.giving-save-btn:active {
    transform: translateY(0);
}
.giving-save-hint {
    font-size: 0.74rem;
    color: var(--text-secondary, #aaa);
    margin: 0;
    text-align: center;
    max-width: 240px;
    line-height: 1.5;
}
</style>

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js" integrity="sha512-CNgIRecGo7nphbeZ04Sc13ka07paqdeTu0WR1IM4kNcpmBAUSHSQX0FslNhTDadL4O5SAGapGt4FodqL8My0mA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script>
(function() {
    var wechatUrl = 'wxp://f2f0grUxoZBXovllSCTovLjaMO6Gu3kLNFvjNWrhP170rrw';

    function initQr() {
        var el = document.getElementById('wechatQrCode');
        if (!el || typeof QRCode === 'undefined') return;
        new QRCode(el, {
            text: wechatUrl,
            width: 180,
            height: 180,
            colorDark: '#000',
            colorLight: '#fff',
            correctLevel: QRCode.CorrectLevel.H
        });
    }

    if (typeof QRCode !== 'undefined') {
        initQr();
    } else {
        var s = document.querySelector('script[src*="qrcode"]');
        if (s) s.addEventListener('load', initQr);
    }
})();

function saveWechatQrWithVerse() {
    var qrEl = document.getElementById('wechatQrCode');
    var qrImg = qrEl ? (qrEl.querySelector('canvas') || qrEl.querySelector('img')) : null;
    if (!qrImg) { alert('二维码尚未生成，请稍候再试。'); return; }

    var btn = document.getElementById('saveQrBtn');
    btn.textContent = '生成中...';
    btn.disabled = true;

    var canvas = document.getElementById('qrCanvas');
    var W = 600, H = 860;
    canvas.width = W;
    canvas.height = H;
    var ctx = canvas.getContext('2d');

    // 深色背景渐变（匹配网站风格）
    var grad = ctx.createLinearGradient(0, 0, 0, H);
    grad.addColorStop(0, '#1a1a2e');
    grad.addColorStop(0.5, '#16213e');
    grad.addColorStop(1, '#0f3460');
    ctx.fillStyle = grad;
    ctx.fillRect(0, 0, W, H);

    // 顶部金色装饰条
    ctx.fillStyle = '#c8a96e';
    ctx.fillRect(0, 0, W, 5);

    // 顶部微光圆圈装饰
    ctx.beginPath();
    ctx.arc(-60, -60, 200, 0, Math.PI * 2);
    ctx.fillStyle = 'rgba(255,255,255,0.02)';
    ctx.fill();
    ctx.beginPath();
    ctx.arc(W + 60, H + 60, 220, 0, Math.PI * 2);
    ctx.fillStyle = 'rgba(255,255,255,0.02)';
    ctx.fill();

    // 教会名称
    ctx.textAlign = 'center';
    ctx.fillStyle = '#ffffff';
    ctx.font = 'bold 28px "PingFang SC", "Microsoft YaHei", sans-serif';
    ctx.fillText('米兰华人复兴教会', W / 2, 64);

    ctx.fillStyle = 'rgba(255,255,255,0.45)';
    ctx.font = '14px "PingFang SC", "Microsoft YaHei", sans-serif';
    ctx.letterSpacing = '0.1em';
    ctx.fillText('MILANO  REVIVAL  CHURCH', W / 2, 90);

    // 金色分隔线
    var lineGrad = ctx.createLinearGradient(60, 0, W - 60, 0);
    lineGrad.addColorStop(0, 'transparent');
    lineGrad.addColorStop(0.5, '#c8a96e');
    lineGrad.addColorStop(1, 'transparent');
    ctx.strokeStyle = lineGrad;
    ctx.lineWidth = 1;
    ctx.beginPath();
    ctx.moveTo(60, 112);
    ctx.lineTo(W - 60, 112);
    ctx.stroke();

    // 「微信奉献」标签
    ctx.fillStyle = 'rgba(200,169,110,0.15)';
    var labelW = 140, labelH = 30, labelX = (W - labelW) / 2, labelY = 128;
    ctx.beginPath();
    if (ctx.roundRect) { ctx.roundRect(labelX, labelY, labelW, labelH, 15); }
    else { ctx.rect(labelX, labelY, labelW, labelH); }
    ctx.fill();
    ctx.strokeStyle = 'rgba(200,169,110,0.4)';
    ctx.lineWidth = 1;
    ctx.stroke();
    ctx.fillStyle = '#c8a96e';
    ctx.font = '13px "PingFang SC", "Microsoft YaHei", sans-serif';
    ctx.fillText('微信奉献收款码', W / 2, 148);

    // 二维码白色底框
    var qrSize = 260;
    var qrX = (W - qrSize) / 2;
    var qrY = 174;
    ctx.fillStyle = '#ffffff';
    ctx.shadowColor = 'rgba(200,169,110,0.25)';
    ctx.shadowBlur = 30;
    ctx.beginPath();
    if (ctx.roundRect) {
        ctx.roundRect(qrX - 20, qrY - 20, qrSize + 40, qrSize + 40, 18);
    } else {
        ctx.rect(qrX - 20, qrY - 20, qrSize + 40, qrSize + 40);
    }
    ctx.fill();
    ctx.shadowBlur = 0;

    // 绘制二维码
    var drawQr = function(src) {
        var img = new Image();
        img.crossOrigin = 'anonymous';
        img.onload = function() {
            ctx.drawImage(img, qrX, qrY, qrSize, qrSize);

            // 扫码提示
            ctx.fillStyle = 'rgba(255,255,255,0.55)';
            ctx.font = '14px "PingFang SC", "Microsoft YaHei", sans-serif';
            ctx.fillText('微信扫一扫 · 即可奉献', W / 2, qrY + qrSize + 44);

            // 经文区金色分隔线
            var vLineGrad = ctx.createLinearGradient(60, 0, W - 60, 0);
            vLineGrad.addColorStop(0, 'transparent');
            vLineGrad.addColorStop(0.5, 'rgba(200,169,110,0.5)');
            vLineGrad.addColorStop(1, 'transparent');
            ctx.strokeStyle = vLineGrad;
            ctx.lineWidth = 1;
            ctx.beginPath();
            ctx.moveTo(60, qrY + qrSize + 66);
            ctx.lineTo(W - 60, qrY + qrSize + 66);
            ctx.stroke();

            // 经文正文（自动换行）
            var verse = '\u300C\u5404\u4EBA\u8981\u968F\u672C\u5FC3\u6240\u9178\u5B9A\u7684\uFF0C\u4E0D\u8981\u4F5C\u96BE\uFF0C\u4E0D\u8981\u52C9\u5F3A\uFF0C\u56E0\u4E3A\u635F\u5F97\u4E50\u610F\u7684\u4EBA\u662F\u795E\u6240\u559C\u7231\u7684\u3002\u300D';
            ctx.fillStyle = 'rgba(255,255,255,0.82)';
            ctx.font = '18px "PingFang SC", "Microsoft YaHei", sans-serif';
            ctx.textAlign = 'center';
            var lineY = qrY + qrSize + 102;
            var maxW = W - 100;
            var words = verse.split('');
            var line = '';
            for (var i = 0; i < words.length; i++) {
                var test = line + words[i];
                if (ctx.measureText(test).width > maxW && line !== '') {
                    ctx.fillText(line, W / 2, lineY);
                    line = words[i];
                    lineY += 32;
                } else {
                    line = test;
                }
            }
            if (line) { ctx.fillText(line, W / 2, lineY); lineY += 32; }

            // 经文出处
            ctx.fillStyle = '#c8a96e';
            ctx.font = '13px "PingFang SC", "Microsoft YaHei", sans-serif';
            ctx.fillText('— \u54E5\u6797\u591A\u540E\u4E66 9:7', W / 2, lineY + 10);

            // 底部金色装饰条
            ctx.fillStyle = '#c8a96e';
            ctx.fillRect(0, H - 5, W, 5);

            // 下载
            var link = document.createElement('a');
            link.download = '\u7C73\u5170\u590D\u5174\u6559\u4F1A_\u5FAE\u4FE1\u5949\u732E\u7801.png';
            link.href = canvas.toDataURL('image/png');
            link.click();

            btn.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg> \u4FDD\u5B58\u6536\u6B3E\u7801';
            btn.disabled = false;
        };
        img.onerror = function() {
            alert('\u56FE\u7247\u751F\u6210\u5931\u8D25\uFF0C\u8BF7\u7A0D\u540E\u518D\u8BD5\u3002');
            btn.innerHTML = '\u4FDD\u5B58\u6536\u6B3E\u7801';
            btn.disabled = false;
        };
        img.src = src;
    };

    if (qrImg.tagName === 'CANVAS') {
        drawQr(qrImg.toDataURL());
    } else {
        drawQr(qrImg.src);
    }
}
</script>

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

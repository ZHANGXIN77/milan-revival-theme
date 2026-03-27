<?php
/**
 * Milan Revival Church Theme Functions
 */

// ========== Theme Setup ==========
function milan_revival_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('custom-logo', array(
        'height'      => 80,
        'width'       => 200,
        'flex-height' => true,
        'flex-width'  => true,
    ));
    add_theme_support('html5', array(
        'search-form', 'comment-form', 'comment-list', 'gallery', 'caption',
    ));

    register_nav_menus(array(
        'primary' => '主导航菜单',
    ));
}
add_action('after_setup_theme', 'milan_revival_setup');

// ========== Enqueue Styles & Scripts ==========
function milan_revival_scripts() {
    // Google Fonts - Noto Sans SC
    wp_enqueue_style(
        'google-fonts',
        'https://fonts.googleapis.com/css2?family=Noto+Sans+SC:wght@300;400;500;600;700;800&display=swap',
        array(),
        null
    );

    // Main stylesheet
    wp_enqueue_style(
        'milan-revival-style',
        get_stylesheet_uri(),
        array('google-fonts'),
        wp_get_theme()->get('Version')
    );

    // Main JavaScript
    wp_enqueue_script(
        'milan-revival-main',
        get_template_directory_uri() . '/js/main.js',
        array(),
        wp_get_theme()->get('Version'),
        true
    );
}
add_action('wp_enqueue_scripts', 'milan_revival_scripts');

// ========== Custom Menu Walker for Simple Output ==========
class Milan_Revival_Walker_Nav extends Walker_Nav_Menu {
    function start_el(&$output, $item, $depth = 0, $args = null, $id = 0) {
        $output .= '<li>';
        $output .= '<a href="' . esc_url($item->url) . '"';
        if ($item->url && strpos($item->url, '#') !== false) {
            $output .= ' data-scroll';
        }
        $output .= '>' . esc_html($item->title) . '</a>';
    }
}

// ========== Customizer Settings ==========
function milan_revival_customizer($wp_customize) {
    // Hero Section
    $wp_customize->add_section('milan_hero', array(
        'title'    => '首页横幅',
        'priority' => 30,
    ));

    $wp_customize->add_setting('hero_background', array(
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ));
    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'hero_background', array(
        'label'   => '横幅背景图',
        'section' => 'milan_hero',
    )));

    // Pastor Section
    $wp_customize->add_section('milan_pastor', array(
        'title'    => '牧师欢迎',
        'priority' => 31,
    ));

    $wp_customize->add_setting('pastor_photo', array(
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ));
    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'pastor_photo', array(
        'label'   => '牧师照片',
        'section' => 'milan_pastor',
    )));
}
add_action('customize_register', 'milan_revival_customizer');

// ========== YouTube Latest Video (via RSS, cached 30 min) ==========
function milan_revival_get_latest_video() {
    $channel_id = 'UCSNVRXPho2A_xXp-hB3_iig';
    $cache_key = 'milan_revival_yt_latest';
    $cached = get_transient($cache_key);

    if ($cached !== false) {
        return $cached;
    }

    $feed_url = 'https://www.youtube.com/feeds/videos.xml?channel_id=' . $channel_id;
    $response = wp_remote_get($feed_url, array('timeout' => 10));

    if (is_wp_error($response)) {
        return false;
    }

    $body = wp_remote_retrieve_body($response);
    $xml = @simplexml_load_string($body);

    if ($xml === false || !isset($xml->entry[0])) {
        return false;
    }

    $entry = $xml->entry[0];
    $yt_ns = $entry->children('yt', true);
    $video_id = (string) $yt_ns->videoId;

    if (empty($video_id)) {
        return false;
    }

    set_transient($cache_key, $video_id, 30 * MINUTE_IN_SECONDS);
    return $video_id;
}

function milan_revival_yt_ajax() {
    $video_id = milan_revival_get_latest_video();
    if ($video_id) {
        wp_send_json_success(array('video_id' => $video_id));
    } else {
        wp_send_json_error();
    }
}
add_action('wp_ajax_get_latest_video', 'milan_revival_yt_ajax');
add_action('wp_ajax_nopriv_get_latest_video', 'milan_revival_yt_ajax');

function milan_revival_localize_script() {
    wp_localize_script('milan-revival-main', 'milanRevival', array(
        'ajaxUrl' => admin_url('admin-ajax.php'),
    ));
}
add_action('wp_enqueue_scripts', 'milan_revival_localize_script');

// ========== YouTube Recent Videos (via RSS, cached 30 min) ==========
function milan_revival_get_recent_videos($count = 6) {
    $channel_id = 'UCSNVRXPho2A_xXp-hB3_iig';
    $cache_key = 'milan_revival_yt_recent_' . $count;
    $cached = get_transient($cache_key);

    if ($cached !== false) {
        return $cached;
    }

    $feed_url = 'https://www.youtube.com/feeds/videos.xml?channel_id=' . $channel_id;
    $response = wp_remote_get($feed_url, array('timeout' => 10));

    if (is_wp_error($response)) {
        return array();
    }

    $body = wp_remote_retrieve_body($response);
    $xml = @simplexml_load_string($body);

    if ($xml === false || !isset($xml->entry[0])) {
        return array();
    }

    $videos = array();
    $i = 0;
    foreach ($xml->entry as $entry) {
        if ($i >= $count) break;

        $yt_ns = $entry->children('yt', true);
        $media_ns = $entry->children('media', true);
        $video_id = (string) $yt_ns->videoId;

        if (empty($video_id)) continue;

        $title = (string) $entry->title;
        $published = (string) $entry->published;

        $videos[] = array(
            'id'        => $video_id,
            'title'     => $title,
            'date'      => date('Y年n月j日', strtotime($published)),
            'thumbnail' => 'https://img.youtube.com/vi/' . $video_id . '/mqdefault.jpg',
            'url'       => 'https://www.youtube.com/watch?v=' . $video_id,
        );
        $i++;
    }

    set_transient($cache_key, $videos, 30 * MINUTE_IN_SECONDS);
    return $videos;
}

// ========== News Posts Settings ==========
function milan_revival_posts_per_page($query) {
    if (!is_admin() && $query->is_main_query()) {
        if (is_home() || is_archive()) {
            $query->set('posts_per_page', 6);
        }
    }
}
add_action('pre_get_posts', 'milan_revival_posts_per_page');

function milan_revival_excerpt_length($length) {
    return 40;
}
add_filter('excerpt_length', 'milan_revival_excerpt_length');

function milan_revival_excerpt_more($more) {
    return '...';
}
add_filter('excerpt_more', 'milan_revival_excerpt_more');

function milan_revival_image_sizes() {
    add_image_size('news-thumbnail', 1200, 630, true);
}
add_action('after_setup_theme', 'milan_revival_image_sizes');

// ========== Auto-create Visit Page ==========
function milan_revival_create_visit_page() {
    $page = get_page_by_path('visit');
    if (!$page) {
        $page_id = wp_insert_post(array(
            'post_title'   => '初次来访',
            'post_name'    => 'visit',
            'post_status'  => 'publish',
            'post_type'    => 'page',
            'post_content' => '',
        ));
        if ($page_id && !is_wp_error($page_id)) {
            update_post_meta($page_id, '_wp_page_template', 'page-visit.php');
        }
    }

    $news = get_page_by_path('news');
    if (!$news) {
        $news_id = wp_insert_post(array(
            'post_title'   => '最新消息',
            'post_name'    => 'news',
            'post_status'  => 'publish',
            'post_type'    => 'page',
            'post_content' => '',
        ));
        if ($news_id && !is_wp_error($news_id)) {
            update_post_meta($news_id, '_wp_page_template', 'page-news.php');
        }
    }

    $about = get_page_by_path('about');
    if (!$about) {
        $about_id = wp_insert_post(array(
            'post_title'   => '关于我们',
            'post_name'    => 'about',
            'post_status'  => 'publish',
            'post_type'    => 'page',
            'post_content' => '',
        ));
        if ($about_id && !is_wp_error($about_id)) {
            update_post_meta($about_id, '_wp_page_template', 'page-about.php');
        }
    }

    $sunday = get_page_by_path('sunday');
    if (!$sunday) {
        $sunday_id = wp_insert_post(array(
            'post_title'   => '主日崇拜',
            'post_name'    => 'sunday',
            'post_status'  => 'publish',
            'post_type'    => 'page',
            'post_content' => '',
        ));
        if ($sunday_id && !is_wp_error($sunday_id)) {
            update_post_meta($sunday_id, '_wp_page_template', 'page-sunday.php');
        }
    }

    $giving = get_page_by_path('giving');
    if (!$giving) {
        $giving_id = wp_insert_post(array(
            'post_title'   => '线上奉献',
            'post_name'    => 'giving',
            'post_status'  => 'publish',
            'post_type'    => 'page',
            'post_content' => '',
        ));
        if ($giving_id && !is_wp_error($giving_id)) {
            update_post_meta($giving_id, '_wp_page_template', 'page-giving.php');
        }
    }
}
add_action('after_switch_theme', 'milan_revival_create_visit_page');
add_action('init', 'milan_revival_create_visit_page');

// ========== Auto-create Event Pages ==========
function milan_revival_create_event_pages() {
    $easter = get_page_by_path('easter-2026');
    if (!$easter) {
        $id = wp_insert_post(array(
            'post_title'   => '北部复活节联合崇拜',
            'post_name'    => 'easter-2026',
            'post_status'  => 'publish',
            'post_type'    => 'page',
            'post_content' => '',
        ));
        if ($id && !is_wp_error($id)) {
            update_post_meta($id, '_wp_page_template', 'page-easter.php');
        }
    }

    $concert = get_page_by_path('concert-2026');
    if (!$concert) {
        $id = wp_insert_post(array(
            'post_title'   => '没有不可能！黄国伦 x 寇乃馨 音乐布道会',
            'post_name'    => 'concert-2026',
            'post_status'  => 'publish',
            'post_type'    => 'page',
            'post_content' => '',
        ));
        if ($id && !is_wp_error($id)) {
            update_post_meta($id, '_wp_page_template', 'page-concert.php');
        }
    }
}
add_action('init', 'milan_revival_create_event_pages');

// ========== Visitor Form Handler ==========
function milan_revival_visitor_form() {
    if (!isset($_POST['milan_visitor_nonce']) || !wp_verify_nonce($_POST['milan_visitor_nonce'], 'milan_visitor_submit')) {
        wp_send_json_error('安全验证失败，请刷新页面后重试。');
    }

    $name    = sanitize_text_field($_POST['visitor_name'] ?? '');
    $phone   = sanitize_text_field($_POST['visitor_phone'] ?? '');
    $wechat  = sanitize_text_field($_POST['visitor_wechat'] ?? '');
    $email   = sanitize_email($_POST['visitor_email'] ?? '');
    $source  = sanitize_text_field($_POST['visitor_source'] ?? '');
    $message = sanitize_textarea_field($_POST['visitor_message'] ?? '');

    $interests = array();
    if (!empty($_POST['visitor_interests']) && is_array($_POST['visitor_interests'])) {
        foreach ($_POST['visitor_interests'] as $item) {
            $interests[] = sanitize_text_field($item);
        }
    }

    if (empty($name)) {
        wp_send_json_error('请填写您的姓名。');
    }

    $to = 'zanmeizhixin@gmail.com';
    $subject = '新朋友来访卡 - ' . $name;

    $body  = "姓名：{$name}\n";
    $body .= "电话/WhatsApp：" . ($phone ?: '未填写') . "\n";
    $body .= "微信号：" . ($wechat ?: '未填写') . "\n";
    $body .= "邮箱：" . ($email ?: '未填写') . "\n";
    $body .= "来访方式：" . ($source ?: '未填写') . "\n";
    $body .= "想了解的内容：" . (!empty($interests) ? implode('、', $interests) : '未选择') . "\n";
    $body .= "留言：" . ($message ?: '无') . "\n";
    $body .= "\n---\n";
    $body .= "提交时间：" . current_time('Y-m-d H:i:s') . "\n";
    $body .= "来自：米兰华人复兴教会官网";

    $headers = array('Content-Type: text/plain; charset=UTF-8');
    if (!empty($email)) {
        $headers[] = 'Reply-To: ' . $name . ' <' . $email . '>';
    }

    $sent = wp_mail($to, $subject, $body, $headers);

    if ($sent) {
        wp_send_json_success('感谢您的填写！我们会尽快与您联系。');
    } else {
        wp_send_json_error('发送失败，请稍后再试，或直接联系我们的邮箱。');
    }
}
add_action('wp_ajax_visitor_form', 'milan_revival_visitor_form');
add_action('wp_ajax_nopriv_visitor_form', 'milan_revival_visitor_form');

// ========== Remove Unnecessary WordPress Features ==========
function milan_revival_cleanup() {
    remove_action('wp_head', 'wp_generator');
    remove_action('wp_head', 'wlwmanifest_link');
    remove_action('wp_head', 'rsd_link');
}
add_action('init', 'milan_revival_cleanup');

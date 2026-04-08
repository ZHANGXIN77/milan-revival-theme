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

// ========== Pastoral System REST API ==========
// 数据 API（建表 + CRUD）在单独文件中管理
require_once get_template_directory() . '/pastoral-data-api.php';

define('PASTORAL_PASTOR_EMAIL', 'zanmeizhixin@gmail.com');

function pastoral_get_authorized_users() {
    $users = get_option('pastoral_authorized_users', array());
    // 确保牧区长始终在列表中
    $pastor_email = PASTORAL_PASTOR_EMAIL;
    if (!isset($users[$pastor_email])) {
        $users[$pastor_email] = array('role' => 'pastor', 'name' => '');
    }
    return $users;
}

function pastoral_api_get_users() {
    return rest_ensure_response(pastoral_get_authorized_users());
}

function pastoral_api_update_user($request) {
    $body = $request->get_json_params();
    $credential = $request->get_header('X-Google-Credential');

    // 验证请求者是牧区长
    if (!$credential || !pastoral_verify_pastor($credential)) {
        return new WP_Error('unauthorized', '无权限操作', array('status' => 403));
    }

    $email = sanitize_email($body['email'] ?? '');
    $action = sanitize_text_field($body['action'] ?? '');

    if (empty($email)) {
        return new WP_Error('bad_request', '缺少邮箱', array('status' => 400));
    }

    // 不允许修改牧区长自身
    if ($email === PASTORAL_PASTOR_EMAIL && $action === 'remove') {
        return new WP_Error('forbidden', '不能移除牧区长', array('status' => 403));
    }

    $users = pastoral_get_authorized_users();

    if ($action === 'remove') {
        unset($users[$email]);
    } else {
        $role = sanitize_text_field($body['role'] ?? 'youth');
        $name = sanitize_text_field($body['name'] ?? '');
        $group_id = isset($body['groupId']) ? intval($body['groupId']) : null;
        $users[$email] = array('role' => $role, 'name' => $name);
        if ($group_id) {
            $users[$email]['groupId'] = $group_id;
        }
    }

    update_option('pastoral_authorized_users', $users);
    return rest_ensure_response($users);
}

// 登录时注册用户（任何 Google 用户都可以请求，但未授权的只被记录为 pending）
function pastoral_api_register($request) {
    $body = $request->get_json_params();
    $email = sanitize_email($body['email'] ?? '');
    $name = sanitize_text_field($body['name'] ?? '');
    $avatar = esc_url_raw($body['avatar'] ?? '');

    if (empty($email)) {
        return new WP_Error('bad_request', '缺少邮箱', array('status' => 400));
    }

    $users = pastoral_get_authorized_users();

    // 已授权用户：返回其角色
    if (isset($users[$email])) {
        $user_data = $users[$email];
        // 更新名字和头像
        $user_data['name'] = $name ?: ($user_data['name'] ?? '');
        $user_data['avatar'] = $avatar;
        $users[$email] = $user_data;
        update_option('pastoral_authorized_users', $users);
        return rest_ensure_response(array('authorized' => true, 'user' => $user_data));
    }

    // 未授权：记录到待审核列表
    $pending = get_option('pastoral_pending_users', array());
    $pending[$email] = array('name' => $name, 'avatar' => $avatar, 'time' => current_time('mysql'));
    update_option('pastoral_pending_users', $pending);

    return rest_ensure_response(array('authorized' => false));
}

// 获取待审核用户列表（仅牧区长）
function pastoral_api_get_pending($request) {
    $credential = $request->get_header('X-Google-Credential');
    if (!$credential || !pastoral_verify_pastor($credential)) {
        return new WP_Error('unauthorized', '无权限', array('status' => 403));
    }
    $pending = get_option('pastoral_pending_users', array());
    return rest_ensure_response($pending);
}

// 审批待审核用户（仅牧区长）
function pastoral_api_approve($request) {
    $credential = $request->get_header('X-Google-Credential');
    if (!$credential || !pastoral_verify_pastor($credential)) {
        return new WP_Error('unauthorized', '无权限', array('status' => 403));
    }

    $body = $request->get_json_params();
    $email = sanitize_email($body['email'] ?? '');
    $role = sanitize_text_field($body['role'] ?? 'youth');
    $action = sanitize_text_field($body['action'] ?? 'approve');

    $pending = get_option('pastoral_pending_users', array());

    if ($action === 'approve' && isset($pending[$email])) {
        $users = pastoral_get_authorized_users();
        $users[$email] = array(
            'role' => $role,
            'name' => $pending[$email]['name'] ?? '',
        );
        if (isset($body['groupId'])) {
            $users[$email]['groupId'] = intval($body['groupId']);
        }
        update_option('pastoral_authorized_users', $users);
    }

    unset($pending[$email]);
    update_option('pastoral_pending_users', $pending);

    return rest_ensure_response(array('success' => true));
}

// 验证 Google JWT 中的邮箱是否为牧区长（使用安全验证，签名经 Google 官方接口核实）
function pastoral_verify_pastor($credential) {
    $email = pastoral_verify_credential($credential);
    return $email !== false && $email === PASTORAL_PASTOR_EMAIL;
}

// 注册 REST 路由
add_action('rest_api_init', function () {
    $ns = 'pastoral/v1';

    register_rest_route($ns, '/users', array(
        array('methods' => 'GET', 'callback' => 'pastoral_api_get_users', 'permission_callback' => '__return_true'),
        array('methods' => 'POST', 'callback' => 'pastoral_api_update_user', 'permission_callback' => '__return_true'),
    ));

    register_rest_route($ns, '/register', array(
        array('methods' => 'POST', 'callback' => 'pastoral_api_register', 'permission_callback' => '__return_true'),
    ));

    register_rest_route($ns, '/pending', array(
        array('methods' => 'GET', 'callback' => 'pastoral_api_get_pending', 'permission_callback' => '__return_true'),
    ));

    register_rest_route($ns, '/approve', array(
        array('methods' => 'POST', 'callback' => 'pastoral_api_approve', 'permission_callback' => '__return_true'),
    ));
});

// ========== Remove Unnecessary WordPress Features ==========
function milan_revival_cleanup() {
    remove_action('wp_head', 'wp_generator');
    remove_action('wp_head', 'wlwmanifest_link');
    remove_action('wp_head', 'rsd_link');
}
add_action('init', 'milan_revival_cleanup');

// ========== Hide /admin and /wp-admin from unauthorized access ==========
function milan_revival_hide_admin_access() {
    $request_uri = $_SERVER['REQUEST_URI'] ?? '';
    $path = parse_url($request_uri, PHP_URL_PATH);
    $path = rtrim($path, '/');

    // Block /admin redirect (WordPress auto-redirects /admin to /wp-admin)
    if ($path === '/admin') {
        wp_redirect(home_url('/'), 302);
        exit;
    }

    // Block /wp-admin access for non-logged-in users (allow admin-ajax.php and REST API)
    if (!is_user_logged_in()
        && strpos($path, '/wp-admin') !== false
        && strpos($path, 'admin-ajax.php') === false
        && strpos($path, 'admin-post.php') === false
    ) {
        wp_redirect(home_url('/'), 302);
        exit;
    }
}
add_action('init', 'milan_revival_hide_admin_access');

// Hide /wp-login.php for non-authorized access (redirect to home unless posting login)
function milan_revival_hide_wp_login() {
    $request_uri = $_SERVER['REQUEST_URI'] ?? '';
    if (strpos($request_uri, 'wp-login.php') !== false
        && $_SERVER['REQUEST_METHOD'] === 'GET'
        && !is_user_logged_in()
        && empty($_GET['action'])
    ) {
        wp_redirect(home_url('/'), 302);
        exit;
    }
}
add_action('init', 'milan_revival_hide_wp_login');

<?php
/**
 * Pastoral System — Data API
 * 数据库建表 + JWT 安全验证 + 成员/小组/出席/笔记/代祷/会议 CRUD API
 */

define('PASTORAL_GOOGLE_CLIENT_ID', '71104447040-0rmgpcc9014ml2qf8kfkd96gl5duqn2r.apps.googleusercontent.com');
define('PASTORAL_DB_VERSION', '1.1');

// ─────────────────────────────────────────────────────────────────────────────
// 建表
// ─────────────────────────────────────────────────────────────────────────────
function pastoral_create_tables() {
    global $wpdb;
    $c = $wpdb->get_charset_collate();
    $p = $wpdb->prefix;

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';

    dbDelta("CREATE TABLE IF NOT EXISTS {$p}pastoral_members (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        english_name VARCHAR(100) DEFAULT '',
        phone VARCHAR(30) DEFAULT '',
        email VARCHAR(100) DEFAULT '',
        join_date DATE DEFAULT NULL,
        birthday DATE DEFAULT NULL,
        group_id INT DEFAULT NULL,
        stage TINYINT DEFAULT 0,
        status VARCHAR(20) DEFAULT '活跃',
        baptized TINYINT(1) DEFAULT 0,
        baptize_date DATE DEFAULT NULL,
        mbti VARCHAR(10) DEFAULT NULL,
        gdpr_consent TINYINT(1) DEFAULT 0,
        is_group_leader TINYINT(1) DEFAULT 0,
        avatar TEXT,
        notes_text TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) $c;");

    dbDelta("CREATE TABLE IF NOT EXISTS {$p}pastoral_groups (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        leader_id INT DEFAULT NULL,
        description TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) $c;");

    dbDelta("CREATE TABLE IF NOT EXISTS {$p}pastoral_attendance (
        id INT AUTO_INCREMENT PRIMARY KEY,
        date DATE NOT NULL,
        UNIQUE KEY date_unique (date)
    ) $c;");

    dbDelta("CREATE TABLE IF NOT EXISTS {$p}pastoral_attendance_records (
        id INT AUTO_INCREMENT PRIMARY KEY,
        attendance_id INT NOT NULL,
        member_id INT NOT NULL,
        present TINYINT(1) DEFAULT 0,
        UNIQUE KEY att_member (attendance_id, member_id)
    ) $c;");

    dbDelta("CREATE TABLE IF NOT EXISTS {$p}pastoral_notes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        member_id INT NOT NULL,
        type VARCHAR(50),
        content TEXT,
        author_email VARCHAR(100),
        date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) $c;");

    dbDelta("CREATE TABLE IF NOT EXISTS {$p}pastoral_prayers (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(200),
        content TEXT,
        author_email VARCHAR(100),
        is_anonymous TINYINT(1) DEFAULT 0,
        status VARCHAR(20) DEFAULT 'active',
        date DATE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) $c;");

    dbDelta("CREATE TABLE IF NOT EXISTS {$p}pastoral_meetings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(200),
        date DATE,
        group_id INT DEFAULT NULL,
        notes TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) $c;");

    update_option('pastoral_db_version', PASTORAL_DB_VERSION);
}

add_action('after_switch_theme', 'pastoral_create_tables');
add_action('init', function () {
    if (get_option('pastoral_db_version') !== PASTORAL_DB_VERSION) {
        pastoral_create_tables();
    }
});

// ─────────────────────────────────────────────────────────────────────────────
// JWT 安全验证（使用 Google 官方 tokeninfo API + WordPress Transient 缓存）
// ─────────────────────────────────────────────────────────────────────────────

/**
 * 验证 Google JWT，返回 email 字符串，失败返回 false。
 * 首次验证需请求 Google 服务器（约 200ms），之后缓存至 token 过期。
 */
function pastoral_verify_credential($credential) {
    if (empty($credential)) return false;

    $cache_key = 'pjwt_' . substr(md5($credential), 0, 20);
    $cached    = get_transient($cache_key);
    if ($cached !== false) {
        return $cached === '__INVALID__' ? false : $cached;
    }

    $response = wp_remote_get(
        'https://oauth2.googleapis.com/tokeninfo?id_token=' . urlencode($credential),
        array('timeout' => 5, 'sslverify' => true)
    );

    if (is_wp_error($response) || wp_remote_retrieve_response_code($response) !== 200) {
        set_transient($cache_key, '__INVALID__', 60);
        return false;
    }

    $body = json_decode(wp_remote_retrieve_body($response), true);

    if (
        empty($body['email']) ||
        ($body['aud'] ?? '') !== PASTORAL_GOOGLE_CLIENT_ID ||
        intval($body['exp'] ?? 0) < time()
    ) {
        set_transient($cache_key, '__INVALID__', 60);
        return false;
    }

    $email = strtolower($body['email']);
    $ttl   = min(max(300, intval($body['exp']) - time()), 3600);
    set_transient($cache_key, $email, $ttl);

    return $email;
}

/**
 * 验证请求者身份并检查角色权限。
 * 返回 array('email', 'role', 'groupId', 'user') 或 WP_Error。
 */
function pastoral_auth($request, $allowed_roles = array()) {
    $credential = $request->get_header('X-Google-Credential');
    $email      = pastoral_verify_credential($credential);

    if (!$email) {
        return new WP_Error('unauthorized', '登录凭证无效或已过期，请重新登录', array('status' => 401));
    }

    $users = pastoral_get_authorized_users();
    if (!isset($users[$email])) {
        return new WP_Error('forbidden', '您的账号尚未获得授权', array('status' => 403));
    }

    $user_data = $users[$email];
    $role      = $user_data['role'] ?? 'youth';

    if (!empty($allowed_roles) && !in_array($role, $allowed_roles, true)) {
        return new WP_Error('forbidden', '权限不足', array('status' => 403));
    }

    return array(
        'email'   => $email,
        'role'    => $role,
        'groupId' => isset($user_data['groupId']) ? intval($user_data['groupId']) : null,
        'user'    => $user_data,
    );
}

// ─────────────────────────────────────────────────────────────────────────────
// 成员 API
// ─────────────────────────────────────────────────────────────────────────────

function pastoral_api_get_members($request) {
    $auth = pastoral_auth($request, array('pastor', 'leader'));
    if (is_wp_error($auth)) return $auth;

    global $wpdb;
    $p = $wpdb->prefix;

    if ($auth['role'] === 'leader' && $auth['groupId']) {
        $rows = $wpdb->get_results(
            $wpdb->prepare("SELECT * FROM {$p}pastoral_members WHERE group_id = %d ORDER BY name", $auth['groupId']),
            ARRAY_A
        );
    } else {
        $rows = $wpdb->get_results("SELECT * FROM {$p}pastoral_members ORDER BY name", ARRAY_A);
    }

    return rest_ensure_response(array_map('pastoral_fmt_member', $rows));
}

function pastoral_api_add_member($request) {
    $auth = pastoral_auth($request, array('pastor', 'leader'));
    if (is_wp_error($auth)) return $auth;

    global $wpdb;
    $d = $request->get_json_params();

    if (empty(trim($d['name'] ?? ''))) {
        return new WP_Error('bad_request', '姓名不能为空', array('status' => 400));
    }

    $wpdb->insert("{$wpdb->prefix}pastoral_members", array(
        'name'            => sanitize_text_field($d['name']),
        'english_name'    => sanitize_text_field($d['englishName'] ?? ''),
        'phone'           => sanitize_text_field($d['phone'] ?? ''),
        'email'           => sanitize_email($d['email'] ?? ''),
        'join_date'       => !empty($d['joinDate']) ? sanitize_text_field($d['joinDate']) : null,
        'birthday'        => !empty($d['birthday']) ? sanitize_text_field($d['birthday']) : null,
        'group_id'        => !empty($d['groupId']) ? intval($d['groupId']) : null,
        'stage'           => intval($d['stage'] ?? 0),
        'status'          => sanitize_text_field($d['status'] ?? '活跃'),
        'baptized'        => !empty($d['baptized']) ? 1 : 0,
        'baptize_date'    => !empty($d['baptizeDate']) ? sanitize_text_field($d['baptizeDate']) : null,
        'mbti'            => !empty($d['mbti']) ? sanitize_text_field($d['mbti']) : null,
        'gdpr_consent'    => !empty($d['gdprConsent']) ? 1 : 0,
        'is_group_leader' => !empty($d['isGroupLeader']) ? 1 : 0,
        'notes_text'      => sanitize_textarea_field($d['notesText'] ?? ''),
    ));

    $row = $wpdb->get_row(
        $wpdb->prepare("SELECT * FROM {$wpdb->prefix}pastoral_members WHERE id = %d", $wpdb->insert_id),
        ARRAY_A
    );
    return rest_ensure_response(pastoral_fmt_member($row));
}

function pastoral_api_update_member($request) {
    $auth = pastoral_auth($request, array('pastor', 'leader'));
    if (is_wp_error($auth)) return $auth;

    global $wpdb;
    $id = intval($request['id']);
    $d  = $request->get_json_params();

    $field_map = array(
        'name'          => array('name',            'sanitize_text_field'),
        'englishName'   => array('english_name',    'sanitize_text_field'),
        'phone'         => array('phone',            'sanitize_text_field'),
        'email'         => array('email',            'sanitize_email'),
        'joinDate'      => array('join_date',        'sanitize_text_field'),
        'birthday'      => array('birthday',         'sanitize_text_field'),
        'groupId'       => array('group_id',         'intval'),
        'stage'         => array('stage',            'intval'),
        'status'        => array('status',           'sanitize_text_field'),
        'baptized'      => array('baptized',         'intval'),
        'baptizeDate'   => array('baptize_date',     'sanitize_text_field'),
        'mbti'          => array('mbti',             'sanitize_text_field'),
        'gdprConsent'   => array('gdpr_consent',     'intval'),
        'isGroupLeader' => array('is_group_leader',  'intval'),
        'notesText'     => array('notes_text',       'sanitize_textarea_field'),
    );

    $update = array();
    foreach ($field_map as $js => $php) {
        if (array_key_exists($js, $d)) {
            $update[$php[0]] = is_null($d[$js]) ? null : call_user_func($php[1], $d[$js]);
        }
    }

    if (!empty($update)) {
        $wpdb->update("{$wpdb->prefix}pastoral_members", $update, array('id' => $id));
    }

    $row = $wpdb->get_row(
        $wpdb->prepare("SELECT * FROM {$wpdb->prefix}pastoral_members WHERE id = %d", $id),
        ARRAY_A
    );
    return rest_ensure_response(pastoral_fmt_member($row));
}

function pastoral_fmt_member($row) {
    if (!$row) return null;
    return array(
        'id'            => intval($row['id']),
        'name'          => $row['name'],
        'englishName'   => $row['english_name'],
        'phone'         => $row['phone'],
        'email'         => $row['email'],
        'joinDate'      => $row['join_date'],
        'birthday'      => $row['birthday'],
        'groupId'       => $row['group_id'] ? intval($row['group_id']) : null,
        'stage'         => intval($row['stage']),
        'status'        => $row['status'],
        'baptized'      => (bool) $row['baptized'],
        'baptizeDate'   => $row['baptize_date'],
        'mbti'          => $row['mbti'],
        'gdprConsent'   => (bool) $row['gdpr_consent'],
        'isGroupLeader' => (bool) $row['is_group_leader'],
        'avatar'        => $row['avatar'],
        'notesText'     => $row['notes_text'],
    );
}

// ─────────────────────────────────────────────────────────────────────────────
// 小组 API
// ─────────────────────────────────────────────────────────────────────────────

function pastoral_api_get_groups($request) {
    $auth = pastoral_auth($request, array('pastor', 'leader'));
    if (is_wp_error($auth)) return $auth;

    global $wpdb;
    $rows = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}pastoral_groups ORDER BY name", ARRAY_A);
    return rest_ensure_response(array_map('pastoral_fmt_group', $rows));
}

function pastoral_api_add_group($request) {
    $auth = pastoral_auth($request, array('pastor'));
    if (is_wp_error($auth)) return $auth;

    global $wpdb;
    $d = $request->get_json_params();
    $wpdb->insert("{$wpdb->prefix}pastoral_groups", array(
        'name'        => sanitize_text_field($d['name'] ?? ''),
        'leader_id'   => !empty($d['leaderId']) ? intval($d['leaderId']) : null,
        'description' => sanitize_textarea_field($d['description'] ?? ''),
    ));
    $row = $wpdb->get_row(
        $wpdb->prepare("SELECT * FROM {$wpdb->prefix}pastoral_groups WHERE id = %d", $wpdb->insert_id),
        ARRAY_A
    );
    return rest_ensure_response(pastoral_fmt_group($row));
}

function pastoral_api_update_group($request) {
    $auth = pastoral_auth($request, array('pastor'));
    if (is_wp_error($auth)) return $auth;

    global $wpdb;
    $id     = intval($request['id']);
    $d      = $request->get_json_params();
    $update = array();
    if (isset($d['name']))        $update['name']        = sanitize_text_field($d['name']);
    if (isset($d['description'])) $update['description'] = sanitize_textarea_field($d['description']);
    if (array_key_exists('leaderId', $d)) {
        $update['leader_id'] = $d['leaderId'] ? intval($d['leaderId']) : null;
    }
    if (!empty($update)) {
        $wpdb->update("{$wpdb->prefix}pastoral_groups", $update, array('id' => $id));
    }
    $row = $wpdb->get_row(
        $wpdb->prepare("SELECT * FROM {$wpdb->prefix}pastoral_groups WHERE id = %d", $id),
        ARRAY_A
    );
    return rest_ensure_response(pastoral_fmt_group($row));
}

function pastoral_fmt_group($row) {
    return array(
        'id'          => intval($row['id']),
        'name'        => $row['name'],
        'leaderId'    => $row['leader_id'] ? intval($row['leader_id']) : null,
        'description' => $row['description'],
    );
}

// ─────────────────────────────────────────────────────────────────────────────
// 出席 API
// ─────────────────────────────────────────────────────────────────────────────

function pastoral_api_get_attendance($request) {
    $auth = pastoral_auth($request, array('pastor', 'leader'));
    if (is_wp_error($auth)) return $auth;

    global $wpdb;
    $p        = $wpdb->prefix;
    $sessions = $wpdb->get_results(
        "SELECT * FROM {$p}pastoral_attendance ORDER BY date DESC LIMIT 20", ARRAY_A
    );

    $result = array();
    foreach ($sessions as $session) {
        $records = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT member_id, present FROM {$p}pastoral_attendance_records WHERE attendance_id = %d",
                $session['id']
            ),
            ARRAY_A
        );
        $result[] = array(
            'id'      => intval($session['id']),
            'date'    => $session['date'],
            'records' => array_map(function ($r) {
                return array('memberId' => intval($r['member_id']), 'present' => (bool) $r['present']);
            }, $records),
        );
    }
    return rest_ensure_response($result);
}

function pastoral_api_record_attendance($request) {
    $auth = pastoral_auth($request, array('pastor', 'leader'));
    if (is_wp_error($auth)) return $auth;

    global $wpdb;
    $p       = $wpdb->prefix;
    $d       = $request->get_json_params();
    $date    = sanitize_text_field($d['date'] ?? '');
    $records = $d['records'] ?? array();

    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
        return new WP_Error('bad_request', '日期格式无效', array('status' => 400));
    }

    $existing = $wpdb->get_row(
        $wpdb->prepare("SELECT id FROM {$p}pastoral_attendance WHERE date = %s", $date),
        ARRAY_A
    );

    if ($existing) {
        $session_id = $existing['id'];
        $wpdb->delete("{$p}pastoral_attendance_records", array('attendance_id' => $session_id));
    } else {
        $wpdb->insert("{$p}pastoral_attendance", array('date' => $date));
        $session_id = $wpdb->insert_id;
    }

    foreach ($records as $rec) {
        $wpdb->insert("{$p}pastoral_attendance_records", array(
            'attendance_id' => $session_id,
            'member_id'     => intval($rec['memberId']),
            'present'       => empty($rec['present']) ? 0 : 1,
        ));
    }

    return rest_ensure_response(array('success' => true, 'date' => $date));
}

// ─────────────────────────────────────────────────────────────────────────────
// 笔记 API
// ─────────────────────────────────────────────────────────────────────────────

function pastoral_api_get_notes($request) {
    $auth = pastoral_auth($request, array('pastor', 'leader'));
    if (is_wp_error($auth)) return $auth;

    global $wpdb;
    $p         = $wpdb->prefix;
    $member_id = intval($request->get_param('memberId') ?? 0);

    if ($member_id) {
        $rows = $wpdb->get_results(
            $wpdb->prepare("SELECT * FROM {$p}pastoral_notes WHERE member_id = %d ORDER BY date DESC", $member_id),
            ARRAY_A
        );
    } else {
        $rows = $wpdb->get_results(
            "SELECT * FROM {$p}pastoral_notes ORDER BY date DESC LIMIT 100", ARRAY_A
        );
    }
    return rest_ensure_response(array_map('pastoral_fmt_note', $rows));
}

function pastoral_api_add_note($request) {
    $auth = pastoral_auth($request, array('pastor', 'leader'));
    if (is_wp_error($auth)) return $auth;

    global $wpdb;
    $d = $request->get_json_params();
    $wpdb->insert("{$wpdb->prefix}pastoral_notes", array(
        'member_id'    => intval($d['memberId'] ?? 0),
        'type'         => sanitize_text_field($d['type'] ?? ''),
        'content'      => sanitize_textarea_field($d['content'] ?? ''),
        'author_email' => $auth['email'],
    ));
    $row = $wpdb->get_row(
        $wpdb->prepare("SELECT * FROM {$wpdb->prefix}pastoral_notes WHERE id = %d", $wpdb->insert_id),
        ARRAY_A
    );
    return rest_ensure_response(pastoral_fmt_note($row));
}

function pastoral_fmt_note($row) {
    return array(
        'id'       => intval($row['id']),
        'memberId' => intval($row['member_id']),
        'type'     => $row['type'],
        'content'  => $row['content'],
        'date'     => $row['date'],
    );
}

// ─────────────────────────────────────────────────────────────────────────────
// 代祷 API
// ─────────────────────────────────────────────────────────────────────────────

function pastoral_api_get_prayers($request) {
    $auth = pastoral_auth($request);
    if (is_wp_error($auth)) return $auth;

    global $wpdb;
    $rows = $wpdb->get_results(
        "SELECT * FROM {$wpdb->prefix}pastoral_prayers ORDER BY created_at DESC", ARRAY_A
    );
    $viewer = $auth['email'];
    return rest_ensure_response(array_map(function ($row) use ($viewer) {
        return pastoral_fmt_prayer($row, $viewer);
    }, $rows));
}

function pastoral_api_add_prayer($request) {
    $auth = pastoral_auth($request);
    if (is_wp_error($auth)) return $auth;

    global $wpdb;
    $d = $request->get_json_params();
    $wpdb->insert("{$wpdb->prefix}pastoral_prayers", array(
        'title'        => sanitize_text_field($d['title'] ?? ''),
        'content'      => sanitize_textarea_field($d['content'] ?? ''),
        'author_email' => $auth['email'],
        'is_anonymous' => !empty($d['isAnonymous']) ? 1 : 0,
        'status'       => 'active',
        'date'         => current_time('Y-m-d'),
    ));
    $row = $wpdb->get_row(
        $wpdb->prepare("SELECT * FROM {$wpdb->prefix}pastoral_prayers WHERE id = %d", $wpdb->insert_id),
        ARRAY_A
    );
    return rest_ensure_response(pastoral_fmt_prayer($row, $auth['email']));
}

function pastoral_api_update_prayer($request) {
    $auth = pastoral_auth($request);
    if (is_wp_error($auth)) return $auth;

    global $wpdb;
    $id  = intval($request['id']);
    $d   = $request->get_json_params();
    $row = $wpdb->get_row(
        $wpdb->prepare("SELECT * FROM {$wpdb->prefix}pastoral_prayers WHERE id = %d", $id),
        ARRAY_A
    );

    if (!$row) return new WP_Error('not_found', '未找到', array('status' => 404));
    if ($row['author_email'] !== $auth['email'] && $auth['role'] !== 'pastor') {
        return new WP_Error('forbidden', '无权限', array('status' => 403));
    }

    $update = array();
    if (isset($d['status']))  $update['status']  = sanitize_text_field($d['status']);
    if (isset($d['content'])) $update['content'] = sanitize_textarea_field($d['content']);
    if (!empty($update)) {
        $wpdb->update("{$wpdb->prefix}pastoral_prayers", $update, array('id' => $id));
    }

    $row = $wpdb->get_row(
        $wpdb->prepare("SELECT * FROM {$wpdb->prefix}pastoral_prayers WHERE id = %d", $id),
        ARRAY_A
    );
    return rest_ensure_response(pastoral_fmt_prayer($row, $auth['email']));
}

function pastoral_api_delete_prayer($request) {
    $auth = pastoral_auth($request);
    if (is_wp_error($auth)) return $auth;

    global $wpdb;
    $id  = intval($request['id']);
    $row = $wpdb->get_row(
        $wpdb->prepare("SELECT author_email FROM {$wpdb->prefix}pastoral_prayers WHERE id = %d", $id),
        ARRAY_A
    );
    if (!$row) return new WP_Error('not_found', '未找到', array('status' => 404));
    if ($row['author_email'] !== $auth['email'] && $auth['role'] !== 'pastor') {
        return new WP_Error('forbidden', '无权限', array('status' => 403));
    }

    $wpdb->delete("{$wpdb->prefix}pastoral_prayers", array('id' => $id));
    return rest_ensure_response(array('success' => true));
}

function pastoral_fmt_prayer($row, $viewer_email = '') {
    return array(
        'id'          => intval($row['id']),
        'title'       => $row['title'],
        'content'     => $row['content'],
        'authorId'    => 0,
        'isAnonymous' => (bool) $row['is_anonymous'],
        'isOwn'       => $row['author_email'] === $viewer_email,
        'status'      => $row['status'],
        'date'        => $row['date'],
    );
}

// ─────────────────────────────────────────────────────────────────────────────
// 会议 API
// ─────────────────────────────────────────────────────────────────────────────

function pastoral_api_get_meetings($request) {
    $auth = pastoral_auth($request);
    if (is_wp_error($auth)) return $auth;

    global $wpdb;
    $rows = $wpdb->get_results(
        "SELECT * FROM {$wpdb->prefix}pastoral_meetings ORDER BY date DESC", ARRAY_A
    );
    return rest_ensure_response(array_map('pastoral_fmt_meeting', $rows));
}

function pastoral_api_add_meeting($request) {
    $auth = pastoral_auth($request, array('pastor', 'leader'));
    if (is_wp_error($auth)) return $auth;

    global $wpdb;
    $d = $request->get_json_params();
    $wpdb->insert("{$wpdb->prefix}pastoral_meetings", array(
        'title'    => sanitize_text_field($d['title'] ?? ''),
        'date'     => sanitize_text_field($d['date'] ?? ''),
        'group_id' => !empty($d['groupId']) ? intval($d['groupId']) : null,
        'notes'    => sanitize_textarea_field($d['notes'] ?? ''),
    ));
    $row = $wpdb->get_row(
        $wpdb->prepare("SELECT * FROM {$wpdb->prefix}pastoral_meetings WHERE id = %d", $wpdb->insert_id),
        ARRAY_A
    );
    return rest_ensure_response(pastoral_fmt_meeting($row));
}

function pastoral_api_update_meeting($request) {
    $auth = pastoral_auth($request, array('pastor', 'leader'));
    if (is_wp_error($auth)) return $auth;

    global $wpdb;
    $id     = intval($request['id']);
    $d      = $request->get_json_params();
    $update = array();
    if (isset($d['title'])) $update['title'] = sanitize_text_field($d['title']);
    if (isset($d['date']))  $update['date']  = sanitize_text_field($d['date']);
    if (isset($d['notes'])) $update['notes'] = sanitize_textarea_field($d['notes']);
    if (array_key_exists('groupId', $d)) {
        $update['group_id'] = $d['groupId'] ? intval($d['groupId']) : null;
    }
    if (!empty($update)) {
        $wpdb->update("{$wpdb->prefix}pastoral_meetings", $update, array('id' => $id));
    }
    $row = $wpdb->get_row(
        $wpdb->prepare("SELECT * FROM {$wpdb->prefix}pastoral_meetings WHERE id = %d", $id),
        ARRAY_A
    );
    return rest_ensure_response(pastoral_fmt_meeting($row));
}

function pastoral_api_delete_meeting($request) {
    $auth = pastoral_auth($request, array('pastor', 'leader'));
    if (is_wp_error($auth)) return $auth;

    global $wpdb;
    $wpdb->delete("{$wpdb->prefix}pastoral_meetings", array('id' => intval($request['id'])));
    return rest_ensure_response(array('success' => true));
}

function pastoral_fmt_meeting($row) {
    return array(
        'id'      => intval($row['id']),
        'title'   => $row['title'],
        'date'    => $row['date'],
        'groupId' => $row['group_id'] ? intval($row['group_id']) : null,
        'notes'   => $row['notes'],
    );
}

// ─────────────────────────────────────────────────────────────────────────────
// 注册 REST 路由
// ─────────────────────────────────────────────────────────────────────────────
add_action('rest_api_init', function () {
    $ns  = 'pastoral/v1';
    $pub = '__return_true'; // 权限由回调函数内部的 pastoral_auth() 控制

    // 成员
    register_rest_route($ns, '/members', array(
        array('methods' => 'GET',  'callback' => 'pastoral_api_get_members', 'permission_callback' => $pub),
        array('methods' => 'POST', 'callback' => 'pastoral_api_add_member',  'permission_callback' => $pub),
    ));
    register_rest_route($ns, '/members/(?P<id>\d+)', array(
        array('methods' => 'PUT', 'callback' => 'pastoral_api_update_member', 'permission_callback' => $pub),
    ));

    // 小组
    register_rest_route($ns, '/groups', array(
        array('methods' => 'GET',  'callback' => 'pastoral_api_get_groups', 'permission_callback' => $pub),
        array('methods' => 'POST', 'callback' => 'pastoral_api_add_group',  'permission_callback' => $pub),
    ));
    register_rest_route($ns, '/groups/(?P<id>\d+)', array(
        array('methods' => 'PUT', 'callback' => 'pastoral_api_update_group', 'permission_callback' => $pub),
    ));

    // 出席
    register_rest_route($ns, '/attendance', array(
        array('methods' => 'GET',  'callback' => 'pastoral_api_get_attendance',    'permission_callback' => $pub),
        array('methods' => 'POST', 'callback' => 'pastoral_api_record_attendance', 'permission_callback' => $pub),
    ));

    // 笔记
    register_rest_route($ns, '/notes', array(
        array('methods' => 'GET',  'callback' => 'pastoral_api_get_notes', 'permission_callback' => $pub),
        array('methods' => 'POST', 'callback' => 'pastoral_api_add_note',  'permission_callback' => $pub),
    ));

    // 代祷
    register_rest_route($ns, '/prayers', array(
        array('methods' => 'GET',  'callback' => 'pastoral_api_get_prayers', 'permission_callback' => $pub),
        array('methods' => 'POST', 'callback' => 'pastoral_api_add_prayer',  'permission_callback' => $pub),
    ));
    register_rest_route($ns, '/prayers/(?P<id>\d+)', array(
        array('methods' => 'PUT',    'callback' => 'pastoral_api_update_prayer', 'permission_callback' => $pub),
        array('methods' => 'DELETE', 'callback' => 'pastoral_api_delete_prayer', 'permission_callback' => $pub),
    ));

    // 会议
    register_rest_route($ns, '/meetings', array(
        array('methods' => 'GET',  'callback' => 'pastoral_api_get_meetings', 'permission_callback' => $pub),
        array('methods' => 'POST', 'callback' => 'pastoral_api_add_meeting',  'permission_callback' => $pub),
    ));
    register_rest_route($ns, '/meetings/(?P<id>\d+)', array(
        array('methods' => 'PUT',    'callback' => 'pastoral_api_update_meeting', 'permission_callback' => $pub),
        array('methods' => 'DELETE', 'callback' => 'pastoral_api_delete_meeting', 'permission_callback' => $pub),
    ));
});

<?php
/**
 * Pastoral System — 数据迁移工具
 *
 * 用途：将从浏览器导出的旧 localStorage JSON 数据批量导入 MySQL 数据库。
 *
 * 使用方法：
 *   1. 在浏览器控制台执行以下命令，复制输出结果：
 *      copy(JSON.stringify(JSON.parse(localStorage.getItem('pastoral_data_v1'))))
 *
 *   2. 将复制的 JSON 粘贴到下方 $json_data 变量中（替换空字符串）。
 *
 *   3. 通过浏览器或 WP-CLI 运行本文件（需登录 WordPress 管理员）：
 *      访问：https://你的域名/wp-content/themes/milan-revival-theme/pastoral-data-migrate.php?key=migrate2024
 *
 *   4. 迁移完成后立即删除本文件。
 */

// 安全密钥（防止意外访问）
define('MIGRATE_KEY', 'migrate2024');

if (!isset($_GET['key']) || $_GET['key'] !== MIGRATE_KEY) {
    die('Access denied. Add ?key=' . MIGRATE_KEY . ' to URL.');
}

// 必须先加载 WordPress
$wp_load_path = dirname(__FILE__) . '/../../../../../../../wp-load.php';
if (!file_exists($wp_load_path)) {
    // 尝试其他路径
    $wp_load_path = dirname(__FILE__) . '/../../../../wp-load.php';
}
if (!file_exists($wp_load_path)) {
    die('Cannot find wp-load.php. Please adjust the path in this script.');
}
require_once $wp_load_path;

if (!current_user_can('manage_options')) {
    die('请先登录 WordPress 管理员账号再运行迁移。');
}

// ──────────────────────────────────────────────────────────────────────────────
// 将从 localStorage 复制的 JSON 粘贴在这里
// ──────────────────────────────────────────────────────────────────────────────
$json_data = '';
// 示例格式：
// $json_data = '{"members":[...],"groups":[...],"attendance":[...],"notes":[...],"prayers":[...],"meetings":[...]}';
// ──────────────────────────────────────────────────────────────────────────────

if (empty($json_data)) {
    die('<pre>请先在 $json_data 变量中粘贴从浏览器导出的 JSON 数据。</pre>');
}

$data = json_decode($json_data, true);
if (!$data) {
    die('<pre>JSON 解析失败：' . json_last_error_msg() . '</pre>');
}

global $wpdb;
$p       = $wpdb->prefix;
$results = array();
$errors  = array();

// ── 确保表已建立 ────────────────────────────────────────────────────────────
if (function_exists('pastoral_create_tables')) {
    pastoral_create_tables();
}

// ── 迁移小组 ────────────────────────────────────────────────────────────────
$group_id_map = array(); // 旧 ID → 新 ID
if (!empty($data['groups'])) {
    foreach ($data['groups'] as $g) {
        $existing = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM {$p}pastoral_groups WHERE name = %s LIMIT 1", $g['name']
        ));
        if ($existing) {
            $group_id_map[$g['id']] = intval($existing);
            continue;
        }
        $wpdb->insert("{$p}pastoral_groups", array(
            'name'        => sanitize_text_field($g['name']),
            'description' => sanitize_textarea_field($g['description'] ?? ''),
        ));
        $new_id = $wpdb->insert_id;
        $group_id_map[$g['id']] = $new_id;
    }
    $results[] = '✅ 小组：已处理 ' . count($data['groups']) . ' 条';
}

// ── 迁移成员 ────────────────────────────────────────────────────────────────
$member_id_map = array(); // 旧 ID → 新 ID
if (!empty($data['members'])) {
    $count = 0;
    foreach ($data['members'] as $m) {
        // 按姓名查重
        $existing = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM {$p}pastoral_members WHERE name = %s LIMIT 1", $m['name']
        ));
        if ($existing) {
            $member_id_map[$m['id']] = intval($existing);
            continue;
        }
        $new_group_id = isset($m['groupId']) && isset($group_id_map[$m['groupId']])
            ? $group_id_map[$m['groupId']] : null;

        $wpdb->insert("{$p}pastoral_members", array(
            'name'            => sanitize_text_field($m['name']),
            'english_name'    => sanitize_text_field($m['englishName'] ?? ''),
            'phone'           => sanitize_text_field($m['phone'] ?? ''),
            'email'           => sanitize_email($m['email'] ?? ''),
            'join_date'       => !empty($m['joinDate']) ? $m['joinDate'] : null,
            'birthday'        => !empty($m['birthday']) ? $m['birthday'] : null,
            'group_id'        => $new_group_id,
            'stage'           => intval($m['stage'] ?? 0),
            'status'          => sanitize_text_field($m['status'] ?? '活跃'),
            'baptized'        => !empty($m['baptized']) ? 1 : 0,
            'baptize_date'    => !empty($m['baptizeDate']) ? $m['baptizeDate'] : null,
            'mbti'            => !empty($m['mbti']) ? $m['mbti'] : null,
            'gdpr_consent'    => !empty($m['gdprConsent']) ? 1 : 0,
            'is_group_leader' => !empty($m['isGroupLeader']) ? 1 : 0,
            'notes_text'      => sanitize_textarea_field($m['notesText'] ?? ''),
        ));
        $member_id_map[$m['id']] = $wpdb->insert_id;
        $count++;
    }
    $results[] = '✅ 成员：已导入 ' . $count . ' 条（重复跳过 ' . (count($data['members']) - $count) . ' 条）';
}

// ── 迁移笔记 ────────────────────────────────────────────────────────────────
if (!empty($data['notes'])) {
    $count = 0;
    foreach ($data['notes'] as $n) {
        $new_member_id = $member_id_map[$n['memberId']] ?? null;
        if (!$new_member_id) continue;
        $wpdb->insert("{$p}pastoral_notes", array(
            'member_id'    => $new_member_id,
            'type'         => sanitize_text_field($n['type'] ?? ''),
            'content'      => sanitize_textarea_field($n['content'] ?? ''),
            'author_email' => '',
            'date'         => $n['date'] ?? current_time('mysql'),
        ));
        $count++;
    }
    $results[] = '✅ 笔记：已导入 ' . $count . ' 条';
}

// ── 迁移出席记录 ────────────────────────────────────────────────────────────
if (!empty($data['attendance'])) {
    $count = 0;
    foreach ($data['attendance'] as $a) {
        $date = $a['date'];
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) continue;

        $existing = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM {$p}pastoral_attendance WHERE date = %s", $date
        ));
        if ($existing) continue; // 已有该日期记录，跳过

        $wpdb->insert("{$p}pastoral_attendance", array('date' => $date));
        $session_id = $wpdb->insert_id;

        foreach ($a['records'] as $rec) {
            $new_member_id = $member_id_map[$rec['memberId']] ?? null;
            if (!$new_member_id) continue;
            $wpdb->insert("{$p}pastoral_attendance_records", array(
                'attendance_id' => $session_id,
                'member_id'     => $new_member_id,
                'present'       => !empty($rec['present']) ? 1 : 0,
            ));
        }
        $count++;
    }
    $results[] = '✅ 出席记录：已导入 ' . $count . ' 次主日';
}

// ── 迁移代祷事项 ────────────────────────────────────────────────────────────
if (!empty($data['prayers'])) {
    $count = 0;
    foreach ($data['prayers'] as $pr) {
        $wpdb->insert("{$p}pastoral_prayers", array(
            'title'        => sanitize_text_field($pr['title'] ?? ''),
            'content'      => sanitize_textarea_field($pr['content'] ?? ''),
            'author_email' => '',
            'is_anonymous' => !empty($pr['isAnonymous']) ? 1 : 0,
            'status'       => sanitize_text_field($pr['status'] ?? 'active'),
            'date'         => !empty($pr['date']) ? $pr['date'] : current_time('Y-m-d'),
        ));
        $count++;
    }
    $results[] = '✅ 代祷事项：已导入 ' . $count . ' 条';
}

// ── 迁移会议 ────────────────────────────────────────────────────────────────
if (!empty($data['meetings'])) {
    $count = 0;
    foreach ($data['meetings'] as $mt) {
        $new_group_id = isset($mt['groupId']) && isset($group_id_map[$mt['groupId']])
            ? $group_id_map[$mt['groupId']] : null;
        $wpdb->insert("{$p}pastoral_meetings", array(
            'title'    => sanitize_text_field($mt['title'] ?? ''),
            'date'     => $mt['date'] ?? '',
            'group_id' => $new_group_id,
            'notes'    => sanitize_textarea_field($mt['notes'] ?? ''),
        ));
        $count++;
    }
    $results[] = '✅ 会议安排：已导入 ' . $count . ' 条';
}

// ── 输出结果 ────────────────────────────────────────────────────────────────
echo '<html><head><meta charset="utf-8"><title>数据迁移</title></head><body>';
echo '<h2>数据迁移完成</h2><ul>';
foreach ($results as $r) echo "<li>$r</li>";
if ($errors) {
    echo '<li style="color:red">⚠️ 错误：<br>' . implode('<br>', $errors) . '</li>';
}
echo '</ul>';
echo '<p style="color:red;font-weight:bold">⚠️ 请立即删除本文件（pastoral-data-migrate.php）防止重复执行！</p>';
echo '</body></html>';

<?php
/**
 * Notice Model - Admin unique feature
 */
require_once __DIR__ . '/../config/database.php';

function notice_create($data) {
    $title = db_escape($data['title']);
    $content = db_escape($data['content']);
    $target = db_escape($data['target_role'] ?? 'all');
    $priority = db_escape($data['priority'] ?? 'medium');
    $active = isset($data['is_active']) ? (int)$data['is_active'] : 1;
    $created_by = (int)$data['created_by'];
    $expires = !empty($data['expires_at']) ? "'" . db_escape($data['expires_at']) . "'" : 'NULL';
    
    $sql = "INSERT INTO notices (title, content, target_role, priority, is_active, created_by, expires_at)
            VALUES ('$title', '$content', '$target', '$priority', $active, $created_by, $expires)";
    return db_query($sql) ? db_insert_id() : false;
}

function notice_update($id, $data) {
    $id = (int)$id;
    $sets = [];
    foreach (['title','content','target_role','priority'] as $f) {
        if (isset($data[$f])) $sets[] = "$f = '" . db_escape($data[$f]) . "'";
    }
    if (isset($data['is_active'])) $sets[] = "is_active = " . (int)$data['is_active'];
    if (isset($data['expires_at'])) {
        $sets[] = empty($data['expires_at']) ? "expires_at = NULL" : "expires_at = '" . db_escape($data['expires_at']) . "'";
    }
    if (empty($sets)) return false;
    return db_query("UPDATE notices SET " . implode(', ', $sets) . " WHERE id = $id");
}

function notice_delete($id) {
    $id = (int)$id;
    return db_query("DELETE FROM notices WHERE id = $id");
}

function notice_find($id) {
    $id = (int)$id;
    $result = db_query("SELECT n.*, u.full_name AS author FROM notices n LEFT JOIN users u ON n.created_by = u.id WHERE n.id = $id");
    return $result ? mysqli_fetch_assoc($result) : null;
}

function notice_list($role = 'all', $active_only = true) {
    $where = ["1=1"];
    if ($active_only) {
        $where[] = "n.is_active = 1";
        $where[] = "(n.expires_at IS NULL OR n.expires_at >= CURDATE())";
    }
    if ($role !== 'all') {
        $role = db_escape($role);
        $where[] = "(n.target_role = 'all' OR n.target_role = '$role')";
    }
    $where_sql = implode(' AND ', $where);
    
    $sql = "SELECT n.*, u.full_name AS author
            FROM notices n
            LEFT JOIN users u ON n.created_by = u.id
            WHERE $where_sql
            ORDER BY FIELD(n.priority,'high','medium','low'), n.created_at DESC";
    $result = db_query($sql);
    $rows = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
    }
    return $rows;
}

function notice_search($keyword = '') {
    $where = ["1=1"];
    if ($keyword !== '') {
        $kw = db_escape($keyword);
        $where[] = "(n.title LIKE '%$kw%' OR n.content LIKE '%$kw%')";
    }
    $where_sql = implode(' AND ', $where);
    $sql = "SELECT n.*, u.full_name AS author FROM notices n LEFT JOIN users u ON n.created_by = u.id WHERE $where_sql ORDER BY n.created_at DESC";
    $result = db_query($sql);
    $rows = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
    }
    return $rows;
}

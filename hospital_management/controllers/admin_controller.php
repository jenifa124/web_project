<?php
/**
 * Admin Controller
 * Unique features: 1. User Management  2. Revenue Reports  3. Notices + Activity Logs
 */
require_once __DIR__ . '/../helpers/helpers.php';
require_once __DIR__ . '/../models/user_model.php';
require_once __DIR__ . '/../models/admin_model.php';
require_once __DIR__ . '/../models/notice_model.php';
require_once __DIR__ . '/../models/log_model.php';

function admin_dashboard() {
    require_role('admin');
    $stats = admin_dashboard_stats();
    $notices = notice_list('admin');
    require __DIR__ . '/../views/admin/dashboard.php';
}

function admin_users() {
    require_role('admin');
    
    $action = $_GET['action'] ?? 'list';
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    
    // Handle POST actions
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!verify_csrf()) {
            set_flash('danger', 'Invalid CSRF token.');
            redirect('index.php?page=admin_users');
        }
        
        $post_action = $_POST['form_action'] ?? '';
        
        if ($post_action === 'create' || $post_action === 'update') {
            $data = [
                'username' => clean($_POST['username'] ?? ''),
                'email' => clean($_POST['email'] ?? ''),
                'full_name' => clean($_POST['full_name'] ?? ''),
                'role' => clean($_POST['role'] ?? 'patient'),
                'phone' => clean($_POST['phone'] ?? ''),
                'specialization' => clean($_POST['specialization'] ?? ''),
                'status' => clean($_POST['status'] ?? 'active'),
            ];
            
            $errors = [];
            if (strlen($data['username']) < 3) $errors[] = 'Username too short.';
            if (!is_valid_email($data['email'])) $errors[] = 'Invalid email.';
            if (empty($data['full_name'])) $errors[] = 'Name required.';
            if (!in_array($data['role'], ['admin','doctor','patient','receptionist'])) $errors[] = 'Invalid role.';
            
            if ($post_action === 'create') {
                if (strlen($_POST['password'] ?? '') < 6) $errors[] = 'Password min 6 chars.';
                if (user_find_by_username($data['username'])) $errors[] = 'Username exists.';
                if (user_find_by_email($data['email'])) $errors[] = 'Email exists.';
                
                if (empty($errors)) {
                    $data['password'] = hash_password($_POST['password']);
                    $uid = user_create($data);
                    if ($uid && $data['role'] === 'patient') {
                        require_once __DIR__ . '/../models/patient_model.php';
                        patient_create_profile($uid);
                    }
                    log_activity('user_create', "Created user: {$data['username']} ({$data['role']})");
                    set_flash('success', 'User created successfully.');
                } else {
                    set_flash('danger', implode(' ', $errors));
                }
            } else { // update
                $uid = (int)$_POST['user_id'];
                if (!empty($_POST['password'])) {
                    if (strlen($_POST['password']) < 6) $errors[] = 'Password min 6 chars.';
                    else $data['password'] = hash_password($_POST['password']);
                }
                if (empty($errors)) {
                    user_update($uid, $data);
                    log_activity('user_update', "Updated user ID: $uid");
                    set_flash('success', 'User updated successfully.');
                } else {
                    set_flash('danger', implode(' ', $errors));
                }
            }
            redirect('index.php?page=admin_users');
        }
        
        if ($post_action === 'delete') {
            $uid = (int)$_POST['user_id'];
            if ($uid == current_user()['id']) {
                set_flash('danger', 'You cannot delete yourself.');
            } else {
                user_delete($uid);
                log_activity('user_delete', "Deleted user ID: $uid");
                set_flash('success', 'User deleted.');
            }
            redirect('index.php?page=admin_users');
        }
    }
    
    $keyword = clean($_GET['q'] ?? '');
    $role_filter = clean($_GET['role'] ?? '');
    $users = user_search($keyword, $role_filter);
    $edit_user = $id ? user_find_by_id($id) : null;
    
    require __DIR__ . '/../views/admin/users.php';
}

function admin_revenue() {
    require_role('admin');
    $from = clean($_GET['from'] ?? date('Y-m-01'));
    $to   = clean($_GET['to'] ?? date('Y-m-d'));
    
    $report = admin_revenue_report($from, $to);
    $summary = admin_revenue_summary($from, $to);
    
    require __DIR__ . '/../views/admin/revenue.php';
}

function admin_notices() {
    require_role('admin');
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!verify_csrf()) {
            set_flash('danger', 'Invalid CSRF.');
            redirect('index.php?page=admin_notices');
        }
        $action = $_POST['form_action'] ?? '';
        
        if ($action === 'create') {
            $id = notice_create([
                'title' => clean($_POST['title'] ?? ''),
                'content' => clean($_POST['content'] ?? ''),
                'target_role' => clean($_POST['target_role'] ?? 'all'),
                'priority' => clean($_POST['priority'] ?? 'medium'),
                'created_by' => current_user()['id'],
                'expires_at' => clean($_POST['expires_at'] ?? ''),
                'is_active' => 1
            ]);
            if ($id) {
                log_activity('notice_create', "Created notice ID: $id");
                set_flash('success', 'Notice created.');
            } else {
                set_flash('danger', 'Failed to create notice.');
            }
        } elseif ($action === 'update') {
            $nid = (int)$_POST['notice_id'];
            notice_update($nid, [
                'title' => clean($_POST['title'] ?? ''),
                'content' => clean($_POST['content'] ?? ''),
                'target_role' => clean($_POST['target_role'] ?? 'all'),
                'priority' => clean($_POST['priority'] ?? 'medium'),
                'expires_at' => clean($_POST['expires_at'] ?? ''),
                'is_active' => isset($_POST['is_active']) ? 1 : 0
            ]);
            log_activity('notice_update', "Updated notice ID: $nid");
            set_flash('success', 'Notice updated.');
        } elseif ($action === 'delete') {
            $nid = (int)$_POST['notice_id'];
            notice_delete($nid);
            log_activity('notice_delete', "Deleted notice ID: $nid");
            set_flash('success', 'Notice deleted.');
        }
        redirect('index.php?page=admin_notices');
    }
    
    $keyword = clean($_GET['q'] ?? '');
    $notices = $keyword ? notice_search($keyword) : notice_search();
    $edit = isset($_GET['id']) ? notice_find((int)$_GET['id']) : null;
    
    require __DIR__ . '/../views/admin/notices.php';
}

function admin_activity_logs() {
    require_role('admin');
    $filters = [
        'keyword' => clean($_GET['q'] ?? ''),
        'from' => clean($_GET['from'] ?? ''),
        'to' => clean($_GET['to'] ?? ''),
        'user_id' => !empty($_GET['user_id']) ? (int)$_GET['user_id'] : null
    ];
    $logs = log_search($filters);
    require __DIR__ . '/../views/admin/activity_logs.php';
}

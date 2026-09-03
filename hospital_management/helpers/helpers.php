<?php
/**
 * Helper functions - Hospital Management System
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Redirect helper
 */
function redirect($url) {
    header('Location: ' . $url);
    exit;
}

/**
 * Flash message (session based)
 */
function set_flash($type, $message) {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function get_flash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * Check if user is logged in
 */
function is_logged_in() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Get current user data from session
 */
function current_user() {
    if (!is_logged_in()) return null;
    return [
        'id'       => $_SESSION['user_id'],
        'username' => $_SESSION['username'] ?? '',
        'full_name'=> $_SESSION['full_name'] ?? '',
        'role'     => $_SESSION['role'] ?? '',
        'email'    => $_SESSION['email'] ?? ''
    ];
}

/**
 * Require login
 */
function require_login() {
    if (!is_logged_in()) {
        set_flash('danger', 'Please login to continue.');
        redirect('index.php?page=login');
    }
}

/**
 * Require specific role(s)
 */
function require_role($roles) {
    require_login();
    if (!is_array($roles)) $roles = [$roles];
    $user = current_user();
    if (!in_array($user['role'], $roles)) {
        set_flash('danger', 'Access denied. Insufficient privileges.');
        redirect('index.php');
    }
}

/**
 * CSRF token generation & validation
 */
function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field() {
    $token = generate_csrf_token();
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
}

function verify_csrf() {
    if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token'])) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']);
}

/**
 * Sanitize output
 */
function e($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Simple input sanitizer
 */
function clean($data) {
    if (is_array($data)) {
        return array_map('clean', $data);
    }
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

/**
 * Validate email
 */
function is_valid_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Password hashing
 */
function hash_password($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

function verify_password($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Log activity
 */
function log_activity($action, $details = '') {
    require_once __DIR__ . '/../config/database.php';
    $user_id = $_SESSION['user_id'] ?? 'NULL';
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    $ua = substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 250);
    $action = db_escape($action);
    $details = db_escape($details);
    $ip = db_escape($ip);
    $ua = db_escape($ua);
    $sql = "INSERT INTO activity_logs (user_id, action, details, ip_address, user_agent) 
            VALUES ($user_id, '$action', '$details', '$ip', '$ua')";
    db_query($sql);
}

/**
 * Generate unique invoice number
 */
function generate_invoice_number() {
    return 'INV-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
}

/**
 * Format currency
 */
function format_money($amount) {
    return number_format((float)$amount, 2);
}

/**
 * JSON response helper for AJAX
 */
function json_response($data, $status = 200) {
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

/**
 * Get base URL helper (simple)
 */
function base_url($path = '') {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    // Adjust if project is in a subfolder
    $script = dirname($_SERVER['SCRIPT_NAME']);
    $base = rtrim($protocol . '://' . $host . $script, '/');
    return $base . '/' . ltrim($path, '/');
}

/**
 * Role display name
 */
function role_label($role) {
    $labels = [
        'admin' => 'Administrator',
        'doctor' => 'Doctor',
        'patient' => 'Patient',
        'receptionist' => 'Receptionist'
    ];
    return $labels[$role] ?? ucfirst($role);
}

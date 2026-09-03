<?php
/**
 * Auth Controller
 */
require_once __DIR__ . '/../helpers/helpers.php';
require_once __DIR__ . '/../models/user_model.php';
require_once __DIR__ . '/../models/patient_model.php';

function auth_login() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        require __DIR__ . '/../views/auth/login.php';
        return;
    }
    
    if (!verify_csrf()) {
        set_flash('danger', 'Invalid security token. Please try again.');
        redirect('index.php?page=login');
    }
    
    $username = clean($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // PHP Validation
    $errors = [];
    if (empty($username)) $errors[] = 'Username is required.';
    if (empty($password)) $errors[] = 'Password is required.';
    
    if (!empty($errors)) {
        set_flash('danger', implode(' ', $errors));
        redirect('index.php?page=login');
    }
    
    $user = user_find_by_username($username);
    if (!$user || !verify_password($password, $user['password'])) {
        // Also try email login
        $user = user_find_by_email($username);
        if (!$user || !verify_password($password, $user['password'])) {
            set_flash('danger', 'Invalid username or password.');
            log_activity('login_failed', "Failed login attempt for: $username");
            redirect('index.php?page=login');
        }
    }
    
    if ($user['status'] !== 'active') {
        set_flash('danger', 'Your account is inactive. Contact administrator.');
        redirect('index.php?page=login');
    }
    
    // Set session
    $_SESSION['user_id']   = $user['id'];
    $_SESSION['username']  = $user['username'];
    $_SESSION['full_name'] = $user['full_name'];
    $_SESSION['role']      = $user['role'];
    $_SESSION['email']     = $user['email'];
    
    // Optional remember me cookie (7 days)
    if (!empty($_POST['remember'])) {
        $token = bin2hex(random_bytes(32));
        setcookie('remember_token', $token, time() + 60*60*24*7, '/', '', false, true);
        // In production store hashed token in DB
    }
    
    log_activity('login', 'User logged in successfully');
    
    // Redirect by role
    $role = $user['role'];
    redirect("index.php?page={$role}_dashboard");
}

function auth_register() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        require __DIR__ . '/../views/auth/register.php';
        return;
    }
    
    if (!verify_csrf()) {
        set_flash('danger', 'Invalid security token.');
        redirect('index.php?page=register');
    }
    
    $data = [
        'username'  => clean($_POST['username'] ?? ''),
        'email'     => clean($_POST['email'] ?? ''),
        'password'  => $_POST['password'] ?? '',
        'confirm'   => $_POST['confirm_password'] ?? '',
        'full_name' => clean($_POST['full_name'] ?? ''),
        'phone'     => clean($_POST['phone'] ?? ''),
        'gender'    => clean($_POST['gender'] ?? ''),
        'date_of_birth' => clean($_POST['date_of_birth'] ?? ''),
    ];
    
    // PHP Validation
    $errors = [];
    if (strlen($data['username']) < 3) $errors[] = 'Username must be at least 3 characters.';
    if (!is_valid_email($data['email'])) $errors[] = 'Valid email is required.';
    if (strlen($data['password']) < 6) $errors[] = 'Password must be at least 6 characters.';
    if ($data['password'] !== $data['confirm']) $errors[] = 'Passwords do not match.';
    if (empty($data['full_name'])) $errors[] = 'Full name is required.';
    
    if (user_find_by_username($data['username'])) $errors[] = 'Username already taken.';
    if (user_find_by_email($data['email'])) $errors[] = 'Email already registered.';
    
    if (!empty($errors)) {
        set_flash('danger', implode(' ', $errors));
        redirect('index.php?page=register');
    }
    
    $hashed = hash_password($data['password']);
    $insert = [
        'username' => $data['username'],
        'email' => $data['email'],
        'password' => $hashed,
        'full_name' => $data['full_name'],
        'role' => 'patient', // public registration only for patients
        'phone' => $data['phone'],
        'gender' => $data['gender'],
        'date_of_birth' => $data['date_of_birth'],
        'status' => 'active'
    ];
    
    $user_id = user_create($insert);
    if ($user_id) {
        patient_create_profile($user_id, [
            'blood_group' => clean($_POST['blood_group'] ?? ''),
            'emergency_contact' => clean($_POST['emergency_contact'] ?? '')
        ]);
        log_activity('register', "New patient registered: {$data['username']}");
        set_flash('success', 'Registration successful! Please login.');
        redirect('index.php?page=login');
    } else {
        set_flash('danger', 'Registration failed. Please try again.');
        redirect('index.php?page=register');
    }
}

function auth_logout() {
    if (is_logged_in()) {
        log_activity('logout', 'User logged out');
    }
    session_unset();
    session_destroy();
    if (isset($_COOKIE['remember_token'])) {
        setcookie('remember_token', '', time() - 3600, '/');
    }
    redirect('index.php?page=login');
}

<?php
/**
 * AJAX / JSON Controller
 */
require_once __DIR__ . '/../helpers/helpers.php';
require_once __DIR__ . '/../models/user_model.php';
require_once __DIR__ . '/../models/patient_model.php';
require_once __DIR__ . '/../models/appointment_model.php';
require_once __DIR__ . '/../models/queue_model.php';
require_once __DIR__ . '/../models/doctor_model.php';

// All AJAX endpoints require login
if (!is_logged_in()) {
    json_response(['success' => false, 'message' => 'Unauthorized'], 401);
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'search_patients':
        $q = clean($_GET['q'] ?? '');
        $patients = patient_search($q, 20);
        json_response(['success' => true, 'data' => $patients]);
        break;
        
    case 'search_doctors':
        $q = clean($_GET['q'] ?? '');
        $doctors = user_get_doctors(true);
        if ($q) {
            $doctors = array_values(array_filter($doctors, function($d) use ($q) {
                return stripos($d['full_name'], $q) !== false || stripos($d['specialization'] ?? '', $q) !== false;
            }));
        }
        json_response(['success' => true, 'data' => $doctors]);
        break;
        
    case 'doctor_availability':
        $doctor_id = (int)($_GET['doctor_id'] ?? 0);
        $slots = doctor_get_availability($doctor_id);
        json_response(['success' => true, 'data' => $slots]);
        break;
        
    case 'queue_refresh':
        require_role(['receptionist', 'admin', 'doctor']);
        $date = clean($_GET['date'] ?? date('Y-m-d'));
        $doctor_id = !empty($_GET['doctor_id']) ? (int)$_GET['doctor_id'] : null;
        $list = queue_list($date, $doctor_id);
        json_response(['success' => true, 'data' => $list]);
        break;
        
    case 'check_username':
        $username = clean($_GET['username'] ?? '');
        $exists = user_find_by_username($username) ? true : false;
        json_response(['success' => true, 'exists' => $exists]);
        break;
        
    case 'check_email':
        $email = clean($_GET['email'] ?? '');
        $exists = user_find_by_email($email) ? true : false;
        json_response(['success' => true, 'exists' => $exists]);
        break;
        
    case 'dashboard_stats':
        $role = current_user()['role'];
        $stats = [];
        if ($role === 'admin') {
            require_once __DIR__ . '/../models/admin_model.php';
            $stats = admin_dashboard_stats();
        } elseif ($role === 'doctor') {
            $stats = doctor_dashboard_stats(current_user()['id']);
        } elseif ($role === 'receptionist') {
            require_once __DIR__ . '/../models/receptionist_model.php';
            $stats = receptionist_dashboard_stats();
        } elseif ($role === 'patient') {
            $stats = patient_dashboard_stats(current_user()['id']);
        }
        json_response(['success' => true, 'data' => $stats]);
        break;
        
    default:
        json_response(['success' => false, 'message' => 'Unknown action'], 400);
}

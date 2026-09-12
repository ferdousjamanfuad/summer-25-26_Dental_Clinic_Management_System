<?php
// ================================================================
// CONTROLLER: ADMIN dashboard
// CRUD  : user accounts (every role)
// Extras: 1) equipment inventory check & manage
//         2) monthly report & activity monitor
//         3) doctor availability & shift schedule
// ================================================================

function admin_controller($conn) {
    $action = $_GET['action'] ?? 'users';
    $me = current_user();
    $error = '';

    /* -------- CRUD: SYSTEM USERS -------- */
    if ($action === 'add_user' && is_post()) {
        csrf_check();
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $contact = trim($_POST['contact'] ?? '');
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? 'patient';
        
        if (is_blank($name) || is_blank($email) || is_blank($password) || is_blank($username)) {
            $error = "Name, email, username and password are required.";
        } else {
            if (add_user($conn, $name, $email, $contact, $username, $password, $role, 'active')) {
                set_flash('success', 'User added successfully!');
                redirect('index.php?page=admin&action=users');
            } else {
                $error = "Failed to add user. Username or email might exist.";
            }
        }
    }

    if ($action === 'status') {
        csrf_check();
        $id = (int)($_GET['id'] ?? 0);
        $status = $_GET['status'] ?? '';
        if (in_array($status, ['active', 'suspended', 'pending']) && update_user_status($conn, $id, $status)) {
            set_flash('success', 'User status updated to ' . $status);
        } else {
            set_flash('error', 'Failed to update user status.');
        }
        redirect('index.php?page=admin&action=users');
    }

    /* -------- Feature 1: Equipment Check / Manage -------- */
    if ($action === 'add_equipment' && is_post()) {
        csrf_check();
        $name = trim($_POST['name'] ?? '');
        $desc = trim($_POST['description'] ?? '');
        $qty = (int)($_POST['quantity'] ?? 1);
        
        if (is_blank($name)) {
            set_flash('error', 'Equipment name is required.');
        } else {
            add_equipment($conn, $name, $desc, $qty);
            set_flash('success', 'Equipment added successfully.');
        }
        redirect('index.php?page=admin&action=equipment');
    }

    /* -------- Feature 3: Doctor Availability Set -------- */
    if ($action === 'set_schedule' && is_post()) {
        csrf_check();
        $doctor_id   = (int)($_POST['doctor_id'] ?? 0);
        $day_of_week = $_POST['day_of_week'] ?? '';
        $start_time  = $_POST['start_time'] ?? '';
        $end_time    = $_POST['end_time'] ?? '';

        if ($doctor_id <= 0 || empty($day_of_week) || empty($start_time) || empty($end_time)) {
            set_flash('error', 'All schedule fields are required.');
        } else {
            if (add_doctor_schedule($conn, $doctor_id, $day_of_week, $start_time, $end_time)) {
                set_flash('success', 'Doctor availability schedule saved successfully.');
            } else {
                set_flash('error', 'Failed to save doctor schedule.');
            }
        }
        redirect('index.php?page=admin&action=doctor_availability');
    }

    if ($action === 'delete_schedule') {
        csrf_check();
        $id = (int)($_GET['id'] ?? 0);
        if (delete_schedule($conn, $id)) {
            set_flash('success', 'Schedule removed successfully.');
        } else {
            set_flash('error', 'Could not remove schedule.');
        }
        redirect('index.php?page=admin&action=doctor_availability');
    }

    /* -------- Fetch Data for Views -------- */
    if ($action === 'users') {
        $users = get_users($conn);
    } elseif ($action === 'equipment') {
        $equipments = get_equipments($conn);
    } elseif ($action === 'report') {
        // Feature 2: Monthly report & monitor stats
        $all_users = get_users($conn);
        $stats = [
            'total_users'        => count($all_users),
            'total_doctors'      => count(get_users($conn, 'doctor')),
            'total_patients'     => count(get_users($conn, 'patient')),
            'total_receptionist' => count(get_users($conn, 'receptionist')),
            'pending_approvals'  => count(array_filter($all_users, fn($u) => $u['status'] === 'pending'))
        ];
    } elseif ($action === 'doctor_availability') {
        $doctors   = get_users($conn, 'doctor');
        $schedules = get_doctor_schedules($conn);
    }

    // Load the Admin Dashboard View
    require __DIR__ . '/../views/admin/dashboard.php';
}
?>

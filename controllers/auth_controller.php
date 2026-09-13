<?php
// ================================================================
// CONTROLLER: AUTHENTICATION
// ================================================================

function login_controller($conn) {
    if (is_logged_in()) {
        redirect('index.php?page=' . current_role());
    }

    $error = '';
    
    if (is_post()) {
        csrf_check();
        
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        
        if (is_blank($email) || is_blank($password)) {
            $error = 'Please enter email and password.';
        } else {
            $user = get_user_by_email($conn, $email);
            
            if ($user && password_verify($password, $user['password'])) {
                if ($user['status'] === 'suspended') {
                    $error = 'Your account is suspended. Contact Admin.';
                } elseif ($user['status'] === 'pending') {
                    $error = 'Your account is pending admin approval.';
                } else {
                    // Success
                    $_SESSION['user'] = [
                        'id' => $user['id'],
                        'name' => $user['name'],
                        'email' => $user['email'],
                        'role' => $user['role']
                    ];
                    $_SESSION['last_active'] = time();
                    
                    set_flash('success', 'Welcome back, ' . esc($user['name']) . '!');
                    redirect('index.php?page=' . $user['role']);
                }
            } else {
                $error = 'Invalid email or password.';
            }
        }
    }
    
    require __DIR__ . '/../views/auth/login.php';
}

function register_controller($conn) {
    if (is_logged_in()) {
        redirect('index.php?page=' . current_role());
    }

    $error = '';
    $allowed_roles = ['doctor', 'patient', 'receptionist']; // Admin can NOT register

    if (is_post()) {
        csrf_check();

        $name     = trim($_POST['name'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $contact  = trim($_POST['contact'] ?? '');
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $role     = $_POST['role'] ?? 'patient';

        if (is_blank($name) || is_blank($email) || is_blank($contact) || is_blank($username) || is_blank($password)) {
            $error = 'Please fill in all required fields.';
        } elseif (!in_array($role, $allowed_roles, true)) {
            $error = 'Invalid role selected. Admin registration is not permitted.';
        } elseif (!valid_email($email)) {
            $error = 'Please provide a valid email address.';
        } else {
            // Check if email or username already exists
            if (get_user_by_email($conn, $email)) {
                $error = 'An account with this email already exists.';
            } else {
                // Doctor and receptionist need approval; patient is active by default
                $status = ($role === 'patient') ? 'active' : 'pending';
                
                if (add_user($conn, $name, $email, $contact, $username, $password, $role, $status)) {
                    if ($status === 'pending') {
                        set_flash('success', 'Registration successful! Your account is pending admin approval.');
                    } else {
                        set_flash('success', 'Registration successful! You can now log in.');
                    }
                    redirect('index.php?page=login');
                } else {
                    $error = 'Registration failed. Username might be already taken.';
                }
            }
        }
    }

    require __DIR__ . '/../views/auth/register.php';
}

function logout_controller($conn) {
    $_SESSION = [];
    session_regenerate_id(true);
    set_flash('success', 'You have been logged out safely.');
    redirect('index.php?page=login');
}


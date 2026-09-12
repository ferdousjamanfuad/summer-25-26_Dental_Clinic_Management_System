<?php
// ================================================================
// MODEL: USERS (Admin, Doctor, Patient, Receptionist)
// ================================================================

function get_user_by_email($conn, $email) {
    $sql  = "SELECT * FROM users WHERE email = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);
    $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);
    return $row;
}

function get_users($conn, $role = '') {
    if ($role === '') {
        $sql = "SELECT * FROM users ORDER BY created_at DESC";
        $stmt = mysqli_prepare($conn, $sql);
    } else {
        $sql = "SELECT * FROM users WHERE role = ? ORDER BY created_at DESC";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 's', $role);
    }
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $users = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $users[] = $row;
    }
    mysqli_stmt_close($stmt);
    return $users;
}

function add_user($conn, $name, $email, $contact, $username, $password, $role, $status = 'active') {
    $sql = "INSERT INTO users (name, email, contact, username, password, role, status) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    $hash = password_hash($password, PASSWORD_DEFAULT);
    mysqli_stmt_bind_param($stmt, 'sssssss', $name, $email, $contact, $username, $hash, $role, $status);
    $success = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    return $success;
}

function update_user_status($conn, $id, $status) {
    $sql = "UPDATE users SET status = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'si', $status, $id);
    $success = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    return $success;
}

// ================================================================
// SCHEDULE FUNCTIONS (Doctor Availability)
// ================================================================

function add_doctor_schedule($conn, $doctor_id, $day_of_week, $start_time, $end_time) {
    $sql = "INSERT INTO schedules (doctor_id, day_of_week, start_time, end_time) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'isss', $doctor_id, $day_of_week, $start_time, $end_time);
    $success = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    return $success;
}

function get_doctor_schedules($conn, $doctor_id = null) {
    if ($doctor_id) {
        $sql = "SELECT s.*, u.name as doctor_name FROM schedules s JOIN users u ON s.doctor_id = u.id WHERE s.doctor_id = ? ORDER BY FIELD(s.day_of_week, 'Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday')";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 'i', $doctor_id);
    } else {
        $sql = "SELECT s.*, u.name as doctor_name FROM schedules s JOIN users u ON s.doctor_id = u.id ORDER BY u.name ASC, FIELD(s.day_of_week, 'Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday')";
        $stmt = mysqli_prepare($conn, $sql);
    }
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $rows = [];
    while ($r = mysqli_fetch_assoc($res)) {
        $rows[] = $r;
    }
    mysqli_stmt_close($stmt);
    return $rows;
}

function delete_schedule($conn, $schedule_id) {
    $sql = "DELETE FROM schedules WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $schedule_id);
    $success = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    return $success;
}

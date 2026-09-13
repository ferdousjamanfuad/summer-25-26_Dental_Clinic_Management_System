<?php
// ================================================================
// MODEL: appointments
// ================================================================

// Gets all appointments for a specific doctor, ordered by date and time
function get_doctor_appointments($conn, $doctor_id, $status = '') {
    if ($status) {
        $sql = "SELECT a.*, p.name as patient_name, p.contact, p.gender, p.dob 
                FROM appointments a 
                JOIN users p ON a.patient_id = p.id 
                WHERE a.doctor_id = ? AND a.status = ?
                ORDER BY a.appointment_date ASC, a.appointment_time ASC";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 'is', $doctor_id, $status);
    } else {
        $sql = "SELECT a.*, p.name as patient_name, p.contact, p.gender, p.dob 
                FROM appointments a 
                JOIN users p ON a.patient_id = p.id 
                WHERE a.doctor_id = ?
                ORDER BY a.appointment_date ASC, a.appointment_time ASC";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 'i', $doctor_id);
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

// Get a single appointment by ID
function get_appointment($conn, $id) {
    $sql = "SELECT a.*, p.name as patient_name, p.contact as patient_contact, 
            d.name as doctor_name 
            FROM appointments a 
            JOIN users p ON a.patient_id = p.id 
            JOIN users d ON a.doctor_id = d.id 
            WHERE a.id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);
    return $row;
}

// Update appointment status
function update_appointment_status($conn, $id, $status) {
    $sql = "UPDATE appointments SET status = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'si', $status, $id);
    $success = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    return $success;
}

// Gets all appointments for a specific patient
function get_patient_appointments($conn, $patient_id) {
    $sql = "SELECT a.*, d.name as doctor_name 
            FROM appointments a 
            JOIN users d ON a.doctor_id = d.id 
            WHERE a.patient_id = ?
            ORDER BY a.appointment_date DESC, a.appointment_time DESC";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $patient_id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $rows = [];
    while ($r = mysqli_fetch_assoc($res)) {
        $rows[] = $r;
    }
    mysqli_stmt_close($stmt);
    return $rows;
}

// Books a new appointment and auto-generates serial number
function book_appointment($conn, $patient_id, $doctor_id, $date, $time, $notes) {
    // Calculate Serial No (count appointments for this doctor on this date + 1)
    $sql_serial = "SELECT COUNT(*) as total FROM appointments WHERE doctor_id = ? AND appointment_date = ?";
    $stmt_serial = mysqli_prepare($conn, $sql_serial);
    mysqli_stmt_bind_param($stmt_serial, 'is', $doctor_id, $date);
    mysqli_stmt_execute($stmt_serial);
    $res_serial = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_serial));
    $serial_no = $res_serial['total'] + 1;
    mysqli_stmt_close($stmt_serial);

    // Insert Appointment
    $sql = "INSERT INTO appointments (patient_id, doctor_id, appointment_date, appointment_time, serial_no, status, notes) 
            VALUES (?, ?, ?, ?, ?, 'pending', ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'iissss', $patient_id, $doctor_id, $date, $time, $serial_no, $notes);
    $success = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    return $success;
}

// Update payment method for patient
function update_payment_method($conn, $appointment_id, $patient_id, $payment_method) {
    $sql = "UPDATE appointments SET payment_method = ? WHERE id = ? AND patient_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'sii', $payment_method, $appointment_id, $patient_id);
    $success = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    return $success;
}

// Gets ALL appointments (for Receptionist)
function get_all_appointments($conn) {
    $sql = "SELECT a.*, p.name as patient_name, d.name as doctor_name 
            FROM appointments a 
            JOIN users p ON a.patient_id = p.id 
            JOIN users d ON a.doctor_id = d.id 
            ORDER BY a.appointment_date DESC, a.appointment_time DESC";
    $result = mysqli_query($conn, $sql);
    $rows = [];
    if($result) {
        while ($r = mysqli_fetch_assoc($result)) {
            $rows[] = $r;
        }
    }
    return $rows;
}

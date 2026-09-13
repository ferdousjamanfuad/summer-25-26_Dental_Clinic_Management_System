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

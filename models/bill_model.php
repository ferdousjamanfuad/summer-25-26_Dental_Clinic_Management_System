<?php
// ================================================================
// MODEL: BILLS
// ================================================================

function generate_bill($conn, $appointment_id, $patient_id, $total_amount, $generated_by) {
    $sql = "INSERT INTO bills (appointment_id, patient_id, total_amount, status, generated_by) VALUES (?, ?, ?, 'unpaid', ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'iidi', $appointment_id, $patient_id, $total_amount, $generated_by);
    $success = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    return $success;
}

function get_all_bills($conn) {
    $sql = "SELECT b.*, p.name as patient_name, a.appointment_date 
            FROM bills b 
            JOIN users p ON b.patient_id = p.id 
            JOIN appointments a ON b.appointment_id = a.id 
            ORDER BY b.created_at DESC";
    $result = mysqli_query($conn, $sql);
    $rows = [];
    if($result) {
        while ($r = mysqli_fetch_assoc($result)) {
            $rows[] = $r;
        }
    }
    return $rows;
}

function get_unbilled_appointments($conn) {
    // Get completed appointments that don't have a bill yet
    $sql = "SELECT a.*, p.name as patient_name, d.name as doctor_name 
            FROM appointments a 
            JOIN users p ON a.patient_id = p.id 
            JOIN users d ON a.doctor_id = d.id 
            WHERE a.status = 'completed' 
            AND a.id NOT IN (SELECT appointment_id FROM bills)
            ORDER BY a.appointment_date DESC";
    $result = mysqli_query($conn, $sql);
    $rows = [];
    if($result) {
        while ($r = mysqli_fetch_assoc($result)) {
            $rows[] = $r;
        }
    }
    return $rows;
}

function update_bill_payment($conn, $bill_id, $paid_amount, $status) {
    $sql = "UPDATE bills SET paid_amount = ?, status = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'dsi', $paid_amount, $status, $bill_id);
    $success = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    return $success;
}


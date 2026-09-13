<?php
// ================================================================
// MODEL: prescriptions
// ================================================================

function add_prescription($conn, $appointment_id, $doctor_id, $patient_id, $diagnosis, $notes, $medicines) {
    mysqli_begin_transaction($conn);
    try {
        // Insert Prescription
        $sql = "INSERT INTO prescriptions (appointment_id, doctor_id, patient_id, diagnosis, notes) VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 'iiiss', $appointment_id, $doctor_id, $patient_id, $diagnosis, $notes);
        mysqli_stmt_execute($stmt);
        $prescription_id = mysqli_insert_id($conn);
        mysqli_stmt_close($stmt);

        // Insert Medicines
        if (!empty($medicines) && is_array($medicines)) {
            $sql_med = "INSERT INTO prescription_medicines (prescription_id, medicine_name, dosage, instructions) VALUES (?, ?, ?, ?)";
            $stmt_med = mysqli_prepare($conn, $sql_med);
            foreach ($medicines as $med) {
                if (!empty($med['name'])) {
                    mysqli_stmt_bind_param($stmt_med, 'isss', $prescription_id, $med['name'], $med['dosage'], $med['instructions']);
                    mysqli_stmt_execute($stmt_med);
                }
            }
            mysqli_stmt_close($stmt_med);
        }

        // Update Appointment Status to completed
        $sql_up = "UPDATE appointments SET status = 'completed' WHERE id = ?";
        $stmt_up = mysqli_prepare($conn, $sql_up);
        mysqli_stmt_bind_param($stmt_up, 'i', $appointment_id);
        mysqli_stmt_execute($stmt_up);
        mysqli_stmt_close($stmt_up);

        mysqli_commit($conn);
        return $prescription_id;
    } catch (Exception $e) {
        mysqli_rollback($conn);
        return false;
    }
}

function get_patient_history($conn, $patient_id) {
    $sql = "SELECT p.*, a.appointment_date, d.name as doctor_name 
            FROM prescriptions p
            JOIN appointments a ON p.appointment_id = a.id
            JOIN users d ON p.doctor_id = d.id
            WHERE p.patient_id = ?
            ORDER BY p.created_at DESC";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $patient_id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $history = [];
    while ($r = mysqli_fetch_assoc($res)) {
        // Fetch medicines for each prescription
        $sql_med = "SELECT * FROM prescription_medicines WHERE prescription_id = ?";
        $stmt_med = mysqli_prepare($conn, $sql_med);
        mysqli_stmt_bind_param($stmt_med, 'i', $r['id']);
        mysqli_stmt_execute($stmt_med);
        $res_med = mysqli_stmt_get_result($stmt_med);
        $medicines = [];
        while ($m = mysqli_fetch_assoc($res_med)) {
            $medicines[] = $m;
        }
        mysqli_stmt_close($stmt_med);
        
        $r['medicines'] = $medicines;
        $history[] = $r;
    }
    mysqli_stmt_close($stmt);
    return $history;
}

// Get all unique patients that have ever booked this doctor
function get_patients_by_doctor($conn, $doctor_id) {
    $sql = "SELECT DISTINCT u.id, u.name, u.email, u.contact, u.gender, u.dob
            FROM users u
            JOIN appointments a ON u.id = a.patient_id
            WHERE a.doctor_id = ?
            ORDER BY u.name ASC";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $doctor_id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $patients = [];
    while ($r = mysqli_fetch_assoc($res)) {
        $patients[] = $r;
    }
    mysqli_stmt_close($stmt);
    return $patients;
}

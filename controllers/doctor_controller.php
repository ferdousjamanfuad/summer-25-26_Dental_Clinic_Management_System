<?php
// ================================================================
// CONTROLLER: DOCTOR
// ================================================================

function doctor_controller($conn) {
    $action = $_GET['action'] ?? 'queue';
    $me = current_user();
    $error = '';

    /* -------- Prescription Management -------- */
    if ($action === 'save_prescription' && is_post()) {
        csrf_check();
        $appointment_id = (int)($_POST['appointment_id'] ?? 0);
        $patient_id     = (int)($_POST['patient_id'] ?? 0);
        $diagnosis      = trim($_POST['diagnosis'] ?? '');
        $notes          = trim($_POST['notes'] ?? '');
        
        $med_names  = $_POST['med_name'] ?? [];
        $med_doses  = $_POST['med_dosage'] ?? [];
        $med_instrs = $_POST['med_instructions'] ?? [];
        
        if ($appointment_id <= 0 || $patient_id <= 0 || is_blank($diagnosis)) {
            set_flash('error', 'Diagnosis is required to generate a prescription.');
            redirect('index.php?page=doctor&action=prescribe&id=' . $appointment_id);
        } else {
            $medicines = [];
            for ($i = 0; $i < count($med_names); $i++) {
                if (!empty(trim($med_names[$i]))) {
                    $medicines[] = [
                        'name' => trim($med_names[$i]),
                        'dosage' => trim($med_doses[$i] ?? ''),
                        'instructions' => trim($med_instrs[$i] ?? '')
                    ];
                }
            }
            
            if (add_prescription($conn, $appointment_id, $me['id'], $patient_id, $diagnosis, $notes, $medicines)) {
                set_flash('success', 'Prescription generated and appointment marked as completed.');
                redirect('index.php?page=doctor&action=queue');
            } else {
                set_flash('error', 'Failed to generate prescription.');
                redirect('index.php?page=doctor&action=prescribe&id=' . $appointment_id);
            }
        }
    }

    /* -------- Fetch Data for Views -------- */
    if ($action === 'queue') {
        $appointments = get_doctor_appointments($conn, $me['id']);
    } elseif ($action === 'prescribe') {
        $id = (int)($_GET['id'] ?? 0);
        $appointment = get_appointment($conn, $id);
        if (!$appointment || $appointment['doctor_id'] !== $me['id'] || $appointment['status'] === 'completed') {
            set_flash('error', 'Invalid appointment or already completed.');
            redirect('index.php?page=doctor&action=queue');
        }
    } elseif ($action === 'patients') {
        $patients = get_patients_by_doctor($conn, $me['id']);
    } elseif ($action === 'patient_details') {
        $id = (int)($_GET['id'] ?? 0);
        $patient_history = get_patient_history($conn, $id);
    }

    require __DIR__ . '/../views/doctor/dashboard.php';
}

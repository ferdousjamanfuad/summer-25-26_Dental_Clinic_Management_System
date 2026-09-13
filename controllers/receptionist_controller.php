<?php
// ================================================================
// CONTROLLER: RECEPTIONIST
// ================================================================

function receptionist_controller($conn) {
    $action = $_GET['action'] ?? 'appointments';
    $me = current_user();
    $error = '';

    /* -------- Appointment Scheduling -------- */
    if ($action === 'update_appointment' && is_post()) {
        csrf_check();
        $appointment_id = (int)($_POST['appointment_id'] ?? 0);
        $status         = $_POST['status'] ?? '';
        
        $valid_statuses = ['pending', 'confirmed', 'cancelled'];
        if (in_array($status, $valid_statuses)) {
            if (update_appointment_status($conn, $appointment_id, $status)) {
                set_flash('success', 'Appointment status updated to ' . ucfirst($status) . '.');
            } else {
                set_flash('error', 'Failed to update appointment status.');
            }
        } else {
            set_flash('error', 'Invalid status.');
        }
        redirect('index.php?page=receptionist&action=appointments');
    }

    /* -------- Billing & Invoices -------- */
    if ($action === 'generate_bill' && is_post()) {
        csrf_check();
        $appointment_id = (int)($_POST['appointment_id'] ?? 0);
        $patient_id     = (int)($_POST['patient_id'] ?? 0);
        $total_amount   = (float)($_POST['total_amount'] ?? 0);
        
        if ($total_amount <= 0) {
            set_flash('error', 'Total amount must be greater than zero.');
        } else {
            if (generate_bill($conn, $appointment_id, $patient_id, $total_amount, $me['id'])) {
                set_flash('success', 'Bill generated successfully.');
            } else {
                set_flash('error', 'Failed to generate bill.');
            }
        }
        redirect('index.php?page=receptionist&action=billing');
    }

    if ($action === 'update_payment' && is_post()) {
        csrf_check();
        $bill_id     = (int)($_POST['bill_id'] ?? 0);
        $paid_amount = (float)($_POST['paid_amount'] ?? 0);
        $status      = $_POST['status'] ?? 'unpaid';
        
        if (update_bill_payment($conn, $bill_id, $paid_amount, $status)) {
            set_flash('success', 'Payment status updated.');
        } else {
            set_flash('error', 'Failed to update payment.');
        }
        redirect('index.php?page=receptionist&action=billing');
    }

    /* -------- Equipment Assignment -------- */
    if ($action === 'assign_equipment' && is_post()) {
        csrf_check();
        $equipment_id = (int)($_POST['equipment_id'] ?? 0);
        $doctor_id    = (int)($_POST['doctor_id'] ?? 0);
        
        if ($doctor_id > 0) {
            assign_equipment($conn, $equipment_id, $doctor_id);
            set_flash('success', 'Equipment assigned to doctor.');
        }
        redirect('index.php?page=receptionist&action=equipment');
    }
    
    if ($action === 'unassign_equipment' && is_post()) {
        csrf_check();
        $equipment_id = (int)($_POST['equipment_id'] ?? 0);
        unassign_equipment($conn, $equipment_id);
        set_flash('success', 'Equipment released to inventory.');
        redirect('index.php?page=receptionist&action=equipment');
    }

    /* -------- Fetch Data for Views -------- */
    if ($action === 'appointments') {
        $appointments = get_all_appointments($conn);
    } elseif ($action === 'billing') {
        $unbilled_appointments = get_unbilled_appointments($conn);
        $bills = get_all_bills($conn);
    } elseif ($action === 'equipment') {
        $equipment = get_equipments($conn);
        $doctors = get_users($conn, 'doctor');
    }

    require __DIR__ . '/../views/receptionist/dashboard.php';
}

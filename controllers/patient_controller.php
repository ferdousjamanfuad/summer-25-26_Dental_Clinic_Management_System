<?php
// ================================================================
// CONTROLLER: PATIENT
// ================================================================

function patient_controller($conn) {
    $action = $_GET['action'] ?? 'appointments';
    $me = current_user();
    $error = '';

    /* -------- Appointment Booking -------- */
    if ($action === 'save_booking' && is_post()) {
        csrf_check();
        $doctor_id = (int)($_POST['doctor_id'] ?? 0);
        $date      = $_POST['appointment_date'] ?? '';
        $time      = $_POST['appointment_time'] ?? '';
        $notes     = trim($_POST['notes'] ?? '');

        if ($doctor_id <= 0 || empty($date) || empty($time)) {
            set_flash('error', 'Doctor, Date, and Time are required.');
        } elseif (!valid_date($date)) {
            set_flash('error', 'Invalid date format.');
        } else {
            if (book_appointment($conn, $me['id'], $doctor_id, $date, $time, $notes)) {
                set_flash('success', 'Appointment booked successfully! Your serial number has been generated.');
            } else {
                set_flash('error', 'Failed to book appointment. Please try again.');
            }
        }
        redirect('index.php?page=patient&action=appointments');
    }

    /* -------- Payment Selection -------- */
    if ($action === 'update_payment' && is_post()) {
        csrf_check();
        $appointment_id = (int)($_POST['appointment_id'] ?? 0);
        $payment_method = $_POST['payment_method'] ?? '';
        $allowed_methods = ['cash', 'card', 'mobile_banking', 'insurance'];

        if (in_array($payment_method, $allowed_methods)) {
            if (update_payment_method($conn, $appointment_id, $me['id'], $payment_method)) {
                set_flash('success', 'Payment method updated successfully.');
            } else {
                set_flash('error', 'Failed to update payment method.');
            }
        } else {
            set_flash('error', 'Invalid payment method selected.');
        }
        redirect('index.php?page=patient&action=appointments');
    }

    /* -------- Fetch Data for Views -------- */
    if ($action === 'appointments') {
        $appointments = get_patient_appointments($conn, $me['id']);
    } elseif ($action === 'book') {
        $doctors = get_users($conn, 'doctor');
        $schedules = [];
        foreach ($doctors as $doc) {
            $schedules[$doc['id']] = get_doctor_schedules($conn, $doc['id']);
        }
    } elseif ($action === 'prescriptions') {
        $history = get_patient_history($conn, $me['id']);
    }

    require __DIR__ . '/../views/patient/dashboard.php';
}

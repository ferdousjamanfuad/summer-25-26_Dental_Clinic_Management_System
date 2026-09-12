<?php
// ================================================================
// CONTROLLER: PATIENT
// ================================================================

function patient_controller($conn) {
    $me = current_user();
    require __DIR__ . '/../views/patient/dashboard.php';
}

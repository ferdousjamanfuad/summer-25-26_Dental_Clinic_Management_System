<?php
// ================================================================
// CONTROLLER: DOCTOR
// ================================================================

function doctor_controller($conn) {
    $me = current_user();
    require __DIR__ . '/../views/doctor/dashboard.php';
}

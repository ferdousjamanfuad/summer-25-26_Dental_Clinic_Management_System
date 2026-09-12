<?php
// ================================================================
// CONTROLLER: RECEPTIONIST
// ================================================================

function receptionist_controller($conn) {
    $me = current_user();
    require __DIR__ . '/../views/receptionist/dashboard.php';
}

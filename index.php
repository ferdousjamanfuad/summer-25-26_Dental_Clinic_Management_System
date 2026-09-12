<?php
// ================================================================
// FRONT CONTROLLER (the router)
// Every request in the whole app enters here:
//   index.php?page=<where>&action=<what>
// ================================================================

/* ---- 1. Load the app ---- */
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/helpers/helpers.php';

/* ---- 2. Load the MODELS ---- */
require_once __DIR__ . '/models/user_model.php';
require_once __DIR__ . '/models/appointment_model.php';
require_once __DIR__ . '/models/prescription_model.php';
require_once __DIR__ . '/models/equipment_model.php';
require_once __DIR__ . '/models/bill_model.php';

/* ---- 3. Load the CONTROLLERS ---- */
require_once __DIR__ . '/controllers/auth_controller.php';
require_once __DIR__ . '/controllers/admin_controller.php';
require_once __DIR__ . '/controllers/doctor_controller.php';
require_once __DIR__ . '/controllers/patient_controller.php';
require_once __DIR__ . '/controllers/receptionist_controller.php';
require_once __DIR__ . '/controllers/ajax_controller.php';

/* ---- 4. Sign the user out if they have been idle too long ---- */
check_session_timeout();

/* ---- 5. Decide which page was asked for ---- */
$page = $_GET['page'] ?? 'login';

switch ($page) {

    // Public pages
    case 'login':
        login_controller($conn);
        break;

    case 'register':
        register_controller($conn);
        break;

    case 'logout':
        logout_controller($conn);
        break;

    // JSON endpoints
    case 'ajax':
        ajax_controller($conn);
        break;

    // Private dashboards - require_role() blocks everyone else
    case 'admin':
        require_role('admin');
        admin_controller($conn);
        break;

    case 'doctor':
        require_role('doctor');
        doctor_controller($conn);
        break;

    case 'patient':
        require_role('patient');
        patient_controller($conn);
        break;

    case 'receptionist':
        require_role('receptionist');
        receptionist_controller($conn);
        break;

    // Anything else goes home
    default:
        redirect('index.php?page=login');
}

mysqli_close($conn);
?>

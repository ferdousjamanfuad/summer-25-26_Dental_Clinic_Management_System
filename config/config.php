<?php
// ================================================================
// CONFIGURATION
// ================================================================

session_start();

define('APP_NAME', 'Safe Plus Dental Care');
define('CURRENCY', '৳');
define('SESSION_TIMEOUT', 1800); // 30 minutes

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'dental_clinic_db');

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    mysqli_set_charset($conn, "utf8mb4");
} catch (mysqli_sql_exception $e) {
    die("<h1>Database Connection Failed</h1><p>Please import the <b>database.sql</b> file into phpMyAdmin first.</p>");
}

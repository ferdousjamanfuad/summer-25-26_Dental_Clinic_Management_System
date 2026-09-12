<?php
// ================================================================
// MODEL: EQUIPMENT
// ================================================================

function get_equipments($conn) {
    $sql = "SELECT e.*, u.name as doctor_name 
            FROM equipment e 
            LEFT JOIN users u ON e.assigned_to = u.id 
            ORDER BY e.created_at DESC";
    $result = mysqli_query($conn, $sql);
    $items = [];
    if($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $items[] = $row;
        }
    }
    return $items;
}

function add_equipment($conn, $name, $description, $quantity) {
    $sql = "INSERT INTO equipment (name, description, quantity) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'ssi', $name, $description, $quantity);
    $success = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    return $success;
}
?>

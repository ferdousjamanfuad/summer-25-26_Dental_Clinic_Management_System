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

function assign_equipment($conn, $equipment_id, $doctor_id) {
    $sql = "UPDATE equipment SET assigned_to = ?, status = 'in_use' WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'ii', $doctor_id, $equipment_id);
    $success = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    return $success;
}

function unassign_equipment($conn, $equipment_id) {
    $sql = "UPDATE equipment SET assigned_to = NULL, status = 'available' WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $equipment_id);
    $success = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    return $success;
}

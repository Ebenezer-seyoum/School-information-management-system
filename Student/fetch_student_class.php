<?php
session_start();
include('../connection/connection.php');

$sid = $_SESSION['sid'] ?? '';
$year = mysqli_real_escape_string($conn, $_POST['academic_year'] ?? '');
$data = [];

if ($sid && $year) {
    $q = mysqli_query($conn, "
        SELECT s.cid AS section_id, s.section_name, s.class_type
        FROM assign_student ast
        JOIN sections s ON ast.section_id = s.cid
        JOIN students st ON ast.student_id = st.sid
        WHERE st.student_id = '$sid'
          AND ast.academic_year = '$year'
        ORDER BY s.class_type, s.section_name ASC
    ") or die(mysqli_error($conn));

    while($row = mysqli_fetch_assoc($q)){
        $data[] = [
            'section_id'   => $row['section_id'],
            'section_name' => $row['section_name'],
            'class_type'   => $row['class_type']
        ];
    }
}

// Always return JSON
header('Content-Type: application/json');
echo json_encode($data);

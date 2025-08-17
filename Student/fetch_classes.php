<?php
session_start();
include('../connection/connection.php');

$student_sid = $_SESSION['sid'] ?? '';
$academic_year = mysqli_real_escape_string($conn, $_POST['academic_year'] ?? '');
$result = [];

if($student_sid && $academic_year){
    // Get classes assigned to this student in the selected year
    $q = mysqli_query($conn, "
        SELECT s.cid, s.section_name, s.class_type
        FROM assign_student ast
        JOIN sections s ON ast.section_id = s.cid
        JOIN students c ON ast.student_id = c.sid
        WHERE c.student_id = '$student_sid'
          AND ast.academic_year = '$academic_year'
        ORDER BY s.class_type, s.section_name ASC
    ") or die(mysqli_error($conn));

    while($row = mysqli_fetch_assoc($q)){
        $result[] = [
            'cid' => $row['cid'],
            'section_name' => $row['section_name'],
            'class_type' => $row['class_type']
        ];
    }
}

// Always return JSON
header('Content-Type: application/json');
echo json_encode($result);

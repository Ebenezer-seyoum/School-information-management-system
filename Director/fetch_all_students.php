<?php
include('../connection/connection.php');

$section_id = $_POST['section_id'] ?? '';
$academic_year = $_POST['academic_year'] ?? '';

$response = [];

if($section_id && $academic_year){
    $q = mysqli_query($conn, "SELECT s.sid, s.student_id, s.first_name, s.father_name, s.gender
        FROM assign_student a
        JOIN students s ON s.sid=a.student_id
        WHERE a.section_id='$section_id' AND a.academic_year='$academic_year'
        ORDER BY s.first_name ASC");
    while($row = mysqli_fetch_assoc($q)){
        $response[] = $row;
    }
}

echo json_encode($response);

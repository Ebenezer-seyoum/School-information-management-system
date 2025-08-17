<?php
include('../connection/connection.php');

$section_id = $_POST['section_id'] ?? '';
$academic_year = $_POST['academic_year'] ?? '';

$response = [];

if($section_id && $academic_year){
    $q = mysqli_query($conn, "SELECT * FROM students s
        WHERE s.sid NOT IN (
            SELECT student_id FROM assign_student 
            WHERE section_id='$section_id' AND academic_year='$academic_year'
        )
        ORDER BY s.first_name ASC");
    while($row = mysqli_fetch_assoc($q)){
        $response[] = $row;
    }
}

echo json_encode($response);

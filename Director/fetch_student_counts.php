<?php
include('../connection/connection.php');

$section_id = $_POST['section_id'] ?? '';
$academic_year = $_POST['academic_year'] ?? '';

$response = ['status'=>false];

if($section_id && $academic_year){
    // Total students
    $total_q = mysqli_query($conn, "SELECT COUNT(*) AS total FROM assign_student a
        JOIN students s ON s.sid=a.student_id
        WHERE a.section_id='$section_id' AND a.academic_year='$academic_year'");
    $total = mysqli_fetch_assoc($total_q)['total'] ?? 0;

    // Male students
    $male_q = mysqli_query($conn, "SELECT COUNT(*) AS male FROM assign_student a
        JOIN students s ON s.sid=a.student_id
        WHERE a.section_id='$section_id' AND a.academic_year='$academic_year' AND s.gender='M'");
    $male = mysqli_fetch_assoc($male_q)['male'] ?? 0;

    // Female students
    $female_q = mysqli_query($conn, "SELECT COUNT(*) AS female FROM assign_student a
        JOIN students s ON s.sid=a.student_id
        WHERE a.section_id='$section_id' AND a.academic_year='$academic_year' AND s.gender='F'");
    $female = mysqli_fetch_assoc($female_q)['female'] ?? 0;

    $response = ['status'=>true,'total'=>$total,'male'=>$male,'female'=>$female];
}

echo json_encode($response);

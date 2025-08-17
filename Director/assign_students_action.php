<?php
include('../connection/connection.php');

$section_id = $_POST['section_id'] ?? '';
$academic_year = $_POST['academic_year'] ?? '';
$student_ids = $_POST['student_ids'] ?? [];

$response = ['status'=>false,'message'=>'No students selected.'];

if($section_id && $academic_year && !empty($student_ids)){
    $added = 0;
    foreach($student_ids as $sid){
        $check = mysqli_query($conn, "SELECT * FROM assign_student 
            WHERE student_id='$sid' AND section_id='$section_id' AND academic_year='$academic_year'");
        if(mysqli_num_rows($check) == 0){
            mysqli_query($conn, "INSERT INTO assign_student (student_id, section_id, academic_year) 
                VALUES ('$sid','$section_id','$academic_year')");
            $added++;
        }
    }
    $response = ['status'=>true,'message'=>"Successfully assigned $added students."];
}

echo json_encode($response);

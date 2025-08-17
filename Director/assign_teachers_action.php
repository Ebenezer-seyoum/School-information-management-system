<?php
include('../connection/connection.php');

header('Content-Type: application/json');

$response = ['status' => false, 'message' => 'Something went wrong.'];

$section_id = (int)($_POST['section_id'] ?? 0);
$academic_year = mysqli_real_escape_string($conn, $_POST['academic_year'] ?? '');
$subject_teacher = $_POST['subject_teacher'] ?? [];

if($section_id && $academic_year && !empty($subject_teacher)){
    foreach($subject_teacher as $subject_id => $teacher_id){
        $subject_id = (int)$subject_id;
        $teacher_id = (int)$teacher_id;

        // Check if already assigned
        $check_q = mysqli_query($conn, "SELECT * FROM assign_teacher WHERE section_id=$section_id AND subject_id=$subject_id AND academic_year='$academic_year'");
        if(mysqli_num_rows($check_q) > 0){
            // Already assigned, skip or update if needed
            continue;
        }

        // Insert assignment
        mysqli_query($conn, "INSERT INTO assign_teacher (section_id, subject_id, teacher_id, academic_year) VALUES ($section_id, $subject_id, $teacher_id, '$academic_year')");
    }

    $response['status'] = true;
    $response['message'] = 'Teachers assigned successfully!';
} else {
    $response['message'] = 'Please select section, academic year, and at least one teacher.';
}

echo json_encode($response);

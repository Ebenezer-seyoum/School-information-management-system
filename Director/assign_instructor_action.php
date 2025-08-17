<?php
include('../connection/connection.php');

$response = ['status' => false, 'message' => 'Something went wrong!'];

// Check required POST data
$instructor_ids = $_POST['instructor_id'] ?? [];
$academic_year  = $_POST['academic_year'] ?? '';

if($academic_year && !empty($instructor_ids)){
    $academic_year_safe = mysqli_real_escape_string($conn, $academic_year);

    $success_count = 0;

    foreach($instructor_ids as $section_id => $instructor_id){
        $section_id   = (int)$section_id;
        $instructor_id = (int)$instructor_id;

        if(!$instructor_id) continue; // skip empty selections

        // Check if already assigned
        $check_q = mysqli_query($conn, "SELECT * FROM assign_instructor WHERE section_id = $section_id AND academic_year = '$academic_year_safe'");
        if(mysqli_num_rows($check_q) > 0){
            // Update existing assignment
            $update_q = mysqli_query($conn, "UPDATE assign_instructor SET instructor_id = $instructor_id WHERE section_id = $section_id AND academic_year = '$academic_year_safe'");
            if($update_q) $success_count++;
        } else {
            // Insert new assignment
            $insert_q = mysqli_query($conn, "INSERT INTO assign_instructor (section_id, instructor_id, academic_year) VALUES ($section_id, $instructor_id, '$academic_year_safe')");
            if($insert_q) $success_count++;
        }
    }

    $response['status'] = true;
    $response['message'] = "Successfully assigned $success_count section(s)!";
} else {
    $response['message'] = "No instructors selected or academic year missing!";
}

echo json_encode($response);

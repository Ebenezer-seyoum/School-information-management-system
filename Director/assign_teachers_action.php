<?php
include('../connection/connection.php');

header('Content-Type: application/json');

$response = ['status' => false, 'message' => 'Something went wrong.'];

$section_id = (int)($_POST['section_id'] ?? 0);
$academic_year = mysqli_real_escape_string($conn, $_POST['academic_year'] ?? '');
$subject_teacher = $_POST['subject_teacher'] ?? [];

if($section_id && $academic_year && !empty($subject_teacher)){
    $inserted = 0; $updated = 0; $unchanged = 0;
    foreach($subject_teacher as $subject_id => $teacher_id){
        $subject_id = (int)$subject_id;
        $teacher_id = (int)$teacher_id;

        // Validate inputs
        if ($subject_id <= 0 || $teacher_id <= 0) { continue; }

        // Check if already assigned
        $check_q = mysqli_query($conn, "SELECT teacher_id FROM assign_teacher WHERE section_id=$section_id AND subject_id=$subject_id AND academic_year='$academic_year' LIMIT 1");
        if(mysqli_num_rows($check_q) > 0){
            $row = mysqli_fetch_assoc($check_q);
            $current = (int)$row['teacher_id'];
            if ($current !== $teacher_id){
                mysqli_query($conn, "UPDATE assign_teacher SET teacher_id=$teacher_id WHERE section_id=$section_id AND subject_id=$subject_id AND academic_year='$academic_year' LIMIT 1");
                if (mysqli_affected_rows($conn) >= 0) $updated++;
            } else {
                $unchanged++;
            }
        } else {
            // Insert assignment
            mysqli_query($conn, "INSERT INTO assign_teacher (section_id, subject_id, teacher_id, academic_year) VALUES ($section_id, $subject_id, $teacher_id, '$academic_year')");
            if (mysqli_affected_rows($conn) > 0) $inserted++;
        }
    }

    $response['status'] = true;
    $response['message'] = 'Assignment saved. Inserted: ' . $inserted . ', Updated: ' . $updated . ($unchanged? ', Unchanged: ' . $unchanged : '');
} else {
    $response['message'] = 'Please select section, academic year, and at least one teacher.';
}

echo json_encode($response);

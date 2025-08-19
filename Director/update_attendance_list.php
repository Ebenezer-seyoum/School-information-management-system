<?php
include('../connection/connection.php');
session_start();
header('Content-Type: application/json');
ob_clean(); // clear any accidental whitespace/HTML

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(["success" => false, "message" => "Invalid request method."]);
        exit;
    }

    $section_id    = (int)$_POST['section_id'];
    $academic_year = mysqli_real_escape_string($conn, $_POST['academic_year']);
    $instructor_id = (int)$_POST['instructor_id'];
    $semester      = (int)$_POST['semester'];
    $attendance    = $_POST['attendance'] ?? [];

    foreach ($attendance as $sid => $days) {
        foreach ($days as $date => $data) {
            $sid       = (int)$sid;
            $dateEsc   = mysqli_real_escape_string($conn, $date);
            $attend_id = isset($data['attend_id']) ? (int)$data['attend_id'] : 0;
            $statusEsc = mysqli_real_escape_string($conn, $data['status']);

            if ($attend_id > 0) {
                // update existing
                $q = "UPDATE attendance 
                      SET status='$statusEsc', instructor_id=$instructor_id 
                      WHERE attend_id=$attend_id";
                mysqli_query($conn, $q);
            } else {
                // insert new
                $q = "INSERT INTO attendance 
                      (student_id, section_id, academic_year, semester, attendance_date, status, instructor_id) 
                      VALUES ($sid, $section_id, '$academic_year', $semester, '$dateEsc', '$statusEsc', $instructor_id)";
                mysqli_query($conn, $q);
            }
        }
    }

    echo json_encode(["success" => true, "message" => "Attendance updated successfully."]);

} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}

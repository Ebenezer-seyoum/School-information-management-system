<?php
include('../connection/connection.php');
session_start();

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_POST['attendance'])) {
    echo json_encode(["success" => false, "message" => "Invalid request."]);
    exit;
}

$section_id = (int)$_POST['section_id'];
$year = mysqli_real_escape_string($conn, $_POST['academic_year']);
$instructor = (int)$_POST['instructor_id'];
$semester = (int)$_POST['semester'];
$sessionName = "Morning";

// IMPORTANT: Ensure you have a UNIQUE KEY like:
// ALTER TABLE attendance ADD UNIQUE KEY uniq_att (student_id, section_id, attendance_date, session);

foreach ($_POST['attendance'] as $student_id => $days) {
    $student_id = (int)$student_id;

    foreach ($days as $date => $status) {
        $date = mysqli_real_escape_string($conn, $date);
        $status = mysqli_real_escape_string($conn, $status);

        $sql = "INSERT INTO attendance 
                (student_id, section_id, academic_year, semester, instructor_id, attendance_date, session, status)
                VALUES ($student_id, $section_id, '$year', $semester, $instructor, '$date', '$sessionName', '$status')
                ON DUPLICATE KEY UPDATE status='$status'";

        if (!mysqli_query($conn, $sql)) {
            echo json_encode(["success" => false, "message" => "DB Error: " . mysqli_error($conn)]);
            exit;
        }
    }
}

echo json_encode(["success" => true, "message" => "Attendance saved successfully!"]);
